<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_number',
        'opening_balance',
        'closing_balance',
        'cash_sales',
        'visa_sales',
        'total_sales',
        'started_at',
        'ended_at',
        'notes',
        'status',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'cash_sales' => 'decimal:2',
        'visa_sales' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function closeShift($closingBalance)
    {
        $this->closing_balance = $closingBalance;
        $this->ended_at = now();
        $this->status = 'closed';
        $this->save();
    }

    public function generateShiftNumber()
    {
        $prefix = 'SHF';
        $date = now()->format('Ymd');
        $lastShift = self::whereDate('created_at', today())->latest()->first();
        $sequence = $lastShift ? intval(substr($lastShift->shift_number, -4)) + 1 : 1;
        $this->shift_number = $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
        $this->save();
    }
}
