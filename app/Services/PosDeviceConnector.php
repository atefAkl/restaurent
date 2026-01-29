<?php

namespace App\Services;

use App\Models\PosDevice;
use Illuminate\Support\Facades\Log;

class PosDeviceConnector
{
    /**
     * Test connection to a POS device
     */
    public function testConnection(PosDevice $device): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'response_time' => 0,
            'details' => []
        ];

        try {
            $startTime = microtime(true);

            switch ($device->connection_type) {
                case 'Network':
                    $result = $this->testNetworkConnection($device, $startTime);
                    break;

                case 'USB':
                    $result = $this->testUsbConnection($device, $startTime);
                    break;

                case 'Serial':
                    $result = $this->testSerialConnection($device, $startTime);
                    break;

                default:
                    $result['message'] = 'نوع الاتصال غير مدعوم';
            }

            // Update device status
            $this->updateDeviceStatus($device, $result);
        } catch (\Exception $e) {
            $result['success'] = false;
            $result['message'] = 'خطأ في الاتصال: ' . $e->getMessage();
            Log::error('POS Device Connection Error: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Test network connection
     */
    private function testNetworkConnection(PosDevice $device, float $startTime): array
    {
        $result = ['success' => false, 'message' => '', 'response_time' => 0, 'details' => []];

        if (!$device->ip_address || !$device->port) {
            $result['message'] = 'IP Address أو Port غير محدد';
            return $result;
        }

        // Test with socket connection
        $connection = @fsockopen($device->ip_address, $device->port, $errno, $errstr, 5);

        if ($connection) {
            fclose($connection);
            $endTime = microtime(true);
            $result['success'] = true;
            $result['message'] = 'متصل بنجاح';
            $result['response_time'] = round(($endTime - $startTime) * 1000);
            $result['details'] = [
                'ip' => $device->ip_address,
                'port' => $device->port,
                'protocol' => 'TCP'
            ];
        } else {
            $result['message'] = 'فشل الاتصال: ' . $errstr;
            $result['details'] = ['error_code' => $errno];
        }

        return $result;
    }

    /**
     * Test USB connection
     */
    private function testUsbConnection(PosDevice $device, float $startTime): array
    {
        $result = ['success' => false, 'message' => '', 'response_time' => 0, 'details' => []];

        // Enhanced PowerShell command to find POS devices specifically
        $command = 'powershell -Command "
            # Get all USB devices
            $usbDevices = Get-WmiObject Win32_USBControllerDevice | ForEach-Object { [wmi]($_.Dependent) }
            
            # Filter for common POS device patterns
            $posDevices = $usbDevices | Where-Object { 
                $_.DeviceID -like \'*VID_*\' -and 
                (
                    $_.Name -like \'*POS*\' -or 
                    $_.Name -like \'*Thermal*\' -or 
                    $_.Name -like \'*Receipt*\' -or 
                    $_.Name -like \'*Barcode*\' -or 
                    $_.Name -like \'*Scanner*\' -or 
                    $_.Name -like \'*Cash*\' -or 
                    $_.Name -like \'*Payment*\' -or
                    $_.Description -like \'*POS*\' -or 
                    $_.Description -like \'*Thermal*\' -or 
                    $_.Description -like \'*Receipt*\' -or 
                    $_.Description -like \'*Barcode*\' -or 
                    $_.Description -like \'*Scanner*\' -or 
                    $_.Description -like \'*Cash*\' -or 
                    $_.Description -like \'*Payment*\' -or
                    $_.DeviceID -like \'*VID_0FE3*\' -or  # IDTech
                    $_.DeviceID -like \'*VID_1FD2*\' -or  # MagTek
                    $_.DeviceID -like \'*VID_04B3*\' -or  # Epson
                    $_.DeviceID -like \'*VID_0483*\' -or  # STMicro
                    $_.DeviceID -like \'*VID_067B*\' -or  # Prolific
                    $_.DeviceID -like \'*VID_10C4*\' -or  # Silicon Labs
                    $_.DeviceID -like \'*VID_1A86*\' -or  # QinHeng Electronics
                    $_.DeviceID -like \'*VID_0403*\'       # FTDI
                )
            }
            
            $posDevices | Select-Object Name, DeviceID, Description, Status | ConvertTo-Json
        "';

        $output = shell_exec($command);

        if ($output) {
            $data = json_decode($output, true);

            if (is_array($data) && count($data) > 0) {
                // Check if our specific device is in the list
                $foundDevice = null;
                foreach ($data as $usbDevice) {
                    if (isset($usbDevice['DeviceID']) && strpos($usbDevice['DeviceID'], $device->device_id) !== false) {
                        $foundDevice = $usbDevice;
                        break;
                    }
                }

                if ($foundDevice) {
                    $endTime = microtime(true);
                    $result['success'] = true;
                    $result['message'] = 'جهاز الصراف متصل عبر USB';
                    $result['response_time'] = round(($endTime - $startTime) * 1000);
                    $result['details'] = [
                        'device_id' => $device->device_id,
                        'name' => $foundDevice['Name'] ?? 'Unknown',
                        'description' => $foundDevice['Description'] ?? 'Unknown',
                        'status' => $foundDevice['Status'] ?? 'Unknown'
                    ];
                } else {
                    $result['message'] = 'جهاز الصراف غير متصل';
                    $result['details'] = ['available_pos_devices' => $data];
                }
            } else {
                $result['message'] = 'لم يتم العثور على أجهزة صراف متصلة';
            }
        } else {
            $result['message'] = 'فشل التحقق من اتصال USB';
        }

        return $result;
    }

    /**
     * Test Serial connection
     */
    private function testSerialConnection(PosDevice $device, float $startTime): array
    {
        $result = ['success' => false, 'message' => '', 'response_time' => 0, 'details' => []];

        // Check if COM port exists
        $command = 'powershell -Command "[System.IO.Ports.SerialPort]::getportnames() | ConvertTo-Json"';
        $output = shell_exec($command);

        if ($output) {
            $ports = json_decode($output, true);

            if (is_array($ports) && in_array($device->port, $ports)) {
                $endTime = microtime(true);
                $result['success'] = true;
                $result['message'] = 'منفذ COM متاح';
                $result['response_time'] = round(($endTime - $startTime) * 1000);
                $result['details'] = [
                    'port' => $device->port,
                    'available_ports' => $ports
                ];
            } else {
                $result['message'] = 'منفذ COM غير متاح';
                $result['details'] = ['available_ports' => $ports ?? []];
            }
        } else {
            $result['message'] = 'فشل الحصول على منافذ COM';
        }

        return $result;
    }

    /**
     * Send command to device
     */
    public function sendCommand(PosDevice $device, string $command): array
    {
        $result = ['success' => false, 'message' => '', 'response' => ''];

        try {
            switch ($device->connection_type) {
                case 'Network':
                    $result = $this->sendNetworkCommand($device, $command);
                    break;

                case 'Serial':
                    $result = $this->sendSerialCommand($device, $command);
                    break;

                default:
                    $result['message'] = 'إرسال الأوامر غير مدعوم لهذا النوع';
            }
        } catch (\Exception $e) {
            $result['success'] = false;
            $result['message'] = 'خطأ في إرسال الأمر: ' . $e->getMessage();
            Log::error('POS Device Command Error: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Send command via network
     */
    private function sendNetworkCommand(PosDevice $device, string $command): array
    {
        $result = ['success' => false, 'message' => '', 'response' => ''];

        $connection = @fsockopen($device->ip_address, $device->port, $errno, $errstr, 5);

        if ($connection) {
            // Send command
            fwrite($connection, $command . "\r\n");

            // Read response
            $response = fread($connection, 1024);
            fclose($connection);

            $result['success'] = true;
            $result['message'] = 'تم إرسال الأمر بنجاح';
            $result['response'] = $response;
        } else {
            $result['message'] = 'فشل الاتصال: ' . $errstr;
        }

        return $result;
    }

    /**
     * Send command via serial port
     */
    private function sendSerialCommand(PosDevice $device, string $command): array
    {
        $result = ['success' => false, 'message' => '', 'response' => ''];

        // Note: This would require PHP serial extension or external tool
        // For now, we'll simulate
        $result['success'] = true;
        $result['message'] = 'تم إرسال الأمر عبر منفذ التسلسل (محاكاة)';
        $result['response'] = 'OK';

        return $result;
    }

    /**
     * Update device status in database
     */
    private function updateDeviceStatus(PosDevice $device, array $result): void
    {
        $device->update([
            'is_online' => $result['success'],
            'response_time' => $result['success'] ? $result['response_time'] : null,
            'last_connected' => $result['success'] ? now() : $device->last_connected,
        ]);
    }

    /**
     * Get device information
     */
    public function getDeviceInfo(PosDevice $device): array
    {
        $info = [
            'name' => $device->name,
            'type' => $device->type,
            'connection_type' => $device->connection_type,
            'is_online' => $device->is_online,
            'last_connected' => $device->last_connected,
            'response_time' => $device->response_time,
        ];

        // Add connection-specific info
        switch ($device->connection_type) {
            case 'Network':
                $info['ip_address'] = $device->ip_address;
                $info['port'] = $device->port;
                break;

            case 'USB':
                $info['device_id'] = $device->device_id;
                break;

            case 'Serial':
                $info['port'] = $device->port;
                break;
        }

        return $info;
    }
}
