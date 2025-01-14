<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PartKeluar;
use App\Models\Sparepart;
use Exception;

class PartKeluarController extends Controller
{
    public function index()
    {
        
    }
    public function create()
    {
        return view('data.partkeluar');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'pic' => 'required|string|max:255',
            'keperluan' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'required|string|max:255',
            'qty' => 'required|numeric|min:1',
        ]);
    
        try {
            $sparepart = Sparepart::where('nama_barang', $validatedData['nama_barang'])
                                ->where('kode_barang', $validatedData['kode_barang'])
                                ->first();
    
            if ($sparepart) {
                // Jika nama_barang sudah ada, tambahkan quantity
                $sparepart->part_keluar += $validatedData['qty'];
                
                // Hitung stock akhir warehouse
                $part_masuk = $sparepart->part_masuk ?? 0;
                $sparepart->stock_akhir_wrhs = $sparepart->stock_wrhs + $part_masuk - $sparepart->part_keluar;
    
                $sparepart->save();
            } else {
                // Jika nama_barang belum ada, buat record baru
                Sparepart::create([
                    'part_keluar' => $validatedData['qty'],
                    'stock_wrhs' => -$validatedData['qty'], // Set stock_wrhs sesuai -qty
                    'nama_barang' => $validatedData['nama_barang'],
                    'kode_barang' => $validatedData['kode_barang'],
                    'stock_akhir_wrhs' => -$validatedData['qty'], // Set stock_akhir_wrhs sesuai -qty
                ]);
            }
    
            PartKeluar::create([
                'tanggal' => $validatedData['tanggal'],
                'pic' => $validatedData['pic'],
                'keperluan' => $validatedData['keperluan'],
                'nama_barang' => $validatedData['nama_barang'],
                'kode_barang' => $validatedData['kode_barang'],
                'qty' => $validatedData['qty'],
            ]);

            session()->flash('nama_barang', $request->input('nama_barang'));
            session()->flash('kode_barang', $request->input('kode_barang'));
    
            return redirect()->route('partkeluar.create')->with('success', 'Data berhasil disimpan.');
    
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return redirect()->route('partkeluar.create')->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }
    

    public function show(PartKeluar $data)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PartKeluar $data)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PartKeluar $data)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PartKeluar $data)
    {
        //
    }
}
