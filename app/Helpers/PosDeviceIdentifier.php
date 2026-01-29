<?php

namespace App\Helpers;

class PosDeviceIdentifier
{
    /**
     * Identify device type based on name and description
     */
    public static function identifyDeviceType(string $name, string $description = ''): string
    {
        $searchText = strtolower($name . ' ' . $description);
        
        // Thermal Printers
        if (self::containsAny($searchText, ['thermal', 'receipt', 'printer', 'epson', 'citizen', 'bixolon'])) {
            return 'Thermal Printer';
        }
        
        // Barcode Scanners
        if (self::containsAny($searchText, ['barcode', 'scanner', 'symbol', 'zebra', 'honeywell'])) {
            return 'Barcode Scanner';
        }
        
        // Cash Drawers
        if (self::containsAny($searchText, ['cash', 'drawer', 'apg', 'mmf', 'logic'])) {
            return 'Cash Drawer';
        }
        
        // Payment Terminals
        if (self::containsAny($searchText, ['payment', 'terminal', 'verifone', 'ingenico', 'pax', 'idtech', 'magtek'])) {
            return 'Payment Terminal';
        }
        
        // POS Terminals
        if (self::containsAny($searchText, ['pos', 'terminal', 'touch', 'screen'])) {
            return 'POS Terminal';
        }
        
        // Customer Displays
        if (self::containsAny($searchText, ['customer', 'display', 'pole', 'lcd'])) {
            return 'Customer Display';
        }
        
        return 'Unknown POS Device';
    }
    
    /**
     * Check if text contains any of the keywords
     */
    private static function containsAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get manufacturer from DeviceID
     */
    public static function getManufacturer(string $deviceId): string
    {
        $manufacturers = [
            'VID_0FE3' => 'IDTech',
            'VID_1FD2' => 'MagTek',
            'VID_04B3' => 'Epson',
            'VID_0483' => 'STMicroelectronics',
            'VID_067B' => 'Prolific',
            'VID_10C4' => 'Silicon Labs',
            'VID_1A86' => 'QinHeng Electronics',
            'VID_0403' => 'FTDI',
            'VID_154B' => 'PNY Technologies',
            'VID_05E0' => 'Symbol Technologies',
            'VID_058F' => 'Alcor Micro',
            'VID_0A5C' => 'Broadcom',
            'VID_13FE' => 'Kingston',
        ];
        
        foreach ($manufacturers as $vid => $manufacturer) {
            if (strpos($deviceId, $vid) !== false) {
                return $manufacturer;
            }
        }
        
        return 'Unknown';
    }
    
    /**
     * Check if device is likely a POS device
     */
    public static function isPosDevice(string $name, string $description = '', string $deviceId = ''): bool
    {
        $searchText = strtolower($name . ' ' . $description);
        
        // POS keywords
        $posKeywords = [
            'pos', 'thermal', 'receipt', 'barcode', 'scanner', 'cash', 'drawer',
            'payment', 'terminal', 'customer', 'display', 'pole', 'magnetic',
            'card', 'reader', 'verifone', 'ingenico', 'pax', 'epson', 'citizen',
            'bixolon', 'symbol', 'zebra', 'honeywell', 'idtech', 'magtek'
        ];
        
        // Check keywords
        if (self::containsAny($searchText, $posKeywords)) {
            return true;
        }
        
        // Check manufacturer VIDs
        $posVids = [
            'VID_0FE3', 'VID_1FD2', 'VID_04B3', 'VID_0483', 'VID_067B',
            'VID_10C4', 'VID_1A86', 'VID_0403', 'VID_05E0'
        ];
        
        foreach ($posVids as $vid) {
            if (strpos($deviceId, $vid) !== false) {
                return true;
            }
        }
        
        return false;
    }
}
