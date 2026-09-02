<?php

namespace App\Http\Controllers\Api;

use App\Models\MstPaket;
use App\Models\MstPerson;
use App\Models\MstTravel;
use App\Models\AgenTravel;
use App\Models\JamaahPaket;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class JamaahController extends Controller
{
    use UploadFileTrait;

    public function __construct(
        protected MstPerson $MstPerson,
        protected MstPaket $MstPaket,
        protected JamaahPaket $JamaahPaket,
        protected MstTravel $MstTravel,
        protected AgenTravel $AgenTravel
    ) {}

    public function paid($IDPerson)
    {
        $result = $this->JamaahPaket->findPaidJamaah($IDPerson);
        if ($result) return response()->json(['status' => true, 'data' => $result]);
        return response()->json(['status' => false, 'message' => 'data jamaah tidak ditemukan'], 404);
    }

    public function paidall(Request $request)
    {
        $IDTravel = $request->query('IDTravel');
        $IDPaket = $request->query('IDPaket');

        $result = $this->JamaahPaket->setLimit(-1)->findAllPaidJamaah($IDTravel, $IDPaket);
        if ($result) return response()->json(['status' => true, 'data' => $result]);
        return response()->json(['status' => false, 'message' => 'data jamaah tidak ditemukan'], 404);
    }

    public function person(Request $request, $IDPerson)
    {
        $page = $request->query('page');
        $cari = $request->query('cari');

        $result = $this->JamaahPaket
            ->setCari($cari)
            ->setPage($page)
            ->setLimit(10)
            ->findByPerson($IDPerson);

        if ($result) return response()->json(['status' => true, 'data' => $result]);
        return response()->json(['status' => false, 'message' => 'data jamaah tidak ditemukan'], 404);
    }

    public function history(Request $request, $IDPerson)
    {
        $page = $request->query('page');
        $cari = $request->query('cari');

        $result = $this->JamaahPaket
            ->setCari($cari)
            ->setPage($page)
            ->setLimit(10)
            ->findHistoryPerson($IDPerson);

        if ($result) return response()->json(['status' => true, 'data' => $result]);
        return response()->json(['status' => false, 'message' => 'data jamaah tidak ditemukan'], 404);
    }

    public function historyagen(Request $request, $IDPerson)
    {
        $page = $request->query('page');
        $cari = $request->query('cari');

        $result = $this->JamaahPaket
            ->setCari($cari)
            ->setPage($page)
            ->setLimit(10)
            ->findHistoryAgen($IDPerson);

        if ($result) return response()->json(['status' => true, 'data' => $result]);
        return response()->json(['status' => false, 'message' => 'data jamaah tidak ditemukan'], 404);
    }

    public function paket(Request $request)
    {
        $IDTravel = $request->query('IDTravel');
        $IDPaket = $request->query('IDPaket');
        $page = $request->query('page');
        $cari = $request->query('cari');

        $result = $this->JamaahPaket
            ->setCari($cari)
            ->setPage($page)
            ->setLimit(10)
            ->findByPaket($IDTravel, $IDPaket);

        if ($result) return response()->json(['status' => true, 'data' => $result]);
        return response()->json(['status' => false, 'message' => 'data jamaah tidak ditemukan'], 404);
    }

    public function detail(Request $request)
    {
        $IDTravel = $request->query('IDTravel');
        $IDPaket = $request->query('IDPaket');
        $IDPerson = $request->query('IDPerson');

        $detailJamaah = $this->JamaahPaket->getDetail($IDTravel, $IDPaket, $IDPerson);
        if ($detailJamaah) {
            $detailJamaah->DokumenSyarat = $this->JamaahPaket->getDokumenSyarat($IDTravel, $IDPaket, $IDPerson);
            $detailJamaah->DetailPeserta = $this->MstPerson->getDetail($IDPerson);
            $detailJamaah->DetailAgen = $this->MstPerson->getDetail($detailJamaah->IDPersonAgen ?? null);
            $detailPaket = $this->MstPaket->detailInstance($detailJamaah->IDTravel, $detailJamaah->IDPaket);
            if ($detailPaket) {
                $detailPaket->syaratPaket = $this->MstPaket->getSyaratPaketInstance($IDTravel, $IDPaket);
                $detailPaket->travel = $this->MstTravel->detail($IDTravel);
            }
            $detailJamaah->DetailPaket = $detailPaket;

            $detailJamaah->AgenTravel = $this->AgenTravel->findAgentForAgenAndBooking($detailJamaah->IDPersonAgen ?? null, $detailJamaah->TanggalBooking ?? null);

            $detailJamaah->Peserta = $this->JamaahPaket->findPeserta($IDTravel, $IDPaket);

            return response()->json(['status' => true, 'data' => $detailJamaah]);
        }
        return response()->json(['status' => false, 'message' => 'data jamaah tidak ditemukan','data'=>$detailJamaah], 404);
    }

    public function index(Request $request)
    {
        $data = $request->all();

        $IDPaket = $data['IDPaket'] ?? null;
        $IDTravel = $data['IDTravel'] ?? null;

        $detilPaket = $this->MstPaket->detailInstance($IDTravel, $IDPaket);
        $syaratPaket = $this->MstPaket->getSyaratPaketInstance($IDTravel, $IDPaket);

        $IDCabang = $data['IDCabang'] ?? null;
        $IDPersonAgen = $data['IDPersonAgen'] ?? null;
        $JumlahTrip = $data['JumlahTrip'] ?? null;

        $InfoTagihan = $detilPaket->BiayaTrip ?? 0;
        if ($JumlahTrip == 4) $InfoTagihan = $detilPaket->BiayaQuad ?? $InfoTagihan;
        if ($JumlahTrip == 3) $InfoTagihan = $detilPaket->BiayaTriple ?? $InfoTagihan;
        if ($JumlahTrip == 2) $InfoTagihan = $detilPaket->BiayaDouble ?? $InfoTagihan;

        $IsPersonTerdaftar = false;
        $person = null;

        if (isset($data['IDPerson'])) {
            $IDPerson = $data['IDPerson'];
            $person = $this->MstPerson->getDetail($IDPerson);
            $IsPersonTerdaftar = $person != null;
        }

        if ($person == null) {
            if (empty($data['Email'])) return response()->json(['status' => false, 'message' => 'data email tidak valid'], 406);
            if ($this->MstPerson->getByEmail($data['Email'])) return response()->json(['status' => false, 'message' => 'data email telah digunakan'], 406);
            if (empty($data['NoHP'])) return response()->json(['status' => false, 'message' => 'data nomor telepon tidak valid'], 406);
            if ($this->MstPerson->getByNoHP($data['NoHP'])) return response()->json(['status' => false, 'message' => 'data nomor telepon telah digunakan'], 406);

            $KodeVerifikasi = $this->MstPerson::generateRandomString(6);

            $person = [
                'NamaJamaah' => $data['NamaJamaah'] ?? null,
                'NIK' => $data['NIK'] ?? null,
                'Email' => $data['Email'],
                'NoHP' => $data['NoHP'],
                'IDPerson' => $this->MstPerson->generateIDPerson(),
                'LastModified' => now()->format('Y-m-d H:i:s'),
                'Tgl_Register' => now()->format('Y-m-d H:i:s'),
                'StatusPerson' => config('constants.PERSON_JAMAAH') ?? 'JAMAAH',
                'UserName' => $data['NoHP'],
                'Password' => Hash::make($data['Password']),
                'KodeVerifikasi' => $KodeVerifikasi,
                'IsVerified' => 1,
                'IsAktif' => 1,
                'IDProv' => $data['IDProv'] ?? null,
                'IDKab' => $data['IDKab'] ?? null,
                'IDKec' => $data['IDKec'] ?? null,
                'IDDesa' => $data['IDDesa'] ?? null,
                'TempatLahir' => $data['TempatLahir'] ?? null,
                'TanggalLahir' => $data['TanggalLahir'] ?? null,
                'Alamat' => $data['Alamat'] ?? null,
            ];
        } else {
            $cekJamaahTerdaftar = $this->JamaahPaket->cekPersonTerdaftar($IDTravel, $IDPaket, $person->IDPerson ?? $person['IDPerson']);
            if (sizeof($cekJamaahTerdaftar) > 0) return response()->json(['status' => false, 'message' => 'data jamaah telah terdaftar pada paket ini'], 406);
        }

        $IDPerson = is_object($person) ? ($person->IDPerson ?? null) : ($person['IDPerson'] ?? null);
        if (!$IDPerson && is_array($person)) $IDPerson = $person['IDPerson'];
        if (!$IDPerson && isset($person['IDPerson'])) $IDPerson = $person['IDPerson'];

        $jamaahPaket = [
            'IDTravel' => $IDTravel,
            'IDPaket' => $IDPaket,
            'IDPerson' => $IDPerson,
            'IDPersonAgen' => $IDPersonAgen,
            'TanggalBooking' => now()->format('Y-m-d H:i:s'),
            'InfoTagihan' => $InfoTagihan,
            'InfoTerbayar' => 0,
            'InfoSisaTagihan' => $InfoTagihan,
            'NominalPaid' => 0,
            'StatusJamaah' => config('constants.ST_JAMAAH_JAMAAH') ?? 'JAMAAH',
            'IDCabang' => $IDCabang,
            'JumlahTrip' => $JumlahTrip,
        ];

        $dokumenSyarat = [];
        $syaratDir = 'assets/dokumen_syarat';
        $allowedFile = ['pdf','png','jpeg','jpg','bmp'];

        foreach ($syaratPaket as $syarat) {
            $param = $syarat->IDTravel . '_' . $syarat->IDPaket . '_' . $syarat->NoUrutSyarat;
            if ($request->hasFile($param)) {
                $upload = $this->doUpload($request, $param, $syaratDir, $allowedFile);
                if ($upload === 'error') {
                    return response()->json(['status' => false, 'message' => 'proses upload dokumen syarat ' . $syarat->NamaSyarat . ' gagal'], 406);
                }
                $dokumenSyarat[] = [
                    'IDTravel' => $syarat->IDTravel,
                    'IDPaket' => $syarat->IDPaket,
                    'NoUrutSyarat' => $syarat->NoUrutSyarat,
                    'IDPerson' => $IDPerson,
                    'FileDokumen' => $upload,
                    'Keterangan' => $syarat->Keterangan ?? null,
                    'TglUpload' => now()->format('Y-m-d H:i:s'),
                ];
            }
        }

        $saved = $this->JamaahPaket->registerJamaah(is_array($person) ? $person : (array) $person, $jamaahPaket, $dokumenSyarat, $IsPersonTerdaftar);

        if ($saved) return response()->json(['status' => true, 'message' => 'proses pendaftaran jamaah berhasil'], 201);
        return response()->json(['status' => false, 'message' => 'proses pendaftaran jamaah gagal, silahkan ulangi beberapa saat lagi'], 304);
    }

    public function dokumen(Request $request)
    {
        $syaratDir = 'assets/dokumen_syarat';
        $allowedFile = ['pdf','png','jpeg','jpg','bmp'];

        $IDTravel = $request->input('IDTravel');
        $IDPaket = $request->input('IDPaket');
        $IDPerson = $request->input('IDPerson');
        $NoUrutSyarat = $request->input('NoUrutSyarat');
        $Keterangan = $request->input('Keterangan');

        $detailDokumen = $this->JamaahPaket->findDokumen($IDTravel, $IDPaket, $IDPerson, $NoUrutSyarat);

        $param = 'FileDokumen';
        $file = $request->file($param);
        if (!$file) return response()->json(['status'=>false,'message'=>'proses upload dokumen syarat gagal'], 406);

        $upload = $this->doUpload($request, $param, $syaratDir, $allowedFile);
        if ($upload === 'error') return response()->json(['status'=>false,'message'=>'proses upload dokumen syarat gagal'], 406);

        $updated = $this->JamaahPaket->gantiDokumen($IDTravel, $IDPaket, $IDPerson, $NoUrutSyarat, $upload, $Keterangan);
        if (!$updated) return response()->json(['status'=>false,'message'=>'proses simpan dokumen syarat gagal'], 304);

        if ($detailDokumen && isset($detailDokumen->FileDokumen)) {
            $path = public_path($syaratDir . '/' . $detailDokumen->FileDokumen);
            if (file_exists($path)) @unlink($path);
        }

        return response()->json(['status' => true, 'message' => 'proses simpan dokumen syarat berhasil']);
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $IDPerson = $data['IDPerson'] ?? null;
        $updatePerson = [];
        if (isset($data['NamaJamaah'])) $updatePerson['NamaJamaah'] = $data['NamaJamaah'];
        if (isset($data['Email'])) {
            if ($this->MstPerson->getByEmail($data['Email'])) return response()->json(['status'=>false,'message'=>'alamat email telah digunakan'], 406);
            $updatePerson['Email'] = $data['Email'];
        }
        if (isset($data['NoHP'])) {
            if ($this->MstPerson->getByNoHP($data['NoHP'])) return response()->json(['status'=>false,'message'=>'nomor telepon telah digunakan'], 406);
            $updatePerson['NoHP'] = $data['NoHP'];
        }
        if (isset($data['NIK'])) {
            if ($this->MstPerson->getByNIK($data['NIK'])) return response()->json(['status'=>false,'message'=>'nomor NIK telah digunakan'], 406);
            $updatePerson['NIK'] = $data['NIK'];
        }
        if (isset($data['IDDesa'])) $updatePerson['IDDesa'] = $data['IDDesa'];
        if (isset($data['IDKec'])) $updatePerson['IDKec'] = $data['IDKec'];
        if (isset($data['IDKab'])) $updatePerson['IDKab'] = $data['IDKab'];
        if (isset($data['IDProv'])) $updatePerson['IDProv'] = $data['IDProv'];
        if (isset($data['TempatLahir'])) $updatePerson['TempatLahir'] = $data['TempatLahir'];
        if (isset($data['TanggalLahir'])) $updatePerson['TanggalLahir'] = $data['TanggalLahir'];
        if (isset($data['Alamat'])) $updatePerson['Alamat'] = $data['Alamat'];

        $updated = $this->MstPerson->updatePerson($IDPerson, $updatePerson);
        if ($updated) return response()->json(['status' => true, 'message' => 'proses update data jamaah berhasil'],201);
        return response()->json(['status' => false, 'message' => 'proses update data jamaah gagal'], 304);
    }

    public function alert(Request $request)
    {
        $IDPerson = $request->input('IDPerson');
        $IDPaket = $request->input('IDPaket');
        $IDTravel = $request->input('IDTravel');

        $toggled = $this->JamaahPaket->toggleAlert($IDTravel, $IDPaket, $IDPerson);
        if ($toggled) return response()->json(['status' => true, 'message' => 'update berhasil']);
        return response()->json(['status' => false, 'message' => 'update jamaah gagal'], 406);
    }

    public function testimoni(Request $request)
    {
        $IDPerson = $request->input('IDPerson');
        $IDTravel = $request->input('IDTravel');
        $IDPaket = $request->input('IDPaket');
        $NarasiTestimoni = $request->input('Testimoni');
        $RatingTestimoni = $request->input('RatingTestimoni');
        $TanggalTestimoni = $request->input('TanggalTestimoni');

        $updated = $this->JamaahPaket->updateTestimoni($IDTravel, $IDPaket, $IDPerson, $NarasiTestimoni, $RatingTestimoni, $TanggalTestimoni);
        if ($updated) return response()->json(['status' => true, 'message' => 'simpan testimoni berhasil']);
        return response()->json(['status' => false, 'message' => 'simpan testimoni gagal'], 406);
    }
}
