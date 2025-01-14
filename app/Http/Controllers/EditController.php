<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Record;
use App\Models\M_Model;
use App\Models\Delivery;
use App\Models\PurchaseRequest;
use Exception;

class EditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Delivery::where('flag',1)->get();
            return datatables()->of($data)->make(true);
        }
    
        return view('data.editdata');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('data.editdata');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        $delivery = Delivery::findOrFail($id);
        return response()->json($delivery);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $delivery = Delivery::findOrFail($id);
        $delivery->update($request->all());
        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delivery = Delivery::findOrFail($id);
        $delivery->delete();
        return response()->json(['success' => true]);
    }
}
