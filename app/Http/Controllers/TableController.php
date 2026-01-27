<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Room;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::with('room')->get();
        return view('settings.tables.index', compact('tables'));
    }

    public function create()
    {
        $rooms = Room::all();
        return view('settings.tables.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'number' => 'required|string|max:20|unique:tables,number',
            'room_id' => 'nullable|exists:rooms,id',
            'status' => 'nullable|string|max:50',
        ]);
        Table::create($request->only(['name', 'number', 'room_id', 'status']));
        return redirect()->route('tables.index')->with('success', 'تمت إضافة الطاولة بنجاح');
    }

    public function edit(Table $table)
    {
        $rooms = Room::all();
        return view('settings.tables.edit', compact('table', 'rooms'));
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'number' => 'required|string|max:20|unique:tables,number,' . $table->id,
            'room_id' => 'nullable|exists:rooms,id',
            'status' => 'nullable|string|max:50',
        ]);
        $table->update($request->only(['name', 'number', 'room_id', 'status']));
        return redirect()->route('tables.index')->with('success', 'تم تحديث بيانات الطاولة');
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('tables.index')->with('success', 'تم حذف الطاولة');
    }
}
