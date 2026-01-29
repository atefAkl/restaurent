<?php

namespace App\Helpers;

class PosDevicePorts
{
    /**
     * Get default ports for different POS device types
     */
    public static function getDefaultPorts(): array
    {
        return [
            // Thermal Printers
            'thermal_printer' => [
                'Epson' => [9100, 8008, 8009],
                'Star' => [9100, 5969, 5968],
                'Citizen' => [9100, 5969],
                'Bixolon' => [9100, 8008],
                'Custom' => [9100]
            ],
            
            // Barcode Scanners
            'barcode_scanner' => [
                'Symbol' => [23, 24, 9100],
                'Zebra' => [9100, 23, 24],
                'Honeywell' => [23, 24, 9100],
                'Datalogic' => [23, 24, 9100],
                'Custom' => [23]
            ],
            
            // Cash Drawers
            'cash_drawer' => [
                'APG' => [9100, 4999],
                'MMF' => [9100, 4999],
                'Logic Controls' => [9100, 4999],
                'Custom' => [9100]
            ],
            
            // Payment Terminals
            'payment_terminal' => [
                'Verifone' => [10009, 10001, 9100],
                'Ingenico' => [10009, 10001, 9100],
                'PAX' => [10009, 10001, 9100],
                'IDTech' => [9100, 6100],
                'MagTek' => [9100, 6100],
                'Custom' => [10009]
            ],
            
            // Customer Displays
            'customer_display' => [
                'Epson' => [9100, 4000],
                'Star' => [9100, 4000],
                'Custom' => [9100]
            ],
            
            // POS Terminals
            'pos_terminal' => [
                'Touch' => [9100, 23, 24],
                'Windows' => [3389, 5900], // RDP, VNC
                'Custom' => [9100]
            ]
        ];
    }
    
    /**
     * Get common port for device type
     */
    public static function getCommonPort(string $deviceType, string $manufacturer = 'Custom'): int
    {
        $ports = self::getDefaultPorts();
        
        $deviceKey = strtolower(str_replace(' ', '_', $deviceType));
        
        if (isset($ports[$deviceKey][$manufacturer])) {
            return $ports[$deviceKey][$manufacturer][0];
        }
        
        if (isset($ports[$deviceKey]['Custom'])) {
            return $ports[$deviceKey]['Custom'][0];
        }
        
        return 9100; // Default port
    }
    
    /**
     * Get all possible ports for device type
     */
    public static function getAllPorts(string $deviceType, string $manufacturer = 'Custom'): array
    {
        $ports = self::getDefaultPorts();
        
        $deviceKey = strtolower(str_replace(' ', '_', $deviceType));
        
        if (isset($ports[$deviceKey][$manufacturer])) {
            return $ports[$deviceKey][$manufacturer];
        }
        
        return [9100]; // Default
    }
    
    /**
     * Check if port is commonly used for POS
     */
    public static function isPosPort(int $port): bool
    {
        $commonPorts = [23, 24, 9100, 8008, 8009, 5969, 5968, 4000, 4999, 6100, 10009, 10001, 3389, 5900];
        return in_array($port, $commonPorts);
    }
    
    /**
     * Get port description
     */
    public static function getPortDescription(int $port): string
    {
        $descriptions = [
            23 => 'Telnet (Barcode Scanners)',
            24 => 'Telnet Private (Barcode Scanners)',
            9100 => 'Raw TCP (Most POS Printers)',
            8008 => 'HTTP (Some Printers)',
            8009 => 'HTTPS (Some Printers)',
            5969 => 'Star Printer Port',
            5968 => 'Star Printer Port',
            4000 => 'Customer Display',
            4999 => 'Cash Drawer',
            6100 => 'Payment Terminal',
            10009 => 'Payment Terminal',
            10001 => 'Payment Terminal',
            3389 => 'Remote Desktop (POS Terminal)',
            5900 => 'VNC (POS Terminal)'
        ];
        
        return $descriptions[$port] ?? 'Custom Port';
    }
}
