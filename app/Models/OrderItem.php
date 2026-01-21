<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name_ar',
        'product_name_en',
        'unit_price',
        'quantity',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderItem) {
            if ($orderItem->product) {
                $orderItem->product_name_ar = $orderItem->product->name_ar;
                $orderItem->product_name_en = $orderItem->product->name_en;
                $orderItem->unit_price = $orderItem->product->price;
            }
            $orderItem->total_price = $orderItem->unit_price * $orderItem->quantity;
        });

        static::created(function ($orderItem) {
            if ($orderItem->product) {
                $orderItem->product->updateStock($orderItem->quantity, 'sale');
            }
        });
    }
}
