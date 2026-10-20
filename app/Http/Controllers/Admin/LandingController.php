<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServerLog;
use App\Models\MstKonten;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Traits\DatatablesResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    use UploadFileTrait, DatatablesResponse;

    protected MstKonten $konten;
    protected ServerLog $serverlog;

    /**
     * File aset master/default yang dilindungi dari penghapusan fisik saat update/remove file.
     */
    protected array $protectedMasterFiles = [
        'image1.jpeg', 'image2.jpeg', 'image3.jpeg', 'image4.jpeg',
        'leadership.svg', 'social-justice.svg', 'check.svg',
        'suitcase.svg', 'map-book.svg', 'user-group.svg', 'family.svg',
        'default.png', 'haromain.png', 'Gbr1.png'
    ];

    public function __construct(MstKonten $konten, ServerLog $serverlog)
    {
        $this->konten = $konten;
        $this->serverlog = $serverlog;
    }

    /**
     * Override deleteFile untuk memastikan aset master dan file yang masih direferensikan baris lain tidak pernah terhapus dari filesystem.
     *
     * @param string $filePath
     * @param string|null $currentId IDKonten baris yang sedang diupdate (jika ada)
     * @return bool|string
     */
    protected function deleteFile(string $filePath, ?string $currentId = null)
    {
        $filename = basename($filePath);

        // 1. Whitelist Check: Jangan pernah hapus file master bawaan sistem
        if (in_array($filename, $this->protectedMasterFiles, true)) {
            return true;
        }

        // 2. Reference Check: Pastikan file tidak digunakan oleh baris lain di mstkonten
        $query = DB::table('mstkonten')->where('GambarKonten', $filename);
        if (!empty($currentId)) {
            $query->where('IDKonten', '!=', $currentId);
        }

        $isReferenced = $query->exists();
        if ($isReferenced) {
            return true; // Dilewati karena masih digunakan oleh record lain
        }

        // 3. Hapus file fisik jika aman
        $fullPath = public_path($filePath);

        if (file_exists($fullPath)) {
            @unlink($fullPath);
            return true;
        }
        return 'error';
    }

    /**
     * Mapping kategori konten landing agar mudah dipahami admin awam.
     */
    public static function getKategoriList(): array
    {
        return [
            'section-1' => 'Seksi 1: Pelayanan Lebih Baik',
            'section-2' => 'Seksi 2: Peran Haromain (Standar Pelayanan)',
            'section-3' => 'Seksi 3: Fitur Unggulan (Tahapan Perjalanan)',
            'section-4' => 'Seksi 4: Target Pengguna (4 Pilar Audiens)',
            'section-5' => 'Seksi 5: Video Simulasi Lapangan',
            'section-6' => 'Seksi 6: Mitra Travel Haromain',
            'global-setting' => 'Footer, Kontak & Pengaturan Global',
            'LANDING_MEMAHAMI_TENTANG' => 'Seksi: Memahami Tentang',
            'LANDING_PERAN_HAROMAIN' => 'Seksi: Peran Haromain (Header)',
            'LANDING_PERAN_HAROMAIN_LIST' => 'Seksi: Peran Haromain (Item List)',
            'LANDING_PERAN_HAROMAIN_FOOTER' => 'Seksi: Peran Haromain (Footer)',
            'LANDING_FITUR_UNGGULAN' => 'Seksi: Fitur Unggulan (Header)',
            'LANDING_FITUR_UNGGULAN_LIST' => 'Seksi: Fitur Unggulan (Item List)',
            'LANDING_SIAPA_YANG' => 'Seksi: Target Pengguna (Header)',
            'LANDING_SIAPA_YANG_LIST' => 'Seksi: Target Pengguna (Item List)',
            'LANDING_KONDISI_PENGGUNA' => 'Seksi: Video / Kondisi Pengguna',
            'LANDING_MITRA_TRAVEL_LIST' => 'Seksi: Mitra Travel (List)',
            'LANDING_TESTIMONIAL' => 'Seksi: Testimoni (Header)',
            'LANDING_TESTIMONIAL_LIST' => 'Seksi: Testimoni (Item List)',
            'LANDING_PERTANYAAN' => 'Seksi: FAQ (Header)',
            'LANDING_PERTANYAAN_LIST' => 'Seksi: FAQ (Item List)',
            'LANDING_ACTION_BUTTON' => 'Seksi: Tombol Konsultasi CTA',
            'LANDING_KATALOG_HEADER' => 'Katalog: Header',
            'LANDING_KATALOG_FOOTER' => 'Katalog: Footer',
            'LANDING_GALERI_HEADER' => 'Galeri: Header',
            'LANDING_GALERI_FOOTER' => 'Galeri: Footer',
            'LANDING_GLOBAL_SETTING' => 'Pengaturan Global (Kontak & Footer)'
        ];
    }

    public static function getFixedSections(): array
    {
        return [
            'section-1' => [
                'no' => 1,
                'name' => 'Section 1 (Pelayanan Lebih Baik)',
                'description' => 'Memahami Tentang - Pengantar pelayanan & pendampingan jamaah',
                'types' => ['LANDING_MEMAHAMI_TENTANG'],
                'slug' => 'section-1'
            ],
            'section-2' => [
                'no' => 2,
                'name' => 'Section 2 (Bukan Sekadar Aplikasi)',
                'description' => 'Peran Haromain - Header, 3 Kartu Nilai Tambah, & Penutup Section',
                'types' => ['LANDING_PERAN_HAROMAIN', 'LANDING_PERAN_HAROMAIN_LIST', 'LANDING_PERAN_HAROMAIN_FOOTER'],
                'slug' => 'section-2'
            ],
            'section-3' => [
                'no' => 3,
                'name' => 'Section 3 (Fitur Unggulan Tahap A, B, C)',
                'description' => 'Satu Aplikasi untuk Setiap Tahapan Perjalanan (Sebelum, Saat, Sesudah)',
                'types' => ['LANDING_FITUR_UNGGULAN', 'LANDING_FITUR_UNGGULAN_LIST'],
                'slug' => 'section-3'
            ],
            'section-4' => [
                'no' => 4,
                'name' => 'Section 4 (Satu Sistem, Manfaat untuk Semua)',
                'description' => 'Target Pengguna - Header & 4 Pilar (Travel, Mutowif, Jamaah, Keluarga)',
                'types' => ['LANDING_SIAPA_YANG', 'LANDING_SIAPA_YANG_LIST'],
                'slug' => 'section-4'
            ],
            'section-5' => [
                'no' => 5,
                'name' => 'Section 5 (Video Simulasi Terpisah)',
                'description' => 'Kondisi Pengguna - Narasi & Tautan Video Simulasi Lapangan',
                'types' => ['LANDING_KONDISI_PENGGUNA'],
                'slug' => 'section-5'
            ],
            'section-6' => [
                'no' => 6,
                'name' => 'Section 6 (Mitra Travel Haromain)',
                'description' => 'Logo dan profil Mitra Travel yang bekerjasama dengan Haromain',
                'types' => ['LANDING_MITRA_TRAVEL_LIST'],
                'slug' => 'section-6'
            ],
            'section-7' => [
                'no' => 7,
                'name' => 'Section 7 (Banner Ajakan / CTA Penutup)',
                'description' => 'Judul ajakan kerja sama, deskripsi penutup, dan tombol WhatsApp CTA',
                'types' => ['LANDING_CARA_MEMULAI', 'LANDING_ACTION_BUTTON'],
                'slug' => 'section-7'
            ],
            'footer' => [
                'no' => 8,
                'name' => 'Footer & Kontak Global',
                'description' => 'WhatsApp Konsultasi, Email, Akun Medsos, & Tautan Aplikasi',
                'types' => ['LANDING_GLOBAL_SETTING'],
                'slug' => 'footer'
            ],
        ];
    }

    public function index()
    {
        $sectionsDef = self::getFixedSections();
        $sectionsData = [];

        foreach ($sectionsDef as $slug => $def) {
            $rows = MstKonten::whereIn('JenisKonten', $def['types'])->get();

            $preview = '-';
            $mainRow = $rows->first();

            if ($slug === 'section-1' && $mainRow) {
                $preview = $mainRow->JudulKonten;
            } elseif ($slug === 'section-2') {
                $header = $rows->where('JenisKonten', 'LANDING_PERAN_HAROMAIN')->first();
                $cardsCount = $rows->where('JenisKonten', 'LANDING_PERAN_HAROMAIN_LIST')->count();
                $preview = ($header ? $header->JudulKonten : 'Peran Haromain') . " ({$cardsCount} Nilai Tambah)";
            } elseif ($slug === 'section-3') {
                $header = $rows->where('JenisKonten', 'LANDING_FITUR_UNGGULAN')->first();
                $preview = ($header ? $header->JudulKonten : 'Fitur Unggulan') . ' (3 Tahapan: A, B, C)';
            } elseif ($slug === 'section-4') {
                $header = $rows->where('JenisKonten', 'LANDING_SIAPA_YANG')->first();
                $preview = ($header ? $header->JudulKonten : 'Target Pengguna') . ' (4 Pilar: Travel, Mutowif, Jamaah, Keluarga)';
            } elseif ($slug === 'section-5' && $mainRow) {
                $preview = $mainRow->JudulKonten . ' (' . ($mainRow->GambarKonten ? 'Video Terpasang' : 'Belum Ada Video') . ')';
            } elseif ($slug === 'section-6') {
                $count = $rows->count();
                $preview = "{$count} Mitra Travel Terdaftar";
            } elseif ($slug === 'section-7') {
                $ctaHeader = $rows->where('JenisKonten', 'LANDING_CARA_MEMULAI')->first();
                $preview = $ctaHeader ? $ctaHeader->JudulKonten : 'Tingkatkan Pelayanan, Tumbuhkan Kepercayaan Jamaah';
            } elseif ($slug === 'footer') {
                $setting = app(\App\Services\Landing\BerandaService::class)->getGlobalSetting();
                $preview = 'WhatsApp: ' . ($setting['WhatsAppText'] ?? $setting['WhatsApp'] ?? '-') . ' | ' . ($setting['Email'] ?? '-');
            }

            $sectionsData[] = [
                'no' => $def['no'],
                'slug' => $slug,
                'name' => $def['name'],
                'description' => $def['description'],
                'preview' => $preview,
                'edit_url' => url("admin/landing/edit/{$slug}"),
            ];
        }

        return view('admin-page.landing.index', [
            'menu' => 'Manajemen Landing',
            'sections' => $sectionsData,
        ]);
    }

    /**
     * Tampilan Edit Agregat per-Section atau Single Item
     * Mendukung URL: admin/landing/edit/section-2 atau admin/landing/edit?id=...
     */
    public function edit(Request $request, $section = null)
    {
        $target = $section ?: $request->query('section') ?: $request->query('id');

        // Normalisasi slug
        $slug = strtolower(trim($target ?? ''));

        // =========================================================================
        // AGGREGATE 1: SECTION 1 (Pelayanan Lebih Baik)
        // =========================================================================
        if (in_array($slug, ['section-1', 'section1', 'landing_memahami_tentang'])) {
            $data = MstKonten::where('JenisKonten', 'LANDING_MEMAHAMI_TENTANG')->first();
            return view('admin-page.landing.edit-section', [
                'menu' => 'Manajemen Landing',
                'section' => 'section-1',
                'title' => 'Seksi 1: Pelayanan Lebih Baik',
                'data' => $data,
            ]);
        }

        // =========================================================================
        // AGGREGATE 2: SECTION 2 (Peran Haromain: Header, 3 Kartu, Footer)
        // =========================================================================
        if (in_array($slug, ['section-2', 'section2', 'landing_peran_haromain', 'landing_peran_haromain_list', 'landing_peran_haromain_footer'])) {
            $header = MstKonten::where('JenisKonten', 'LANDING_PERAN_HAROMAIN')->first();
            $list = MstKonten::where('JenisKonten', 'LANDING_PERAN_HAROMAIN_LIST')
                ->where('IsAktif', 1)
                ->orderBy('TanggalKonten', 'ASC')
                ->get();
            $footer = MstKonten::where('JenisKonten', 'LANDING_PERAN_HAROMAIN_FOOTER')->first();

            return view('admin-page.landing.edit-section', [
                'menu' => 'Manajemen Landing',
                'section' => 'section-2',
                'title' => 'Seksi 2: Peran Haromain (Standar Pelayanan)',
                'header' => $header,
                'list' => $list,
                'footer' => $footer,
            ]);
        }

        // =========================================================================
        // AGGREGATE 3: SECTION 3 (Fitur Unggulan: Header, 3 Tahapan A/B/C)
        // =========================================================================
        if (in_array($slug, ['section-3', 'section3', 'landing_fitur_unggulan', 'landing_fitur_unggulan_list'])) {
            $header = MstKonten::where('JenisKonten', 'LANDING_FITUR_UNGGULAN')->first();
            $list = MstKonten::where('JenisKonten', 'LANDING_FITUR_UNGGULAN_LIST')
                ->orderBy('NoUrut', 'ASC')
                ->orderBy('TanggalKonten', 'ASC')
                ->get();

            return view('admin-page.landing.edit-section', [
                'menu' => 'Manajemen Landing',
                'section' => 'section-3',
                'title' => 'Seksi 3: Fitur Unggulan (Tahapan Perjalanan)',
                'header' => $header,
                'list' => $list,
            ]);
        }

        // =========================================================================
        // AGGREGATE 4: SECTION 4 (Target Pengguna: Header, 4 Audiens)
        // =========================================================================
        if (in_array($slug, ['section-4', 'section4', 'landing_siapa_yang', 'landing_siapa_yang_list'])) {
            $header = MstKonten::where('JenisKonten', 'LANDING_SIAPA_YANG')->first();
            $list = MstKonten::where('JenisKonten', 'LANDING_SIAPA_YANG_LIST')
                ->orderBy('TanggalKonten', 'ASC')
                ->get();

            return view('admin-page.landing.edit-section', [
                'menu' => 'Manajemen Landing',
                'section' => 'section-4',
                'title' => 'Seksi 4: Target Pengguna (4 Pilar Audiens)',
                'header' => $header,
                'list' => $list,
            ]);
        }

        // =========================================================================
        // AGGREGATE 5: SECTION 5 (Video Simulasi Lapangan)
        // =========================================================================
        if (in_array($slug, ['section-5', 'section5', 'landing_kondisi_pengguna'])) {
            $data = MstKonten::where('JenisKonten', 'LANDING_KONDISI_PENGGUNA')->first();
            return view('admin-page.landing.edit-section', [
                'menu' => 'Manajemen Landing',
                'section' => 'section-5',
                'title' => 'Seksi 5: Video Simulasi Lapangan',
                'data' => $data,
            ]);
        }

        // =========================================================================
        // AGGREGATE 6: SECTION 6 (Mitra Travel Haromain)
        // =========================================================================
        if (in_array($slug, ['section-6', 'section6', 'landing_mitra_travel', 'landing_mitra_travel_list'])) {
            $header = MstKonten::where('JenisKonten', 'LANDING_MITRA_TRAVEL')->first();
            $list = MstKonten::where('JenisKonten', 'LANDING_MITRA_TRAVEL_LIST')
                ->orderBy('TanggalKonten', 'ASC')
                ->get();

            return view('admin-page.landing.edit-section', [
                'menu' => 'Manajemen Landing',
                'section' => 'section-6',
                'title' => 'Seksi 6: Mitra Travel Haromain',
                'header' => $header,
                'list' => $list,
            ]);
        }

        // =========================================================================
        // AGGREGATE 7: SECTION 7 (Final CTA Banner)
        // =========================================================================
        if (in_array($slug, ['section-7', 'section7', 'landing_cara_memulai', 'landing_action_button'])) {
            $ctaHeader = MstKonten::where('JenisKonten', 'LANDING_CARA_MEMULAI')->first();
            $actionBtn = MstKonten::where('JenisKonten', 'LANDING_ACTION_BUTTON')->first();
            return view('admin-page.landing.edit-section', [
                'menu' => 'Manajemen Landing',
                'section' => 'section-7',
                'title' => 'Seksi 7: Banner Ajakan / CTA Penutup',
                'ctaHeader' => $ctaHeader,
                'actionBtn' => $actionBtn,
            ]);
        }

        // =========================================================================
        // AGGREGATE 8: GLOBAL SETTING (Footer & Kontak)
        // =========================================================================
        if (in_array($slug, ['footer', 'global-setting', 'global_setting', 'landing_global_setting'])) {
            $row = MstKonten::where('JenisKonten', 'LANDING_GLOBAL_SETTING')->first();
            $setting = [];
            if ($row && !empty($row->IsiKonten)) {
                $setting = json_decode($row->IsiKonten, true) ?? [];
            }

            return view('admin-page.landing.edit-section', [
                'menu' => 'Manajemen Landing',
                'section' => 'global-setting',
                'title' => 'Footer, Kontak & Pengaturan Global',
                'setting' => $setting,
                'data' => $row,
            ]);
        }

        // =========================================================================
        // FALLBACK: Single item edit by IDKonten
        // =========================================================================
        $konten = MstKonten::where('JenisKonten', 'like', 'LANDING_%')
            ->where(function ($q) use ($target) {
                $q->where('IDKonten', $target)
                  ->orWhere('JenisKonten', $target);
            })
            ->first();

        if ($konten) {
            // Arahkan ke form agregat sesuai jenis kontennya agar tidak terpecah
            $jenis = $konten->JenisKonten;
            if (str_starts_with($jenis, 'LANDING_PERAN_HAROMAIN')) {
                return redirect()->route('admin.landing.edit', ['section' => 'section-2']);
            }
            if (str_starts_with($jenis, 'LANDING_FITUR_UNGGULAN')) {
                return redirect()->route('admin.landing.edit', ['section' => 'section-3']);
            }
            if (str_starts_with($jenis, 'LANDING_SIAPA_YANG')) {
                return redirect()->route('admin.landing.edit', ['section' => 'section-4']);
            }
            if ($jenis === 'LANDING_MEMAHAMI_TENTANG') {
                return redirect()->route('admin.landing.edit', ['section' => 'section-1']);
            }
            if ($jenis === 'LANDING_KONDISI_PENGGUNA') {
                return redirect()->route('admin.landing.edit', ['section' => 'section-5']);
            }
            if ($jenis === 'LANDING_MITRA_TRAVEL_LIST') {
                return redirect()->route('admin.landing.edit', ['section' => 'section-6']);
            }
            if ($jenis === 'LANDING_GLOBAL_SETTING') {
                return redirect()->route('admin.landing.edit', ['section' => 'global-setting']);
            }

            // Tampilkan single edit view jika di luar seksi 1-7 (misal Testimoni/FAQ)
            return view('admin-page.landing.edit', [
                'menu' => 'Manajemen Landing',
                'data' => $konten,
                'kategori_list' => self::getKategoriList()
            ]);
        }

        abort(404, 'Seksi landing page tidak ditemukan.');
    }

    /**
     * Simpan Data Agregat per-Section Menggunakan Transaksi Atomic
     */
    public function simpan(Request $request)
    {
        $section = $request->input('section');

        // Jika single-item edit lama
        if (empty($section) && $request->filled('id')) {
            return $this->simpanSingle($request);
        }

        try {
            DB::beginTransaction();

            switch ($section) {
                case 'section-1':
                    $this->simpanSection1($request);
                    break;
                case 'section-2':
                    $this->simpanSection2($request);
                    break;
                case 'section-3':
                    $this->simpanSection3($request);
                    break;
                case 'section-4':
                    $this->simpanSection4($request);
                    break;
                case 'section-5':
                    $this->simpanSection5($request);
                    break;
                case 'section-6':
                    $this->simpanSection6($request);
                    break;
                case 'section-7':
                    $this->simpanSection7($request);
                    break;
                case 'footer':
                case 'global-setting':
                    $this->simpanGlobalSetting($request);
                    break;
                default:
                    return response()->json([
                        'status' => false,
                        'message' => 'Identifikasi seksi tidak valid!'
                    ], 422);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil memperbarui data seksi secara lengkap!',
                'redirect' => route('admin.landing.index')
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan perubahan: ' . $th->getMessage()
            ], 500);
        }
    }

    protected function simpanSection1(Request $request)
    {
        if (empty(trim($request->input('JudulKonten', '')))) {
            throw new \InvalidArgumentException('Judul Konten pada Seksi 1 tidak boleh kosong!');
        }

        $target = MstKonten::where('JenisKonten', 'LANDING_MEMAHAMI_TENTANG')->lockForUpdate()->first();
        $data = [
            'JudulKonten' => $request->JudulKonten ?? '-',
            'IsiKonten' => $request->IsiKonten,
            'Author' => Auth::user()->NamaUser ?? Auth::user()->name ?? 'Admin',
        ];

        if ($request->hasFile('GambarKonten')) {
            $upload = $this->doUpload($request, 'GambarKonten', 'assets/konten', ['jpg', 'jpeg', 'png', 'webp', 'svg']);
            if ($upload !== 'error') {
                $data['GambarKonten'] = $upload;
                if ($target && !empty($target->GambarKonten) && !filter_var($target->GambarKonten, FILTER_VALIDATE_URL)) {
                    $this->deleteFile('assets/konten/' . $target->GambarKonten, $target->IDKonten);
                }
            }
        }

        if ($target) {
            $target->update($data);
            $this->serverlog->insertLog($target->IDKonten, 'Edit', 'Landing Seksi 1');
        }
    }

    protected function simpanSection2(Request $request)
    {
        if (empty(trim($request->input('header_JudulKonten', '')))) {
            throw new \InvalidArgumentException('Judul Utama Seksi 2 tidak boleh kosong!');
        }

        // 1. Header
        if ($request->filled('header_id')) {
            MstKonten::where('IDKonten', $request->header_id)
                ->where('JenisKonten', 'LANDING_PERAN_HAROMAIN')
                ->lockForUpdate()
                ->update([
                    'JudulKonten' => $request->header_JudulKonten ?? '-',
                    'IsiKonten' => $request->header_IsiKonten,
                ]);
        }

        // 2. 3 Kartu List
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $id => $val) {
                if (empty(trim($val['JudulKonten'] ?? ''))) {
                    throw new \InvalidArgumentException('Judul Kartu pada Seksi 2 tidak boleh kosong!');
                }

                $item = MstKonten::where('IDKonten', $id)
                    ->where('JenisKonten', 'LANDING_PERAN_HAROMAIN_LIST')
                    ->lockForUpdate()
                    ->first();

                if ($item) {
                    $update = [
                        'JudulKonten' => $val['JudulKonten'] ?? $item->JudulKonten,
                        'IsiKonten' => $val['IsiKonten'] ?? $item->IsiKonten,
                    ];

                    if ($request->hasFile("items.$id.GambarKonten")) {
                        $file = $request->file("items.$id.GambarKonten");
                        if ($file->isValid() && in_array(strtolower($file->extension()), ['jpg', 'jpeg', 'png', 'webp', 'svg'])) {
                            $filename = time() . '_' . uniqid() . '.' . $file->extension();
                            $file->move(public_path('assets/konten'), $filename);
                            $update['GambarKonten'] = $filename;
                            if (!empty($item->GambarKonten) && !filter_var($item->GambarKonten, FILTER_VALIDATE_URL)) {
                                $this->deleteFile('assets/konten/' . $item->GambarKonten, $item->IDKonten);
                            }
                        }
                    }

                    $item->update($update);
                }
            }
        }

        // 3. Footer
        if ($request->filled('footer_id')) {
            MstKonten::where('IDKonten', $request->footer_id)
                ->where('JenisKonten', 'LANDING_PERAN_HAROMAIN_FOOTER')
                ->lockForUpdate()
                ->update([
                    'IsiKonten' => $request->footer_IsiKonten,
                ]);
        }

        $this->serverlog->insertLog('SECTION-2', 'Edit', 'Landing Seksi 2 Agregat');
    }

    protected function simpanSection3(Request $request)
    {
        if (empty(trim($request->input('header_JudulKonten', '')))) {
            throw new \InvalidArgumentException('Judul Utama Seksi 3 tidak boleh kosong!');
        }

        // 1. Header
        if ($request->filled('header_id')) {
            MstKonten::where('IDKonten', $request->header_id)
                ->where('JenisKonten', 'LANDING_FITUR_UNGGULAN')
                ->lockForUpdate()
                ->update([
                    'JudulKonten' => $request->header_JudulKonten ?? '-',
                    'IsiKonten' => $request->header_IsiKonten,
                ]);
        }

        // 2. Tahapan A/B/C List
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $id => $val) {
                if (empty(trim($val['JudulKonten'] ?? ''))) {
                    throw new \InvalidArgumentException('Judul Tahapan pada Seksi 3 tidak boleh kosong!');
                }

                $item = MstKonten::where('IDKonten', $id)
                    ->where('JenisKonten', 'LANDING_FITUR_UNGGULAN_LIST')
                    ->lockForUpdate()
                    ->first();

                if ($item) {
                    $update = [
                        'JudulKonten' => $val['JudulKonten'] ?? $item->JudulKonten,
                        'IsiKonten' => $val['IsiKonten'] ?? $item->IsiKonten,
                    ];

                    if ($request->hasFile("items.$id.GambarKonten")) {
                        $file = $request->file("items.$id.GambarKonten");
                        if ($file->isValid() && in_array(strtolower($file->extension()), ['jpg', 'jpeg', 'png', 'webp', 'svg'])) {
                            $filename = time() . '_' . uniqid() . '.' . $file->extension();
                            $file->move(public_path('assets/konten'), $filename);
                            $update['GambarKonten'] = $filename;
                            if (!empty($item->GambarKonten) && !filter_var($item->GambarKonten, FILTER_VALIDATE_URL)) {
                                $this->deleteFile('assets/konten/' . $item->GambarKonten, $item->IDKonten);
                            }
                        }
                    }

                    $item->update($update);
                }
            }
        }

        $this->serverlog->insertLog('SECTION-3', 'Edit', 'Landing Seksi 3 Agregat');
    }

    protected function simpanSection4(Request $request)
    {
        if (empty(trim($request->input('header_JudulKonten', '')))) {
            throw new \InvalidArgumentException('Judul Utama Seksi 4 tidak boleh kosong!');
        }

        // 1. Header
        if ($request->filled('header_id')) {
            MstKonten::where('IDKonten', $request->header_id)
                ->where('JenisKonten', 'LANDING_SIAPA_YANG')
                ->lockForUpdate()
                ->update([
                    'JudulKonten' => $request->header_JudulKonten ?? '-',
                    'IsiKonten' => $request->header_IsiKonten,
                ]);
        }

        // 2. 4 Pilar Audiens List
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $id => $val) {
                if (empty(trim($val['JudulKonten'] ?? ''))) {
                    throw new \InvalidArgumentException('Judul Pilar Audiens pada Seksi 4 tidak boleh kosong!');
                }

                $item = MstKonten::where('IDKonten', $id)
                    ->where('JenisKonten', 'LANDING_SIAPA_YANG_LIST')
                    ->lockForUpdate()
                    ->first();

                if ($item) {
                    $update = [
                        'JudulKonten' => $val['JudulKonten'] ?? $item->JudulKonten,
                        'IsiKonten' => $val['IsiKonten'] ?? $item->IsiKonten,
                    ];

                    if ($request->hasFile("items.$id.GambarKonten")) {
                        $file = $request->file("items.$id.GambarKonten");
                        if ($file->isValid() && in_array(strtolower($file->extension()), ['jpg', 'jpeg', 'png', 'webp', 'svg'])) {
                            $filename = time() . '_' . uniqid() . '.' . $file->extension();
                            $file->move(public_path('assets/konten'), $filename);
                            $update['GambarKonten'] = $filename;
                            if (!empty($item->GambarKonten) && !filter_var($item->GambarKonten, FILTER_VALIDATE_URL)) {
                                $this->deleteFile('assets/konten/' . $item->GambarKonten, $item->IDKonten);
                            }
                        }
                    }

                    $item->update($update);
                }
            }
        }

        $this->serverlog->insertLog('SECTION-4', 'Edit', 'Landing Seksi 4 Agregat');
    }

    protected function simpanSection5(Request $request)
    {
        if (empty(trim($request->input('JudulKonten', '')))) {
            throw new \InvalidArgumentException('Judul Konten pada Seksi 5 tidak boleh kosong!');
        }

        $target = MstKonten::where('JenisKonten', 'LANDING_KONDISI_PENGGUNA')->lockForUpdate()->first();
        
        // Logika prioritas yang jelas antara VideoUrl (YouTube) dan GambarUpload (file lokal)
        $mediaValue = $target ? $target->GambarKonten : null;

        if ($request->hasFile('GambarUpload')) {
            // Prioritas tertinggi: File lokal baru yang diupload
            $upload = $this->doUpload($request, 'GambarUpload', 'assets/konten', ['jpg', 'jpeg', 'png', 'webp', 'svg', 'mp4']);
            if ($upload !== 'error') {
                $mediaValue = $upload;
                if ($target && !empty($target->GambarKonten) && !filter_var($target->GambarKonten, FILTER_VALIDATE_URL)) {
                    $this->deleteFile('assets/konten/' . $target->GambarKonten, $target->IDKonten);
                }
            }
        } elseif ($request->filled('VideoUrl')) {
            // Jika ada input VideoUrl baru/eksplisit dan tidak ada upload baru
            $mediaValue = trim($request->VideoUrl);
        } elseif ($request->filled('GambarKonten')) {
            // Fallback ke media eksisting yang dikirim form
            $mediaValue = trim($request->GambarKonten);
        }

        $data = [
            'JudulKonten' => $request->JudulKonten ?? '-',
            'IsiKonten' => $request->IsiKonten,
            'GambarKonten' => $mediaValue,
            'Author' => Auth::user()->NamaUser ?? Auth::user()->name ?? 'Admin',
        ];

        if ($target) {
            $target->update($data);
            $this->serverlog->insertLog($target->IDKonten, 'Edit', 'Landing Seksi 5');
        }
    }

    protected function simpanSection6(Request $request)
    {
        // 1. Simpan data judul dan deskripsi seksi ke JenisKonten 'LANDING_MITRA_TRAVEL'
        if ($request->filled('header_JudulKonten')) {
            MstKonten::updateOrCreate(
                ['JenisKonten' => 'LANDING_MITRA_TRAVEL'],
                [
                    'JudulKonten'   => $request->header_JudulKonten,
                    'IsiKonten'     => $request->header_IsiKonten,
                    'TanggalKonten' => now()->format('Y-m-d'),
                    'IsAktif'       => 1,
                    'Author'        => Auth::user()->name ?? 'Admin',
                ]
            );
        }

        // 2. Handle deleted_ids
        if ($request->filled('deleted_ids')) {
            $delIds = is_array($request->deleted_ids)
                ? $request->deleted_ids
                : explode(',', $request->deleted_ids);

            $delIds = array_filter(array_map('trim', $delIds));

            if (!empty($delIds)) {
                $deletedItems = MstKonten::where('JenisKonten', 'LANDING_MITRA_TRAVEL_LIST')
                    ->whereIn('IDKonten', $delIds)
                    ->get();

                foreach ($deletedItems as $delItem) {
                    if (!empty($delItem->GambarKonten) && !filter_var($delItem->GambarKonten, FILTER_VALIDATE_URL)) {
                        $this->deleteFile('assets/konten/' . $delItem->GambarKonten, $delItem->IDKonten);
                    }
                    $delItem->delete();
                }
            }
        }

        // 2. Handle items (update eksisting & insert new_)
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $id => $val) {
                if (empty(trim($val['JudulKonten'] ?? ''))) {
                    throw new \InvalidArgumentException('Nama Mitra Travel tidak boleh kosong!');
                }

                if (str_starts_with((string)$id, 'new_') || empty($id)) {
                    // Create record baru
                    $newId = $this->konten->generateID();
                    $filename = null;

                    if ($request->hasFile("items.$id.GambarKonten")) {
                        $file = $request->file("items.$id.GambarKonten");
                        if ($file->getSize() > 2048 * 1024) {
                            throw new \InvalidArgumentException('Ukuran file logo travel terlalu besar! Maksimal 2MB.');
                        }
                        if ($file->isValid() && in_array(strtolower($file->extension()), ['jpg', 'jpeg', 'png', 'webp', 'svg'])) {
                            $filename = time() . '_' . uniqid() . '.' . $file->extension();
                            $file->move(public_path('assets/konten'), $filename);
                            $this->autoCropLogoWhitespace(public_path('assets/konten/' . $filename));
                        }
                    }

                    MstKonten::create([
                        'IDKonten' => $newId,
                        'JudulKonten' => trim($val['JudulKonten']),
                        'GambarKonten' => $filename,
                        'JenisKonten' => 'LANDING_MITRA_TRAVEL_LIST',
                        'TanggalKonten' => now()->format('Y-m-d'),
                        'IsAktif' => 1,
                        'Author' => Auth::user()->name ?? 'Admin',
                    ]);
                } else {
                    // Update record eksisting
                    $item = MstKonten::where('IDKonten', $id)
                        ->where('JenisKonten', 'LANDING_MITRA_TRAVEL_LIST')
                        ->lockForUpdate()
                        ->first();

                    if ($item) {
                        $update = [
                            'JudulKonten' => $val['JudulKonten'] ?? $item->JudulKonten,
                        ];

                        if ($request->hasFile("items.$id.GambarKonten")) {
                            $file = $request->file("items.$id.GambarKonten");
                            if ($file->getSize() > 2048 * 1024) {
                                throw new \InvalidArgumentException('Ukuran file logo travel terlalu besar! Maksimal 2MB.');
                            }
                            if ($file->isValid() && in_array(strtolower($file->extension()), ['jpg', 'jpeg', 'png', 'webp', 'svg'])) {
                                $filename = time() . '_' . uniqid() . '.' . $file->extension();
                                $file->move(public_path('assets/konten'), $filename);
                                $this->autoCropLogoWhitespace(public_path('assets/konten/' . $filename));
                                $update['GambarKonten'] = $filename;
                                if (!empty($item->GambarKonten) && !filter_var($item->GambarKonten, FILTER_VALIDATE_URL)) {
                                    $this->deleteFile('assets/konten/' . $item->GambarKonten, $item->IDKonten);
                                }
                            }
                        }

                        $item->update($update);
                    }
                }
            }
        }

        $this->serverlog->insertLog('SECTION-6', 'Edit', 'Landing Seksi 6');
    }

    protected function simpanSection7(Request $request)
    {
        // 1. Simpan Judul & Deskripsi Banner CTA ke LANDING_CARA_MEMULAI
        $ctaHeader = MstKonten::where('JenisKonten', 'LANDING_CARA_MEMULAI')->lockForUpdate()->first();
        if ($ctaHeader) {
            $ctaHeader->update([
                'JudulKonten' => $request->cta_judul ?? 'Tingkatkan Pelayanan, Tumbuhkan Kepercayaan Jamaah',
                'IsiKonten'   => $request->cta_deskripsi ?? '',
                'TanggalKonten' => now()->format('Y-m-d'),
            ]);
        } else {
            MstKonten::create([
                'IDKonten'      => $this->konten->generateID(),
                'JudulKonten'   => $request->cta_judul ?? 'Tingkatkan Pelayanan, Tumbuhkan Kepercayaan Jamaah',
                'IsiKonten'     => $request->cta_deskripsi ?? '',
                'JenisKonten'   => 'LANDING_CARA_MEMULAI',
                'TanggalKonten' => now()->format('Y-m-d'),
                'IsAktif'       => 1,
                'Author'        => Auth::user()->name ?? 'Admin',
            ]);
        }
        // 2. Simpan Label Tombol & Link WA ke LANDING_ACTION_BUTTON
        $actionBtn = MstKonten::where('JenisKonten', 'LANDING_ACTION_BUTTON')->lockForUpdate()->first();
        if ($actionBtn) {
            $actionBtn->update([
                'JudulKonten'   => $request->btn_label ?? 'Diskusikan Kerja Sama Haromain',
                'IsiKonten'     => $request->btn_url ?? 'https://wa.me/6287777235337',
                'TanggalKonten' => now()->format('Y-m-d'),
            ]);
        } else {
            MstKonten::create([
                'IDKonten'      => $this->konten->generateID(),
                'JudulKonten'   => $request->btn_label ?? 'Diskusikan Kerja Sama Haromain',
                'IsiKonten'     => $request->btn_url ?? 'https://wa.me/6287777235337',
                'JenisKonten'   => 'LANDING_ACTION_BUTTON',
                'TanggalKonten' => now()->format('Y-m-d'),
                'IsAktif'       => 1,
                'Author'        => Auth::user()->name ?? 'Admin',
            ]);
        }
        $this->serverlog->insertLog('SECTION-7', 'Edit', 'Landing Seksi 7 CTA');
    }

    protected function simpanGlobalSetting(Request $request)
    {
        $settingData = [
            'whatsapp' => $request->whatsapp,
            'email' => $request->email,
            'instagram' => $request->instagram,
            'facebook' => $request->facebook,
            'tiktok' => $request->tiktok,
            'appstore' => $request->appstore,
            'playstore' => $request->playstore,
            'description' => $request->description,
        ];

        $json = json_encode($settingData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $row = MstKonten::where('JenisKonten', 'LANDING_GLOBAL_SETTING')->lockForUpdate()->first();
        if ($row) {
            $row->update([
                'IsiKonten' => $json,
                'TanggalKonten' => now()->format('Y-m-d'),
            ]);
            $this->serverlog->insertLog($row->IDKonten, 'Edit', 'Landing Global Setting');
        } else {
            $id = $this->konten->generateID();
            MstKonten::create([
                'IDKonten' => $id,
                'JudulKonten' => 'Global Landing Setting',
                'IsiKonten' => $json,
                'JenisKonten' => 'LANDING_GLOBAL_SETTING',
                'TanggalKonten' => now()->format('Y-m-d'),
                'IsAktif' => 1,
                'Author' => Auth::user()->name ?? 'Admin',
            ]);
            $this->serverlog->insertLog($id, 'Tambah', 'Landing Global Setting');
        }
    }

    /**
     * Fallback untuk single-item edit
     */
    protected function simpanSingle(Request $request)
    {
        $id = $request->input('id');
        $target = MstKonten::where('IDKonten', $id)
            ->where('JenisKonten', 'like', 'LANDING_%')
            ->lockForUpdate()
            ->first();

        if (!$target) {
            return response()->json([
                'status' => false,
                'message' => 'Konten tidak ditemukan atau dilindungi dari modifikasi.'
            ], 403);
        }

        $data = [
            'JudulKonten' => $request->JudulKonten ?? '-',
            'IsiKonten'   => $request->IsiKonten,
            'NoUrut'      => $request->filled('NoUrut') ? (int)$request->NoUrut : null,
            'Author'      => Auth::user()->NamaUser ?? Auth::user()->name ?? 'Admin',
        ];

        if ($target->JenisKonten === 'LANDING_KONDISI_PENGGUNA' && $request->filled('VideoUrl')) {
            $data['GambarKonten'] = $request->VideoUrl;
        }

        if ($request->hasFile('GambarKonten')) {
            $upload = $this->doUpload(
                $request,
                'GambarKonten',
                'assets/konten',
                ['jpg', 'jpeg', 'png', 'webp', 'svg']
            );

            if ($upload === 'error') {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengupload gambar!'
                ], 422);
            }

            $data['GambarKonten'] = $upload;

            if (!empty($target->GambarKonten) && !filter_var($target->GambarKonten, FILTER_VALIDATE_URL)) {
                $this->deleteFile('assets/konten/' . $target->GambarKonten);
            }
        }

        if ((int)$request->is_remove === 1) {
            if (!empty($target->GambarKonten) && !filter_var($target->GambarKonten, FILTER_VALIDATE_URL)) {
                $this->deleteFile('assets/konten/' . $target->GambarKonten);
            }
            $data['GambarKonten'] = null;
        }

        DB::transaction(function () use ($id, $data) {
            MstKonten::where('IDKonten', $id)->update($data);
            $this->serverlog->insertLog($id, 'Edit', 'Landing Page');
        });

        return response()->json([
            'status' => true,
            'message' => 'Berhasil memperbarui konten landing page.',
            'redirect' => route('admin.landing.index')
        ]);
    }

    public function setaktif(Request $request)
    {
        $id = $request->query('id');
        $isAktif = (int)$request->query('isaktif') === 1 ? 0 : 1;

        $konten = MstKonten::where('IDKonten', $id)
            ->where('JenisKonten', 'like', 'LANDING_%')
            ->first();

        if (!$konten) {
            return response()->json([
                'status' => false,
                'message' => 'Konten tidak ditemukan atau dilindungi.'
            ], 404);
        }

        try {
            MstKonten::where('IDKonten', $id)
                ->where('JenisKonten', 'like', 'LANDING_%')
                ->update(['IsAktif' => $isAktif]);

            $statusText = $isAktif === 1 ? 'Aktifkan' : 'Nonaktifkan';
            $this->serverlog->insertLog($id, $statusText, 'Landing Page');

            return response()->json([
                'status' => true,
                'message' => 'Status berhasil diubah.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function get(Request $request)
    {
        // STRICT SCOPE LOCKING
        $query = DB::table('mstkonten')
            ->where('JenisKonten', 'like', 'LANDING_%');

        // Filter Jenis / Kategori Konten
        $jenisFilter = $request->input('JenisKonten');
        if (!empty($jenisFilter)) {
            $query->where('JenisKonten', $jenisFilter);
        }

        // Global search DataTables
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('JudulKonten', 'like', '%' . $searchValue . '%')
                    ->orWhere('IsiKonten', 'like', '%' . $searchValue . '%')
                    ->orWhere('JenisKonten', 'like', '%' . $searchValue . '%');
            });
        }

        // Custom Keyword Search
        $keyword = $request->input('searchKeyword');
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('JudulKonten', 'like', '%' . $keyword . '%')
                    ->orWhere('IsiKonten', 'like', '%' . $keyword . '%');
            });
        }

        $recordsTotal = DB::table('mstkonten')
            ->where('JenisKonten', 'like', 'LANDING_%')
            ->count();

        $recordsFiltered = (clone $query)->count();

        // Ordering
        $orderColumn = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        $columnMap = [
            0 => 'NoUrut',
            1 => 'JenisKonten',
            2 => 'JudulKonten',
            3 => 'GambarKonten',
            4 => 'NoUrut',
            5 => 'IsAktif',
        ];

        if (isset($columnMap[$orderColumn])) {
            $query->orderBy($columnMap[$orderColumn], $orderDir);
        } else {
            $query->orderBy('JenisKonten', 'asc')->orderBy('NoUrut', 'asc');
        }

        // Pagination
        $start = (int)$request->input('start', 0);
        $length = (int)$request->input('length', 10);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $list = $query->get();
        $kategoriList = self::getKategoriList();

        $no = $start + 1;
        $data = [];
        foreach ($list as $row) {
            $kategoriLabel = $kategoriList[$row->JenisKonten] ?? str_replace('LANDING_', '', $row->JenisKonten);

            $data[] = [
                'No' => $no++,
                'IDKonten' => $row->IDKonten,
                'JenisKonten' => $row->JenisKonten,
                'KategoriLabel' => $kategoriLabel,
                'JudulKonten' => $row->JudulKonten ?? '-',
                'IsiKonten' => strip_tags($row->IsiKonten ?? ''),
                'GambarKonten' => $row->GambarKonten,
                'NoUrut' => $row->NoUrut ?? '-',
                'IsAktif' => (int)$row->IsAktif,
                'TanggalKonten' => $row->TanggalKonten,
            ];
        }

        return response()->json([
            'draw' => (int)$request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    /**
     * Otomatis memotong (trim) margin putih / transparan berlebih di sekeliling logo travel
     */
    protected function autoCropLogoWhitespace($filePath)
    {
        if (!file_exists($filePath) || !extension_loaded('gd')) {
            return;
        }
        $info = @getimagesize($filePath);
        if (!$info) return;
        $mime = $info['mime'];
        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            $img = @imagecreatefromjpeg($filePath);
            if ($img) {
                $cropped = imagecropauto($img, IMG_CROP_WHITE);
                if ($cropped !== false) {
                    imagejpeg($cropped, $filePath, 95);
                    imagedestroy($cropped);
                }
                imagedestroy($img);
            }
        } elseif ($mime === 'image/png') {
            $img = @imagecreatefrompng($filePath);
            if ($img) {
                $cropped = imagecropauto($img, IMG_CROP_TRANSPARENT);
                if ($cropped === false) {
                    $cropped = imagecropauto($img, IMG_CROP_WHITE);
                }
                if ($cropped !== false) {
                    imagealphablending($cropped, false);
                    imagesavealpha($cropped, true);
                    imagepng($cropped, $filePath);
                    imagedestroy($cropped);
                }
                imagedestroy($img);
            }
        }
    }
}
