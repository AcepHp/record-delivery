<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Namkod;
use App\Models\PurchaseRequest;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Carbon\Carbon;

class PRController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {    
        return view('data.purchase');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ref_pp' => 'required|string|max:255',
            'req_date' => 'required|date',
            'nama_barang.*' => 'required|string|max:255',
            'kode_barang.*' => 'required|string|max:255',
            'qty_pr.*' => 'nullable|integer|min:0',
        ]);
    
        try {
            foreach ($validatedData['nama_barang'] as $index => $nama_barang) {
                PurchaseRequest::create([
                    'ref_pp' => $validatedData['ref_pp'],
                    'req_date' => $validatedData['req_date'],
                    'nama_barang' => $nama_barang,
                    'kode_barang' => $validatedData['kode_barang'][$index],
                    'qty_pr' => $validatedData['qty_pr'][$index] ?? 0,
                    'sisa_rcvid' => $validatedData['qty_pr'][$index] ?? 0,
                ]);
            }
    
            return redirect()->route('purchase.create')->with('success', 'Data berhasil disimpan');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Data $data)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Data $data)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Data $data)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Data $data)
    {
        //
    }


    public function searchNamaBarang($term)
    {
        $results = PurchaseRequest::where('ref_pp', 'like', "%{$term}%")
                          ->distinct()
                          ->get(['ref_pp']);

        return response()->json($results);
    }
    

    public function getSparepartsfix()
    {
        $spareparts = Sparepart::select([
            'nama_barang', 
            'kode_barang', 
            'address',
            'total_qty_pr',
            'leadtime',
            'lifetime',
            'min_stock',
            'stock_akhir_wrhs',
        ]);

        return DataTables::of($spareparts)
            ->make(true);
    }

   


    
}
