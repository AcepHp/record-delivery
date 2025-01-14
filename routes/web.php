<?php

use App\Http\Controllers\EditController;
use App\Http\Controllers\PartMasukController;
use App\Http\Controllers\PartKeluarController;
use App\Http\Controllers\PartKeluarControllerUser;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\SparepartControllerUser;
use App\Http\Controllers\StockController;
use App\Http\Controllers\HargaController;
use App\Http\Controllers\PRController;
use App\Http\Controllers\PRTableController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\DeliveryController;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\NewDataController;
use App\Http\Controllers\ErrorController;

Route::get('/', function () {
    return redirect('/login');
});

Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.post');
Route::get('/error', [ErrorController::class, 'show']);
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');


Route::middleware('auth')->group(function () {
    //dashboard 
    Route::get('/spareparts-data', [SparepartController::class, 'getSpareparts'])->name('spareparts.data');
    Route::get('/dashboard', [SparepartController::class,'index'])->name('dashboard');
    Route::get('/dashboarduser', [SparepartControllerUser::class,'index'])->name('dashboarduser');
    //profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    //import excel
    Route::get('/import', function() {
        return view('pemindahan.import'); 
    })->name('spareparts.import.view');

    Route::get('/import/data', [DataController::class, 'getSpareparts'])->name('spareparts.table');
    Route::post('/import', [SparepartController::class, 'import'])->name('spareparts.import');
    
    //folder data komponen
    Route::resource('/data', DataController::class);
    //folder data komponen baru
    Route::prefix('data/new')->group(function () {
        Route::get('/create', [NewDataController::class, 'create'])->name('data.new.create');
        Route::post('/store', [NewDataController::class, 'store'])->name('data.new.store');
    });
    //input part masuk
    Route::resource('/partmasuk', PartMasukController::class);


    //record
    Route::resource('/record', RecordController::class);
    Route::get('/get-model-data/{model}', [RecordController::class, 'getModelData']);
    //delivery
    Route::resource('/delivery', DeliveryController::class);
    Route::post('/delivery/compare', [DeliveryController::class, 'compareQty'])->name('delivery.compare');
    Route::get('/delivery-data', [DeliveryController::class, 'getDeliveryData'])->name('delivery.data');
    //edit
    Route::resource('/editdata', EditController::class);

    //input part keluar
    Route::resource('/partkeluar', PartKeluarController::class);
    Route::resource('/partkeluaruser', PartKeluarControllerUser::class);
    //input stock part
    Route::resource('/stock', StockController::class);
    //input harga part
    Route::resource('/harga', HargaController::class);
    //fungsi filter
    Route::get('/data/no-stations/{line}', [DataController::class, 'getNoStationsByLine'])->name('data.no-stations');
    Route::get('/data/nama-stations/{line}', [DataController::class, 'getNamaStationsByLine'])->name('data.nama-stations');
    Route::get('/data/search-nama-barang/{term}', [DataController::class, 'searchNamaBarang'])->name('data.search-nama-barang');
    Route::get('/data/search-no-purchase/{term}', [PRController::class, 'searchNamaBarang'])->name('data.search-no-purchase');
    Route::get('/data/lines', [DataController::class, 'getLines'])->name('data.lines');
    // purchase request
    Route::resource('/purchase', PRController::class);
    //folder data komponen baru
    Route::prefix('purchase/new')->group(function () {
        Route::get('/create', [PRTableController::class, 'create'])->name('purchase.new.create');
        Route::post('/store', [PRTableController::class, 'store'])->name('purchase.new.store');
        Route::get('/purchase-data', [PRTableController::class, 'getSpareparts'])->name('purchase.new.data');
        Route::get('/purchase-table', [PRTableController::class, 'getData'])->name('purchase.new.tabel');
        Route::post('/purchase/update/status', [PRTableController::class, 'updateStatus'])->name('purchase.update.status');
        Route::get('/purchase/check-status', [PRTableController::class, 'checkStatus'])->name('purchase.check.status');
    });
});


require __DIR__.'/auth.php';

