<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('tables')->get();
        return view('settings.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('settings.rooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:rooms,name',
            'number' => 'required|string|max:20|unique:rooms,number',
            'status' => 'nullable|string|max:50',
        ]);
        Room::create($request->only(['name', 'number', 'status']));
        return redirect()->route('rooms.index')->with('success', 'تمت إضافة الغرفة بنجاح');
    }

    public function edit(Room $room)
    {
        return view('settings.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:rooms,name,' . $room->id,
            'number' => 'required|string|max:20|unique:rooms,number,' . $room->id,
            'status' => 'nullable|string|max:50',
        ]);
        $room->update($request->only(['name', 'number', 'status']));
        return redirect()->route('rooms.index')->with('success', 'تم تحديث بيانات الغرفة');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'تم حذف الغرفة');
    }
}
