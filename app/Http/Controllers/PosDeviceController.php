<?php

namespace App\Http\Controllers;

use App\Models\PosDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:POS,Payment Terminal,Cash Drawer,Barcode Scanner,Thermal Printer,POS Terminal,Customer Display,Unknown POS Device',
                'connection_type' => 'required|in:Network,USB,Serial',
                'ip_address' => 'nullable|required_if:connection_type,Network|ip',
                'port' => 'nullable|required_if:connection_type,Network|integer|min:1|max:65535',
                'location' => 'nullable|string|max:255',
                'manufacturer' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'device_id' => 'nullable|string|max:255',
            ], [
                'name.required' => 'اسم الجهاز مطلوب',
                'type.required' => 'نوع الجهاز مطلوب',
                'type.in' => 'نوع الجهاز غير صالح',
                'connection_type.required' => 'نوع الاتصال مطلوب',
                'connection_type.in' => 'نوع الاتصال غير صالح',
                'ip_address.required_if' => 'عنوان IP مطلوب عند اختيار اتصال Network',
                'ip_address.ip' => 'عنوان IP غير صالح',
                'port.required_if' => 'المنفذ مطلوب عند اختيار اتصال Network',
                'port.integer' => 'المنفذ يجب أن يكون رقم',
                'port.min' => 'المنفذ يجب أن يكون أكبر من 0',
                'port.max' => 'المنفذ يجب أن يكون أقل من 65536',
            ]);

            // Log the request data for debugging
            Log::info('PosDevice Store Request:', $request->all());

            $device = PosDevice::create([
                'name' => $request->name,
                'type' => $request->type,
                'connection_type' => $request->connection_type,
                'device_id' => $request->device_id,
                'ip_address' => $request->ip_address,
                'port' => $request->port,
                'location' => $request->location,
                'manufacturer' => $request->manufacturer,
                'model' => $request->model,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            Log::info('PosDevice Created:', $device->toArray());

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الجهاز بنجاح',
                'device' => $device
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('PosDevice Validation Error:', $e->errors());

            return response()->json([
                'success' => false,
                'message' => 'فشل التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('PosDevice Store Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة الجهاز: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $device = PosDevice::findOrFail($id);

            return response()->json([
                'success' => true,
                'device' => $device
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على الجهاز'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $device = PosDevice::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:POS,Payment Terminal,Cash Drawer,Barcode Scanner,Thermal Printer,POS Terminal,Customer Display,Unknown POS Device',
            'connection_type' => 'required|in:Network,USB,Serial',
            'ip_address' => 'nullable|required_if:connection_type,Network|ip',
            'port' => 'nullable|required_if:connection_type,Network|integer|min:1|max:65535',
            'location' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'اسم الجهاز مطلوب',
            'type.required' => 'نوع الجهاز مطلوب',
            'type.in' => 'نوع الجهاز غير صالح',
            'connection_type.required' => 'نوع الاتصال مطلوب',
            'connection_type.in' => 'نوع الاتصال غير صالح',
            'ip_address.required_if' => 'عنوان IP مطلوب عند اختيار اتصال Network',
            'ip_address.ip' => 'عنوان IP غير صالح',
            'port.required_if' => 'المنفذ مطلوب عند اختيار اتصال Network',
            'port.integer' => 'المنفذ يجب أن يكون رقم',
            'port.min' => 'المنفذ يجب أن يكون أكبر من 0',
            'port.max' => 'المنفذ يجب أن يكون أقل من 65536',
        ]);

        $device->update([
            'name' => $request->name,
            'type' => $request->type,
            'connection_type' => $request->connection_type,
            'device_id' => $request->device_id,
            'ip_address' => $request->ip_address,
            'port' => $request->port,
            'location' => $request->location,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
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
}
