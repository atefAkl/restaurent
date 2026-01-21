<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Expense;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function salesReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfDay());
        $groupBy = $request->get('group_by', 'day'); // day, week, month

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $salesData = [];
        $totalSales = 0;
        $totalOrders = 0;
        $totalTax = 0;
        $totalDiscount = 0;

        switch ($groupBy) {
            case 'day':
                $grouped = $orders->groupBy(function ($order) {
                    return $order->created_at->format('Y-m-d');
                });
                break;
            case 'week':
                $grouped = $orders->groupBy(function ($order) {
                    return $order->created_at->format('Y-W');
                });
                break;
            case 'month':
                $grouped = $orders->groupBy(function ($order) {
                    return $order->created_at->format('Y-m');
                });
                break;
        }

        foreach ($grouped as $key => $dayOrders) {
            $salesData[] = [
                'period' => $key,
                'orders_count' => $dayOrders->count(),
                'total_sales' => $dayOrders->sum('total_amount'),
                'subtotal' => $dayOrders->sum('subtotal'),
                'tax' => $dayOrders->sum('tax_amount'),
                'discount' => $dayOrders->sum('discount_amount'),
                'average_order' => $dayOrders->count() > 0 ? $dayOrders->sum('total_amount') / $dayOrders->count() : 0
            ];
        }

        $summary = [
            'total_sales' => $orders->sum('total_amount'),
            'total_orders' => $orders->count(),
            'total_tax' => $orders->sum('tax_amount'),
            'total_discount' => $orders->sum('discount_amount'),
            'average_order' => $orders->count() > 0 ? $orders->sum('total_amount') / $orders->count() : 0
        ];

        return view('reports.sales', compact('salesData', 'summary', 'startDate', 'endDate', 'groupBy'));
    }

    public function productsReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfDay());

        $orderItems = \App\Models\OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed');
        })->get();

        $productsData = $orderItems->groupBy('product_id')->map(function ($items) {
            $product = $items->first()->product;
            return [
                'product_id' => $product->id,
                'product_name' => $product->name_ar,
                'category' => $product->category->name_ar,
                'quantity_sold' => $items->sum('quantity'),
                'total_revenue' => $items->sum('total_price'),
                'average_price' => $items->sum('total_price') / $items->sum('quantity'),
                'orders_count' => $items->count()
            ];
        })->sortByDesc('quantity_sold');

        $summary = [
            'total_products' => $productsData->count(),
            'total_quantity' => $productsData->sum('quantity_sold'),
            'total_revenue' => $productsData->sum('total_revenue')
        ];

        return view('reports.products', compact('productsData', 'summary', 'startDate', 'endDate'));
    }

    public function expensesReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfDay());

        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->get();

        $expensesByCategory = $expenses->groupBy('category')->map(function ($items) {
            return [
                'category' => $this->getCategoryName($items->first()->category),
                'amount' => $items->sum('amount'),
                'count' => $items->count(),
                'percentage' => 0 // Will be calculated
            ];
        });

        $totalExpenses = $expenses->sum('amount');

        // Calculate percentages
        $expensesByCategory = $expensesByCategory->map(function ($item) use ($totalExpenses) {
            $item['percentage'] = $totalExpenses > 0 ? ($item['amount'] / $totalExpenses) * 100 : 0;
            return $item;
        });

        $summary = [
            'total_amount' => $totalExpenses,
            'total_count' => $expenses->count(),
            'daily_average' => $expenses->count() > 0 ? $totalExpenses / $expenses->count() : 0
        ];

        return view('reports.expenses', compact('expensesByCategory', 'summary', 'startDate', 'endDate'));
    }

    public function profitLossReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfDay());

        // Revenue
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalTax = $orders->sum('tax_amount');
        $netRevenue = $totalRevenue - $totalTax;

        // Expenses
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->get();
        $totalExpenses = $expenses->sum('amount');

        // Cost of Goods Sold (COGS)
        $orderItems = \App\Models\OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed');
        })->get();

        $totalCOGS = 0;
        foreach ($orderItems as $item) {
            if ($item->product && $item->product->cost) {
                $totalCOGS += $item->product->cost * $item->quantity;
            }
        }

        $grossProfit = $netRevenue - $totalCOGS;
        $netProfit = $grossProfit - $totalExpenses;

        $profitLoss = [
            'revenue' => [
                'total' => $totalRevenue,
                'tax' => $totalTax,
                'net' => $netRevenue
            ],
            'cogs' => $totalCOGS,
            'gross_profit' => $grossProfit,
            'expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'profit_margin' => $netRevenue > 0 ? ($netProfit / $netRevenue) * 100 : 0
        ];

        return view('reports.profit-loss', compact('profitLoss', 'startDate', 'endDate'));
    }

    public function shiftsReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfDay());

        $shifts = Shift::whereBetween('created_at', [$startDate, $endDate])
            ->with('user')
            ->get();

        $shiftsData = $shifts->map(function ($shift) {
            return [
                'shift_number' => $shift->shift_number,
                'user_name' => $shift->user->name,
                'opening_balance' => $shift->opening_balance,
                'closing_balance' => $shift->closing_balance,
                'cash_sales' => $shift->cash_sales,
                'visa_sales' => $shift->visa_sales,
                'total_sales' => $shift->total_sales,
                'difference' => $shift->closing_balance - ($shift->opening_balance + $shift->cash_sales),
                'duration' => $shift->started_at->diffInHours($shift->ended_at ?? now())
            ];
        });

        $summary = [
            'total_shifts' => $shifts->count(),
            'total_sales' => $shifts->sum('total_sales'),
            'cash_sales' => $shifts->sum('cash_sales'),
            'visa_sales' => $shifts->sum('visa_sales'),
            'average_sales_per_shift' => $shifts->count() > 0 ? $shifts->sum('total_sales') / $shifts->count() : 0
        ];

        return view('reports.shifts', compact('shiftsData', 'summary', 'startDate', 'endDate'));
    }

    private function getCategoryName($category)
    {
        $categories = [
            'rent' => 'الإيجار',
            'utilities' => 'المرافق',
            'salaries' => 'الرواتب',
            'supplies' => 'المستلزمات',
            'maintenance' => 'الصيانة',
            'other' => 'أخرى'
        ];

        return $categories[$category] ?? $category;
    }
}
