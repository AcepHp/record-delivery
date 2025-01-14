<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Record;
use App\Models\M_Model;
use App\Models\Delivery;
use App\Models\PurchaseRequest;
use Yajra\DataTables\DataTables;
use Exception;

class RecordController extends Controller
{
    public function index()
    {
        
    }

    public function create()
    {
        $models = M_Model::select('model')
                       ->whereNotNull('model')
                       ->distinct()
                       ->pluck('model');     

        return view('data.record', compact('models'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tgl_bln_thn' => 'required|date',
            'model' => 'required|string',
            'qty' => 'required|integer',
        ]);

        $noTransaksi = 'AVI' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

        $record = new Record([
            'no_transaksi' => $noTransaksi,
            'tgl_bln_thn' => $validatedData['tgl_bln_thn'],
            'model' => $validatedData['model'],
            'plant_dest' => $request->input('plant_dest'), 
            'pic' => $request->input('pic'),  
            'flag' => 1,             
            'qty' => $validatedData['qty'],
        ]);

        if ($record->save()) {
            session([
                'no_transaksi' => $noTransaksi,
                'tgl_bln_thn' => $validatedData['tgl_bln_thn'],
                'plant_dest' => $request->input('plant_dest'),
                'model' => $validatedData['model'],
                'qty' => $validatedData['qty'],
                'pic' => $request->input('pic'),
            ]);
    
            return redirect()->route('delivery.create')->with('success', 'Data berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Data gagal disimpan');
        }
    }    

    public function show(Record $data)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Record $data)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Record $data)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Record $data)
    {
        //
    }

    public function getModelData($model)
    {
        // Fetch the data for the selected model
        $data = M_Model::where('model', $model)->first(['plant_dest', 'pic']);

        // Return the data as JSON
        return response()->json($data);
    }


}
