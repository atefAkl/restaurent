<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use PDF;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('user')
            ->when(request('search'), function ($query, $search) {
                $query->where('description_ar', 'like', "%{$search}%")
                    ->orWhere('description_en', 'like', "%{$search}%")
                    ->orWhere('expense_number', 'like', "%{$search}%");
            })
            ->when(request('category'), function ($query, $category) {
                $query->where('category', $category);
            })
            ->when(request('date_from'), function ($query, $dateFrom) {
                $query->whereDate('expense_date', '>=', $dateFrom);
            })
            ->when(request('date_to'), function ($query, $dateTo) {
                $query->whereDate('expense_date', '<=', $dateTo);
            })
            ->latest('expense_date')
            ->paginate(20);

        $categories = [
            'rent' => 'الإيجار',
            'utilities' => 'المرافق',
            'salaries' => 'الرواتب',
            'supplies' => 'المستلزمات',
            'maintenance' => 'الصيانة',
            'other' => 'أخرى'
        ];

        return view('expenses.index', compact('expenses', 'categories'));
    }

    public function create()
    {
        $categories = [
            'rent' => 'الإيجار',
            'utilities' => 'المرافق',
            'salaries' => 'الرواتب',
            'supplies' => 'المستلزمات',
            'maintenance' => 'الصيانة',
            'other' => 'أخرى'
        ];

        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description_ar' => 'required|string|max:255',
            'description_en' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|in:rent,utilities,salaries,supplies,maintenance,other',
            'expense_date' => 'required|date',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string'
        ]);

        try {
            $data = $request->except('receipt_image');
            $data['user_id'] = auth()->id();

            // Handle receipt image upload
            if ($request->hasFile('receipt_image')) {
                $image = $request->file('receipt_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('expenses', $imageName, 'public');
                $data['receipt_image'] = 'expenses/' . $imageName;
            }

            $expense = Expense::create($data);
            $expense->generateExpenseNumber();

            return response()->json([
                'success' => true,
                'expense' => $expense->load('user'),
                'message' => 'تم إضافة المصروف بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة المصروف'
            ], 400);
        }
    }

    public function show(Expense $expense)
    {
        $expense->load('user');
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $categories = [
            'rent' => 'الإيجار',
            'utilities' => 'المرافق',
            'salaries' => 'الرواتب',
            'supplies' => 'المستلزمات',
            'maintenance' => 'الصيانة',
            'other' => 'أخرى'
        ];

        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'description_ar' => 'required|string|max:255',
            'description_en' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|in:rent,utilities,salaries,supplies,maintenance,other',
            'expense_date' => 'required|date',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string'
        ]);

        try {
            $data = $request->except('receipt_image');

            // Handle receipt image upload
            if ($request->hasFile('receipt_image')) {
                // Delete old image
                if ($expense->receipt_image) {
                    Storage::disk('public')->delete($expense->receipt_image);
                }

                $image = $request->file('receipt_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('expenses', $imageName, 'public');
                $data['receipt_image'] = 'expenses/' . $imageName;
            }

            $expense->update($data);

            return response()->json([
                'success' => true,
                'expense' => $expense->load('user'),
                'message' => 'تم تحديث المصروف بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المصروف'
            ], 400);
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            // Delete receipt image
            if ($expense->receipt_image) {
                Storage::disk('public')->delete($expense->receipt_image);
            }

            $expense->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المصروف بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المصروف'
            ], 400);
        }
    }

    public function getExpensesSummary(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->get();

        $summary = [
            'total_amount' => $expenses->sum('amount'),
            'total_count' => $expenses->count(),
            'by_category' => $expenses->groupBy('category')->map(function ($items) {
                return [
                    'amount' => $items->sum('amount'),
                    'count' => $items->count()
                ];
            }),
            'daily_average' => $expenses->count() > 0 ? $expenses->sum('amount') / $expenses->count() : 0
        ];

        return response()->json($summary);
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $expenses = Expense::with('user')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->get();

        $total = $expenses->sum('amount');

        $pdf = PDF::loadView('expenses.pdf', compact('expenses', 'total', 'startDate', 'endDate'));

        return $pdf->download('expenses-report.pdf');
    }
}
