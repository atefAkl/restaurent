<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashierSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashier_name',
        'cash_drawer',
        'opening_balance',
        'closing_balance',
        'total_sales',
        'total_transactions',
        'cash_payments',
        'card_payments',
        'transfer_payments',
        'account_payments',
        'pos_device',
        'printer',
        'notes',
        'status',
        'start_time',
        'end_time',
        'user_id',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'cash_payments' => 'decimal:2',
        'card_payments' => 'decimal:2',
        'transfer_payments' => 'decimal:2',
        'account_payments' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getDurationAttribute()
    {
        if ($this->end_time) {
            return $this->start_time->diffForHumans($this->end_time, true);
        }
        return $this->start_time->diffForHumans(now(), true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }
}
