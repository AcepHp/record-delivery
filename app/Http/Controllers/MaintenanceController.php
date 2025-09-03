<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\DeliveryRch;
use App\Models\ArchiveLotFull;
use App\Models\ArchiveLotReceh;
use DB;

class MaintenanceController extends Controller
{
    public function backupForm()
    {
        return view('maintenance.backup_restore');
    }

    public function backupData(Request $request)
    {
        // set time no limit
        set_time_limit(0); 
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'password' => 'required',
        ]);

        // cek password
        if ($request->password !== env('BACKUP_RESTORE_PASSWORD')) {
            return redirect()->back()->with('error', 'Password salah!');
        }

        $start = $request->start_date;
        $end = $request->end_date;

        DB::transaction(function() use ($start, $end) {

            // Backup Delivery 
            Delivery::whereBetween('tgl_bln_thn', [$start, $end])
                ->chunkById(200, function($deliveries) {
                    $archiveData = $deliveries->map(function($d) {
                        return [
                            'no_transaksi' => $d->no_transaksi,
                            'tgl_bln_thn' => $d->tgl_bln_thn,
                            'part_number' => $d->part_number,
                            'lot_number' => $d->lot_number,
                            'qty' => $d->qty,
                            'archive_date' => now(),
                            'flag' => $d->flag,
                        ];
                    })->toArray();

                    ArchiveLotFull::insert($archiveData); // bulk insert
                    Delivery::whereIn('id', $deliveries->pluck('id'))->delete(); 
                });

            // Backup DeliveryRch 
            DeliveryRch::whereBetween('tgl_bln_thn', [$start, $end])
                ->chunkById(200, function($deliveriesRch) {
                    $archiveData = $deliveriesRch->map(function($d) {
                        return [
                            'no_transaksi' => $d->no_transaksi,
                            'tgl_bln_thn' => $d->tgl_bln_thn,
                            'part_number' => $d->part_number,
                            'serial_number' => $d->serial_number ?? null,
                            'lot_number' => $d->lot_number,
                            'qty' => $d->qty,
                            'archive_date' => now(),
                        ];
                    })->toArray();

                    ArchiveLotReceh::insert($archiveData); 
                    DeliveryRch::whereIn('id', $deliveriesRch->pluck('id'))->delete(); 
                });
        });

        return redirect()->back()->with('success', 'Backup berhasil dari '.$start.' sampai '.$end);
    }

    public function restoreData(Request $request)
    {
        set_time_limit(0);
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'password' => 'required',
        ]);

        // cek password
        if ($request->password !== env('BACKUP_RESTORE_PASSWORD')) {
            return redirect()->back()->with('error', 'Password salah!');
        }

        $start = $request->start_date;
        $end = $request->end_date;

        DB::transaction(function() use ($start, $end) {

            // Restore ArchiveLotFull 
            ArchiveLotFull::whereBetween('tgl_bln_thn', [$start, $end])
                ->chunkById(200, function($archivedDeliveries) {
                    $restoreData = $archivedDeliveries->map(function($d) {
                        return [
                            'no_transaksi' => $d->no_transaksi,
                            'tgl_bln_thn' => $d->tgl_bln_thn,
                            'part_number' => $d->part_number,
                            'lot_number' => $d->lot_number,
                            'qty' => $d->qty,
                            'flag' => $d->flag ?? 0,
                        ];
                    })->toArray();

                    Delivery::insert($restoreData); 
                    ArchiveLotFull::whereIn('id', $archivedDeliveries->pluck('id'))->delete();
                });

            // Restore ArchiveLotReceh
            ArchiveLotReceh::whereBetween('tgl_bln_thn', [$start, $end])
                ->chunkById(200, function($archivedRch) {
                    $restoreData = $archivedRch->map(function($d) {
                        return [
                            'no_transaksi' => $d->no_transaksi,
                            'tgl_bln_thn' => $d->tgl_bln_thn,
                            'part_number' => $d->part_number,
                            'serial_number' => $d->serial_number,
                            'lot_number' => $d->lot_number,
                            'qty' => $d->qty,
                            'flag' => $d->flag ?? 0,
                        ];
                    })->toArray();

                    DeliveryRch::insert($restoreData);
                    ArchiveLotReceh::whereIn('id', $archivedRch->pluck('id'))->delete();
                });
        });

        return redirect()->back()->with('success', 'Restore berhasil dari '.$start.' sampai '.$end);
    }
}
