<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class PrinterDiscoveryController extends Controller
{
    /**
     * Discover printers using Windows API
     */
    public function discover()
    {
        try {
            // Use simple shell_exec instead of Process
            $command = 'wmic printer get name';
            $output = shell_exec($command);

            if (!empty($output)) {
                $printers = [];
                $lines = explode("\n", $output);

                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === 'Name' || empty($line)) {
                        continue;
                    }
                    if (!empty($line)) {
                        $printers[] = $line;
                    }
                }

                return response()->json([
                    'success' => true,
                    'printers' => $printers,
                    'count' => count($printers)
                ]);
            } else {
                // Fallback to PowerShell
                return $this->discoverWithPowerShell();
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to discover printers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse WMIC output to extract printer names
     */
    private function parseWmicOutput($output)
    {
        $printers = [];
        $lines = explode("\n", $output);

        foreach ($lines as $line) {
            $line = trim($line);
            // Skip header and empty lines
            if ($line === 'Name' || empty($line)) {
                continue;
            }
            // Add any non-empty line as printer name
            if (!empty($line)) {
                $printers[] = $line;
            }
        }

        return array_filter($printers);
    }

    /**
     * Discover printers using PowerShell as fallback
     */
    private function discoverWithPowerShell()
    {
        try {
            $command = 'powershell "Get-Printer | Select-Object Name | ConvertTo-Json"';
            $output = shell_exec($command);

            if (!empty($output)) {
                $data = json_decode($output, true);

                if (is_array($data)) {
                    $printers = [];
                    foreach ($data as $printer) {
                        if (isset($printer['Name']) && !empty($printer['Name'])) {
                            $printers[] = $printer['Name'];
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'printers' => $printers,
                        'count' => count($printers)
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Final fallback - return common printer names
            return response()->json([
                'success' => true,
                'printers' => [
                    'Microsoft Print to PDF',
                    'Microsoft XPS Document Writer',
                    'Fax'
                ],
                'count' => 3,
                'message' => 'Using fallback method - please check printer connections manually'
            ]);
        }
    }

    /**
     * Test printer connection
     */
    public function testPrinter($printerName)
    {
        try {
            $command = 'powershell "Get-Printer -Name \'' . $printerName . '\' | Select-Object Name, PrinterStatus, PrinterState"';
            $process = Process::run($command);

            if ($process->successful()) {
                $output = $process->output();

                return response()->json([
                    'success' => true,
                    'printer' => $printerName,
                    'status' => 'Connected',
                    'details' => $output
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Printer not found or not accessible'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to test printer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print test page
     */
    public function printTestPage(Request $request)
    {
        $printerName = $request->input('printer_name');

        if (!$printerName) {
            return response()->json([
                'success' => false,
                'message' => 'Printer name is required'
            ], 400);
        }

        try {
            // Create a simple test file
            $testContent = "Printer Test Page\n\nPrinter: " . $printerName . "\nDate: " . date('Y-m-d H:i:s') . "\n\nThis is a test page from the restaurant management system.";

            $tempFile = tempnam(sys_get_temp_dir(), 'printer_test_') . '.txt';
            file_put_contents($tempFile, $testContent);

            // Print the test file
            $command = 'powershell "Get-Content -Path \'' . $tempFile . '\' | Out-Printer -Name \'' . $printerName . '\'"';
            $process = Process::run($command);

            // Clean up temp file
            unlink($tempFile);

            if ($process->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test page sent to printer: ' . $printerName
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send test page to printer'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to print test page: ' . $e->getMessage()
            ], 500);
        }
    }
}
