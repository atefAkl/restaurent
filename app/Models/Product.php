<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'price',
        'cost',
        'image',
        'barcode',
        'sku',
        'track_inventory',
        'stock_quantity',
        'min_stock_alert',
        'is_active',
        'is_seasonal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'track_inventory' => 'boolean',
        'is_active' => 'boolean',
        'is_seasonal' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function isLowStock()
    {
        return $this->track_inventory && $this->stock_quantity <= $this->min_stock_alert;
    }

    public function updateStock($quantity, $type = 'sale')
    {
        if ($this->track_inventory) {
            if ($type === 'sale') {
                $this->stock_quantity -= $quantity;
            } elseif ($type === 'purchase') {
                $this->stock_quantity += $quantity;
            }
            $this->save();
        }
    }

    public function getPriceWithTax($taxRate = 0.15)
    {
        return $this->price * (1 + $taxRate);
    }
}
