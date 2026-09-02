<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\MobileModels\Person;
use App\Models\MobileModels\Setting;
use App\Models\MobileModels\VoucherPerson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PersonController extends BaseApiController
{
    /**
     * GET /api/person/{IDPerson}
     * Get detail person
     */
    public function detail(string $IDPerson): JsonResponse
    {
        $result = Person::getDetail($IDPerson);

        if (!$result) {
            return $this->notFound('Data person tidak ditemukan');
        }

        return $this->success($result);
    }

    /**
     * PUT /api/person
     * Update person data (location update)
     */
    public function update(Request $request): JsonResponse
    {
        $data = $request->all();
        $IDPerson = $data['IDPerson'] ?? null;

        if (!$IDPerson) {
            return $this->validationError('IDPerson harus diisi');
        }

        $result = Person::getDetail($IDPerson);

        if (!$result) {
            return $this->notFound('Data person tidak ditemukan');
        }

        // Filter data yang valid untuk tabel mstperson
        // IsBantuan tidak ada di mstperson, hanya di Firebase
        $allowedFields = [
            'KoorLat', 'KoorLong', 'LastModified',
            'NamaJamaah', 'Email', 'NoHP', 'NIK', 'IDProv', 'IDKab', 
            'IDKec', 'IDDesa', 'TempatLahir', 'TanggalLahir', 'Alamat'
        ];
        
        $filteredData = [];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $filteredData[$field] = $data[$field];
            }
        }
        $filteredData['LastModified'] = now();

        // Logging - Detail semua data yang dikirim mobile
        $log = now()->format('H:i:s') . ' - update lokasi jamaah executed' . PHP_EOL;
        $log .= 'Nama Jamaah : ' . $result['NamaJamaah'] . PHP_EOL;
        $log .= 'ID Jamaah : ' . $IDPerson . PHP_EOL;
        $log .= '=== MOBILE APP DATA ===' . PHP_EOL;
        $log .= 'Raw Request Data : ' . json_encode($request->all(), JSON_PRETTY_PRINT) . PHP_EOL;
        $log .= 'Filtered Data (Valid for mstperson) : ' . json_encode($filteredData, JSON_PRETTY_PRINT) . PHP_EOL;
        
        // Cek apakah ada data IsBantuan
        if (isset($filteredData['IsBantuan'])) {
            $log .= 'IS BANTUAN DETECTED : ' . ($filteredData['IsBantuan'] ? 'TRUE (Minta Bantuan)' : 'FALSE (Tidak Minta Bantuan)') . PHP_EOL;
        } else {
            $log .= 'IS BANTUAN : Tidak ada data IsBantuan' . PHP_EOL;
        }
        
        // Cek data lokasi
        if (isset($filteredData['KoorLat']) && isset($filteredData['KoorLong'])) {
            $log .= 'LOCATION : Lat=' . $filteredData['KoorLat'] . ', Long=' . $filteredData['KoorLong'] . PHP_EOL;
        }
        
        // Log data yang di-filter
        $log .= 'Data yang akan di-update ke DB : ' . json_encode($filteredData) . PHP_EOL;
        $log .= '=== END MOBILE DATA ===' . PHP_EOL;

        try {
            $updated = Person::where('IDPerson', $IDPerson)->update($filteredData);

            if ($updated) {
                $log .= 'Status : Berhasil Update' . PHP_EOL;

                return $this->success(null, 'Data person berhasil diperbarui');
            }

            $log .= 'Status : Gagal Update' . PHP_EOL;

            return $this->error('Data person gagal diperbarui', 406, $filteredData);
        } catch (\Exception $e) {
            $log .= 'EXCEPTION : ' . $e->getMessage() . PHP_EOL;
            return $this->error('Data person gagal diperbarui', 406);
        }
    }

    /**
     * PUT /api/person/hapus
     * Soft delete person (set IsAktif = 0)
     */
    public function hapus(Request $request): JsonResponse
    {
        $IDPerson = $request->input('IDPerson');

        if (!$IDPerson) {
            return $this->validationError('IDPerson harus diisi');
        }

        $result = Person::getDetail($IDPerson);

        if (!$result) {
            return $this->notFound('Data person tidak ditemukan');
        }

        $updated = Person::where('IDPerson', $IDPerson)->update([
            'IsAktif' => 0,
            'LastModified' => now(),
        ]);

        if ($updated) {
            return $this->success(null, 'Data person berhasil diperbarui');
        }

        return $this->error('Data person gagal diperbarui', 406);
    }

    /**
     * POST /api/person
     * Register new person
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'Email' => 'required|email',
            'NoHP' => 'required',
            'NamaJamaah' => 'required',
            'Password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->first());
        }

        $data = $request->all();

        // Cek email tersedia
        $cekEmail = Person::findByEmail($data['Email']);
        if (!empty($cekEmail)) {
            return $this->validationError('Data email telah digunakan');
        }

        // Cek NoHP tersedia
        $cekNoHP = Person::findByNoHP($data['NoHP']);
        if (!empty($cekNoHP)) {
            return $this->validationError('Data nomor telepon telah digunakan');
        }

        $KodeVerifikasi = Person::generateRandomString(6);

        // Cek referral code
        if (!empty($data['KodeReferral'])) {
            $cekReferral = Person::findByReferral($data['KodeReferral']);
            if (empty($cekReferral)) {
                return $this->validationError('Data kode referral tidak ditemukan');
            }
            $data['IDPersonReferral'] = $cekReferral[0]['IDPerson'];
        }

        $data['IDPerson'] = Person::generateIDPerson();
        $data['LastModified'] = now();
        $data['Tgl_Register'] = now();
        $data['StatusPerson'] = Person::STATUS_JAMAAH;
        $data['UserName'] = $data['NoHP'];
        $data['KodeVerifikasi'] = $KodeVerifikasi;
        $data['IsAktif'] = 1;
        $data['KodeReferral'] = Person::generateKodeReferral();
        $data['IsVerified'] = 1; // Auto verified
        $data['Password'] = md5($data['Password']);

        DB::beginTransaction();
        try {
            $inserted = Person::create($data);

            if ($inserted) {
                // Process voucher
                $voucherSettings = Setting::getVoucher();
                foreach ($voucherSettings as $setting) {
                    // Voucher referral
                    if (
                        !empty($data['IDPersonReferral']) &&
                        $setting['KodeSetting'] === Setting::KODE_VOUCHER_REFERRAL &&
                        $setting['Value1'] === 'AKTIF'
                    ) {
                        VoucherPerson::createVoucher([
                            'IDPerson' => $data['IDPersonReferral'],
                            'Debet' => $setting['Value'],
                            'NamaVoucher' => $setting['Jenis'],
                            'Keterangan' => "Pendapatan referral dari pendaftaran jamaah atas nama " .
                                strtoupper($data['NamaJamaah']) . " (" . $data['IDPerson'] . ").",
                            'IsKlaim' => 0,
                        ]);
                    }

                    // Voucher pendaftaran
                    if (
                        $setting['KodeSetting'] === Setting::KODE_VOUCHER_PENDAFTARAN &&
                        $setting['Value1'] === 'AKTIF'
                    ) {
                        VoucherPerson::createVoucher([
                            'IDPerson' => $data['IDPerson'],
                            'Debet' => $setting['Value'],
                            'NamaVoucher' => $setting['Jenis'],
                            'Keterangan' => "Pendapatan voucher dari pendaftaran pertama.",
                            'IsKlaim' => 0,
                        ]);
                    }
                }

                DB::commit();

                // Send email verification (optional - can be implemented with Laravel Mail)
                // $this->sendVerificationEmail($data['Email'], $KodeVerifikasi);

                return $this->success(
                    Person::getDetail($data['IDPerson']),
                    'Pendaftaran berhasil, silahkan buka email untuk memverifikasi akun anda'
                );
            }

            DB::rollBack();
            return $this->error('Pendaftaran gagal, data pendaftaran tidak dapat disimpan', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Proses pendaftaran gagal', 401);
        }
    }

    /**
     * GET /api/person/login
     * Login person
     */
    public function login(Request $request): JsonResponse
    {
        $StatusPerson = $this->sanitize($request->input('StatusPerson'));
        $UserName = $this->sanitize($request->input('UserName'));
        $Password = $this->sanitize($request->input('Password'));

        if (!$UserName || !$Password) {
            return $this->validationError('Username dan password harus diisi');
        }

        if ($StatusPerson === Person::STATUS_AGEN) {
            $result = Person::findByLogin($UserName, $Password, Person::STATUS_AGEN);
            if ($result) {
                if ($result['IsAktif'] != 1) {
                    return $this->notFound('Data person agen nonaktif, silahkan hubungi customer service');
                }
                return $this->success($result);
            }
            return $this->notFound('Data person agen tidak ditemukan');
        }

        if ($StatusPerson === Person::STATUS_JAMAAH) {
            $result = Person::findByLogin($UserName, $Password, Person::STATUS_JAMAAH);
            if ($result) {
                if ($result['IsAktif'] != 1) {
                    return $this->notFound('Data person jamaah nonaktif, silahkan hubungi customer service');
                }
                return $this->success($result);
            }
            return $this->notFound('Data person jamaah tidak ditemukan');
        }

        if ($StatusPerson === Person::STATUS_KELUARGA) {
            $result = Person::findByLogin($UserName, $Password, Person::STATUS_JAMAAH);
            if ($result) {
                if ($result['IsAktif'] != 1) {
                    return $this->notFound('Data person jamaah nonaktif, silahkan hubungi customer service');
                }
                if ($result['IsVerified'] != 1) {
                    return $this->notFound('Data person jamaah belum terverifikasi, silahkan hubungi customer service');
                }
                return $this->success($result);
            }
            return $this->notFound('Data person jamaah tidak ditemukan');
        }

        return $this->validationError('Data login tidak valid');
    }

    /**
     * POST /api/person/verifikasi
     * Verify account
     */
    public function verifikasi(Request $request): JsonResponse
    {
        $IDPerson = $this->sanitize($request->input('IDPerson'));
        $KodeVerifikasi = $this->sanitize($request->input('KodeVerifikasi'));

        if (!$IDPerson || !$KodeVerifikasi) {
            return $this->validationError('IDPerson dan KodeVerifikasi harus diisi');
        }

        $verified = Person::verifikasi($IDPerson, $KodeVerifikasi);

        if ($verified) {
            return $this->success(null, 'Selamat, akun anda berhasil diverifikasi', 201);
        }

        return $this->validationError('Proses verifikasi gagal, kode verifikasi tidak sesuai');
    }

    /**
     * POST /api/person/forgotpass
     * Request forgot password
     */
    public function forgotpass(Request $request): JsonResponse
    {
        $Email = $this->sanitize($request->input('Email'));

        if (!$Email) {
            return $this->validationError('Email harus diisi');
        }

        $cekEmail = Person::findByEmail($Email);
        if (empty($cekEmail)) {
            return $this->validationError('Data email tidak terdaftar');
        }

        $person = $cekEmail[0];
        if ($person['IsAktif'] != 1) {
            return $this->validationError('Data akun telah dinonaktifkan');
        }

        // TODO: Implement email sending with Laravel Mail
        // Mail::to($Email)->send(new ForgotPasswordMail($person));

        return $this->success(null, 'Permintaan atur ulang kata sandi berhasil, silahkan buka kotak masuk email anda');
    }

    /**
     * PUT /api/person/password
     * Update password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $IDPerson = $request->input('IDPerson');
        $Password = $request->input('Password');
        $PasswordLama = $request->input('PasswordLama');

        if (!$IDPerson || !$Password) {
            return $this->validationError('IDPerson dan Password harus diisi');
        }

        $detailPerson = Person::getDetail($IDPerson);
        if (!$detailPerson) {
            return $this->validationError('Data person tidak valid');
        }

        if ($detailPerson['IsAktif'] != 1) {
            return $this->validationError('Data person tidak aktif');
        }

        // Verify old password if provided
        if (!empty($PasswordLama)) {
            $cekPasswordLama = Person::findByLogin(
                $detailPerson['UserName'],
                $PasswordLama,
                $detailPerson['StatusPerson']
            );
            if (!$cekPasswordLama) {
                return $this->validationError('Password lama tidak benar');
            }
        }

        $updated = Person::updatePassword($IDPerson, $Password);

        if ($updated) {
            return $this->success(null, 'Perubahan password berhasil disimpan');
        }

        return $this->error('Perubahan password gagal disimpan', 304);
    }

    /**
     * POST /api/person/photo
     * Upload profile photo
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $IDPerson = $this->sanitize($request->input('IDPerson'));

        if (!$IDPerson) {
            return $this->validationError('IDPerson harus diisi');
        }

        $detailPerson = Person::where('IDPerson', $IDPerson)->first();
        if (!$detailPerson) {
            return $this->validationError('Data person tidak valid');
        }

        $allowedFile = ['png', 'jpg', 'jpeg', 'bmp'];
        $targetDir = 'assets/foto-profil';

        $FotoProfil = $this->upload($request, 'FotoProfil', $targetDir, $allowedFile);

        if (!$FotoProfil) {
            return $this->validationError('File photo gagal diupload: ' . $this->getErrorMessage());
        }

        $updated = Person::where('IDPerson', $IDPerson)->update(['FotoProfil' => $FotoProfil]);

        if (!$updated) {
            return $this->error('Proses simpan foto profil gagal', 304);
        }

        // Delete old photo
        $prevFilePath = public_path($targetDir . '/' . $detailPerson->FotoProfil);
        if ($detailPerson->FotoProfil && file_exists($prevFilePath)) {
            @unlink($prevFilePath);
        }

        return $this->success(null, 'Proses simpan foto profil berhasil');
    }

    /**
     * PUT /api/person/bantuan
     * Update status bantuan jamaah ke Firebase
     */
    public function updateBantuan(Request $request): JsonResponse
    {
        $data = $request->all();
        $IDPerson = $data['IDPerson'] ?? null;
        $IsBantuan = $data['IsBantuan'] ?? null;

        if (!$IDPerson || $IsBantuan === null) {
            return $this->validationError('IDPerson dan IsBantuan harus diisi');
        }

        try {
            // Update Firebase Realtime Database
            $serviceAccountPath = storage_path('app/firebase/haromain-afindo-firebase-adminsdk-qzlal-87b11d086c.json');
            
            if (!file_exists($serviceAccountPath)) {
                return $this->error('Firebase service account tidak ditemukan', 500);
            }

            $factory = (new \Kreait\Firebase\Factory())
                ->withServiceAccount($serviceAccountPath)
                ->withDatabaseUri('https://haromain-afindo-default-rtdb.asia-southeast1.firebasedatabase.app');

            $database = $factory->createDatabase();
            
            // Update status bantuan di Firebase
            $database->getReference('person/' . $IDPerson)->update([
                'IsBantuan' => (bool)$IsBantuan,
                'LastModified' => now()->toISOString()
            ]);

            // Log aktivitas
            $log = now()->format('H:i:s') . ' - update bantuan jamaah executed' . PHP_EOL;
            $log .= 'ID Jamaah : ' . $IDPerson . PHP_EOL;
            $log .= 'Status Bantuan : ' . ($IsBantuan ? 'AKTIF (Minta Bantuan)' : 'NONAKTIF (Bantuan Dimatikan)') . PHP_EOL;

            return $this->success(null, 'Status bantuan berhasil diperbarui');
            
        } catch (\Exception $e) {
            return $this->error('Gagal memperbarui status bantuan: ' . $e->getMessage(), 500);
        }
    }
}
