<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use App\Models\Sparepart;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SparepartsImports;
use DB;

class SparepartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil data dari database
        $partMasuk = Sparepart::sum('part_masuk');
        $partKeluar = Sparepart::sum('part_keluar');
        $stockWrhs = Sparepart::sum('stock_wrhs');
        $minStock = Sparepart::sum('min_stock');
        $stockAkhirWrhs = Sparepart::sum('stock_akhir_wrhs'); 
        $total_sisaRcvid = PurchaseRequest::sum('sisa_rcvid'); 
    
        // Menghitung jumlah untuk status berdasarkan logika backend
        $statusOK = Sparepart::where(function($query) {
            $query->whereRaw('stock_akhir_wrhs > min_stock');
        })->count();
    
        $statusDanger = Sparepart::where(function($query) {
            $query->whereRaw('min_stock > stock_akhir_wrhs');
        })->count();

        $statusRcvid = PurchaseRequest::where('sisa_rcvid', '<>', 0)->count();

        return view('dashboard', compact('partMasuk', 'partKeluar', 'statusOK', 'statusDanger', 'stockWrhs', 'minStock', 'stockAkhirWrhs', 'statusRcvid'));
    }
      


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
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

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);
        DB::table('test1')->truncate();
    

        Excel::import(new SparepartsImports, $request->file('file'));
    
        return redirect()->back()->with('success', 'Data Excel Berhasil diimport');
    }
    


    public function getData()
    {
        $spareparts = Sparepart::select('nama_barang', 'kode_barang', 'address', 'total_qty', 'lifetime', 'leadtime', 'min_stock', 'stock_akhir_wrhs'); 
        
        return DataTables::of($spareparts)->make(true);
    }

    public function getSpareparts(Request $request)
    {
        $spareparts = Sparepart::select([
            'nama_barang', 'kode_barang', 'address', 'total_qty', 'lifetime', 'leadtime', 
            'min_stock', 'stock_akhir_wrhs'
        ]);
    
        if ($request->has('status') && $request->status != '') {
            $status = $request->status;
            if ($status === 'OK') {
                $spareparts->whereRaw('stock_akhir_wrhs >= min_stock');
            } elseif ($status === 'DANGER') {
                $spareparts->whereRaw('stock_akhir_wrhs < min_stock');
            }
        }
    
        return DataTables::of($spareparts)
            ->addColumn('action', function($row) {
                $status = $row->stock_akhir_wrhs >= $row->min_stock ? 'OK' : 'DANGER';
                $statusClass = $status === 'OK' ? 'text-success' : 'text-danger';
                $statusIcon = $status === 'OK' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
                return '<i class="fas ' . $statusIcon . ' ' . $statusClass . '"></i> ' . $status;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    
    
    
}
