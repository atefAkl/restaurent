<?php

namespace App\Http\Controllers;

use App\Models\PosStation;
use App\Models\Printer;
use App\Models\PosDevice;
use Illuminate\Http\Request;

class PosStationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posStations = PosStation::with(['printer', 'posDevices'])->latest()->paginate(20);
        return view('pos-stations.index', compact('posStations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $printers = Printer::where('is_active', true)->get();
        $posDevices = PosDevice::where('is_active', true)->get();
        return view('pos-stations.create', compact('printers', 'posDevices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:pos_stations',
            'location' => 'required|string|max:255',
            'printer_id' => 'nullable|exists:printers,id',
            'pos_device_ids' => 'nullable|array',
            'pos_device_ids.*' => 'exists:pos_devices,id',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $posStation = PosStation::create($request->all());

        // Attach POS devices if provided
        if ($request->has('pos_device_ids')) {
            foreach ($request->pos_device_ids as $deviceId) {
                $posStation->posDevices()->attach($deviceId, ['is_primary' => false]);
            }
        }

        return redirect()->route('pos-stations.index')
            ->with('success', 'تم إضافة نقطة البيع بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(PosStation $posStation)
    {
        return view('pos-stations.show', compact('posStation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PosStation $posStation)
    {
        $printers = Printer::where('is_active', true)->get();
        return view('pos-stations.edit', compact('posStation', 'printers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PosStation $posStation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:pos_stations,code,' . $posStation->id,
            'location' => 'required|string|max:255',
            'printer_id' => 'nullable|exists:printers,id',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $posStation->update($request->all());

        return redirect()->route('pos-stations.index')
            ->with('success', 'تم تحديث نقطة البيع بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PosStation $posStation)
    {
        $posStation->delete();

        return redirect()->route('pos-stations.index')
            ->with('success', 'تم حذف نقطة البيع بنجاح');
    }
}
