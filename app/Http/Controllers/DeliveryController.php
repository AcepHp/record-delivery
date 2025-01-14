<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Record;
use App\Models\M_Model;
use App\Models\Delivery;
use App\Models\PurchaseRequest;
use Yajra\DataTables\DataTables;
use Exception;

class DeliveryController extends Controller
{
    public function index()
    {
        
    }

    public function create()
    {
        $noTransaksi = session('no_transaksi');
        $tglBlnThn = session('tgl_bln_thn');
        $plantDest = session('plant_dest');
        $model = session('model');
        $qty = session('qty');
        $pic = session('pic');
    
        return view('data.delivery', compact('noTransaksi', 'tglBlnThn', 'plantDest', 'model', 'qty', 'pic'));
    }
    

    public function store(Request $request)
    {
        try {
            $noTransaksi = $request->input('no_transaksi');
            if (!$noTransaksi) {
                return response()->json(['success' => false, 'message' => 'No transaksi tidak ditemukan atau tidak valid!'], 400);
            }

            $qrData = $request->input('qrcode');
            $dataArray = explode('|', $qrData);

            if (count($dataArray) < 4) {
                return response()->json(['success' => false, 'message' => 'Format data salah!'], 400);
            }

            $validatedData = [
                'tgl_bln_thn' => now(),
                'model' => $dataArray[0],
                'qty' => $dataArray[2],
            ];

            $noTransaksi = $request->input('no_transaksi') ?? 'AVI' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

            $record = new Delivery([
                'no_transaksi' => $noTransaksi,
                'tgl_bln_thn' => $validatedData['tgl_bln_thn'],
                'max_model' => $validatedData['model'],
                'lot_number' => $dataArray[3],
                'flag' => 1,
                'qty' => $validatedData['qty'],
            ]);

            $isDuplicate = Delivery::where('no_transaksi', $noTransaksi)
                ->where('max_model', $validatedData['model'])
                ->where('lot_number', $dataArray[3])
                ->exists();

            if ($isDuplicate) {
                return response()->json(['success' => false, 'message' => 'Data sudah ada dalam database'], 409);
            }

            if ($record->save()) {
                return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
            } else {
                return response()->json(['success' => false, 'message' => 'Data gagal disimpan'], 500);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
  
    public function show(Delivery $data)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Delivery $data)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delivery $data)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $data)
    {
        //
    }

    public function compareQty(Request $request)
    {
        try {
            $noTransaksi = session('no_transaksi'); 
            $tglBlnThn = now()->toDateString();
    
            $recordData = Record::where('tgl_bln_thn', $tglBlnThn)
                ->where('no_transaksi', $noTransaksi)
                ->get(['no_transaksi', 'model', 'plant_dest', 'pic', 'qty']);
    
            $deliveryData = Delivery::whereDate('tgl_bln_thn', $tglBlnThn) 
                ->where('no_transaksi', $noTransaksi)
                ->selectRaw('no_transaksi, max_model, SUM(qty) as qty')
                ->groupBy('no_transaksi', 'max_model')
                ->get();

            $recordQty = $recordData->sum('qty');
            $deliveryQty = $deliveryData->sum('qty');
    
            $message = '';
            $status = false;
    
            if ($recordQty === $deliveryQty) {
                Record::where('tgl_bln_thn', $tglBlnThn)->where('flag', 1)->update(['flag' => 0]);
                Delivery::whereDate('tgl_bln_thn', $tglBlnThn)->where('flag', 1)->update(['flag' => 0]);
    
                $message = 'Data cocok, berhasil mengupdate data';
                $status = true;
            } else {
                $message = 'Data tidak cocok, gagal mengupdate data';
            }
    
            return response()->json([
                'success' => $status,
                'message' => $message,
                'recordData' => $recordData,
                'deliveryData' => $deliveryData
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getDeliveryData(Request $request)
    {
        $noTransaksi = session('no_transaksi'); 
    
        $deliveries = Delivery::where('no_transaksi', $noTransaksi)
            ->orderBy('tgl_bln_thn', 'desc') 
            ->get();
    
        return DataTables::of($deliveries)
            ->addIndexColumn() 
            ->make(true);
    }
    

    
    
    

}
