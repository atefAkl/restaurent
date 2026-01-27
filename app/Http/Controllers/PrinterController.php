<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use Illuminate\Http\Request;

class PrinterController extends Controller
{
    public function __construct()
    {
        // Remove middleware calls from constructor
        // We'll handle authorization in each method
    }

    public function index()
    {
        $printers = Printer::orderBy('name')->get();
        return view('printers.index', compact('printers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Thermal,Inkjet,Laser,Dot Matrix,POS',
            'connection_type' => 'required|in:USB,Network,Bluetooth,Serial',
            'ip_address' => 'nullable|required_if:connection_type,Network|ip',
            'port' => 'nullable|integer|min:1|max:65535',
            'paper_type' => 'required|in:thermal,regular,cashier',
            'paper_width' => 'required|in:58,80,112,210',
            'location' => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'print_density' => 'required|in:low,medium,high',
            'description' => 'nullable|string|max:1000',
        ]);

        $printer = Printer::create([
            'name' => $request->name,
            'type' => $request->type,
            'connection_type' => $request->connection_type,
            'ip_address' => $request->ip_address,
            'port' => $request->port,
            'paper_type' => $request->paper_type,
            'paper_width' => $request->paper_width,
            'location' => $request->location,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'print_density' => $request->print_density,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الطابعة بنجاح',
            'printer' => $printer
        ]);
    }

    public function update(Request $request, $id)
    {
        $printer = Printer::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Thermal,Inkjet,Laser,Dot Matrix,POS',
            'connection_type' => 'required|in:USB,Network,Bluetooth,Serial',
            'ip_address' => 'nullable|required_if:connection_type,Network|ip',
            'port' => 'nullable|integer|min:1|max:65535',
            'paper_type' => 'required|in:thermal,regular,cashier',
            'paper_width' => 'required|in:58,80,112,210',
            'location' => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'print_density' => 'required|in:low,medium,high',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $printer->update([
            'name' => $request->name,
            'type' => $request->type,
            'connection_type' => $request->connection_type,
            'ip_address' => $request->ip_address,
            'port' => $request->port,
            'paper_type' => $request->paper_type,
            'paper_width' => $request->paper_width,
            'location' => $request->location,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'print_density' => $request->print_density,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الطابعة بنجاح',
            'printer' => $printer
        ]);
    }

    public function destroy($id)
    {
        $printer = Printer::findOrFail($id);
        $printer->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الطابعة بنجاح'
        ]);
    }

    public function testConnection($id)
    {
        $printer = Printer::findOrFail($id);

        $success = $printer->testConnection();

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'تم الاتصال بالطابعة بنجاح'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'فشل الاتصال بالطابعة'
            ]);
        }
    }

    public function printTest($id)
    {
        $printer = Printer::findOrFail($id);

        if (!$printer->is_online) {
            return response()->json([
                'success' => false,
                'message' => 'الطابعة غير متصلة'
            ]);
        }

        $success = $printer->printTest();

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'تم طباعة اختبار بنجاح'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'فشل الطباعة'
            ]);
        }
    }

    public function show($id)
    {
        $printer = Printer::findOrFail($id);
        return view('printers.show', compact('printer'));
    }

    public function printContent(Request $request, $id)
    {
        $printer = Printer::findOrFail($id);

        $request->validate([
            'content' => 'required|string',
            'type' => 'required|in:order,invoice,receipt'
        ]);

        if (!$printer->is_online || !$printer->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'الطابعة غير متاحة للطباعة'
            ]);
        }

        $success = $printer->printContent($request->content);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'تم الطباعة بنجاح'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'فشل الطباعة'
            ]);
        }
    }
}
