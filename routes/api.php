<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MobileApi\PersonController;
use App\Http\Controllers\MobileApi\TravelController;
use App\Http\Controllers\MobileApi\PaketController;
use App\Http\Controllers\MobileApi\JamaahController;
use App\Http\Controllers\MobileApi\KontenController;
use App\Http\Controllers\MobileApi\PengumumanController;
use App\Http\Controllers\MobileApi\IteneraryController;
use App\Http\Controllers\MobileApi\LokasiController;
use App\Http\Controllers\MobileApi\KotaController;
use App\Http\Controllers\MobileApi\KatKontenController;
use App\Http\Controllers\MobileApi\AgenController;
use App\Http\Controllers\MobileApi\CabangController;
use App\Http\Controllers\MobileApi\SettingController;
use App\Http\Controllers\MobileApi\VoucherController;
use App\Http\Controllers\MobileApi\MeetController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Mobile API Routes
|--------------------------------------------------------------------------
|
| API routes untuk aplikasi mobile Haromain
|
*/

// Person Routes
Route::prefix('person')->group(function () {
    Route::put('/', [PersonController::class, 'update']);
    Route::get('detail/{IDPerson}', [PersonController::class, 'detail']);
    Route::put('update', [PersonController::class, 'update']);
    Route::put('hapus', [PersonController::class, 'hapus']);
    Route::post('store', [PersonController::class, 'store']);
    Route::post('login', [PersonController::class, 'login'])->name('api/person/login');
    Route::post('verifikasi', [PersonController::class, 'verifikasi']);
    Route::post('forgotpass', [PersonController::class, 'forgotpass']);
    Route::put('password', [PersonController::class, 'updatePassword']);
    Route::post('uploadphoto', [PersonController::class, 'uploadPhoto']);//entah dipake ta gak
    Route::post('photo', [PersonController::class, 'uploadPhoto']);
});

// Travel Routes
Route::prefix('travel')->group(function () {
    Route::get('/', [TravelController::class, 'index']);
    Route::get('agen', [TravelController::class, 'agen']);
    Route::get('agenlist', [TravelController::class, 'agenList']);
    Route::get('detail/{IDTravel}', [TravelController::class, 'detail']);
});

// Paket Routes
Route::prefix('paket')->group(function () {
    Route::get('jenis', [PaketController::class, 'jenis']);
    Route::get('agen/{IDPerson}', [PaketController::class, 'agen']);
    Route::get('katalogagen/{IDPerson}', [PaketController::class, 'katalogAgen']);
    Route::get('monitoringagen/{IDPerson}', [PaketController::class, 'monitoringAgen']);
    Route::get('historyagen/{IDPerson}', [PaketController::class, 'historyAgen']);
    Route::get('katalog', [PaketController::class, 'katalog']);
    Route::get('detail/{IDTravel}/{IDPaket}', [PaketController::class, 'detail']);
    Route::get('hotel', [PaketController::class, 'hotel']);
    Route::get('testimoni', [PaketController::class, 'testimoni']);
    Route::get('filter-options', [PaketController::class, 'filterOptions']);
    Route::get('filter', [PaketController::class, 'filter']);
});

// Jamaah Routes
Route::prefix('jamaah')->group(function () {
    Route::get('paid/{IDPerson}', [JamaahController::class, 'paid']);
    Route::get('paidall', [JamaahController::class, 'paidAll']);
    Route::get('person/{IDPerson}', [JamaahController::class, 'person']);
    Route::get('history/{IDPerson}', [JamaahController::class, 'history']);
    Route::get('historyagen/{IDPerson}', [JamaahController::class, 'historyAgen']);
    Route::get('paket', [JamaahController::class, 'paket']);
    Route::get('detail', [JamaahController::class, 'detail']);
    Route::post('/', [JamaahController::class, 'store']);
    Route::post('store', [JamaahController::class, 'store']);
    Route::post('dokumen', [JamaahController::class, 'dokumen']);
    Route::put('/', [JamaahController::class, 'update']);
    Route::put('alert', [JamaahController::class, 'alert']);
    Route::post('testimoni', [JamaahController::class, 'testimoni']);
});

// Konten Routes
Route::prefix('konten')->group(function () {
    Route::get('artikel', [KontenController::class, 'artikel']);
    Route::get('lokasi', [KontenController::class, 'lokasi']);
    Route::get('slider', [KontenController::class, 'slider']);
});

// Pengumuman Routes
Route::prefix('pengumuman')->group(function () {
    Route::get('/', [PengumumanController::class, 'index']);
    // Route::get('count', [PengumumanController::class, 'count']);
    Route::get('/count', [App\Http\Controllers\Api\PengumumanController::class, 'count']);
    Route::post('/', [PengumumanController::class, 'store']);
    Route::put('/', [PengumumanController::class, 'update']);
    Route::delete('{IDPengumuman}', [PengumumanController::class, 'destroy']);
});

// Itenerary Routes
Route::prefix('itenerary')->group(function () {
    Route::get('/', [IteneraryController::class, 'index']);
    Route::get('detail', [IteneraryController::class, 'detail']);
    Route::get('current', [IteneraryController::class, 'current']);
    Route::post('batch', [IteneraryController::class, 'batch']);
    Route::post('/', [IteneraryController::class, 'store']);
    Route::put('/', [IteneraryController::class, 'update']);
    Route::delete('/', [IteneraryController::class, 'destroy']);
});

// Lokasi Routes
Route::prefix('lokasi')->group(function () {
    Route::get('provinsi/{IDProv?}', [LokasiController::class, 'provinsi']);
    Route::get('kabupaten/{IDProv?}/{IDKab?}', [LokasiController::class, 'kabupaten']);
    Route::get('kecamatan/{IDProv?}/{IDKab?}/{IDKec?}', [LokasiController::class, 'kecamatan']);
    Route::get('desa/{IDProv?}/{IDKab?}/{IDKec?}/{IDDesa?}', [LokasiController::class, 'desa']);
});

// Kota Routes
Route::prefix('kota')->group(function () {
    Route::get('{IDKota?}', [KotaController::class, 'index']);
});

// Kategori Konten Routes
Route::prefix('katkonten')->group(function () {
    Route::get('{IDKatKonten?}', [KatKontenController::class, 'index']);
});

// Agen Routes
Route::prefix('agen')->group(function () {
    Route::get('agentravel/{IDPerson}', [AgenController::class, 'agenTravel']);
});

// Cabang Routes
Route::prefix('cabang')->group(function () {
    Route::get('travel/{IDTravel}', [CabangController::class, 'travel']);
    Route::get('agentravel', [CabangController::class, 'agenTravel']);
});

// Setting Routes
Route::prefix('setting')->group(function () {
    Route::get('biayatrip', [SettingController::class, 'biayaTrip']);
});

// Voucher Routes
Route::prefix('voucher')->group(function () {
    Route::get('list', [VoucherController::class, 'list']);
    Route::get('detail/{IDVoucher}', [VoucherController::class, 'detail']);
    Route::get('person/{IDPerson}', [VoucherController::class, 'person']);
    Route::get('balance/{IDPerson}', [VoucherController::class, 'balance']);
    Route::post('create', [VoucherController::class, 'store']);
    Route::put('claim/{IDVoucher}', [VoucherController::class, 'claim']);
});

Route::prefix('meet')->group(function () {
    Route::get('list', [MeetController::class, 'list']);
    Route::get('detail', [MeetController::class, 'detail']);
    Route::post('create', [MeetController::class, 'create']);
    Route::put('update', [MeetController::class, 'update']);
    Route::delete('delete', [MeetController::class, 'delete']);
    Route::post('join', [MeetController::class, 'join']);
    Route::post('leave', [MeetController::class, 'leave']);
    Route::get('participants', [MeetController::class, 'participants']);
    Route::get('check-leader-joined', [MeetController::class, 'checkLeaderJoined']);
    Route::post('get-jitsi-token', [MeetController::class, 'getJitsiToken']);
});
