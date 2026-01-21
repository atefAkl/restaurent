<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'expense_number',
        'description_ar',
        'description_en',
        'amount',
        'category',
        'expense_date',
        'receipt_image',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generateExpenseNumber()
    {
        $prefix = 'EXP';
        $date = now()->format('Ymd');
        $lastExpense = self::whereDate('created_at', today())->latest()->first();
        $sequence = $lastExpense ? intval(substr($lastExpense->expense_number, -4)) + 1 : 1;
        $this->expense_number = $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
        $this->save();
    }

    public static function getTotalExpenses($startDate = null, $endDate = null)
    {
        $query = self::query();

        if ($startDate) {
            $query->whereDate('expense_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('expense_date', '<=', $endDate);
        }

        return $query->sum('amount');
    }
}
