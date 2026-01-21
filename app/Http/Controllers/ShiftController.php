<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::with('user')
            ->when(request('user_id'), function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        $users = \App\Models\User::where('is_active', true)->get();

        return view('shifts.index', compact('shifts', 'users'));
    }

    public function create()
    {
        $users = \App\Models\User::where('is_active', true)->get();
        return view('shifts.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        try {
            // Check if user has open shift
            $existingShift = Shift::where('user_id', $request->user_id)
                ->where('status', 'open')
                ->first();

            if ($existingShift) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم لديه شفت مفتوح بالفعل'
                ], 400);
            }

            $shift = new Shift();
            $shift->user_id = $request->user_id;
            $shift->opening_balance = $request->opening_balance;
            $shift->notes = $request->notes;
            $shift->started_at = now();
            $shift->status = 'open';
            $shift->generateShiftNumber();
            $shift->save();

            return response()->json([
                'success' => true,
                'shift' => $shift->load('user'),
                'message' => 'تم فتح الشفت بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء فتح الشفت'
            ], 400);
        }
    }

    public function show(Shift $shift)
    {
        $shift->load(['user', 'orders' => function ($query) {
            $query->with('orderItems.product');
        }]);

        return view('shifts.show', compact('shift'));
    }

    public function close(Request $request, Shift $shift)
    {
        if ($shift->status === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'الشفت مغلق بالفعل'
            ], 400);
        }

        $request->validate([
            'closing_balance' => 'required|numeric|min:0'
        ]);

        try {
            // Calculate sales for this shift
            $orders = Order::where('user_id', $shift->user_id)
                ->where('created_at', '>=', $shift->started_at)
                ->where('status', 'completed')
                ->get();

            $cashSales = $orders->where('payment_method', 'cash')->sum('total_amount');
            $visaSales = $orders->where('payment_method', 'visa')->sum('total_amount');
            $totalSales = $cashSales + $visaSales;

            // Update shift
            $shift->closing_balance = $request->closing_balance;
            $shift->cash_sales = $cashSales;
            $shift->visa_sales = $visaSales;
            $shift->total_sales = $totalSales;
            $shift->ended_at = now();
            $shift->status = 'closed';
            $shift->save();

            return response()->json([
                'success' => true,
                'shift' => $shift->load('user'),
                'message' => 'تم إغلاق الشفت بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إغلاق الشفت'
            ], 400);
        }
    }

    public function getCurrentShift()
    {
        $shift = Shift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->with('user')
            ->first();

        return response()->json($shift);
    }

    public function openMyShift(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0'
        ]);

        try {
            // Check if user has open shift
            $existingShift = Shift::where('user_id', Auth::id())
                ->where('status', 'open')
                ->first();

            if ($existingShift) {
                return response()->json([
                    'success' => false,
                    'message' => 'لديك شفت مفتوح بالفعل'
                ], 400);
            }

            $shift = new Shift();
            $shift->user_id = Auth::id();
            $shift->opening_balance = $request->opening_balance;
            $shift->started_at = now();
            $shift->status = 'open';
            $shift->generateShiftNumber();
            $shift->save();

            return response()->json([
                'success' => true,
                'shift' => $shift->load('user'),
                'message' => 'تم فتح شفتك بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء فتح الشفت'
            ], 400);
        }
    }

    public function closeMyShift(Request $request)
    {
        $shift = Shift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد شفت مفتوح لك'
            ], 400);
        }

        $request->validate([
            'closing_balance' => 'required|numeric|min:0'
        ]);

        return $this->close($request, $shift);
    }

    public function getShiftReport(Shift $shift)
    {
        $shift->load(['user', 'orders' => function ($query) {
            $query->with('orderItems.product');
        }]);

        $report = [
            'shift_info' => $shift,
            'sales_summary' => [
                'total_orders' => $shift->orders->count(),
                'cash_sales' => $shift->cash_sales,
                'visa_sales' => $shift->visa_sales,
                'total_sales' => $shift->total_sales,
                'expected_cash' => $shift->opening_balance + $shift->cash_sales,
                'difference' => $shift->closing_balance - ($shift->opening_balance + $shift->cash_sales)
            ],
            'top_products' => $shift->orders->flatMap(function ($order) {
                return $order->orderItems;
            })->groupBy('product_id')
                ->map(function ($items) {
                    return [
                        'product_name' => $items->first()->product->name_ar,
                        'quantity' => $items->sum('quantity'),
                        'total' => $items->sum('total_price')
                    ];
                })
                ->sortByDesc('quantity')
                ->take(10)
        ];

        return view('shifts.report', compact('report'));
    }
}
