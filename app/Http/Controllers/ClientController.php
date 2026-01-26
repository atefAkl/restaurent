<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $clients = Client::paginate(15);
        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // send serial number to view
        $serial = Client::newSerialNumber();
        $types = ['dine_in', 'takeaway', 'delivery', 'catering'];
        return view('clients.create', compact('types', 'serial'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:clients,name',
            'type' => 'required|in:dine_in,takeaway,delivery,catering',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            's_number' => 'nullable|string|max:14|unique:clients,s_number'
        ]);

        try {
            $data = $request->only(['name', 'type', 'phone', 'email', 's_number', 'status']);
            $data['status'] = $request->has('status') ? true : false;
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
            // return $data;
            Client::create($data);
            return redirect()->back()->with('success', 'Client created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInputs()->with('error', 'Error creating client' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function searchByNameOrPhone(Request $request)
    {
        $search = $request->search;

        $clients = Client::select('name', 'phone', 'id')
            ->where('name', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%')
            ->where('status', true)
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'clients' => $clients
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {

        try {
            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الطلب بنجاح'
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الطلب'
            ], 400);
        }
    }
}
