<?php

namespace App\Http\Controllers\Api;

use App\Models\MstPerson;
use Illuminate\Http\Request;
use App\Models\SistemSetting;
use App\Models\VoucherPerson;
use App\Traits\UploadFileTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PersonController extends Controller
{
    use UploadFileTrait;

    public function __construct(
        protected MstPerson $MstPerson,
        protected VoucherPerson $VoucherPerson,
        protected SistemSetting $SistemSetting
    ) {}


    public function detail($IDPerson)
    {
        $result = $this->MstPerson->getDetail($IDPerson);

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data person tidak ditemukan'
        ], 404);
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $IDPerson = $data['IDPerson'] ?? null;

        if (!$IDPerson) {
            return response()->json([
                'status' => false,
                'message' => 'IDPerson tidak valid'
            ], 400);
        }

        $result = $this->MstPerson->getDetail($IDPerson);

        if (!$result) {
            return response()->json([
                'status' => false,
                'message' => 'data person tidak ditemukan'
            ], 404);
        }

        $data['LastModified'] = now()->format('Y-m-d H:i:s');

        $log = now()->format('H:i:s') . ' - update lokasi jamaah executed ' . PHP_EOL;
        $log .= 'Nama Jamaah : ' . $result->NamaJamaah . PHP_EOL;
        $log .= 'ID Jamaah : ' . $IDPerson . PHP_EOL;

        $updated = $this->MstPerson->updatePersonData($IDPerson, $data);

        if ($updated) {
            $log .= 'Status : Berhasil Update' . PHP_EOL;
            $log .= 'Data : ' . json_encode($data) . PHP_EOL;
            $log .= PHP_EOL;
            $this->logToFile('log_location', $log);

            return response()->json([
                'status' => true,
                'message' => 'data person berhasil diperbarui'
            ], 200);
        }

        $log .= 'Status : Gagal Update' . PHP_EOL;
        $log .= 'Data : ' . json_encode($data) . PHP_EOL;
        $log .= PHP_EOL;
        $this->logToFile('log_location', $log);

        return response()->json([
            'status' => false,
            'message' => 'data person gagal diperbarui',
            'err' => $data
        ], 406);
    }


    public function hapus(Request $request)
    {
        $IDPerson = $request->input('IDPerson');

        if (!$IDPerson) {
            return response()->json([
                'status' => false,
                'message' => 'IDPerson tidak valid'
            ], 400);
        }

        $result = $this->MstPerson->getDetail($IDPerson);

        if (!$result) {
            return response()->json([
                'status' => false,
                'message' => 'data person tidak ditemukan'
            ], 404);
        }

        $updated = $this->MstPerson->deactivatePerson($IDPerson);

        if ($updated) {
            return response()->json([
                'status' => true,
                'message' => 'data person berhasil diperbarui'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data person gagal diperbarui'
        ], 406);
    }


    public function store(Request $request)
    {
        $data = $request->all();

        if (!isset($data['Email']) || empty($data['Email'])) {
            return response()->json([
                'status' => false,
                'message' => 'data email tidak valid'
            ], 406);
        }

        $cekEmail = $this->MstPerson->getByEmail($data['Email']);
        if ($cekEmail) {
            return response()->json([
                'status' => false,
                'message' => 'data email telah digunakan'
            ], 406);
        }

        // Validasi nomor HP
        if (!isset($data['NoHP']) || empty($data['NoHP'])) {
            return response()->json([
                'status' => false,
                'message' => 'data nomor telepon tidak valid'
            ], 406);
        }

        $cekNoHP = $this->MstPerson->getByNoHP($data['NoHP']);
        if ($cekNoHP) {
            return response()->json([
                'status' => false,
                'message' => 'data nomor telepon telah digunakan'
            ], 406);
        }

        $KodeVerifikasi = $this->MstPerson::generateRandomString(6);

        // Cek referral jika ada
        if (!empty($data['KodeReferral'])) {
            $cekReferral = $this->MstPerson->getByReferral($data['KodeReferral']);
            if (!$cekReferral) {
                return response()->json([
                    'status' => false,
                    'message' => 'data kode referral tidak ditemukan'
                ], 406);
            }
            $data['IDPersonReferral'] = $cekReferral[0]->IDPerson;
        }

        $data['IDPerson'] = $this->MstPerson->generateIDPerson();
        $data['LastModified'] = now()->format('Y-m-d H:i:s');
        $data['Tgl_Register'] = now()->format('Y-m-d H:i:s');
        $data['StatusPerson'] = config('constants.PERSON_JAMAAH');
        $data['UserName'] = $data['NoHP'];
        $data['KodeVerifikasi'] = $KodeVerifikasi;
        $data['IsAktif'] = 1;
        $data['KodeReferral'] = $this->MstPerson->generateKodeReferral();
        $data['IsVerified'] = 1;
        $data['Password'] = Hash::make($data['Password']);

        $inserted = $this->MstPerson->insert($data);

        if ($inserted) {
            try {
                $this->sendVerificationEmail($data);


                $this->processVouchers($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Pendaftaran berhasil, silahkan buka email untuk memverifikasi akun anda',
                    'data' => $inserted,
                ], 200);
            } catch (\Exception $e) {
                Log::error('Email verification failed: ' . $e->getMessage());
                // Rollback jika gagal kirim email
                return $this->cancelRegister($data['IDPerson']);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Pendaftaran gagal, data pendaftaran tidak dapat disimpan'
        ], 500);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function login(Request $request)
    {
        $StatusPerson = $request->query('StatusPerson');
        $UserName = $request->query('UserName');
        $Password = $request->query('Password');

        if ($StatusPerson == config('constants.PERSON_AGEN')) {
            $result = $this->MstPerson->getByLogin($UserName, $Password, config('constants.PERSON_AGEN'));

            if ($result) {
                if ($result['data']->IsAktif != 1) {
                    return response()->json([
                        'status' => false,
                        'message' => 'data person agen nonaktif, silahkan hubungi customer service'
                    ], 404);
                }

                return response()->json([
                    'status' => true,
                    'data' => $result['data'],
                    'token'  => $result['token'],
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => 'data person agen tidak ditemukan',
            ], 404);
        }

        if ($StatusPerson == config('constants.PERSON_JAMAAH')) {
            $result = $this->MstPerson->getByLogin($UserName, $Password);

            if ($result) {
                if ($result['data']->IsAktif != 1) {
                    return response()->json([
                        'status' => false,
                        'message' => 'data person jamaah nonaktif, silahkan hubungi customer service'
                    ], 404);
                }

                return response()->json([
                    'status' => true,
                    'data' => $result['data'],
                    'token'  => $result['token'],
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => 'data person jamaah tidak ditemukan'
            ], 404);
        }

        if ($StatusPerson == config('constants.PERSON_KELUARGA')) {
            $result = $this->MstPerson->getByLogin($UserName, $Password, config('constants.PERSON_JAMAAH'));

            if ($result) {
                if ($result['data']->IsAktif != 1) {
                    return response()->json([
                        'status' => false,
                        'message' => 'data person jamaah nonaktif, silahkan hubungi customer service'
                    ], 404);
                }

                if ($result['data']->IsVerified != 1) {
                    return response()->json([
                        'status' => false,
                        'message' => 'data person jamaah belum terverifikasi, silahkan hubungi customer service'
                    ], 404);
                }

                return response()->json([
                    'status' => true,
                    'data' => $result['data'],
                    'token'  => $result['token'],
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => 'data person jamaah tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => false,
            'message' => 'data login tidak valid'
        ], 406);
    }

    public function verifikasi(Request $request)
    {
        $IDPerson = $request->input('IDPerson');
        $KodeVerifikasi = $request->input('KodeVerifikasi');

        $verify = $this->MstPerson->verifikasi($IDPerson, $KodeVerifikasi);

        if ($verify) {
            return response()->json([
                'status' => true,
                'message' => 'selamat, akun anda berhasil diverifikasi'
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'proses verifikasi gagal, kode verifikasi tidak sesuai'
        ], 406);
    }

    public function forgotpass(Request $request)
    {
        $Email = $request->input('Email');

        if (!$Email) {
            return response()->json([
                'status' => false,
                'message' => 'data email tidak valid'
            ], 406);
        }

        $cekEmail = $this->MstPerson->getByEmail($Email);

        if (!$cekEmail) {
            return response()->json([
                'status' => false,
                'message' => 'data email tidak terdaftar'
            ], 406);
        }

        $person = $cekEmail[0];

        if ($person->IsAktif != 1) {
            return response()->json([
                'status' => false,
                'message' => 'data akun telah dinonaktifkan'
            ], 406);
        }

        try {
            $this->sendForgotPasswordEmail($person);

            return response()->json([
                'status' => true,
                'message' => 'permintaan atur ulang kata sandi berhasil, silahkan buka kotak masuk email anda',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Forgot password email failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'permintaan atur ulang kata sandi gagal'
            ], 406);
        }
    }

    public function password(Request $request)
    {
        $IDPerson = $request->input('IDPerson');
        $Password = $request->input('Password');
        $PasswordLama = $request->input('PasswordLama');

        $detailPerson = $this->MstPerson->getDetail($IDPerson);

        if (!$detailPerson) {
            return response()->json([
                'status' => false,
                'message' => 'data person tidak valid'
            ], 406);
        }

        if ($detailPerson->IsAktif != 1) {
            return response()->json([
                'status' => false,
                'message' => 'data person tidak aktif'
            ], 406);
        }

        if (!empty($PasswordLama)) {
            $cekPasswordLama = $this->MstPerson->getByLogin(
                $detailPerson->UserName,
                $PasswordLama,
                $detailPerson->StatusPerson
            );

            if (!$cekPasswordLama) {
                return response()->json([
                    'status' => false,
                    'message' => 'password lama tidak benar'
                ], 406);
            }
        }

        $updated = $this->MstPerson->updatePassword($IDPerson, $Password);

        if ($updated) {
            return response()->json([
                'status' => true,
                'message' => 'perubahan password berhasil disimpan'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'perubahan password gagal disimpan'
        ], 304);
    }

    public function photo(Request $request)
    {
        $IDPerson = $request->input('IDPerson');

        if (!$IDPerson) {
            return response()->json([
                'status' => false,
                'message' => 'IDPerson tidak valid'
            ], 400);
        }

        $detailPerson = $this->MstPerson->getDetail($IDPerson);

        if (!$detailPerson) {
            return response()->json([
                'status' => false,
                'message' => 'data person tidak valid'
            ], 406);
        }

        $param = 'FotoProfil';
        $targetDir = 'assets/foto-profil';
        $allowedFile = ['png', 'jpg', 'jpeg', 'bmp'];

        if (!$request->hasFile($param)) {
            return response()->json([
                'status' => false,
                'message' => 'field ' . $param . ' is required',
                'data' => $request->hasFile($param)
            ], 406);
        }

        $FotoProfil = $this->doUpload($request, $param, $targetDir, $allowedFile);

        if ($FotoProfil === 'error') {
            return response()->json([
                'status' => false,
                'message' => 'file photo gagal diupload'
            ], 406);
        }

        $updated = $this->MstPerson->updatePerson($IDPerson, ['FotoProfil' => $FotoProfil]);

        if (!$updated) {
            return response()->json([
                'status' => false,
                'message' => 'proses simpan foto profil gagal'
            ], 304);
        }

        // Hapus foto lama jika ada
        if ($detailPerson->FotoProfil) {
            $prevFilePath = public_path($targetDir . '/' . $detailPerson->FotoProfil);
            if (file_exists($prevFilePath)) {
                @unlink($prevFilePath);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'proses simpan foto profil berhasil'
        ], 200);
    }

    private function cancelRegister($IDPerson)
    {
        $this->MstPerson->where('IDPerson', $IDPerson)->delete();

        return response()->json([
            'status' => false,
            'message' => 'proses pendaftaran gagal, verifikasi email gagal'
        ], 401);
    }

    private function sendVerificationEmail(array $data)
    {
        $appName = config('app.name');
        $emailAdmin = config('mail.from.address');

        Mail::send('emails.verification', [
            'appName' => $appName,
            'KodeVerifikasi' => $data['KodeVerifikasi']
        ], function ($message) use ($data, $appName) {
            $message->to($data['Email'])
                ->subject("Verifikasi Pendaftaran Akun $appName");
        });
    }

    private function sendForgotPasswordEmail($person)
    {
        $appName = config('app.name');
        $emailAdmin = config('mail.from.address');

        Mail::send('emails.forgot_password', [
            'appName' => $appName,
            'NamaJamaah' => $person->NamaJamaah,
            'urlResetPass' => url('/person/resetpass?auth='),
            'EmailAdmin' => $emailAdmin,
        ], function ($message) use ($person, $appName) {
            $message->to($person->Email)
                ->subject("Atur Ulang Kata Sandi Akun $appName");
        });
    }


    private function processVouchers(array $data)
    {
        $voucherSettings = $this->SistemSetting->getVoucherSettings();

        foreach ($voucherSettings as $setting) {

            if (
                isset($data['IDPersonReferral'])
                && $setting->KodeSetting == SistemSetting::KODE_VOUCHER_REFERRAL
                && $setting->Value1 == 'AKTIF'
            ) {

                $this->VoucherPerson->create([
                    'IDPerson' => $data['IDPersonReferral'],
                    'Debet' => $setting->Value,
                    'Kredit' => 0,
                    'NamaVoucher' => $setting->Jenis,
                    'Keterangan' => "Pendapatan referral dari pendaftaran jamaah atas nama "
                        . strtoupper($data['NamaJamaah']) . " (" . $data['IDPerson'] . ").",
                    'IsKlaim' => 0,
                ]);
            }


            if (
                $setting->KodeSetting == SistemSetting::KODE_VOUCHER_PENDAFTARAN
                && $setting->Value1 == 'AKTIF'
            ) {

                $this->VoucherPerson->create([
                    'IDPerson' => $data['IDPerson'],
                    'Debet' => $setting->Value,
                    'Kredit' => 0,
                    'NamaVoucher' => $setting->Jenis,
                    'Keterangan' => "Pendapatan voucher dari pendaftaran pertama.",
                    'IsKlaim' => 0,
                ]);
            }
        }
    }

    private function logToFile($prefix, $content)
    {
        $filename = $prefix . '_' . now()->format('j.n.Y') . '.txt';
        $path = storage_path('logs/' . $filename);
        file_put_contents($path, $content, FILE_APPEND);
    }
}
