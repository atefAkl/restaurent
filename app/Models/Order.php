<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_id',
        'in_progress',
        'address_id',
        'room_id',
        'type',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'payment_method',
        'payment_reference',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->orderItems->sum('total_price');
        $this->tax_amount = $this->subtotal * 0.15; // Saudi VAT 15%
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->save();
    }

    public function getRemainingAmount()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function isPaid()
    {
        return $this->paid_amount >= $this->total_amount;
    }

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    public function generateOrderNumber()
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $lastOrder = self::whereDate('created_at', today())->latest()->first();
        $sequence = $lastOrder ? intval(substr($lastOrder->order_number, -4)) + 1 : 1;
        $this->order_number = $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
        $this->save();
    }
}
