<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Expense;
use App\Models\OrderItem;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Today's Statistics
        $todaySales = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_amount');

        $todayOrders = Order::whereDate('created_at', $today)->count();

        $todayExpenses = Expense::whereDate('expense_date', $today)->sum('amount');

        // Monthly Statistics
        $monthlySales = Order::whereDate('created_at', '>=', $thisMonth)
            ->where('status', 'completed')
            ->sum('total_amount');

        $monthlyExpenses = Expense::whereDate('expense_date', '>=', $thisMonth)->sum('amount');

        $monthlyProfit = $monthlySales - $monthlyExpenses;

        // Low Stock Alerts
        $lowStockProducts = Product::where('track_inventory', true)
            ->count();

        // Recent Orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Top Products
        $topProducts = OrderItem::with('product')
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(total_price) as total_sales')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Current Shift
        $currentShift = Shift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        return view('dashboard', compact(
            'todaySales',
            'todayOrders',
            'todayExpenses',
            'monthlySales',
            'monthlyExpenses',
            'monthlyProfit',
            'lowStockProducts',
            'recentOrders',
            'topProducts',
            'currentShift'
        ));
    }

    public function getSalesData(Request $request)
    {
        $period = $request->get('period', 'week');
        $data = [];

        switch ($period) {
            case 'week':
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $sales = Order::whereDate('created_at', $date)
                        ->where('status', 'completed')
                        ->sum('total_amount');
                    $data[] = [
                        'date' => $date->format('Y-m-d'),
                        'sales' => $sales
                    ];
                }
                break;

            case 'month':
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $sales = Order::whereDate('created_at', $date)
                        ->where('status', 'completed')
                        ->sum('total_amount');
                    $data[] = [
                        'date' => $date->format('Y-m-d'),
                        'sales' => $sales
                    ];
                }
                break;

            case 'year':
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $sales = Order::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->where('status', 'completed')
                        ->sum('total_amount');
                    $data[] = [
                        'date' => $date->format('Y-m'),
                        'sales' => $sales
                    ];
                }
                break;
        }

        return response()->json($data);
    }
}
