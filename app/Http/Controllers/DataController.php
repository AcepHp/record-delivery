<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Namkod;
use App\Models\Test;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Carbon\Carbon;

class DataController extends Controller
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
        $lines = Data::select('line')
                       ->whereNotNull('no_station')
                       ->distinct()
                       ->pluck('line');

        $noStations = [];
        $namaStations = [];
        if (session('line')) {
            $line = session('line');
            // Query untuk mendapatkan No Station dan Nama Station berdasarkan Line
            $noStations = Data::where('line', $line)
                                ->whereNotNull('no_station')
                                ->distinct()
                              ->pluck('no_station');
            $namaStations = Data::where('line', $line)
                                ->whereNotNull('nama_station')
                                ->distinct()
                                ->pluck('nama_station');
        }
    
        return view('data.create', compact('lines', 'noStations', 'namaStations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'line' => 'required|string|max:255',
            'nama_station' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'required|string|max:255',
            'qty' => 'nullable|integer|min:0', 
        ]);

        try {
            $data = $request->all();
            $data['created_at'] = Carbon::now('Asia/Jakarta'); 
            $data['updated_at'] = Carbon::now('Asia/Jakarta');
            
            Data::create($data);

            $sparepart = Sparepart::where('nama_barang', $request->input('nama_barang'))
                                ->where('kode_barang', $request->input('kode_barang'))
                                ->first();

            if ($sparepart) {
                switch ($request->input('line')) {
                    case 'FAL1':
                        $sparepart->fa_l1 += $request->input('qty');
                        break;
                    case 'FAL2':
                        $sparepart->fa_l2 += $request->input('qty');
                        break;
                    case 'FAL3':
                        $sparepart->fa_l3 += $request->input('qty');
                        break;
                    case 'FAL5':
                        $sparepart->fa_l5 += $request->input('qty');
                        break;
                    case 'FAL6':
                        $sparepart->fa_l6 += $request->input('qty');
                        break;
                    case 'FA_Rework1':
                        $sparepart->fa_rework1 += $request->input('qty');
                        break;
                    case 'FA_Rework2':
                        $sparepart->fa_rework2 += $request->input('qty');
                        break;
                    case 'FA_SAB':
                        $sparepart->fa_sab += $request->input('qty');
                        break;
                    case 'SMTL Offline':
                        $sparepart->smt_offline += $request->input('qty');
                        break;
                    case 'SMTL1 BACKEND':
                        $sparepart->smt_l1_bckend += $request->input('qty');
                        break;
                    case 'SMTL1 BOTTOM':
                        $sparepart->smt_l1_bot += $request->input('qty');
                        break;
                    case 'SMTL1 TOP':
                        $sparepart->smt_l1_top += $request->input('qty');
                        break;
                    case 'SMTL2 BACKEND':
                        $sparepart->smt_l2_bckend += $request->input('qty');
                        break;
                    case 'SMTL2 TOPBOT':
                        $sparepart->smt_l2_topbot += $request->input('qty');
                        break;
                    case 'UTILITY':
                        $sparepart->utility += $request->input('qty');
                        break;
                }

                // Hitung total_qty
                $sparepart->total_qty = $sparepart->fa_l1 + $sparepart->fa_l2 + $sparepart->fa_l3 + $sparepart->fa_l5 + $sparepart->fa_l6 + $sparepart->fa_rework1 + $sparepart->fa_rework2 + $sparepart->fa_sab + $sparepart->smt_offline + $sparepart->smt_l1_top + $sparepart->smt_l1_bot + $sparepart->smt_l1_bckend + $sparepart->smt_l2_topbot + $sparepart->smt_l2_bckend + $sparepart->utility;

                // Hitung ms_ss sebagai 10% dari total_qty
                $sparepart->ms_ss = $sparepart->total_qty * 0.10;
                
                // Hitung min_stock 
                $sparepart->min_stock = $sparepart->leadtime / $sparepart->lifetime * $sparepart->total_qty + $sparepart->ms_ss;

                // Hitung stock akhir warehouse
                $sparepart->stock_akhir_wrhs = $sparepart->stock_wrhs + $sparepart->part_masuk - $sparepart->part_keluar;

                $sparepart->save();
            } else {
                $sparepartData = [
                    'nama_barang' => $request->input('nama_barang'),
                    'kode_barang' => $request->input('kode_barang'),
                ];

                switch ($request->input('line')) {
                    case 'FAL1':
                        $sparepartData['fa_l1'] = $request->input('qty');
                        break;
                    case 'FAL2':
                        $sparepartData['fa_l2'] = $request->input('qty');
                        break;
                    case 'FAL3':
                        $sparepartData['fa_l3'] = $request->input('qty');
                        break;
                    case 'FAL5':
                        $sparepartData['fa_l5'] = $request->input('qty');
                        break;
                    case 'FAL6':
                        $sparepartData['fa_l6'] = $request->input('qty');
                        break;
                    case 'FA_Rework1':
                        $sparepartData['fa_rework1'] = $request->input('qty');
                        break;
                    case 'FA_Rework2':
                        $sparepartData['fa_rework2'] = $request->input('qty');
                        break;
                    case 'FA_SAB':
                        $sparepartData['fa_sab'] = $request->input('qty');
                        break;
                    case 'SMTL Offline':
                        $sparepartData['smt_offline'] = $request->input('qty');
                        break;
                    case 'SMTL1 BACKEND':
                        $sparepartData['smt_l1_bckend'] = $request->input('qty');
                        break;
                    case 'SMTL1 BOTTOM':
                        $sparepartData['smt_l1_bot'] = $request->input('qty');
                        break;
                    case 'SMTL1 TOP':
                        $sparepartData['smt_l1_top'] = $request->input('qty');
                        break;
                    case 'SMTL2 BACKEND':
                        $sparepartData['smt_l2_bckend'] = $request->input('qty');
                        break;
                    case 'SMTL2 TOPBOT':
                        $sparepartData['smt_l2_topbot'] = $request->input('qty');
                        break;
                    case 'UTILITY':
                        $sparepartData['utility'] = $request->input('qty');
                        break;
                }

                // Hitung total_qty
                $sparepartData['total_qty'] = ($sparepartData['fa_l1'] ?? 0)
                + ($sparepartData['fa_l2'] ?? 0)
                + ($sparepartData['fa_l3'] ?? 0)
                + ($sparepartData['fa_l5'] ?? 0)
                + ($sparepartData['fa_l6'] ?? 0)
                + ($sparepartData['fa_rework1'] ?? 0)
                + ($sparepartData['fa_rework2'] ?? 0)
                + ($sparepartData['fa_sab'] ?? 0)
                + ($sparepartData['smt_offline'] ?? 0)
                + ($sparepartData['smt_l1_top'] ?? 0)
                + ($sparepartData['smt_l1_bot'] ?? 0)
                + ($sparepartData['smt_l1_bckend'] ?? 0)
                + ($sparepartData['smt_l2_topbot'] ?? 0)
                + ($sparepartData['smt_l2_bckend'] ?? 0)
                + ($sparepartData['utility'] ?? 0);

                // Hitung ms_ss sebagai 10% dari total_qty
                $sparepartData['ms_ss'] = $sparepartData['total_qty'] * 0.10;
                
                // Hitung min_stock
                $sparepartData['min_stock'] = $sparepartData['leadtime'] / $sparepartData['lifetime'] * $sparepartData['total_qty'] + $sparepartData['ms_ss'];


                Sparepart::create($sparepartData);
            }

            session(['line' => $request->input('line')]);
            session(['no_station' => $request->input('no_station')]);
            session(['nama_station' => $request->input('nama_station')]);


            return redirect()->route('data.create')->with('success', 'Data berhasil disimpan');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Tolong input data stock part terlebih dahulu');
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

    public function getNamaStationsByLine($line_or_no_station)
    {
        if (strpos($line_or_no_station, 'SMT') !== false) {
            $namaStations = Data::where('line', $line_or_no_station)
                                ->pluck('nama_station')
                                ->unique()
                                ->values();
        } else {
            $namaStations = Data::where('no_station', $line_or_no_station)
                                ->pluck('nama_station')
                                ->unique()
                                ->values();
        }

        return response()->json(['nama_stations' => $namaStations]);
    }

    public function searchNamaBarang($term)
    {
        $results = Namkod::where('nama_barang', 'like', "%{$term}%")
                          ->get(['nama_barang', 'kode_barang']);

        return response()->json($results);
    }
    
    public function getLines()
    {
        $lines = Data::select('line')->distinct()->pluck('line');
        return response()->json(['lines' => $lines]);
    }    


    public function getSpareparts()
    {
        $spareparts = Test::select([
            'A', 'B', 'C' 
        ]);

        return DataTables::of($spareparts)
            ->make(true);
    }

    public function getSparepartsfix()
    {
        $spareparts = Sparepart::select([
            'nama_barang', 
            'kode_barang', 
            'address',
            'total_qty',
            'leadtime',
            'lifetime',
            'min_stock',
            'stock_akhir_wrhs',
        ]);

        return DataTables::of($spareparts)
            ->make(true);
    }

   


    
}
