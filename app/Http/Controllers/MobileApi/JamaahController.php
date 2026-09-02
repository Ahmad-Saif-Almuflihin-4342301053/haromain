<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\MobileModels\JamaahPaket;
use App\Models\MobileModels\Person;
use App\Models\MobileModels\Paket;
use App\Models\MobileModels\Travel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class JamaahController extends BaseApiController
{
    /**
     * GET /api/jamaah/paid/{IDPerson}
     * Get paid jamaah by person
     */
    public function paid(string $IDPerson): JsonResponse
    {
        $result = JamaahPaket::findPaidJamaah($IDPerson);

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data jamaah tidak ditemukan');
    }

    /**
     * GET /api/jamaah/paidall
     * Get all paid jamaah by paket
     */
    public function paidAll(Request $request): JsonResponse
    {
        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $IDPaket = $this->sanitize($request->query('IDPaket'));

        if (!$IDTravel || !$IDPaket) {
            return $this->validationError('IDTravel dan IDPaket harus diisi');
        }

        $result = JamaahPaket::findAllPaidJamaah($IDTravel, $IDPaket);

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data jamaah tidak ditemukan');
    }

    /**
     * GET /api/jamaah/person/{IDPerson}
     * Get jamaah by person
     */
    public function person(string $IDPerson, Request $request): JsonResponse
    {
        $page = (int) ($request->query('page') ?? 0);
        $cari = $this->sanitize($request->query('cari'));

        $result = JamaahPaket::findByPerson($IDPerson, $cari, 10, $page);

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data jamaah tidak ditemukan');
    }

    /**
     * GET /api/jamaah/history/{IDPerson}
     * Get history jamaah
     */
    public function history(string $IDPerson, Request $request): JsonResponse
    {
        $page = (int) ($request->query('page') ?? 0);
        $cari = $this->sanitize($request->query('cari'));

        $result = JamaahPaket::findHistoryPerson($IDPerson, $cari, 10, $page);

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data jamaah tidak ditemukan');
    }

    /**
     * GET /api/jamaah/historyagen/{IDPerson}
     * Get history jamaah untuk agen
     */
    public function historyAgen(string $IDPerson, Request $request): JsonResponse
    {
        $page = (int) ($request->query('page') ?? 0);
        $cari = $this->sanitize($request->query('cari'));

        $result = JamaahPaket::findHistoryAgen($IDPerson, $cari, 10, $page);

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data jamaah tidak ditemukan');
    }

    /**
     * GET /api/jamaah/paket
     * Get jamaah by paket
     */
    public function paket(Request $request): JsonResponse
    {
        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $IDPaket = $this->sanitize($request->query('IDPaket'));
        $page = (int) ($request->query('page') ?? 0);
        $cari = $this->sanitize($request->query('cari'));

        if (!$IDTravel || !$IDPaket) {
            return $this->validationError('IDTravel dan IDPaket harus diisi');
        }

        $result = JamaahPaket::findByPaket($IDTravel, $IDPaket, $cari, 10, $page);

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data jamaah tidak ditemukan');
    }

    /**
     * GET /api/jamaah/detail
     * Get detail jamaah
     */
    public function detail(Request $request): JsonResponse
    {
        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $IDPaket = $this->sanitize($request->query('IDPaket'));
        $IDPerson = $this->sanitize($request->query('IDPerson'));

        if (!$IDTravel || !$IDPaket || !$IDPerson) {
            return $this->validationError('IDTravel, IDPaket, dan IDPerson harus diisi');
        }

        $detailJamaah = JamaahPaket::getDetail($IDTravel, $IDPaket, $IDPerson);

        if (!$detailJamaah) {
            return $this->notFound('Data jamaah tidak ditemukan');
        }

        // Get dokumen syarat
        $detailJamaah['DokumenSyarat'] = JamaahPaket::getDokumenSyarat($IDTravel, $IDPaket, $IDPerson);

        // Get detail peserta
        $detailJamaah['DetailPeserta'] = Person::getDetail($IDPerson);

        // Get detail agen
        $detailJamaah['DetailAgen'] = $detailJamaah['IDPersonAgen']
            ? Person::getDetail($detailJamaah['IDPersonAgen'])
            : null;

        // Get detail paket
        $detailPaket = Paket::getDetail($detailJamaah['IDTravel'], $detailJamaah['IDPaket']);
        if ($detailPaket) {
            $detailPaket['syaratPaket'] = Paket::getSyaratPaket($IDTravel, $IDPaket);
            $detailPaket['travel'] = Travel::getDetail($IDTravel);
        }
        $detailJamaah['DetailPaket'] = $detailPaket;

        // Get agen travel info
        $agenTravel = DB::selectOne("
            SELECT DISTINCT agt.IDTravel, agt.LevelWilayah, agt.IDPerson, tv.NamaTravel,
                   tv.IDProv, prov.NamaProv, tv.IDKab, kab.NamaKab,
                   tv.IDKec, kec.NamaKec, tv.IDDesa, desa.NamaDesa
            FROM agentravel agt
            LEFT JOIN jamaahpaket jp ON jp.IDTravel=agt.IDTravel AND jp.IDPersonAgen=agt.IDPerson
            LEFT JOIN msttravel tv ON tv.IDTravel=agt.IDTravel
            LEFT JOIN mstprov prov ON prov.IDProv=tv.IDProv
            LEFT JOIN mstkab kab ON kab.IDKab=tv.IDKab AND kab.IDProv=tv.IDProv
            LEFT JOIN mstkec kec ON kec.IDKec=tv.IDKec AND kec.IDKab=tv.IDKab AND kec.IDProv=tv.IDProv
            LEFT JOIN mstdesa desa ON desa.IDDesa=tv.IDDesa AND desa.IDKec=tv.IDKec AND desa.IDKab=tv.IDKab AND desa.IDProv=tv.IDProv
            WHERE (DATE(jp.TanggalBooking) BETWEEN agt.TglMulaiAgen AND agt.TglAkhirAgen)
              AND agt.IDPerson = ?
            LIMIT 1
        ", [$detailJamaah['IDPersonAgen']]);
        $detailJamaah['AgenTravel'] = $agenTravel ? (array) $agenTravel : null;

        // Get peserta paket
        $detailJamaah['Peserta'] = JamaahPaket::findPeserta($IDTravel, $IDPaket);

        return $this->success($detailJamaah);
    }

    /**
     * POST /api/jamaah
     * Register jamaah to paket
     */
    public function store(Request $request): JsonResponse
    {
        $IDTravel = $this->sanitize($request->input('IDTravel'));
        $IDPaket = $this->sanitize($request->input('IDPaket'));

        if (!$IDTravel || !$IDPaket) {
            return $this->validationError('IDTravel dan IDPaket harus diisi');
        }

        // Get detail paket
        $detilPaket = Paket::getDetail($IDTravel, $IDPaket);
        if (!$detilPaket) {
            return $this->validationError('Data paket tidak ditemukan');
        }

        $syaratPaket = Paket::getSyaratPaket($IDTravel, $IDPaket);

        $IDCabang = $this->sanitize($request->input('IDCabang'));
        $IDPersonAgen = $this->sanitize($request->input('IDPersonAgen'));
        $JumlahTrip = (int) $request->input('JumlahTrip', 4);

        // Calculate price based on JumlahTrip
        $InfoTagihan = $detilPaket['BiayaTrip'];
        if ($JumlahTrip == 4) {
            $InfoTagihan = $detilPaket['BiayaQuad'];
        } elseif ($JumlahTrip == 3) {
            $InfoTagihan = $detilPaket['BiayaTriple'];
        } elseif ($JumlahTrip == 2) {
            $InfoTagihan = $detilPaket['BiayaDouble'];
        }

        $IsPersonTerdaftar = false;
        $person = null;

        // Check if person already exists
        if ($request->has('IDPerson')) {
            $IDPerson = $this->sanitize($request->input('IDPerson'));
            $person = Person::getDetail($IDPerson);
            $IsPersonTerdaftar = $person !== null;
        }

        if (!$person) {
            // Validate new person data
            $validator = Validator::make($request->all(), [
                'Email' => 'required|email',
                'NoHP' => 'required',
                'NamaJamaah' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors()->first());
            }

            $Email = $this->sanitize($request->input('Email'));
            $NoHP = $this->sanitize($request->input('NoHP'));

            // Check email
            $cekEmail = Person::findByEmail($Email);
            if (!empty($cekEmail)) {
                return $this->validationError('Data email telah digunakan');
            }

            // Check NoHP
            $cekNoHP = Person::findByNoHP($NoHP);
            if (!empty($cekNoHP)) {
                return $this->validationError('Data nomor telepon telah digunakan');
            }

            $person = [
                'NamaJamaah' => $this->sanitize($request->input('NamaJamaah')),
                'NIK' => $this->sanitize($request->input('NIK')),
                'Email' => $Email,
                'NoHP' => $NoHP,
                'IDPerson' => Person::generateIDPerson(),
                'LastModified' => now(),
                'Tgl_Register' => now(),
                'StatusPerson' => Person::STATUS_JAMAAH,
                'UserName' => $NoHP,
                'Password' => md5($request->input('Password', '123456')),
                'KodeVerifikasi' => Person::generateRandomString(6),
                'IsVerified' => 1,
                'IsAktif' => 1,
                'IDProv' => $this->sanitize($request->input('IDProv')),
                'IDKab' => $this->sanitize($request->input('IDKab')),
                'IDKec' => $this->sanitize($request->input('IDKec')),
                'IDDesa' => $this->sanitize($request->input('IDDesa')),
                'TempatLahir' => $this->sanitize($request->input('TempatLahir')),
                'TanggalLahir' => $this->sanitize($request->input('TanggalLahir')),
                'Alamat' => $this->sanitize($request->input('Alamat')),
            ];
        } else {
            // Check if person already registered in this paket
            $cekJamaahTerdaftar = JamaahPaket::cekPersonTerdaftar($IDTravel, $IDPaket, $person['IDPerson']);
            if (!empty($cekJamaahTerdaftar)) {
                return $this->validationError('Data jamaah telah terdaftar pada paket ini');
            }
        }

        $IDPerson = $person['IDPerson'];

        // Prepare jamaah paket data
        $jamaahPaket = [
            'IDTravel' => $IDTravel,
            'IDPaket' => $IDPaket,
            'IDPerson' => $IDPerson,
            'IDPersonAgen' => $IDPersonAgen,
            'TanggalBooking' => now(),
            'InfoTagihan' => $InfoTagihan,
            'InfoTerbayar' => 0,
            'InfoSisaTagihan' => $InfoTagihan,
            'NominalPaid' => 0,
            'StatusJamaah' => JamaahPaket::STATUS_JAMAAH,
            'IDCabang' => $IDCabang,
            'JumlahTrip' => $JumlahTrip,
        ];

        // Handle document uploads
        $dokumenSyarat = [];
        $syaratDir = 'assets/dokumen_syarat';
        $allowedFile = ['pdf', 'png', 'jpeg', 'jpg', 'bmp'];

        foreach ($syaratPaket as $syarat) {
            $param = $syarat->IDTravel . '_' . $syarat->IDPaket . '_' . $syarat->NoUrutSyarat;

            if ($request->hasFile($param)) {
                $uploadSyarat = $this->upload($request, $param, $syaratDir, $allowedFile);

                if (!$uploadSyarat) {
                    return $this->validationError(
                        'Proses upload dokumen syarat ' . $syarat->NamaSyarat . ' gagal, ' . $this->getErrorMessage()
                    );
                }

                $dokumenSyarat[] = [
                    'IDTravel' => $syarat->IDTravel,
                    'IDPaket' => $syarat->IDPaket,
                    'NoUrutSyarat' => $syarat->NoUrutSyarat,
                    'IDPerson' => $IDPerson,
                    'FileDokumen' => $uploadSyarat,
                    'Keterangan' => $syarat->Keterangan,
                    'TglUpload' => now(),
                ];
            }
        }

        DB::beginTransaction();
        try {
            // Insert new person if not exists
            if (!$IsPersonTerdaftar) {
                DB::table('mstperson')->insert($person);
            }

            // Insert jamaah paket
            DB::table('jamaahpaket')->insert($jamaahPaket);

            // Insert dokumen syarat
            if (!empty($dokumenSyarat)) {
                DB::table('dokumensyarat')->insert($dokumenSyarat);
            }

            DB::commit();
            return $this->success(null, 'Proses pendaftaran jamaah berhasil', 201);
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded documents
            foreach ($dokumenSyarat as $ds) {
                $dsPath = public_path($syaratDir . '/' . $ds['FileDokumen']);
                if (file_exists($dsPath)) {
                    @unlink($dsPath);
                }
            }

            Log::error('Jamaah registration error: ' . $e->getMessage());
            return $this->error('Proses pendaftaran jamaah gagal, silahkan ulangi beberapa saat lagi', 304);
        }
    }

    /**
     * POST /api/jamaah/dokumen
     * Upload dokumen syarat
     */
    public function dokumen(Request $request): JsonResponse
    {
        $IDTravel = $this->sanitize($request->input('IDTravel'));
        $IDPaket = $this->sanitize($request->input('IDPaket'));
        $IDPerson = $this->sanitize($request->input('IDPerson'));
        $NoUrutSyarat = (int) $request->input('NoUrutSyarat');
        $Keterangan = $this->sanitize($request->input('Keterangan'));

        if (!$IDTravel || !$IDPaket || !$IDPerson || !$NoUrutSyarat) {
            return $this->validationError('Parameter tidak lengkap');
        }

        $syaratDir = 'assets/dokumen_syarat';
        $allowedFile = ['pdf', 'png', 'jpeg', 'jpg', 'bmp'];

        // Get existing document
        $detailDokumen = JamaahPaket::findDokumen($IDTravel, $IDPaket, $IDPerson, $NoUrutSyarat);

        $FileDokumen = $this->upload($request, 'FileDokumen', $syaratDir, $allowedFile);

        if (!$FileDokumen) {
            return $this->validationError('Proses upload dokumen syarat gagal, ' . $this->getErrorMessage());
        }

        $updated = JamaahPaket::gantiDokumen($IDTravel, $IDPaket, $IDPerson, $NoUrutSyarat, $FileDokumen, $Keterangan);

        if (!$updated) {
            return $this->error('Proses simpan dokumen syarat gagal', 304);
        }

        // Delete old document
        if ($detailDokumen && $detailDokumen->FileDokumen) {
            $oldPath = public_path($syaratDir . '/' . $detailDokumen->FileDokumen);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        return $this->success(null, 'Proses simpan dokumen syarat berhasil');
    }

    /**
     * PUT /api/jamaah
     * Update jamaah data
     */
    public function update(Request $request): JsonResponse
    {
        $data = $request->all();
        $IDPerson = $data['IDPerson'] ?? null;

        if (!$IDPerson) {
            return $this->validationError('IDPerson harus diisi');
        }

        $updatePerson = [];

        if (isset($data['NamaJamaah'])) {
            $updatePerson['NamaJamaah'] = $data['NamaJamaah'];
        }

        if (isset($data['Email'])) {
            $cekEmail = Person::findByEmail($data['Email']);
            if (!empty($cekEmail) && $cekEmail[0]['IDPerson'] !== $IDPerson) {
                return $this->validationError('Alamat email telah digunakan');
            }
            $updatePerson['Email'] = $data['Email'];
        }

        if (isset($data['NoHP'])) {
            $cekNoHP = Person::findByNoHP($data['NoHP']);
            if (!empty($cekNoHP) && $cekNoHP[0]['IDPerson'] !== $IDPerson) {
                return $this->validationError('Nomor telepon telah digunakan');
            }
            $updatePerson['NoHP'] = $data['NoHP'];
        }

        if (isset($data['NIK'])) {
            $cekNIK = Person::findByNIK($data['NIK']);
            if (!empty($cekNIK) && $cekNIK[0]['IDPerson'] !== $IDPerson) {
                return $this->validationError('Nomor NIK telah digunakan');
            }
            $updatePerson['NIK'] = $data['NIK'];
        }

        $optionalFields = ['IDDesa', 'IDKec', 'IDKab', 'IDProv', 'TempatLahir', 'TanggalLahir', 'Alamat'];
        foreach ($optionalFields as $field) {
            if (isset($data[$field])) {
                $updatePerson[$field] = $data[$field];
            }
        }

        if (empty($updatePerson)) {
            return $this->validationError('Tidak ada data yang diupdate');
        }

        $updatePerson['LastModified'] = now();

        $updated = Person::where('IDPerson', $IDPerson)->update($updatePerson);

        if ($updated) {
            return $this->success(null, 'Proses update data jamaah berhasil');
        }

        return $this->error('Proses update data jamaah gagal', 304);
    }

    /**
     * PUT /api/jamaah/alert
     * Toggle alert status
     */
    public function alert(Request $request): JsonResponse
    {
        $IDPerson = $request->input('IDPerson');
        $IDPaket = $request->input('IDPaket');
        $IDTravel = $request->input('IDTravel');

        if (!$IDPerson || !$IDPaket || !$IDTravel) {
            return $this->validationError('Parameter tidak lengkap');
        }

        $updated = DB::statement("
            UPDATE jamaahpaket jp
            SET jp.IsAlert = (IF(jp.IsAlert='1', '0', '1'))
            WHERE jp.IDTravel = ? AND jp.IDPaket = ? AND jp.IDPerson = ?
        ", [$IDTravel, $IDPaket, $IDPerson]);

        if ($updated) {
            return $this->success(null, 'Update berhasil');
        }

        return $this->validationError('Update jamaah gagal');
    }

    /**
     * POST /api/jamaah/testimoni
     * Submit testimoni
     */
    public function testimoni(Request $request): JsonResponse
    {
        $IDPerson = $this->sanitize($request->input('IDPerson'));
        $IDTravel = $this->sanitize($request->input('IDTravel'));
        $IDPaket = $this->sanitize($request->input('IDPaket'));
        $NarasiTestimoni = $this->sanitize($request->input('Testimoni'));
        $RatingTestimoni = (int) $request->input('RatingTestimoni');
        $TanggalTestimoni = $this->sanitize($request->input('TanggalTestimoni'));

        if (!$IDPerson || !$IDTravel || !$IDPaket) {
            return $this->validationError('Parameter tidak lengkap');
        }

        $updated = JamaahPaket::updateTestimoni(
            $IDTravel,
            $IDPaket,
            $IDPerson,
            $NarasiTestimoni,
            $RatingTestimoni,
            $TanggalTestimoni
        );

        if ($updated) {
            return $this->success(null, 'Simpan testimoni berhasil');
        }

        return $this->validationError('Simpan testimoni gagal');
    }
}
