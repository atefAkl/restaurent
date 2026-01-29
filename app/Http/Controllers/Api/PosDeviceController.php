<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosDevice;
use App\Services\PosDeviceConnector;
use App\Helpers\PosDeviceIdentifier;
use App\Helpers\PosDevicePorts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PosDeviceController extends Controller
{
    protected $connector;

    public function __construct(PosDeviceConnector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * Discover connected POS devices
     */
    public function discover()
    {
        try {
            $devices = [];

            // Method 1: Try to get USB devices using PowerShell
            $usbDevices = $this->getUsbDevices();
            $devices = array_merge($devices, $usbDevices);

            // Method 2: Try to get COM ports (for serial devices)
            $comPorts = $this->getComPorts();
            $devices = array_merge($devices, $comPorts);

            // Method 3: Try to get network devices
            $networkDevices = $this->getNetworkDevices();
            $devices = array_merge($devices, $networkDevices);

            return response()->json([
                'success' => true,
                'devices' => $devices,
                'count' => count($devices)
            ]);
        } catch (\Exception $e) {
            Log::error('POS Device Discovery Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'devices' => []
            ], 500);
        }
    }

    /**
     * Get USB devices that might be POS devices
     */
    private function getUsbDevices()
    {
        $devices = [];

        try {
            // Simplified PowerShell command to get USB devices
            $command = 'powershell -Command "Get-WmiObject Win32_USBControllerDevice | ForEach-Object { [wmi]$_.Dependent } | Where-Object { $_.DeviceID -like \'*USB*\' -and $_.DeviceID -like \'*VID_*\' } | Select-Object Name, DeviceID, Description | ConvertTo-Json"';

            $output = shell_exec($command);

            if ($output) {
                $data = json_decode($output, true);

                if (is_array($data)) {
                    foreach ($data as $device) {
                        if (isset($device['Name']) && isset($device['DeviceID'])) {
                            // Use the helper to identify device type
                            $deviceType = PosDeviceIdentifier::identifyDeviceType(
                                $device['Name'],
                                $device['Description'] ?? ''
                            );

                            // Use the helper to get manufacturer
                            $manufacturer = PosDeviceIdentifier::getManufacturer($device['DeviceID']);

                            $devices[] = [
                                'name' => $device['Name'],
                                'device_id' => $device['DeviceID'],
                                'description' => $device['Description'] ?? 'USB POS Device',
                                'type' => $deviceType,
                                'manufacturer' => $manufacturer,
                                'connection_type' => 'USB',
                                'status' => 'connected'
                            ];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('USB Devices Error: ' . $e->getMessage());
        }

        return $devices;
    }

    /**
     * Get COM ports for serial devices
     */
    private function getComPorts()
    {
        $devices = [];

        try {
            // PowerShell command to get COM ports
            $command = 'powershell -Command "[System.IO.Ports.SerialPort]::getportnames() | ConvertTo-Json"';

            $output = shell_exec($command);

            if ($output) {
                $ports = json_decode($output, true);

                if (is_array($ports)) {
                    foreach ($ports as $port) {
                        $devices[] = [
                            'name' => 'Serial Port ' . $port,
                            'device_id' => $port,
                            'description' => 'COM Port for POS Device',
                            'type' => 'Serial',
                            'connection_type' => 'Serial',
                            'port' => $port,
                            'status' => 'available'
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('COM Ports Error: ' . $e->getMessage());
        }

        return $devices;
    }

    /**
     * Get network devices (for network POS devices)
     */
    private function getNetworkDevices()
    {
        $devices = [];

        try {
            // Get local network range (simple approach)
            $localIP = $this->getLocalIP();
            if (!$localIP) {
                return $devices;
            }

            $networkRange = $this->getNetworkRange($localIP);

            // Common POS ports to scan
            $commonPorts = [9100, 8008, 8009, 5969, 5968, 4000, 4999, 6100, 10009, 10001];

            foreach ($networkRange as $ip) {
                foreach ($commonPorts as $port) {
                    if ($this->testPort($ip, $port)) {
                        $deviceType = $this->identifyDeviceByPort($port);

                        $devices[] = [
                            'name' => "Device at {$ip}:{$port}",
                            'device_id' => "NETWORK_{$ip}_{$port}",
                            'ip_address' => $ip,
                            'port' => $port,
                            'description' => "Network {$deviceType} ({$ip}:{$port})",
                            'type' => $deviceType,
                            'manufacturer' => 'Unknown',
                            'connection_type' => 'Network',
                            'status' => 'connected'
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Network Devices Error: ' . $e->getMessage());
        }

        return $devices;
    }

    /**
     * Get local IP address
     */
    private function getLocalIP(): ?string
    {
        $ip = null;

        // Try to get local IP
        $command = 'powershell -Command "(Get-NetIPAddress -AddressFamily IPv4 -InterfaceAlias Ethernet).IPAddress"';
        $output = shell_exec($command);

        if ($output) {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (filter_var(trim($line), FILTER_VALIDATE_IP)) {
                    $ip = trim($line);
                    break;
                }
            }
        }

        // Fallback to localhost
        if (!$ip) {
            $ip = '127.0.0.1';
        }

        return $ip;
    }

    /**
     * Get network range to scan
     */
    private function getNetworkRange(string $localIP): array
    {
        $ips = [];

        // Get first 3 octets and scan last octet
        $parts = explode('.', $localIP);
        if (count($parts) === 4) {
            $baseIP = "{$parts[0]}.{$parts[1]}.{$parts[2]}";

            // Scan common range (1-254)
            for ($i = 1; $i <= 254; $i++) {
                if ($i != $parts[3]) { // Skip our own IP
                    $ips[] = "{$baseIP}.{$i}";
                }
            }
        }

        // Limit to first 50 IPs to avoid long scanning
        return array_slice($ips, 0, 50);
    }

    /**
     * Test if port is open
     */
    private function testPort(string $ip, int $port): bool
    {
        $connection = @fsockopen($ip, $port, $errno, $errstr, 0.5);

        if ($connection) {
            fclose($connection);
            return true;
        }

        return false;
    }

    /**
     * Identify device type by port
     */
    private function identifyDeviceByPort(int $port): string
    {
        $portMap = [
            9100 => 'Thermal Printer',
            8008 => 'Thermal Printer',
            8009 => 'Thermal Printer',
            5969 => 'Thermal Printer',
            5968 => 'Thermal Printer',
            4000 => 'Customer Display',
            4999 => 'Cash Drawer',
            6100 => 'Payment Terminal',
            10009 => 'Payment Terminal',
            10001 => 'Payment Terminal',
            23 => 'Barcode Scanner',
            24 => 'Barcode Scanner'
        ];

        return $portMap[$port] ?? 'Unknown POS Device';
    }

    /**
     * Test connection to a specific POS device
     */
    public function testConnection(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
            'connection_type' => 'required|in:USB,Network,Serial',
            'ip_address' => 'nullable|required_if:connection_type,Network|ip',
            'port' => 'nullable|integer|min:1|max:65535'
        ]);

        try {
            // Create a temporary device object for testing
            $device = new PosDevice([
                'device_id' => $request->device_id,
                'connection_type' => $request->connection_type,
                'ip_address' => $request->ip_address,
                'port' => $request->port,
                'name' => 'Test Device',
                'type' => 'Test'
            ]);

            // Use the connector service to test
            $result = $this->connector->testConnection($device);

            return response()->json([
                'success' => true,
                'status' => $result['success'] ? 'connected' : 'disconnected',
                'message' => $result['message'],
                'response_time' => $result['response_time'],
                'details' => $result['details']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get device information
     */
    public function getDeviceInfo($deviceId)
    {
        try {
            $device = PosDevice::findOrFail($deviceId);
            $info = $this->connector->getDeviceInfo($device);

            return response()->json([
                'success' => true,
                'device' => $info
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get common ports for POS devices
     */
    public function getCommonPorts()
    {
        try {
            $ports = PosDevicePorts::getDefaultPorts();

            return response()->json([
                'success' => true,
                'ports' => $ports
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send command to device
     */
    public function sendCommand(Request $request, $deviceId)
    {
        $request->validate([
            'command' => 'required|string|max:1000'
        ]);

        try {
            $device = PosDevice::findOrFail($deviceId);
            $result = $this->connector->sendCommand($device, $request->command);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'response' => $result['response']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
