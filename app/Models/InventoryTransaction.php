<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'unit_cost',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($transaction) {
            if ($transaction->product && $transaction->product->track_inventory) {
                switch ($transaction->type) {
                    case 'purchase':
                        $transaction->product->stock_quantity += $transaction->quantity;
                        break;
                    case 'sale':
                        $transaction->product->stock_quantity -= $transaction->quantity;
                        break;
                    case 'adjustment':
                        $transaction->product->stock_quantity = $transaction->quantity;
                        break;
                    case 'waste':
                        $transaction->product->stock_quantity -= $transaction->quantity;
                        break;
                }
                $transaction->product->save();
            }
        });
    }

    public static function getLowStockProducts()
    {
        return Product::where('track_inventory', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock_alert')
            ->get();
    }
}
