<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;

class PosDevice extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'name',
        'type',
        'connection_type',
        'device_id',
        'ip_address',
        'port',
        'location',
        'manufacturer',
        'model',
        'description',
        'is_active',
        'is_online',
        'response_time',
        'last_connected',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_online' => 'boolean',
        'response_time' => 'integer',
        'last_connected' => 'datetime',
        'settings' => 'array',
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
        $startTime = microtime(true);

        // Here you would implement actual connection test logic
        // For now, we'll simulate with random success/failure
        $success = rand(0, 1) === 1;

        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000);

        if ($success) {
            $this->update([
                'is_online' => true,
                'response_time' => $responseTime,
                'last_connected' => now(),
            ]);
        } else {
            $this->update([
                'is_online' => false,
                'response_time' => null,
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
        return true;
    }

    // Relationships
    public function posStations()
    {
        return $this->belongsToMany(PosStation::class, 'pos_station_device');
    }
}
