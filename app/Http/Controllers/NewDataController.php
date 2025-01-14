<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Namkod;
use App\Models\PartMasuk;
use Illuminate\Http\Request;
use Exception;

class NewDataController extends Controller
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
        $lines = Data::select('line')->distinct()->pluck('line');
        return view('data.createnosearch', compact('lines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'required|string|max:255',
        ]);

        $isDuplicate = Data::where('nama_barang', $request->nama_barang)
                            ->where('kode_barang', $request->kode_barang)
                            ->exists();

        if ($isDuplicate) {
            return redirect()->route('data.new.create')->with('error', 'Nama Barang dan Kode Barang sudah ada dalam database.');
        }

        try {
            Data::create($request->all());
            Namkod::create($request->all());

            return redirect()->route('data.new.create')->with('success', 'Data berhasil disimpan.');
        } catch (Exception $e) {
            return redirect()->route('data.new.create')->with('error', 'Terjadi kesalahan saat menyimpan data.');
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

    public function getNoStationsByLine($line)
    {
        if (strpos($line, 'SMT') !== false) {
            return response()->json(['no_stations' => []]);
        }

        $noStations = Data::where('line', $line)
                        ->whereNotNull('no_station')
                        ->pluck('no_station')
                        ->unique()
                        ->values(); 

        return response()->json(['no_stations' => $noStations]);
    }

    public function getNamaStationsByLine($line)
    {
        $namaStations = Data::where('line', $line)
                            ->pluck('nama_station')
                            ->unique()
                            ->values();

        return response()->json(['nama_stations' => $namaStations]);
    }

    public function searchNamaBarang($term)
    {
        $results = Namkod::where('nama_barang', 'like', "%{$term}%")
                          ->get(['nama_barang', 'kode_barang']);

        return response()->json($results);
    }

    
}
