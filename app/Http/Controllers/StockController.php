<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Data;
use App\Models\Sparepart;
use Exception;

class StockController extends Controller
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
        return view ('data.stock');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data dengan kolom optional
        $validatedData = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'leadtime' => 'nullable|numeric|min:1',
            'lifetime' => 'nullable|numeric|min:1',
            'stock_wrhs' => 'nullable|numeric|min:0', 
            'part_masuk' => 'nullable|numeric|min:0', // Menambahkan validasi untuk part_masuk
            'part_keluar' => 'nullable|numeric|min:0', // Menambahkan validasi untuk part_keluar
        ]);
    
        try {
            // Cari record yang ada
            $sparepart = Sparepart::where('nama_barang', $validatedData['nama_barang'])
                                ->where('kode_barang', $validatedData['kode_barang'])
                                ->first();
    
            if ($sparepart) {
                // Update existing record
                $sparepart->address = $validatedData['address'] ?? $sparepart->address;
                $sparepart->leadtime = $validatedData['leadtime'] ?? $sparepart->leadtime;
                $sparepart->lifetime = $validatedData['lifetime'] ?? $sparepart->lifetime;
                $sparepart->stock_wrhs = $validatedData['stock_wrhs'] ?? $sparepart->stock_wrhs;
    
                // Pastikan variabel-variabel ini ada sebelum perhitungan
                $leadtime = $sparepart->leadtime ?: 1; // Default 1 untuk menghindari pembagian dengan nol
                $lifetime = $sparepart->lifetime ?: 1; // Default 1 untuk menghindari pembagian dengan nol
                $total_qty = $sparepart->total_qty ?: 0; // Default 0 jika tidak ada total_qty
                $ms_ss = $sparepart->ms_ss ?: 0; // Default 0 jika tidak ada ms_ss
    
                // Perhitungan min_stock
                $sparepart->min_stock = ($leadtime / $lifetime) * $total_qty + $ms_ss;
    
                // Hitung stock akhir warehouse
                $part_masuk = $validatedData['part_masuk'] ?? $sparepart->part_masuk ?? 0;
                $part_keluar = $validatedData['part_keluar'] ?? $sparepart->part_keluar ?? 0;
                $sparepart->stock_akhir_wrhs = $sparepart->stock_wrhs + $part_masuk - $part_keluar;
    
                $sparepart->save();
            } else {
                // Hitung min_stock untuk record baru
                $leadtime = $validatedData['leadtime'] ?? 1; // Default 1 untuk menghindari pembagian dengan nol
                $lifetime = $validatedData['lifetime'] ?? 1; // Default 1 untuk menghindari pembagian dengan nol
                $total_qty = 0; // Default 0 jika tidak ada total_qty
                $ms_ss = 0; // Default 0 jika tidak ada ms_ss
                $min_stock = ($leadtime / $lifetime) * $total_qty + $ms_ss;
    
                // Hitung stock akhir warehouse
                $stock_wrhs = $validatedData['stock_wrhs'] ?? 0;
                $part_masuk = $validatedData['part_masuk'] ?? 0;
                $part_keluar = $validatedData['part_keluar'] ?? 0;
                $stock_akhir_wrhs = $stock_wrhs + $part_masuk - $part_keluar;
    
                // Buat record baru
                Sparepart::create([
                    'nama_barang' => $validatedData['nama_barang'],
                    'kode_barang' => $validatedData['kode_barang'],
                    'address' => $validatedData['address'] ?? '',
                    'leadtime' => $validatedData['leadtime'] ?? null,
                    'lifetime' => $validatedData['lifetime'] ?? null,
                    'stock_wrhs' => $stock_wrhs,
                    'min_stock' => $min_stock,
                    'part_masuk' => $part_masuk,
                    'part_keluar' => $part_keluar,
                    'stock_akhir_wrhs' => $stock_akhir_wrhs,
                ]);
            }
    
            return redirect()->route('stock.create')->with('success', 'Data berhasil disimpan.');
    
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return redirect()->route('stock.create')->with('error', 'Terjadi kesalahan saat menyimpan data.');
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
