<?php

namespace App\Http\Controllers;

use App\Models\CashierSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashierSessionController extends Controller
{
    public function __construct()
    {
        // Remove middleware calls from constructor
        // We'll handle authorization in each method
    }

    public function index()
    {
        $sessions = CashierSession::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $activeSession = CashierSession::active()
            ->where('user_id', Auth::id())
            ->first();

        return view('cashier_sessions.index', compact('sessions', 'activeSession'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cashier_name' => 'required|string|max:255',
            'cash_drawer' => 'required|string|max:255',
            'opening_balance' => 'required|numeric|min:0',
            'pos_device' => 'nullable|string|max:255',
            'printer' => 'nullable|string|max:255',
        ]);

        // Check if user already has an active session
        $existingSession = CashierSession::active()
            ->where('user_id', Auth::id())
            ->first();

        if ($existingSession) {
            return response()->json([
                'success' => false,
                'message' => 'لديك جلسة نشطة بالفعل. يجب إنهاء الجلسة الحالية أولاً.'
            ]);
        }

        $session = CashierSession::create([
            'cashier_name' => $request->cashier_name,
            'cash_drawer' => $request->cash_drawer,
            'opening_balance' => $request->opening_balance,
            'pos_device' => $request->pos_device,
            'printer' => $request->printer,
            'status' => 'active',
            'start_time' => now(),
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم بدء الجلسة بنجاح',
            'session' => $session
        ]);
    }

    public function endSession(Request $request, $id)
    {
        $session = CashierSession::findOrFail($id);

        if ($session->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بإنهاء هذه الجلسة'
            ]);
        }

        if ($session->status === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'الجلسة مغلقة بالفعل'
            ]);
        }

        $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'session_notes' => 'nullable|string|max:1000',
        ]);

        $session->update([
            'closing_balance' => $request->closing_balance,
            'notes' => $request->session_notes,
            'status' => 'closed',
            'end_time' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنهاء الجلسة بنجاح',
            'session' => $session
        ]);
    }

    public function show($id)
    {
        $session = CashierSession::with('user')->findOrFail($id);

        if ($session->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('cashier_sessions.show', compact('session'));
    }

    public function print($id)
    {
        $session = CashierSession::with('user')->findOrFail($id);

        if ($session->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('cashier_sessions.print', compact('session'));
    }
}
