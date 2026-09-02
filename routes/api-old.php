<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AgenController;
use App\Http\Controllers\Api\KotaController;
use App\Http\Controllers\Api\PaketController;
use App\Http\Controllers\Api\CabangController;
use App\Http\Controllers\Api\JamaahController;
use App\Http\Controllers\Api\KontenController;
use App\Http\Controllers\Api\LokasiController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\TravelController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\IteneraryController;
use App\Http\Controllers\Api\KatKontenController;
use App\Http\Controllers\Api\PengumumanController;


Route::prefix('person')->group(function () {
        Route::put('/', [PersonController::class, 'update']);
        Route::get('/login', [PersonController::class, 'login']);
        Route::post('/verifikasi', [PersonController::class, 'verifikasi']);
        Route::post('/forgotpass', [PersonController::class, 'forgotpass']);
        Route::put('/password', [PersonController::class, 'password']);
    });

Route::middleware(['auth:sanctum'])->group(function () {

    Route::prefix('person')->group(function () {
        Route::get('/detail/{IDPerson}', [PersonController::class, 'detail']);
        Route::post('/photo', [PersonController::class, 'photo']);
        Route::post('/', [PersonController::class, 'store']);
        Route::put('/hapus', [PersonController::class, 'hapus']);
    });

    Route::post('/logout', [PersonController::class, 'logout']);

    Route::prefix('itenerary')->group(function () {
        Route::get('/', [IteneraryController::class, 'index']);
        Route::get('/detail', [IteneraryController::class, 'detail']);
        Route::get('/current', [IteneraryController::class, 'current']);
        Route::post('/batch', [IteneraryController::class, 'batch']);
        Route::post('/', [IteneraryController::class, 'store']);
        Route::put('/', [IteneraryController::class, 'update']);
        Route::delete('/', [IteneraryController::class, 'destroy']);
    });



    Route::prefix('jamaah')->group(function () {
        Route::get('/paid/{IDPerson}', [JamaahController::class, 'paid']);
        Route::get('/paidall', [JamaahController::class, 'paidall']);
        Route::get('/person/{IDPerson}', [JamaahController::class, 'person']);
        Route::get('/history/{IDPerson}', [JamaahController::class, 'history']);
        Route::get('/historyagen/{IDPerson}', [JamaahController::class, 'historyagen']);
        Route::get('/paket', [JamaahController::class, 'paket']);
        Route::get('/detail', [JamaahController::class, 'detail']);
        Route::post('/', [JamaahController::class, 'index']);
        Route::post('/dokumen', [JamaahController::class, 'dokumen']);
        Route::put('/', [JamaahController::class, 'update']);
        Route::put('/alert', [JamaahController::class, 'alert']);
        Route::post('/testimoni', [JamaahController::class, 'testimoni']);
    });





    Route::prefix('voucher')->group(function () {
        Route::get('/list', [VoucherController::class, 'list']);
        Route::get('/detail/{IDVoucher}', [VoucherController::class, 'detail']);
        Route::get('/person/{IDPerson}', [VoucherController::class, 'person']);
        Route::get('/balance/{IDPerson}', [VoucherController::class, 'balance']);
        Route::post('/', [VoucherController::class, 'store']);
        Route::put('/{IDVoucher}', [VoucherController::class, 'update']);
        Route::delete('/{IDVoucher}', [VoucherController::class, 'destroy']);
        Route::post('/klaim/{IDVoucher}', [VoucherController::class, 'klaim']);
        Route::get('/summary', [VoucherController::class, 'summary']);
    });

    Route::prefix('agen')->group(function () {
        Route::get('/', [AgenController::class, 'index']);
        Route::get('agentravel', [AgenController::class, 'agentravel']);
    });

    Route::get('/agen/agentravel/{IDPerson}', [AgenController::class, 'agentravel']);

    Route::get('setting/biayatrip', [SettingController::class, 'biayatrip']);

    Route::prefix('kota')->group(function () {
        Route::get('/', [KotaController::class, 'index']);
        Route::get('{IDKota}', [KotaController::class, 'index']);
    });

    Route::prefix('katkonten')->group(function () {
        Route::get('/', [KatKontenController::class, 'index']);
        Route::get('{IDKatKonten}', [KatKontenController::class, 'index']);
    });

    Route::prefix('travel')->group(function () {
        Route::get('/', [TravelController::class, 'index']);
        Route::get('agen', [TravelController::class, 'agen']);
        Route::get('agenlist', [TravelController::class, 'agenList']);
        Route::get('{IDTravel}', [TravelController::class, 'detail']);
    });

    Route::get('/cabang/travel/{IDTravel}', [CabangController::class, 'travel']);
    Route::get('/cabang/agentravel', [CabangController::class, 'agentravel']);



    Route::prefix('paket')->group(function () {
        Route::get('/jenis', [PaketController::class, 'jenis']);
        Route::get('/agen/{IDPerson}', [PaketController::class, 'agen']);
        Route::get('/katalogagen/{IDPerson}', [PaketController::class, 'katalogagen']);
        Route::get('/monitoringagen/{IDPerson}', [PaketController::class, 'monitoringagen']);
        Route::get('/historyagen/{IDPerson}', [PaketController::class, 'historyagen']);
        Route::get('/katalog', [PaketController::class, 'katalog']);
        Route::get('/detail/{IDTravel}/{IDPaket}', [PaketController::class, 'detail']);
        Route::get('/hotel', [PaketController::class, 'hotel']);
        Route::get('/testimoni', [PaketController::class, 'testimoni']);
    });

    Route::prefix('konten')->group(function () {
        Route::get('/artikel', [KontenController::class, 'artikel']);
        Route::get('/lokasi', [KontenController::class, 'lokasi']);
        Route::get('/slider', [KontenController::class, 'slider']);
    });

    Route::prefix('lokasi')->group(function () {
        Route::get('/provinsi/{IDProv?}', [LokasiController::class, 'provinsi']);
        Route::get('/kabupaten/{IDProv?}/{IDKab?}', [LokasiController::class, 'kabupaten']);
        Route::get('/kecamatan/{IDProv?}/{IDKab?}/{IDKec?}', [LokasiController::class, 'kecamatan']);
        Route::get('/desa/{IDProv?}/{IDKab?}/{IDKec?}/{IDDesa?}', [LokasiController::class, 'desa']);
    });

    Route::prefix('pengumuman')->group(function () {
        Route::get('/', [PengumumanController::class, 'index']);
        Route::get('/count', [PengumumanController::class, 'count']);
        Route::post('/', [PengumumanController::class, 'store']);
        Route::put('/', [PengumumanController::class, 'update']);
        Route::delete('/{IDPengumuman}', [PengumumanController::class, 'destroy']);
    });
});
