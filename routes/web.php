<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\Admin\AgenController;
use App\Http\Controllers\Admin\KotaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PaketController as AdminPaketController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CabangController;
use App\Http\Controllers\Admin\JamaahController;
use App\Http\Controllers\Admin\LokasiController;
use App\Http\Controllers\Admin\PersonController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\TravelController;
use App\Http\Controllers\Admin\ArtikelController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\LogdataController;
use App\Http\Controllers\Admin\PromosiController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\BroadcastController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KatlokasiController;
use App\Http\Controllers\Admin\KatarticleController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\PengeluarankasController;
use App\Http\Controllers\Admin\EntryJamaahPaketController;
use App\Http\Controllers\Admin\MaskapaiController;
use App\Http\Controllers\Admin\PaketAdminController;
use App\Http\Controllers\Admin\Tagihan\CallbackController;
use App\Http\Controllers\Admin\Tagihan\PembayaranController;
use App\Http\Controllers\Admin\Tagihan\TerimabayarController;
use App\Http\Controllers\Admin\Tagihan\BiayaAplikasiController;
use App\Http\Controllers\Admin\Tagihan\TagihantravelController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\GaleriController;

// Route::get('/', function () {
//     return view('welcome');
// });

// // VERSI LAMA
// Route::get('/', [HomeController::class, 'index'])->name('home');
// // Public Paket Routes
// Route::get('Paket', [PaketController::class, 'index'])->name('paket.index');
// Route::get('Paket/detail', [PaketController::class, 'detail'])->name('paket.detail');
// Route::get('Paket/get_itenerary', [PaketController::class, 'getItenerary'])->name('paket.getItenerary');
// Route::get('Paket/get_itenerary_hari/{encodedIDPaket}/{encodedIDTravel}/{HariKe}', [PaketController::class, 'getIteneraryHari'])->name('paket.getIteneraryHari');


// VERSI LANDING BARU
Route::livewire('/', 'pages::landing.beranda')->name('home');
// Route::livewire('/katalog', 'pages::landing.katalog')->name('katalog');
// Route::livewire('/katalog/detail', 'pages::landing.katalog.detail')->name('katalog.detail');
Route::livewire('/galeri', 'pages::landing.galeri')->name('galeri');
Route::livewire('/hubungi-kami', 'pages::landing.hubungi-kami')->name('hubungi-kami');
Route::livewire('/privacy-policy', 'pages::landing.privacy-policy')->name('privacy-policy');

Route::get("test-ok", function () {
    return "Test OK";
});

Route::get('auth/login', [AuthController::class, 'index'])->name('auth.login.page');
Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('auth/lupapassword', [AuthController::class, 'lupaPassword'])->name('auth.lupa');
Route::get('auth/resetpassword', [AuthController::class, 'resetPassword'])->name('auth.reset');
Route::post('auth/updatepassword', [AuthController::class, 'updatePassword']);
Route::post('auth/sendemail', [AuthController::class, 'sendEmail']);



Route::get('auth/logout', [AuthController::class, 'logout'])
    ->middleware(['custom'])
    ->name('auth.logout');

Route::middleware(['custom'])->prefix('admin')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('dashboard/grafikPerjalananBulanan', [DashboardController::class, 'grafikPerjalananBulanan'])->name('admin.dashboard.grafikPerjalananBulanan');

    // User management routes
    Route::get('User', [UserController::class, 'index'])->name('admin.user.index');
    Route::post('User/changePassword', [UserController::class, 'changePassword'])->name('admin.user.changePassword');
    Route::get('User/tambah', [UserController::class, 'tambah'])->name('admin.user.tambah');
    Route::get('User/edit', [UserController::class, 'edit'])->name('admin.user.edit');
    Route::post('User/simpan', [UserController::class, 'simpan'])->name('admin.user.simpan');
    Route::get('User/setaktif', [UserController::class, 'setaktif'])->name('admin.user.setaktif');
    Route::get('User/hapus', [UserController::class, 'hapus'])->name('admin.user.hapus');
    Route::post('User/get', [UserController::class, 'get'])->name('admin.user.get');
    Route::get('User/getCabangByTravel/{idTravel}', [UserController::class, 'getCabangByTravel'])->name('admin.user.getCabangByTravel');

    // Travel management routes
    Route::get('Travel', [TravelController::class, 'index'])->name('admin.travel.index');
    Route::get('Travel/tambah', [TravelController::class, 'tambah'])->name('admin.travel.tambah');
    Route::get('Travel/edit', [TravelController::class, 'edit'])->name('admin.travel.edit');
    Route::post('Travel/simpan', [TravelController::class, 'simpan'])->name('admin.travel.simpan');
    Route::get('Travel/setaktif', [TravelController::class, 'setaktif'])->name('admin.travel.setaktif');
    Route::get('Travel/hapus', [TravelController::class, 'hapus'])->name('admin.travel.hapus');
    Route::post('Travel/get', [TravelController::class, 'get'])->name('admin.travel.get');

    // Lokasi endpoints (provinsi/kabupaten/kecamatan/desa)
    Route::get('Lokasi/getprov/{IDProv?}', [LokasiController::class, 'getProv'])->name('admin.lokasi.getProv');
    Route::get('Lokasi/getkab/{IDProv?}/{IDKab?}', [LokasiController::class, 'getKab'])->name('admin.lokasi.getKab');
    Route::get('Lokasi/getkec/{IDProv?}/{IDKab?}/{IDKec?}', [LokasiController::class, 'getKec'])->name('admin.lokasi.getKec');
    Route::get('Lokasi/getdesa/{IDProv?}/{IDKab?}/{IDKec?}/{IDDesa?}', [LokasiController::class, 'getDesa'])->name('admin.lokasi.getDesa');

    // Banner management routes
    Route::get('Banner', [BannerController::class, 'index'])->name('admin.banner.index');
    Route::get('Banner/tambah', [BannerController::class, 'tambah'])->name('admin.banner.tambah');
    Route::get('Banner/edit', [BannerController::class, 'edit'])->name('admin.banner.edit');
    Route::post('Banner/simpan', [BannerController::class, 'simpan'])->name('admin.banner.simpan');
    Route::get('Banner/setaktif', [BannerController::class, 'setaktif'])->name('admin.banner.setaktif');
    Route::get('Banner/hapus', [BannerController::class, 'hapus'])->name('admin.banner.hapus');
    Route::post('Banner/get', [BannerController::class, 'get'])->name('admin.banner.get');

    // Broadcast management routes
    Route::get('Broadcast', [BroadcastController::class, 'index'])->name('admin.broadcast.index');
    Route::get('Broadcast/tambah', [BroadcastController::class, 'tambah'])->name('admin.broadcast.tambah');
    Route::get('Broadcast/edit', [BroadcastController::class, 'edit'])->name('admin.broadcast.edit');
    Route::post('Broadcast/simpan', [BroadcastController::class, 'simpan'])->name('admin.broadcast.simpan');
    Route::get('Broadcast/hapus', [BroadcastController::class, 'hapus'])->name('admin.broadcast.hapus');
    Route::post('Broadcast/get', [BroadcastController::class, 'get'])->name('admin.broadcast.get');

    // Slider management routes
    Route::get('Slider', [SliderController::class, 'index'])->name('admin.slider.index');
    Route::get('Slider/tambah', [SliderController::class, 'tambah'])->name('admin.slider.tambah');
    Route::get('Slider/edit', [SliderController::class, 'edit'])->name('admin.slider.edit');
    Route::post('Slider/simpan', [SliderController::class, 'simpan'])->name('admin.slider.simpan');
    Route::get('Slider/setaktif', [SliderController::class, 'setaktif'])->name('admin.slider.setaktif');
    Route::get('Slider/hapus', [SliderController::class, 'hapus'])->name('admin.slider.hapus');
    Route::post('Slider/get', [SliderController::class, 'get'])->name('admin.slider.get');

    // Kota management routes
    Route::get('kota', [KotaController::class, 'index'])->name('admin.kota.index');
    Route::get('kota/tambah', [KotaController::class, 'tambah'])->name('admin.kota.tambah');
    Route::get('kota/edit', [KotaController::class, 'edit'])->name('admin.kota.edit');
    Route::post('kota/simpan', [KotaController::class, 'simpan'])->name('admin.kota.simpan');
    Route::get('kota/setaktif', [KotaController::class, 'setaktif'])->name('admin.kota.setaktif');
    Route::get('kota/hapus', [KotaController::class, 'hapus'])->name('admin.kota.hapus');
    Route::post('kota/get', [KotaController::class, 'get'])->name('admin.kota.get');

    // maskapai magement route

    Route::get('maskapai', [MaskapaiController::class, 'index'])->name('admin.maskapai.index');
    Route::get('maskapai/tambah', [MaskapaiController::class, 'tambah'])->name('admin.maskapai.tambah');
    Route::get('maskapai/edit', [MaskapaiController::class, 'edit'])->name('admin.maskapai.edit');
    Route::post('maskapai/simpan', [MaskapaiController::class, 'simpan'])->name('admin.maskapai.simpan');
    Route::get('maskapai/setaktif', [MaskapaiController::class, 'setaktif'])->name('admin.maskapai.setaktif');
    Route::get('maskapai/hapus', [MaskapaiController::class, 'hapus'])->name('admin.maskapai.hapus');
    Route::post('maskapai/get', [MaskapaiController::class, 'get'])->name('admin.maskapai.get');

    // Kategori Lokasi (Katlokasi) management routes
    Route::get('katlokasi', [KatlokasiController::class, 'index'])->name('admin.katlokasi.index');
    Route::post('Katlokasi/get', [KatlokasiController::class, 'get'])->name('admin.katlokasi.get');
    Route::get('Katlokasi/tambah', [KatlokasiController::class, 'tambah'])->name('admin.katlokasi.tambah');
    Route::get('Katlokasi/edit', [KatlokasiController::class, 'edit'])->name('admin.katlokasi.edit');
    Route::post('Katlokasi/simpan', [KatlokasiController::class, 'simpan'])->name('admin.katlokasi.simpan');
    Route::get('Katlokasi/setaktif', [KatlokasiController::class, 'setaktif'])->name('admin.katlokasi.setaktif');
    Route::get('Katlokasi/hapus', [KatlokasiController::class, 'hapus'])->name('admin.katlokasi.hapus');

    // Kategori artikel (Katartikel) management routes
    Route::get('katarticle', [KatarticleController::class, 'index'])->name('admin.katarticle.index');
    Route::post('Katarticle/get', [KatarticleController::class, 'get'])->name('admin.katarticle.get');
    Route::get('Katarticle/tambah', [KatarticleController::class, 'tambah'])->name('admin.katarticle.tambah');
    Route::get('Katarticle/edit', [KatarticleController::class, 'edit'])->name('admin.katarticle.edit');
    Route::post('Katarticle/simpan', [KatarticleController::class, 'simpan'])->name('admin.katarticle.simpan');
    Route::get('Katarticle/setaktif', [KatarticleController::class, 'setaktif'])->name('admin.katarticle.setaktif');
    Route::get('Katarticle/hapus', [KatarticleController::class, 'hapus'])->name('admin.katarticle.hapus');

    // Artikel management routes
    Route::get('artikel', [ArtikelController::class, 'index'])->name('admin.artikel.index');
    Route::get('artikel/tambah', [ArtikelController::class, 'tambah'])->name('admin.artikel.tambah');
    Route::get('artikel/edit', [ArtikelController::class, 'edit'])->name('admin.artikel.edit');
    Route::post('artikel/simpan', [ArtikelController::class, 'simpan'])->name('admin.artikel.simpan');
    Route::get('artikel/setrekomendasi', [ArtikelController::class, 'setRekomendasi'])->name('admin.artikel.setrekomendasi');
    Route::get('artikel/setaktif', [ArtikelController::class, 'setAktif'])->name('admin.artikel.setaktif');
    Route::get('artikel/hapus', [ArtikelController::class, 'hapus'])->name('admin.artikel.hapus');
    Route::post('artikel/get', [ArtikelController::class, 'get'])->name('admin.artikel.get');

    Route::get('Setting', [SettingController::class, 'index'])->name('admin.setting.index');
    Route::post('setting/get', [SettingController::class, 'get'])->name('admin.setting.get');
    Route::post('setting/simpan', [SettingController::class, 'simpan'])->name('admin.setting.simpan');

    Route::get('location', [LocationController::class, 'index'])->name('admin.location');
    Route::get('location/tambah', [LocationController::class, 'tambah'])->name('admin.location.tambah');
    Route::get('location/edit', [LocationController::class, 'edit'])->name('admin.location.edit');
    Route::post('location/simpan', [LocationController::class, 'simpan'])->name('admin.location.simpan');
    Route::get('location/setrekomendasi', [LocationController::class, 'setrekomendasi'])->name('admin.location.setrekomendasi');
    Route::get('location/setaktif', [LocationController::class, 'setaktif'])->name('admin.location.setaktif');
    Route::get('location/hapus', [LocationController::class, 'hapus'])->name('admin.location.hapus');
    Route::post('location/get', [LocationController::class, 'get'])->name('admin.location.get');
    Route::get('location/getKotaByTravel/{idTravel}', [LocationController::class, 'getKotaByTravel'])->name('admin.location.getKotaByTravel');

    // Cabang management routes
    Route::get('Cabang', [CabangController::class, 'index'])->name('admin.cabang.index');
    Route::get('Cabang/tambah', [CabangController::class, 'tambah'])->name('admin.cabang.tambah');
    Route::get('Cabang/edit', [CabangController::class, 'edit'])->name('admin.cabang.edit');
    Route::post('Cabang/simpan', [CabangController::class, 'simpan'])->name('admin.cabang.simpan');
    Route::get('Cabang/setaktif', [CabangController::class, 'setAktif'])->name('admin.cabang.setaktif');
    Route::get('Cabang/hapus', [CabangController::class, 'hapus'])->name('admin.cabang.hapus');

    // FAQ management routes
    Route::get('faq', [FaqController::class, 'index'])->name('admin.faq.index');
    Route::get('faq/tambah', [FaqController::class, 'tambah'])->name('admin.faq.tambah');
    Route::get('faq/edit', [FaqController::class, 'edit'])->name('admin.faq.edit');
    Route::post('faq/simpan', [FaqController::class, 'simpan'])->name('admin.faq.simpan');
    Route::get('faq/hapus', [FaqController::class, 'hapus'])->name('admin.faq.hapus');
    Route::post('faq/get', [FaqController::class, 'get'])->name('admin.faq.get');

    // Testimonial management routes
    Route::get('testimonial', [TestimonialController::class, 'index'])->name('admin.testimonial.index');
    Route::get('testimonial/tambah', [TestimonialController::class, 'tambah'])->name('admin.testimonial.tambah');
    Route::get('testimonial/edit', [TestimonialController::class, 'edit'])->name('admin.testimonial.edit');
    Route::post('testimonial/simpan', [TestimonialController::class, 'simpan'])->name('admin.testimonial.simpan');
    Route::get('testimonial/hapus', [TestimonialController::class, 'hapus'])->name('admin.testimonial.hapus');
    Route::post('testimonial/get', [TestimonialController::class, 'get'])->name('admin.testimonial.get');

    // Galeri management routes
    Route::get('galeri', [GaleriController::class, 'index'])->name('admin.galeri.index');
    Route::get('galeri/tambah', [GaleriController::class, 'tambah'])->name('admin.galeri.tambah');
    Route::get('galeri/edit', [GaleriController::class, 'edit'])->name('admin.galeri.edit');
    Route::post('galeri/simpan', [GaleriController::class, 'simpan'])->name('admin.galeri.simpan');
    Route::post('galeri/setting-halaman', [GaleriController::class, 'simpanSettingHalaman'])->name('admin.galeri.setting.update');
    Route::get('galeri/hapus', [GaleriController::class, 'hapus'])->name('admin.galeri.hapus');
    Route::post('galeri/get', [GaleriController::class, 'get'])->name('admin.galeri.get');

    Route::post('Cabang/get', [CabangController::class, 'get'])->name('admin.cabang.get');

    // Person (Jamaah) management routes
    Route::get('Person', [PersonController::class, 'index'])->name('admin.person.index');
    Route::post('Person/reset-password', [PersonController::class, 'resetPassword'])->name('admin.reset-password');
    Route::get('Person/tambah', [PersonController::class, 'tambah'])->name('admin.person.tambah');
    Route::get('Person/edit', [PersonController::class, 'edit'])->name('admin.person.edit');
    Route::post('Person/simpan', [PersonController::class, 'simpan'])->name('admin.person.simpan');
    Route::post('Person/get', [PersonController::class, 'get'])->name('admin.person.get');
    Route::get('Person/setaktif', [PersonController::class, 'setaktif'])->name('admin.person.setaktif');
    Route::get('Person/hapus', [PersonController::class, 'hapus'])->name('admin.person.hapus');
    Route::post('Person/getAgenByLevel', [PersonController::class, 'getAgenByLevel'])->name('admin.person.getAgenByLevel');
    Route::get('Person/forceLogout', [PersonController::class, 'forceLogout'])->name('admin.person.forceLogout');
    Route::get('Person/detail/{id}', [PersonController::class, 'detail'])->name('admin.person.detail');
    Route::post('Person/updateNama', [PersonController::class, 'updateNama']);

    // Jamaah management routes
    Route::get('Jamaah', [JamaahController::class, 'index'])->name('admin.jamaah.index');
    Route::get('Jamaah/tambah', [JamaahController::class, 'tambah'])->name('admin.jamaah.tambah');
    Route::get('Jamaah/edit', [JamaahController::class, 'edit'])->name('admin.jamaah.edit');
    Route::post('Jamaah/simpan', [JamaahController::class, 'simpan'])->name('admin.jamaah.simpan');
    Route::get('Jamaah/getAgenByLevel', [JamaahController::class, 'getAgenByLevel'])->name('admin.jamaah.getAgenByLevel');
    Route::post('Jamaah/get', [JamaahController::class, 'get'])->name('admin.jamaah.get');
    Route::get('Jamaah/setaktif', [JamaahController::class, 'setaktif'])->name('admin.jamaah.setaktif');
    Route::get('Jamaah/hapus', [JamaahController::class, 'hapus'])->name('admin.jamaah.hapus');

    // Entry Jamaah Paket (migrated from CodeIgniter Entryjamaahpaket)
    Route::get('Entryjamaahpaket', [EntryJamaahPaketController::class, 'index'])->name('admin.entryjamaahpaket.index');
    Route::post('Entryjamaahpaket/simpan', [EntryJamaahPaketController::class, 'simpan'])->name('admin.entryjamaahpaket.simpan');
    Route::post('Entryjamaahpaket/get', [EntryJamaahPaketController::class, 'get'])->name('admin.entryjamaahpaket.get');
    Route::post('Entryjamaahpaket/import', [EntryJamaahPaketController::class, 'import'])->name('admin.entryjamaahpaket.import');
    Route::get('Entryjamaahpaket/download_template', [EntryJamaahPaketController::class, 'downloadTemplate'])->name('admin.entryjamaahpaket.download_template');
    Route::get('Entryjamaahpaket/cetak_nota/{IDPaket}/{IDPerson}', [EntryJamaahPaketController::class, 'cetakNota'])->name('admin.entryjamaahpaket.cetak_nota');

    Route::get('Pengeluarankas/cetak_nota/{NoTransaksi}', [PengeluarankasController::class, 'cetakNota'])->name('admin.pengeluarankas.cetak_nota');

    // Agen management routes
    Route::get('Agen', [AgenController::class, 'index'])->name('admin.agen.index');
    Route::get('Agen/tambah', [AgenController::class, 'tambah'])->name('admin.agen.tambah');
    Route::get('Agen/edit', [AgenController::class, 'edit'])->name('admin.agen.edit');
    Route::get('Agen/addfromjamaah', [AgenController::class, 'addFromJamaah'])->name('admin.agen.addfromjamaah');
    Route::get('Agen/getjamaahbytravel', [AgenController::class, 'getJamaahByTravel'])->name('admin.agen.getjamaahbytravel');
    Route::post('Agen/simpan', [AgenController::class, 'simpan'])->name('admin.agen.simpan');
    Route::get('Agen/setaktif', [AgenController::class, 'setAktif'])->name('admin.agen.setaktif');
    Route::get('Agen/hapus', [AgenController::class, 'hapus'])->name('admin.agen.hapus');
    Route::post('Agen/get', [AgenController::class, 'get'])->name('admin.agen.get');

    // Tagihan - Biaya Aplikasi
    Route::get('tagihan/Biayaaplikasi', [BiayaAplikasiController::class, 'index'])->name('admin.tagihan.biayaaplikasi.index');
    Route::post('tagihan/biayaaplikasi/get', [BiayaAplikasiController::class, 'get'])->name('admin.tagihan.biayaaplikasi.get');

    // Paket management routes
    Route::get('Paket', [AdminPaketController::class, 'index'])->name('admin.paket.index');
    Route::get('Paket/tambah', [AdminPaketController::class, 'tambah'])->name('admin.paket.tambah');
    Route::get('Paket/edit', [AdminPaketController::class, 'edit'])->name('admin.paket.edit');
    Route::post('Paket/simpan', [AdminPaketController::class, 'simpan'])->name('admin.paket.simpan');
    Route::get('Paket/setaktif', [AdminPaketController::class, 'setAktif'])->name('admin.paket.setaktif');
    Route::get('Paket/hapus', [AdminPaketController::class, 'hapus'])->name('admin.paket.hapus');
    Route::get('Paket/leader', [AdminPaketController::class, 'leader'])->name('admin.paket.leader');
    Route::get('Paket/find', [AdminPaketController::class, 'find'])->name('admin.paket.find');
    Route::get('Paket/hapusSyarat', [AdminPaketController::class, 'hapusSyarat'])->name('admin.paket.hapusSyarat');
    Route::post('Paket/get', [AdminPaketController::class, 'get'])->name('admin.paket.get');

    // Paket itenerary routes
    Route::get('Paket/itenerary', [AdminPaketController::class, 'itenerary'])->name('admin.paket.itenerary');
    Route::post('Paket/get_itenerary', [AdminPaketController::class, 'getItenerary'])->name('admin.paket.getItenerary');
    Route::get('Paket/tambah_itenerary', [AdminPaketController::class, 'tambahItenerary'])->name('paket.tambah_itenerary');
    Route::get('Paket/edit_itenerary', [AdminPaketController::class, 'editItenerary'])->name('admin.paket.edit_itenerary');
    Route::post('Paket/simpan_itenerary/{encodedIDPaket}/{encodedIDTravel}', [AdminPaketController::class, 'simpanItenerary'])->name('admin.paket.simpanItenerary');
    Route::post('Paket/hapus_itenerary', [AdminPaketController::class, 'hapusItenerary'])->name('admin.paket.hapusItenerary');
    Route::post('Paket/hapus_semua_itenerary', [AdminPaketController::class, 'hapusSemuaItenerary'])->name('admin.paket.hapusSemuaItenerary');
    Route::get('Paket/get_itenerary_hari/{encodedIDPaket}/{encodedIDTravel}/{HariKe}', [AdminPaketController::class, 'getIteneraryHari'])->name('admin.paket.getIteneraryHari');
    Route::post('Paket/import_itenerary/{encodedIDPaket}/{encodedIDTravel}', [AdminPaketController::class, 'importIteneraryExcel'])->name('admin.paket.importIteneraryExcel');
    Route::get('Paket/download_format_itenerary/{encodedIDPaket}/{encodedIDTravel}', [AdminPaketController::class, 'downloadIteneraryTemplate'])->name('admin.paket.downloadIteneraryTemplate');

    // Tagihan - Pembayaran (generate virtual account)
    Route::get('tagihan/Pembayaran', [PembayaranController::class, 'index'])->name('admin.tagihan.pembayaran.index');
    Route::get('tagihan/Pembayaran/generate_virtual_account', [PembayaranController::class, 'generateVirtualAccount'])->name('admin.tagihan.pembayaran.generate');

    // Tagihan - Terima Bayar
    Route::get('tagihan/Terimabayar', [TerimabayarController::class, 'index'])->name('admin.tagihan.terimabayar.index');
    Route::post('tagihan/Terimabayar/simpan', [TerimabayarController::class, 'simpan'])->name('admin.tagihan.terimabayar.simpan');
    Route::get('tagihan/Terimabayar/invoice', [TerimabayarController::class, 'invoice'])->name('admin.tagihan.terimabayar.invoice');
    Route::get('tagihan/Terimabayar/getpaket', [TerimabayarController::class, 'getpaket'])->name('admin.tagihan.terimabayar.getpaket');
    Route::get('tagihan/Terimabayar/gettagihan', [TerimabayarController::class, 'gettagihan'])->name('admin.tagihan.terimabayar.gettagihan');

    // Tagihan - Tagihan Travel
    Route::get('tagihan/Tagihantravel', [TagihanTravelController::class, 'index'])->name('admin.tagihan.tagihantravel.index');
    Route::get('tagihan/Tagihantravel/entrybayarjamaah', [TagihanTravelController::class, 'entryBayarJamaah'])->name('admin.tagihan.entrybayarjamaah');
    Route::get('tagihan/Tagihantravel/bayarjamaah', [TagihanTravelController::class, 'bayarJamaah'])->name('admin.tagihan.bayarjamaah');
    Route::post('tagihan/Tagihantravel/simpanpembayaranjamaah', [TagihanTravelController::class, 'simpanPembayaranJamaah'])->name('admin.tagihan.simpanpembayaranjamaah');
    Route::get('tagihan/Tagihantravel/cetak_kwitansi/{NoTransaksi}', [TagihanTravelController::class, 'cetakKwitansi'])->name('admin.tagihan.cetak_kwitansi');
    Route::post('tagihan/Tagihantravel/simpancetak', [TagihanTravelController::class, 'simpanCetak'])->name('admin.tagihan.simpancetak');
    Route::get('tagihan/Tagihantravel/invoice', [TagihanTravelController::class, 'invoice']);

    // Tagihan - Tagihan Travel AJAX endpoints
    Route::get('tagihan/Tagihantravel/getpaket', [TagihanTravelController::class, 'getPaket'])->name('admin.tagihan.tagihantravel.getpaket');
    Route::get('tagihan/Tagihantravel/getcabang', [TagihanTravelController::class, 'getCabang'])->name('admin.tagihan.tagihantravel.getcabang');
    Route::get('tagihan/Tagihantravel/gettagihan', [TagihanTravelController::class, 'getTagihan'])->name('admin.tagihan.tagihantravel.gettagihan');
    Route::get('tagihan/Tagihantravel/gettagihanjamaah', [TagihanTravelController::class, 'getTagihanJamaah'])->name('admin.tagihan.tagihantravel.gettagihanjamaah');

    
    //Daftarkan paket admin
    Route::get('paket-admin', [PaketAdminController::class, 'index'])->name('admin.paket-admin.index');
    Route::post('paket-admin/get', [PaketAdminController::class, 'get'])->name('admin.paket-admin.get');
    Route::get('paket-admin/getTravel', [PaketAdminController::class, 'getTravel'])->name('admin.paket-admin.getTravel');
    Route::get('paket-admin/getPaket', [PaketAdminController::class, 'getPaket'])->name('admin.paket-admin.getPaket');
    Route::get('paket-admin/getCabang', [PaketAdminController::class, 'getCabang'])->name('admin.paket-admin.getCabang');
    Route::get('paket-admin/getAgen', [PaketAdminController::class, 'getAgen'])->name('admin.paket-admin.getAgen');
    Route::post('paket-admin/simpan', [PaketAdminController::class, 'simpan'])->name('admin.paket-admin.simpan');


    // Monitoring
    Route::get('Monitoring', [MonitoringController::class, 'index'])->name('admin.monitoring.index');
    Route::get('Monitoring/show', [MonitoringController::class, 'show'])->name('admin.monitoring.show');
    Route::post('Monitoring/setpaid', [MonitoringController::class, 'setPaid'])->name('admin.monitoring.setPaid');
    Route::post('Monitoring/getJamaah', [MonitoringController::class, 'getJamaah'])->name('admin.monitoring.getJamaah');
    Route::post('Monitoring/get', [MonitoringController::class, 'get'])->name('admin.monitoring.get');

    // Logdata - Location Tracking Logs
    Route::get('logdata/get_log_data', [LogdataController::class, 'index'])->name('admin.logdata.get_log_data');
    Route::get('logdata/available_dates', [LogdataController::class, 'getAvailableDates'])->name('admin.logdata.available_dates');
    Route::get('logdata/statistics', [LogdataController::class, 'getStatistics'])->name('admin.logdata.statistics');

    // Pengeluaran Kas
    Route::get('Pengeluarankas', [PengeluarankasController::class, 'index'])->name('admin.pengeluarankas.index');
    Route::post('Pengeluarankas/simpan', [PengeluarankasController::class, 'simpan'])->name('admin.pengeluarankas.simpan');

    // Laporan
    Route::get('Laporan/paketperjalanan', [LaporanController::class, 'paketperjalanan'])->name('admin.laporan.paketperjalanan');
    Route::get('Laporan/pembayaran', [LaporanController::class, 'pembayaran'])->name('admin.laporan.pembayaran');
    Route::get('Laporan/pengeluarankas', [LaporanController::class, 'pengeluarankas'])->name('admin.laporan.pengeluarankas');
    Route::get('Laporan/aruskas', [LaporanController::class, 'aruskas'])->name('admin.laporan.aruskas');
    Route::get('Laporan/laporanhistoryjamaah', [LaporanController::class, 'laporanhistoryjamaah'])->name('admin.laporan.historyjamaah');
    Route::get('Laporan/laporanjamaah', [LaporanController::class, 'laporanjamaah'])->name('admin.laporan.jamaah');
    Route::get('Laporan/terimabayar', [LaporanController::class, 'terimabayar'])->name('admin.laporan.terimabayar');
    Route::get('Laporan/tagihanjamaah', [LaporanController::class, 'tagihanjamaah'])->name('admin.laporan.tagihanjamaah');
    Route::get('Laporan/dokumen', [LaporanController::class, 'dokumen'])->name('admin.laporan.dokumen');

    // Laporan AJAX / data endpoints
    Route::post('Laporan/get_paketperjalanan', [LaporanController::class, 'get_paketperjalanan'])->name('admin.laporan.get_paketperjalanan');
    Route::post('Laporan/get_pembayaran', [LaporanController::class, 'get_pembayaran'])->name('admin.laporan.get_pembayaran');
    Route::post('Laporan/get_pengeluarankas', [LaporanController::class, 'get_pengeluarankas'])->name('admin.laporan.get_pengeluarankas');
    Route::post('Laporan/get_aruskas', [LaporanController::class, 'get_aruskas'])->name('admin.laporan.get_aruskas');
    Route::post('Laporan/get_history_jamaah', [LaporanController::class, 'get_history_jamaah'])->name('admin.laporan.get_history_jamaah');
    Route::post('Laporan/get_jamaah', [LaporanController::class, 'get_jamaah'])->name('admin.laporan.get_jamaah');
    Route::post('Laporan/get_terimabayar', [LaporanController::class, 'get_terimabayar'])->name('admin.laporan.get_terimabayar');
    Route::post('Laporan/get_tagihan', [LaporanController::class, 'get_tagihan'])->name('admin.laporan.get_tagihan');
    Route::post('Laporan/get_dokumen', [LaporanController::class, 'get_dokumen'])->name('admin.laporan.get_dokumen');

    // Promosi
    Route::get('Promosi', [PromosiController::class, 'index'])->name('admin.promosi.index');
    Route::get('Promosi/tambah', [PromosiController::class, 'tambah'])->name('admin.promosi.tambah');
    Route::get('Promosi/edit', [PromosiController::class, 'edit'])->name('admin.promosi.edit');
    Route::get('Promosi/getpaket', [PromosiController::class, 'getpaket'])->name('admin.promosi.getpaket');
    Route::post('Promosi/simpan', [PromosiController::class, 'simpan'])->name('admin.promosi.simpan');
    Route::get('Promosi/verifikasi', [PromosiController::class, 'verifikasi'])->name('admin.promosi.verifikasi');
    Route::get('Promosi/hapus', [PromosiController::class, 'hapus'])->name('admin.promosi.hapus');
    Route::post('Promosi/get', [PromosiController::class, 'get'])->name('admin.promosi.get');
});


// Public webhook endpoints for Tagihan callbacks (protected by X_CALLBACK_TOKEN in controller)
Route::post('tagihan/Callback/terbayar', [CallbackController::class, 'terbayar'])->name('tagihan.callback.terbayar');
Route::post('tagihan/Callback/update', [CallbackController::class, 'update'])->name('tagihan.callback.update');
