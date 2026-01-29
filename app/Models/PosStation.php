<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;

class PosStation extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'name',
        'code',
        'location',
        'computer_id',
        'printer_id',
        'pos_device_id',
        'cash_drawer_id',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function printer()
    {
        return $this->belongsTo(Printer::class);
    }

    public function posDevices()
    {
        return $this->belongsToMany(PosDevice::class, 'pos_station_device');
    }

    // Relationships (سنضيفها لما نعمل الموديلات التانية)
    // public function computer()
    // {
    //     return $this->belongsTo(Computer::class);
    // }

    // public function cashDrawer()
    // {
    //     return $this->belongsTo(CashDrawer::class);
    // }

    // public function cashierSessions()
    // {
    //     return $this->hasMany(CashierSession::class, 'pos_station_id');
    // }
}
