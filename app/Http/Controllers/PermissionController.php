<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Exception;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return $request;
        $permission = Permission::create([
            'name' => $request->name,
            'group' => $request->group,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);
        try {
            $permission->save();
            return redirect()->back()->with('success', 'تم إنشاء الصلاحية بنجاح');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الصلاحية');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
