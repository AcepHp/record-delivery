<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Namkod;
use App\Models\PurchaseRequest;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Events\StatusSubmitChanged;
use Exception;
use Carbon\Carbon;

class PRTableController extends Controller
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
        return view('data.purchasetable');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ref_pp' => 'required|string|max:255',
            'req_date' => 'required|date',
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'required|string|max:255',
            'qty_pr' => 'nullable|integer|min:0',
        ]);

        try {
            PurchaseRequest::create([
                'ref_pp' => $validatedData['ref_pp'],
                'req_date' => $validatedData['req_date'],
                'nama_barang' => $validatedData['nama_barang'],
                'kode_barang' => $validatedData['kode_barang'],
                'qty_pr' => $validatedData['qty_pr'] ?? 0, 
            ]);
            

            // Redirect with success message
            return redirect()->route('purchase.new.create')->with('success', 'Data berhasil disimpan');
        } catch (Exception $e) {
            // Handle error and redirect back with error message
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
    
    public function getSpareparts(Request $request)
    {
        $spareparts = PurchaseRequest::select([
            'id', 
            'nama_barang', 
            'kode_barang', 
            'req_date', 
            'ref_pp', 
            'qty_pr',
            'status_submit',
            'submit_date'
        ]);
        
        
        return DataTables::of($spareparts)
            ->addColumn('action', function($row) {
                return '
                    <div class="action-buttons">
                        <button class="btn btn-success approve-btn" data-id="' . $row->id . '">Approve</button>
                        <button class="btn btn-danger reject-btn" data-id="' . $row->id . '">Reject</button>
                    </div>
                ';
            })
            ->addColumn('status_submit', function($row) {
                $status = $row->status_submit;
                if ($status === 'Approve') {
                    return '<span style="color: green; font-weight: bold; text-align: center; display: block;">' . $status . '</span>';
                } elseif ($status === 'Reject') {
                    return '<span style="color: red; font-weight: bold; text-align: center; display: block;">' . $status . '</span>';
                }
                return '<span style="text-align: center; font-weight: bold; display: block;">Pending</span>'; 
            })
            ->rawColumns(['action', 'status_submit']) 
            ->make(true);
    }    

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:purchase_request,id',
            'status' => 'required|in:Approve,Reject',
            'submit_date' => 'required|date', 
        ]);
    
        $purchaseRequest = PurchaseRequest::find($request->id);
        $purchaseRequest->status_submit = $request->status;
        $purchaseRequest->submit_date = $request->submit_date;
        $purchaseRequest->save();
    
        return response()->json(['success' => true]);
    }
    
    public function getData(Request $request)
    {
        $data = PurchaseRequest::select(
                'purchase_request.ref_pp', 
                'purchase_request.req_date', 
                'purchase_request.nama_barang', 
                'purchase_request.kode_barang', 
                'purchase_request.qty_pr', 
                \DB::raw('SUM(part_masuk.qty) as qty'), 
                'part_masuk.tanggal as received_date', 
                'purchase_request.sisa_rcvid'
            )
            ->leftJoin('part_masuk', function($join) {
                $join->on('purchase_request.ref_pp', '=', 'part_masuk.ref_pp')
                    ->on('purchase_request.nama_barang', '=', 'part_masuk.nama_barang') 
                    ->on('purchase_request.kode_barang', '=', 'part_masuk.kode_barang'); 
            })
            // Filter untuk mengecualikan data dengan status "Reject"
            ->whereNotIn('purchase_request.status_submit', ['Reject'])
            ->groupBy('purchase_request.ref_pp', 'purchase_request.req_date', 'purchase_request.nama_barang', 'purchase_request.kode_barang', 'part_masuk.tanggal', 'purchase_request.qty_pr', 'purchase_request.sisa_rcvid')
            ->get();
    
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
        



    
}
