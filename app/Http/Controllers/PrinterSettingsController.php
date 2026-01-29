<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrinterSettingsController extends Controller
{
    /**
     * Display printer settings page
     */
    public function index()
    {
        $printers = Printer::where('is_active', true)->get();
        $defaultPrinter = Printer::where('is_default', true)->first();
        
        return view('printer-settings.index', compact('printers', 'defaultPrinter'));
    }

    /**
     * Update printer settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'default_printer_id' => 'nullable|exists:printers,id',
            'auto_print_receipt' => 'boolean',
            'receipt_header' => 'nullable|string|max:500',
            'receipt_footer' => 'nullable|string|max:500',
            'print_logo' => 'boolean',
            'print_qr_code' => 'boolean',
            'paper_width' => 'required|in:58,80,112',
            'print_density' => 'required|in:low,medium,high',
            'copies' => 'required|integer|min:1|max:5',
        ]);

        // Update default printer
        if ($request->has('default_printer_id')) {
            Printer::where('is_default', true)->update(['is_default' => false]);
            Printer::find($request->default_printer_id)->update(['is_default' => true]);
        }

        // Save settings to session or database
        session([
            'printer_settings' => [
                'auto_print_receipt' => $request->boolean('auto_print_receipt'),
                'receipt_header' => $request->receipt_header,
                'receipt_footer' => $request->receipt_footer,
                'print_logo' => $request->boolean('print_logo'),
                'print_qr_code' => $request->boolean('print_qr_code'),
                'paper_width' => $request->paper_width,
                'print_density' => $request->print_density,
                'copies' => $request->copies,
                'default_printer_id' => $request->default_printer_id,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ إعدادات الطباعة بنجاح'
        ]);
    }

    /**
     * Get current printer settings
     */
    public function getSettings()
    {
        $settings = session('printer_settings', [
            'auto_print_receipt' => true,
            'receipt_header' => 'Restaurant POS',
            'receipt_footer' => 'شكراً لزيارتكم',
            'print_logo' => true,
            'print_qr_code' => true,
            'paper_width' => '80',
            'print_density' => 'medium',
            'copies' => 1,
        ]);

        return response()->json($settings);
    }

    /**
     * Test receipt with current settings
     */
    public function testReceipt()
    {
        $settings = session('printer_settings', []);
        $defaultPrinter = Printer::where('is_default', true)->first();
        
        if (!$defaultPrinter) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم تحديد طابعة افتراضية'
            ], 400);
        }

        try {
            $receiptContent = $this->generateSampleReceipt($settings);
            $result = $this->printReceipt($defaultPrinter, $receiptContent);
            
            return response()->json([
                'success' => true,
                'message' => 'تم طباعة إيصال تجريبي بنجاح',
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل طباعة الإيصال التجريبي: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate sample receipt content
     */
    private function generateSampleReceipt($settings)
    {
        $content = "";
        
        // Header
        if ($settings['print_logo'] ?? true) {
            $content .= "\x1B\x21\x00"; // Double height
            $content .= "      RESTAURANT POS      \n";
            $content .= "\x1B\x21\x00"; // Normal size
        }
        
        if (!empty($settings['receipt_header'])) {
            $content .= $settings['receipt_header'] . "\n";
        }
        
        $content .= "================================\n";
        $content .= "           إيصال تجريبي\n";
        $content .= "================================\n\n";
        
        // Order items
        $content .= str_pad("الصنف", 20) . str_pad("الكمية", 8) . str_pad("السعر", 10) . "\n";
        $content .= "--------------------------------\n";
        $content .= str_pad("شاي", 20) . str_pad("2", 8) . str_pad("10.00", 10) . "\n";
        $content .= str_pad("قهوة", 20) . str_pad("1", 8) . str_pad("15.00", 10) . "\n";
        $content .= str_pad("ساندوتش", 20) . str_pad("3", 8) . str_pad("45.00", 10) . "\n";
        $content .= "--------------------------------\n";
        $content .= str_pad("الإجمالي:", 20) . str_pad("", 8) . str_pad("70.00", 10) . "\n";
        $content .= str_pad("ضريبة:", 20) . str_pad("", 8) . str_pad("7.00", 10) . "\n";
        $content .= str_pad("المجموع:", 20) . str_pad("", 8) . str_pad("77.00", 10) . "\n\n";
        
        // QR Code placeholder
        if ($settings['print_qr_code'] ?? true) {
            $content .= "================================\n";
            $content .= "           QR CODE\n";
            $content .= "    [رمز QR للدفع الإلكتروني]\n";
            $content .= "================================\n\n";
        }
        
        // Footer
        if (!empty($settings['receipt_footer'])) {
            $content .= $settings['receipt_footer'] . "\n";
        }
        
        $content .= "\n";
        $content .= "التاريخ: " . now()->format('Y-m-d H:i:s') . "\n";
        $content .= "الكاشير: " . (Auth::check() ? Auth::user()->name : 'تجريبي') . "\n";
        
        // Add paper feed
        $content .= str_repeat("\n", 5);
        
        return $content;
    }

    /**
     * Print receipt to printer
     */
    private function printReceipt($printer, $content)
    {
        switch ($printer->connection_type) {
            case 'Network':
                return $this->printToNetworkPrinter($printer, $content);
            case 'USB':
                return $this->printToUSBPrinter($printer, $content);
            case 'Bluetooth':
                return $this->printToBluetoothPrinter($printer, $content);
            default:
                throw new \Exception("Unsupported connection type");
        }
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
            // Initialize printer
            fwrite($socket, "\x1B\x40");
            
            // Set alignment
            fwrite($socket, "\x1B\x61\x01");
            
            // Send content
            fwrite($socket, $content);
            
            // Cut paper
            fwrite($socket, "\x1D\x56\x00");
            
            fclose($socket);
            
            return [
                'status' => 'success',
                'message' => 'Receipt printed successfully',
                'bytes_sent' => strlen($content)
            ];
        } catch (\Exception $e) {
            fclose($socket);
            throw $e;
        }
    }

    /**
     * Print to USB printer
     */
    private function printToUSBPrinter($printer, $content)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $tempFile = tempnam(sys_get_temp_dir(), 'receipt_');
            file_put_contents($tempFile, $content);
            
            $command = "powershell -Command \"Get-Content '$tempFile' | Out-Printer -Name '{$printer->name}'\"";
            
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            unlink($tempFile);
            
            if ($returnCode !== 0) {
                throw new \Exception("Failed to print receipt. Return code: {$returnCode}");
            }
            
            return [
                'status' => 'success',
                'message' => 'Receipt printed to USB printer',
                'output' => implode("\n", $output)
            ];
        } else {
            $command = "lp -d '{$printer->name}' -o raw";
            $process = proc_open($command, [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ], $pipes);
            
            if (!is_resource($process)) {
                throw new \Exception("Failed to start print process");
            }
            
            fwrite($pipes[0], $content);
            fclose($pipes[0]);
            
            fclose($pipes[1]);
            fclose($pipes[2]);
            
            $returnCode = proc_close($process);
            
            if ($returnCode !== 0) {
                throw new \Exception("Failed to print receipt");
            }
            
            return [
                'status' => 'success',
                'message' => 'Receipt printed to USB printer'
            ];
        }
    }

    /**
     * Print to Bluetooth printer
     */
    private function printToBluetoothPrinter($printer, $content)
    {
        return [
            'status' => 'not_implemented',
            'message' => 'Bluetooth printing not implemented yet'
        ];
    }
}
