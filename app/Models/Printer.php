<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'connection_type',
        'ip_address',
        'port',
        'paper_type',
        'paper_width',
        'location',
        'manufacturer',
        'model',
        'print_density',
        'description',
        'is_active',
        'is_online',
        'is_default',
        'total_prints',
        'last_used',
        'settings',
        'printer_settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_online' => 'boolean',
        'is_default' => 'boolean',
        'total_prints' => 'integer',
        'last_used' => 'datetime',
        'settings' => 'array',
        'printer_settings' => 'array',
    ];

    public function isActive()
    {
        return $this->is_active;
    }

    public function isOnline()
    {
        return $this->is_online;
    }

    public function getConnectionStatusAttribute()
    {
        if ($this->is_online) {
            return 'متصل';
        }
        return 'منفصل';
    }

    public function getPaperTypeLabelAttribute()
    {
        $labels = [
            'thermal' => 'ورق حراري',
            'regular' => 'ورق عادي',
            'cashier' => 'ورق كاشير',
        ];

        return $labels[$this->paper_type] ?? $this->paper_type;
    }

    public function getPrintDensityLabelAttribute()
    {
        $labels = [
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
        ];

        return $labels[$this->print_density] ?? $this->print_density;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeOffline($query)
    {
        return $query->where('is_online', false);
    }

    public function testConnection()
    {
        // Simulate connection test
        $success = rand(0, 1) === 1;

        if ($success) {
            $this->update([
                'is_online' => true,
                'last_used' => now(),
            ]);
        } else {
            $this->update([
                'is_online' => false,
            ]);
        }

        return $success;
    }

    public function printTest()
    {
        if (!$this->is_online) {
            return false;
        }

        // Here you would implement actual printing logic
        // For now, we'll simulate successful printing
        $this->increment('total_prints');
        $this->update(['last_used' => now()]);

        return true;
    }

    public function printContent($content)
    {
        if (!$this->is_online || !$this->is_active) {
            return false;
        }

        // Here you would implement actual printing logic
        // For now, we'll simulate successful printing
        $this->increment('total_prints');
        $this->update(['last_used' => now()]);

        return true;
    }
}
