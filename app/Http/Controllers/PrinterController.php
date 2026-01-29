<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrinterController extends Controller
{
    public function __construct()
    {
        // Remove middleware calls from constructor
        // We'll handle authorization in each method
    }

    public function index()
    {
        $printers = Printer::orderBy('name')->get();
        return view('printers.index', compact('printers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Thermal,Inkjet,Laser,Dot Matrix,POS',
            'connection_type' => 'required|in:USB,Network,Bluetooth,Serial',
            'ip_address' => 'nullable|required_if:connection_type,Network|ip',
            'port' => 'nullable|integer|min:1|max:65535',
            'paper_type' => 'required|in:thermal,regular,cashier',
            'paper_width' => 'required|in:58,80,112,210',
            'location' => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'print_density' => 'required|in:low,medium,high',
            'description' => 'nullable|string|max:1000',
        ]);

        $printer = Printer::create([
            'name' => $request->name,
            'type' => $request->type,
            'connection_type' => $request->connection_type,
            'ip_address' => $request->ip_address,
            'port' => $request->port,
            'paper_type' => $request->paper_type,
            'paper_width' => $request->paper_width,
            'location' => $request->location,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'print_density' => $request->print_density,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الطابعة بنجاح',
            'printer' => $printer
        ]);
    }

    public function testConnection(Request $request)
    {
        $printerId = $request->input('printer_id');
        $printer = Printer::find($printerId);

        if (!$printer) {
            return response()->json([
                'success' => false,
                'message' => __('printers.printer_not_found')
            ], 404);
        }

        try {
            $connectionInfo = $this->getPrinterConnectionInfo($printer);

            return response()->json([
                'success' => true,
                'message' => __('printers.connection_successful'),
                'printer_info' => $connectionInfo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('printers.connection_failed') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    private function getPrinterConnectionInfo($printer)
    {
        $info = [
            'name' => $printer->name,
            'type' => $printer->type,
            'connection_type' => $printer->connection_type,
            'status' => 'offline',
            'response_time' => null,
            'details' => []
        ];

        if ($printer->connection_type === 'Network') {
            $info = array_merge($info, $this->testNetworkPrinter($printer));
        } elseif ($printer->connection_type === 'USB') {
            $info = array_merge($info, $this->testUSBPrinter($printer));
        } elseif ($printer->connection_type === 'Bluetooth') {
            $info = array_merge($info, $this->testBluetoothPrinter($printer));
        }

        return $info;
    }

    private function testNetworkPrinter($printer)
    {
        $host = $printer->ip_address;
        $port = $printer->port ?? 9100;

        $startTime = microtime(true);

        try {
            $socket = @fsockopen($host, $port, $errno, $errstr, 5);

            if ($socket) {
                fclose($socket);
                $endTime = microtime(true);
                $responseTime = round(($endTime - $startTime) * 1000, 2);

                return [
                    'status' => 'online',
                    'response_time' => $responseTime . 'ms',
                    'details' => [
                        'ip_address' => $host,
                        'port' => $port,
                        'protocol' => 'TCP/IP',
                        'last_tested' => now()->format('Y-m-d H:i:s')
                    ]
                ];
            } else {
                return [
                    'status' => 'offline',
                    'error' => "Connection failed: $errstr ($errno)",
                    'details' => [
                        'ip_address' => $host,
                        'port' => $port,
                        'error_code' => $errno,
                        'error_message' => $errstr
                    ]
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'details' => [
                    'ip_address' => $host,
                    'port' => $port
                ]
            ];
        }
    }

    private function testUSBPrinter($printer)
    {
        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $command = "powershell \"Get-Printer | Where-Object {$printer->name} | Select-Object Name, Type, DriverName, PortName | ConvertTo-Json\"";
                $output = shell_exec($command);

                if ($output) {
                    $printerInfo = json_decode($output, true);
                    if ($printerInfo && isset($printerInfo[0])) {
                        return [
                            'status' => 'online',
                            'details' => [
                                'driver_name' => $printerInfo[0]['DriverName'] ?? 'Unknown',
                                'port_name' => $printerInfo[0]['PortName'] ?? 'Unknown',
                                'printer_type' => $printerInfo[0]['Type'] ?? 'Unknown',
                                'last_tested' => now()->format('Y-m-d H:i:s')
                            ]
                        ];
                    }
                }
            }

            return [
                'status' => 'offline',
                'details' => [
                    'connection_type' => 'USB',
                    'note' => 'USB printer detection requires system-level access',
                    'last_tested' => now()->format('Y-m-d H:i:s')
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'details' => [
                    'connection_type' => 'USB'
                ]
            ];
        }
    }

    private function testBluetoothPrinter($printer)
    {
        return [
            'status' => 'unknown',
            'details' => [
                'connection_type' => 'Bluetooth',
                'note' => 'Bluetooth printer testing requires Bluetooth API integration',
                'last_tested' => now()->format('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * Print test page for a specific printer
     */
    public function printTestPage(Request $request, $id)
    {
        $printer = Printer::findOrFail($id);

        try {
            $result = $this->sendTestPage($printer);

            return response()->json([
                'success' => true,
                'message' => 'Test page sent to printer successfully',
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to print test page: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test page to printer based on connection type
     */
    private function sendTestPage($printer)
    {
        $testContent = $this->generateTestPageContent($printer);

        switch ($printer->connection_type) {
            case 'Network':
                return $this->printToNetworkPrinter($printer, $testContent);
            case 'USB':
                return $this->printToUSBPrinter($printer, $testContent);
            case 'Bluetooth':
                return $this->printToBluetoothPrinter($printer, $testContent);
            default:
                throw new \Exception("Unsupported connection type: {$printer->connection_type}");
        }
    }

    /**
     * Generate test page content
     */
    private function generateTestPageContent($printer)
    {
        $content = "================================\n";
        $content .= "           TEST PAGE\n";
        $content .= "================================\n\n";
        $content .= "Printer Name: {$printer->name}\n";
        $content .= "Type: {$printer->type}\n";
        $content .= "Connection: {$printer->connection_type}\n";
        $content .= "Location: {$printer->location}\n";
        $content .= "Paper Type: {$printer->paper_type}\n";
        $content .= "Paper Width: {$printer->paper_width}mm\n\n";
        $content .= "Test Date: " . now()->format('Y-m-d H:i:s') . "\n\n";
        $content .= "================================\n";
        $content .= "      SYSTEM INFORMATION\n";
        $content .= "================================\n";
        $content .= "Application: Restaurant POS\n";
        $content .= "Server: " . request()->getHost() . "\n";
        $content .= "User: " . (auth()->check() ? auth()->user()->name : 'Guest') . "\n\n";
        $content .= "================================\n";
        $content .= "        PRINT QUALITY TEST\n";
        $content .= "================================\n";
        $content .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ\n";
        $content .= "abcdefghijklmnopqrstuvwxyz\n";
        $content .= "0123456789\n";
        $content .= "!@#$%^&*()_+-=[]{}|;':\",./<>?\n\n";
        $content .= "================================\n";
        $content .= "           END OF TEST\n";
        $content .= "================================\n\n";
        $content .= str_repeat("\n", 5); // Add some blank lines for paper feed

        return $content;
    }

    /**
     * Print to network printer
     */
    private function printToNetworkPrinter($printer, $content)
    {
        $socket = @fsockopen($printer->ip_address, $printer->port ?? 9100, $errno, $errstr, 10);

        if (!$socket) {
            throw new \Exception("Cannot connect to printer: {$errstr} ({$errno})");
        }

        try {
            // Send print commands for thermal printer
            if ($printer->type === 'Thermal' || $printer->type === 'POS') {
                // Initialize printer
                fwrite($socket, "\x1B\x40"); // Initialize

                // Set alignment to center
                fwrite($socket, "\x1B\x61\x01");

                // Send content
                fwrite($socket, $content);

                // Cut paper (if supported)
                fwrite($socket, "\x1D\x56\x00"); // Full cut
            } else {
                // For non-thermal printers, send raw content
                fwrite($socket, $content);
            }

            fclose($socket);

            return [
                'status' => 'success',
                'message' => 'Print job sent successfully',
                'bytes_sent' => strlen($content)
            ];
        } catch (\Exception $e) {
            fclose($socket);
            throw $e;
        }
    }

    /**
     * Print to USB printer (Windows)
     */
    private function printToUSBPrinter($printer, $content)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows: Use PowerShell to send to printer
            $tempFile = tempnam(sys_get_temp_dir(), 'print_test_');
            file_put_contents($tempFile, $content);

            $command = "powershell -Command \"Get-Content '$tempFile' | Out-Printer -Name '{$printer->name}'\"";

            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            unlink($tempFile);

            if ($returnCode !== 0) {
                throw new \Exception("Failed to print to USB printer. Return code: {$returnCode}");
            }

            return [
                'status' => 'success',
                'message' => 'Print job sent to USB printer',
                'output' => implode("\n", $output)
            ];
        } else {
            // Linux/Mac: Use lp command
            $command = "lp -d '{$printer->name}' -o raw";
            $process = proc_open($command, [
                0 => ['pipe', 'r'], // stdin
                1 => ['pipe', 'w'], // stdout
                2 => ['pipe', 'w']  // stderr
            ], $pipes);

            if (!is_resource($process)) {
                throw new \Exception("Failed to start print process");
            }

            fwrite($pipes[0], $content);
            fclose($pipes[0]);

            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $returnCode = proc_close($process);

            if ($returnCode !== 0) {
                throw new \Exception("Failed to print: {$stderr}");
            }

            return [
                'status' => 'success',
                'message' => 'Print job sent to USB printer',
                'output' => $stdout
            ];
        }
    }

    /**
     * Print to Bluetooth printer
     */
    private function printToBluetoothPrinter($printer, $content)
    {
        // Bluetooth printing requires special libraries and pairing
        // For now, return a placeholder response
        return [
            'status' => 'not_implemented',
            'message' => 'Bluetooth printing requires Bluetooth API integration',
            'note' => 'Please implement Bluetooth printing using appropriate libraries'
        ];
    }

    public function update(Request $request, $id)
    {
        $printer = Printer::findOrFail($id);
        //return $printer;

        $request->validate([
            'name'                              => 'required|string|max:255',
            'type'                              => 'required|in:thermal,pos,laser,inkjet,matrix',
            'connection_type'                   => 'required|in:Network,USB,Bluetooth',
            'ip_address'                        => 'nullable|required_if:connection_type,Network|ip',
            'port'                              => 'nullable|integer|min:1|max:65535',
            'location'                          => 'required|string|max:255',
            'manufacturer'                      => 'nullable|string|max:255',
            'model'                             => 'nullable|string|max:255',
            'paper_type'                        => 'required|in:thermal,regular,cashier,label',
            'paper_width'                       => 'required|in:58,80,112,210,297',
            'print_density'                     => 'required|in:low,medium,high',
            'description'                       => 'nullable|string',
            'usb_name'                          => 'nullable|string',
            'bluetooth_address'                 => 'nullable|string',
            'printer_settings.orientation'      => 'string',
            'printer_settings.custom_width'     => 'nullable|integer',
            'printer_settings.custom_height'    => 'nullable|integer',
        ]);

        // Update basic printer info
        $printer->update([
            'name' => $request->name,
            'type' => $request->type,
            'connection_type' => $request->connection_type,
            'ip_address' => $request->ip_address,
            'port' => $request->port ?? 9100,
            'location' => $request->location,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'paper_type' => $request->paper_type,
            'paper_width' => $request->paper_width,
            'print_density' => $request->print_density,
            'description' => $request->description,
        ]);


        // Handle default printer
        if ($request->boolean('is_default')) {
            Printer::where('id', '!=', $printer->id)->update(['is_default' => false]);
        }

        // Update connection-specific settings
        $connectionSettings = [];
        if ($request->connection_type === 'USB' && $request->usb_name) {
            $connectionSettings['usb_name'] = $request->usb_name;
        } elseif ($request->connection_type === 'Bluetooth' && $request->bluetooth_address) {
            $connectionSettings['bluetooth_address'] = $request->bluetooth_address;
        }
        $printer->settings = $connectionSettings;

        // Update physical printer settings
        $printerSettings = [
            'orientation' => $request->input('printer_settings.orientation', 'portrait'),
            'custom_width' => $request->input('printer_settings.custom_width'),
            'custom_height' => $request->input('printer_settings.custom_height'),
            'double_sided' => $request->boolean('printer_settings.double_sided'),
        ];
        $printer->printer_settings = $printerSettings;

        $printer->save();
        // return $printer;

        return redirect()->back()->with('success', 'تم تحديث الطابعة بنجاح');
    }

    public function destroy($id)
    {
        $printer = Printer::findOrFail($id);
        $printer->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الطابعة بنجاح'
        ]);
    }

    public function printTest($id)
    {
        $printer = Printer::findOrFail($id);

        if (!$printer->is_online) {
            return response()->json([
                'success' => false,
                'message' => 'الطابعة غير متصلة'
            ]);
        }

        $success = $printer->printTest();

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'تم طباعة اختبار بنجاح'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'فشل الطباعة'
            ]);
        }
    }

    public function show($id)
    {
        $printer = Printer::findOrFail($id);
        return view('printers.show', compact('printer'));
    }

    /**
     * Show the form for editing the specified printer
     */
    public function edit($id)
    {
        $printer = Printer::findOrFail($id);
        return view('printers.edit', compact('printer'));
    }

    public function printContent(Request $request, $id)
    {
        $printer = Printer::findOrFail($id);

        $request->validate([
            'content' => 'required|string',
            'type' => 'required|in:order,invoice,receipt'
        ]);

        if (!$printer->is_online || !$printer->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'الطابعة غير متاحة للطباعة'
            ]);
        }

        $success = $printer->printContent($request->content);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'تم الطباعة بنجاح'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'فشل الطباعة'
            ]);
        }
    }
}
