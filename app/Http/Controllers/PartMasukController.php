<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PartMasuk;
use App\Models\Sparepart;
use App\Models\PurchaseRequest;
use Exception;

class PartMasukController extends Controller
{
    public function index()
    {
        
    }

    public function create()
    {
        return view('data.partmasuk');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'ref_pp' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'required|string|max:255',
            'qty' => 'required|numeric|min:1',
        ]);
    
        try {
            $sparepart = Sparepart::where('nama_barang', $validatedData['nama_barang'])
                                ->where('kode_barang', $validatedData['kode_barang'])
                                ->first();
    
            if ($sparepart) {
                // Update existing sparepart
                $sparepart->part_masuk += $validatedData['qty'];
    
                // Calculate final stock
                $part_keluar = $sparepart->part_keluar ?? 0;
                $sparepart->stock_akhir_wrhs = $sparepart->stock_wrhs + $sparepart->part_masuk - $part_keluar;
    
                $sparepart->save();
            } else {
                // Create new sparepart
                Sparepart::create([
                    'part_masuk' => $validatedData['qty'],
                    'stock_wrhs' => $validatedData['qty'],
                    'nama_barang' => $validatedData['nama_barang'],
                    'kode_barang' => $validatedData['kode_barang'],
                    'stock_akhir_wrhs' => $validatedData['qty'],
                ]);
            }
    
            // Store the part_masuk data
            $partMasuk = PartMasuk::create([
                'tanggal' => $validatedData['tanggal'],
                'ref_pp' => $validatedData['ref_pp'],
                'nama_barang' => $validatedData['nama_barang'],
                'kode_barang' => $validatedData['kode_barang'],
                'qty' => $validatedData['qty'],
            ]);
    
            // Calculate qty_rcvid and sisa_rcvid
            $qtyRcvid = PartMasuk::where('ref_pp', $validatedData['ref_pp'])
                                 ->where('nama_barang', $validatedData['nama_barang'])
                                 ->where('kode_barang', $validatedData['kode_barang'])
                                 ->sum('qty');
    
            // Assuming you have a way to get the qty_pr from the PurchaseRequest model
            $purchaseRequest = PurchaseRequest::where('ref_pp', $validatedData['ref_pp'])
                                              ->where('nama_barang', $validatedData['nama_barang'])
                                              ->where('kode_barang', $validatedData['kode_barang'])
                                              ->first();
    
            if ($purchaseRequest) {
                // Update qty_rcvid and sisa_rcvid in purchase_request
                $purchaseRequest->qty_rcvid = $qtyRcvid;
                $purchaseRequest->sisa_rcvid = $purchaseRequest->qty_pr - $qtyRcvid; 
                $purchaseRequest->save();
            }
    
            return redirect()->route('partmasuk.create')->with('success', 'Data berhasil disimpan.');
    
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return redirect()->route('partmasuk.create')->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }        

    public function show(PartMasuk $data)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PartMasuk $data)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PartMasuk $data)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PartMasuk $data)
    {
        //
    }

}
