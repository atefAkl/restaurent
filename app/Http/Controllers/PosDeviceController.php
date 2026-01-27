<?php

namespace App\Http\Controllers;

use App\Models\PosDevice;
use Illuminate\Http\Request;

class PosDeviceController extends Controller
{
    public function __construct()
    {
        // Remove middleware calls from constructor
        // We'll handle authorization in each method
    }

    public function index()
    {
        $devices = PosDevice::orderBy('name')->get();
        return view('pos_devices.index', compact('devices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:POS,Payment Terminal,Cash Drawer,Barcode Scanner',
            'ip_address' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'location' => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $device = PosDevice::create([
            'name' => $request->name,
            'type' => $request->type,
            'ip_address' => $request->ip_address,
            'port' => $request->port,
            'location' => $request->location,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الجهاز بنجاح',
            'device' => $device
        ]);
    }

    public function update(Request $request, $id)
    {
        $device = PosDevice::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:POS,Payment Terminal,Cash Drawer,Barcode Scanner',
            'ip_address' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'location' => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $device->update([
            'name' => $request->name,
            'type' => $request->type,
            'ip_address' => $request->ip_address,
            'port' => $request->port,
            'location' => $request->location,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الجهاز بنجاح',
            'device' => $device
        ]);
    }

    public function destroy($id)
    {
        $device = PosDevice::findOrFail($id);
        $device->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الجهاز بنجاح'
        ]);
    }

    public function testConnection($id)
    {
        $device = PosDevice::findOrFail($id);

        $success = $device->testConnection();

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'تم الاتصال بالجهاز بنجاح',
                'response_time' => $device->response_time
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'فشل الاتصال بالجهاز'
            ]);
        }
    }

    public function printTest($id)
    {
        $device = PosDevice::findOrFail($id);

        if (!$device->is_online) {
            return response()->json([
                'success' => false,
                'message' => 'الجهاز غير متصل'
            ]);
        }

        $success = $device->printTest();

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
        $device = PosDevice::findOrFail($id);
        return view('pos_devices.show', compact('device'));
    }
}
