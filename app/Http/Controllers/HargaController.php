<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sparepart;
use App\Models\Jenis;
use Exception;


class HargaController extends Controller
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
        $uoms = Jenis::select('uom')
                       ->whereNotNull('uom')
                       ->distinct()
                       ->pluck('uom');

        $mata_uangs = Jenis::select('mata_uang')
                       ->whereNotNull('mata_uang')
                       ->distinct()
                       ->pluck('mata_uang');         

        return view('data.harga', compact('uoms','mata_uangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'required|string|max:255',
            'harga' => 'required|numeric|min:1',
            'mata_uang' => 'required|string|max:10', 
            'uom' => 'required|string|max:255',
            'vendor' => 'required|string|max:255',
        ]);

        try {
            
            DB::statement ('EXEC InsertUpdateDataHarga ?, ?, ?, ?, ?, ?', [
            $validatedData['nama_barang'],
            $validatedData['kode_barang'],
            $validatedData['harga'],
            $validatedData['mata_uang'],
            $validatedData['uom'],
            $validatedData['vendor'],
        ]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Data berhasil disimpan.']);
            }
    
            return redirect()->route('harga.create');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.']);
            }
    
            return redirect()->route('harga.create');
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
