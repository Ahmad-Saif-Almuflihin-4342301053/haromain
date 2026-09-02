<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstPaket;
use App\Models\MstPerson;
use App\Models\ServerLog;
use App\Models\HotelPaket;

use App\Models\JamaahPaket;
use App\Models\SyaratPaket;
use App\Models\MstItenerary;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\MstMaskapai;
use App\Traits\DatatablesResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PaketController extends Controller
{
    use UploadFileTrait, DatatablesResponse;

    public function __construct(
        protected ServerLog $ServerLog,
        protected MstPaket $MstPaket,
        protected MstItenerary $MstItenerary,
        protected MstPerson $MstPerson,
        protected HotelPaket $HotelPaket,
        protected JamaahPaket $JamaahPaket,
        protected SyaratPaket $SyaratPaket,
    ) {}

    public function index()
    {
        return view('admin-page.paket.index', [
            'menu' => 'Manajemen Paket'
        ]);
    }

    public function tambah()
    {
        return view('admin-page.paket.tambah', [
            'menu' => 'Manajemen Paket',
            'JenisPenerbangan' => config('constants.JENIS_PENERBANGAN'),
            'KategoriPaket' => config('constants.KATEGORI_PAKET'),
            'Maskapai' => MstMaskapai::select('Maskapai')->where('IsAktif', 1)->get()
        ]);
    }

    public function edit(Request $request)
    {
        $id = base64_decode($request->query('id'));

        return view('admin-page.paket.tambah', [
            'menu'         => 'Manajemen Paket',
            'paket'        => MstPaket::where('IDPaket', $id)->first(),
            'syaratpaket'  => SyaratPaket::where('IDPaket', $id)->get()->toJson(),
            'hotelpaket'   => HotelPaket::where('IDPaket', $id)->get()->toJson(),
            'JenisPenerbangan' => config('constants.JENIS_PENERBANGAN'),
            'KategoriPaket' => config('constants.KATEGORI_PAKET'),
            'Maskapai' => MstMaskapai::select('Maskapai')->where('IsAktif', 1)->get()
        ]);
    }

    private function getFileExtension(string $base64): string
    {
        $mime = explode(':', explode(';', $base64)[0])[1];
        return strtolower(str_replace('image/', '', $mime));
    }

    public function simpan(Request $request)
    {
        DB::beginTransaction();
        try {

            $data     = $request->all();
            $IDPaket  = $data['id'] ?? null;
            $IDTravel = Auth::user()->IDTravel;

            $paket = [
                'IDTravel'         => $IDTravel,
                'TglBerangkat'     => $data['TglBerangkat'],
                'TglPulang'        => $data['TglPulang'],
                'Maskapai'         => $data['Maskapai'],
                'JenisPenerbangan' => $data['JenisPenerbangan'],
                'BerangkatDari'    => $data['BerangkatDari'],
                'LandingDi'        => $data['LandingDi'],
                'LamaHari'         => $data['LamaHari'] ?? (new \DateTime($data['TglPulang']))->diff(new \DateTime($data['TglBerangkat']))->days + 1,
                'BiayaQuad'        => str_replace(',', '', $data['BiayaQuad']),
                'BiayaTriple'      => str_replace(',', '', $data['BiayaTriple']),
                'BiayaDouble'      => str_replace(',', '', $data['BiayaDouble']),
                'TglMaxBayar'      => $data['TglMaxBayar'],
                'TglMaxBooking'    => $data['TglMaxBooking'],
                'Deskripsi'        => $data['Deskripsi'],
                'NamaPaket'        => $data['NamaPaket'],
                'KategoriPaket'    => $data['KategoriPaket'],
                'TglMaxDokumen'    => $data['TglMaxDokumen'],
                'KuotaPaket'       => $data['KuotaPaket'],
                'KotaTujuan'       => isset($data['KotaTujuan']) ? json_encode($data['KotaTujuan']) : null,
                'IncludeFasilitas' => isset($data['IncludeFasilitas']) ? json_encode($data['IncludeFasilitas']) : null,
                'ExcludeFasilitas' => isset($data['ExcludeFasilitas']) ? json_encode($data['ExcludeFasilitas']) : null,
            ];

            if (!empty($IDPaket) && $IDPaket !== 'null') {
                unset($paket['IDTravel']); // Prevent updating IDTravel to avoid FK constraint violation

                $gambar = [];

                if ($request->has('temp_Gambar')) {

                    foreach ($data['temp_Gambar'] as $i => $old) {

                        if ($data['Gambar'][$i]) {
                            try {

                                $base64 = $data['Gambar'][$i];

                                preg_match('/^data:image\/(\w+);base64,/', $base64, $match);
                                $ext = $match[1] ?? 'png';

                                [, $content] = explode(',', $base64);
                                $fileData = base64_decode($content);

                                $filename = uniqid('image_') . "." . $ext;
                                $path = public_path('assets/gambar-paket/' . $filename);

                                file_put_contents($path, $fileData);

                                if ($old) {

                                    $oldFilename = is_string($old) ? $old : ($old->getClientOriginalName() ?? null);
                                    if ($oldFilename) {
                                        $oldPath = public_path('assets/gambar-paket/' . $oldFilename);
                                        if (file_exists($oldPath)) {
                                            @unlink($oldPath);
                                        }
                                    }
                                }

                                $gambar[] = $filename;
                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                        } else {
                            // pakai gambar lama
                            $gambar[] = $old;
                        }
                    }

                    foreach ($gambar as $i => $val) {
                        $paket['Gambar' . ($i + 1)] = $val;
                    }
                } else {
                    $paket['Gambar1'] = NULL;
                    $paket['Gambar2'] = NULL;
                    $paket['Gambar3'] = NULL;
                    $paket['Gambar4'] = NULL;
                    $paket['Gambar5'] = NULL;

                    $fileToDelete = MstPaket::select(
                        'Gambar1',
                        'Gambar2',
                        'Gambar3',
                        'Gambar4',
                        'Gambar5'
                    )->where('IDPaket', $IDPaket)->first();

                    foreach (range(1, 5) as $i) {
                        $field = 'Gambar' . $i;

                        if ($fileToDelete->$field) {
                            $path = public_path('assets/gambar-paket/' . $fileToDelete->$field);

                            if (File::exists($path)) {
                                File::delete($path);
                            }
                        }
                    }
                    Log::info($fileToDelete);
                }



                if ($request->has('NamaSyarat')) {
                    foreach ($data['NamaSyarat'] as $i => $value) {
                        if (!empty($data['NoUrutSyarat'][$i])) {
                            SyaratPaket::where('NoUrutSyarat', $data['NoUrutSyarat'][$i])
                                ->update([
                                    'NamaSyarat' => $value,
                                    'Keterangan' => $data['Keterangan'][$i] ?? null,
                                    // Remove explicit FK update if not needed, or ensure it matches
                                ]);
                        } else {
                            SyaratPaket::create([
                                'NoUrutSyarat' => $this->SyaratPaket->generateID(),
                                'NamaSyarat'   => $value,
                                'Keterangan'   => $data['Keterangan'][$i] ?? null,
                                'IDPaket'      => $IDPaket,
                                'IDTravel'     => $IDTravel
                            ]);
                        }
                    }
                }

                if ($request->has('NamaHotel')) {
                    HotelPaket::where('IDPaket', $IDPaket)->delete();

                    foreach ($data['NamaHotel'] as $i => $val) {
                        HotelPaket::create([
                            'IDHotel'     => $this->HotelPaket->generateID(),
                            'NamaHotel'   => $val,
                            'Bintang'     => $data['Bintang'][$i],
                            'AlamatHotel' => $data['AlamatHotel'][$i],
                            'TglMasuk'    => $data['TglMasuk'][$i],
                            'TglKeluar'   => $data['TglKeluar'][$i],
                            'KoorLat'     => $data['KoorLat'][$i],
                            'KoorLong'    => $data['KoorLong'][$i],
                            'IDPaket'     => $IDPaket,
                            'IDTravel'    => $IDTravel,
                        ]);
                    }
                }
                MstPaket::where('IDPaket', $IDPaket)->update($paket);
                // Log::error($paket);
                $this->ServerLog->insertLog($IDPaket, 'Edit', 'Paket');
            } else {


                $gambar = [];
                $paket['Gambar1'] = null;
                $paket['Gambar2'] = null;
                $paket['Gambar3'] = null;
                $paket['Gambar4'] = null;
                $paket['Gambar5'] = null;

                if (isset($data['temp_Gambar'])) {
                    foreach ($data['temp_Gambar'] as $key => $old) {
                        $uploadedFile = null;
                        if ($request->hasFile('Gambar')) {
                            $files = $request->file('Gambar');
                            if (is_array($files) && isset($files[$key])) {
                                $uploadedFile = $files[$key];
                            }
                        }

                        if ($uploadedFile && $uploadedFile->isValid()) {
                            $ext = $uploadedFile->getClientOriginalExtension() ?: 'png';
                            $filename = uniqid('image_') . '.' . $ext;
                            $uploadedFile->move(public_path('assets/gambar-paket'), $filename);
                            $gambar[] = $filename;
                        } elseif (!empty($data['Gambar'][$key]) && str_contains($data['Gambar'][$key], 'base64,')) {

                            [$meta, $content] = explode(',', $data['Gambar'][$key]);
                            $fileData = base64_decode($content);

                            if (preg_match('/^data:image\/(\w+);base64,/', $data['Gambar'][$key], $m)) {
                                $ext = $m[1] ?? 'png';
                            } else {
                                $ext = 'png';
                            }

                            $filename = uniqid('image_') . '.' . $ext;
                            $path = public_path('assets/gambar-paket/' . $filename);
                            file_put_contents($path, $fileData);
                            $gambar[] = $filename;
                        } else {
                            $gambar[] = $old;
                        }
                    }

                    foreach ($gambar as $i => $val) {
                        $paket['Gambar' . ($i + 1)] = $val;
                    }
                }

                $paket['IDPaket'] = $this->MstPaket->generateID();
                $paket['IsAktif'] = 1;

                MstPaket::create($paket);

                if ($request->has('NamaSyarat')) {
                    foreach ($data['NamaSyarat'] as $i => $value) {
                        SyaratPaket::create([
                            'NoUrutSyarat' => $this->SyaratPaket->generateID(),
                            'NamaSyarat'   => $value,
                            'Keterangan'   => $data['Keterangan'][$i] ?? null,
                            'IDPaket'      => $paket['IDPaket'],
                            'IDTravel'     => $IDTravel
                        ]);
                    }
                }

                if ($request->has('NamaHotel')) {
                    foreach ($data['NamaHotel'] as $i => $value) {
                        HotelPaket::create([
                            'IDHotel'     => $this->HotelPaket->generateID(),
                            'NamaHotel'   => $value,
                            'Bintang'     => $data['Bintang'][$i],
                            'AlamatHotel' => $data['AlamatHotel'][$i],
                            'TglMasuk'    => $data['TglMasuk'][$i],
                            'TglKeluar'   => $data['TglKeluar'][$i],
                            'KoorLat'     => $data['KoorLat'][$i],
                            'KoorLong'    => $data['KoorLong'][$i],
                            'IDPaket'     => $paket['IDPaket'],
                            'IDTravel'    => $IDTravel,
                        ]);
                    }
                }

                $this->ServerLog->insertLog($paket['IDPaket'], 'Tambah', 'Paket');

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Berhasil menambah data',
                    'redirect' => route('admin.paket.index')
                ]);
            }


            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menyimpan data',
                'redirect' => route('admin.paket.index')
            ]);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function setAktif(Request $request)
    {
        $id       = $request->query('id');
        $isAktif  = $request->query('isaktif');

        if ($isAktif == 1) {
            MstPaket::where('IDPaket', $id)->update(['IsAktif' => 0]);
            $this->ServerLog->insertLog($id, 'Nonaktifkan', 'Paket');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menonaktifkan data'
            ]);
        }

        MstPaket::where('IDPaket', $id)->update(['IsAktif' => 1]);
        $this->ServerLog->insertLog($id, 'Aktifkan', 'Paket');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengaktifkan data'
        ]);
    }


    public function hapus(Request $request)
    {
        try {
            $id = $request->query('id');

            SyaratPaket::where('IDPaket', $id)->delete();
            MstPaket::where('IDPaket', $id)->delete();

            $this->ServerLog->insertLog($id, 'Hapus', 'Paket');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus data'
            ]);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data'
            ], 500);
        }
    }

    public function leader(Request $request)
    {
        try {
            $id = $request->query('id');
            $leaders = $request->query('leaders');
            $leaderSingle = $request->query('leader');

            if ($leaders === null && $leaderSingle !== null) {
                $leaders = $leaderSingle;
            }

            if (is_string($leaders)) {
                $leaders = array_values(array_filter(array_map('trim', explode(',', $leaders))));
            } elseif (!is_array($leaders)) {
                $leaders = [];
            }

            $idTravel = Auth::user()->IDTravel ?? null;

            $resetQuery = JamaahPaket::where('IDPaket', $id)
                ->where('StatusJamaah', 'Leader');
            if ($idTravel) {
                $resetQuery->where('IDTravel', $idTravel);
            }
            $resetQuery->update(['StatusJamaah' => 'Jamaah']);

            if (!empty($leaders)) {
                $setQuery = JamaahPaket::where('IDPaket', $id);
                if ($idTravel) {
                    $setQuery->where('IDTravel', $idTravel);
                }

                $setQuery->whereIn('IDPerson', $leaders)
                    ->update(['StatusJamaah' => 'Leader']);
            }

            $this->ServerLog->insertLog($id, 'Edit', 'Tour Leader');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengubah data'
            ]);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengubah data'
            ], 500);
        }
    }

    public function find(Request $request)
    {
        try {
            $id = $request->query('id');

            $data = $this->MstPaket->findJoinPerson([
                'jamaahpaket.IDPaket' => $id,
                'jamaahpaket.IDTravel' => Auth::user()->IDTravel,
                'mstperson.StatusPerson' => 'Jamaah'
            ]);

            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Sistem error'
            ], 500);
        }
    }

    public function hapusSyarat(Request $request)
    {
        try {
            $no = $request->query('no');

            SyaratPaket::where('NoUrutSyarat', $no)->delete();

            $this->ServerLog->insertLog($no, 'Hapus', 'Syarat Paket');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus syarat paket'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus syarat paket'
            ], 500);
        }
    }

    public function get(Request $request)
    {
        $list = $this->MstPaket->get_datatables();

        return $this->datatablesResponse(
            $request,
            $list,
            $this->MstPaket->count_all(),
            $this->MstPaket->count_filtered(),
            function ($row) {
                $row->Dokumen = $this->getSyaratPaket($row->IDPaket);
            }
        );
    }

    private function getSyaratPaket($IDPaket): string
    {
        return SyaratPaket::where('IDPaket', $IDPaket)
            ->pluck('NamaSyarat')
            ->implode(', ');
    }

    public function itenerary(Request $request)
    {
        $idpaket  = base64_decode($request->query('pkt'));
        $idtravel = base64_decode($request->query('trv'));

        return view('admin-page.paket.rundown', [
            'title'    => 'Travel Itenerary',
            'menu'     => 'Manajemen Paket',
            'paket'    => MstPaket::where('IDPaket', $idpaket)->first(),
            'idpaket'  => $idpaket,
            'idtravel' => $idtravel
        ]);
    }

    public function getItenerary(Request $request)
    {
        $idpaket  = $request->input('idpaket');
        $idtravel = $request->input('idtravel');

        $list = $this->MstItenerary->getDatatables($idpaket, $idtravel);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->MstItenerary->countAll($idpaket, $idtravel),
            $this->MstItenerary->countFiltered($idpaket, $idtravel)
        );
    }

    public function tambahItenerary(Request $request)
    {
        $idpaket  = base64_decode($request->query('pkt'));
        $idtravel = base64_decode($request->query('trv'));

        $paket = MstPaket::where('IDPaket', $idpaket)->first();

        return view('admin-page.paket.tambah_itenerary', [
            'title'    => 'Tambah Travel Itenerary',
            'menu'     => 'Manajemen Paket',
            'idpaket'  => $idpaket,
            'idtravel' => $idtravel,
            'paket'    => $paket,
            'datahari' => $this->generateDateOptions(
                $paket->TglBerangkat,
                $paket->TglPulang
            ),
            'scripts'  => 'admin-page/paket/tambah_itenerary_scripts'
        ]);
    }

    private function generateDateOptions($startDate, $endDate): array
    {
        $start = new \DateTime($startDate);
        $end   = new \DateTime($endDate);

        $interval = $end->diff($start)->days + 1;
        $result = [];

        for ($i = 0; $i < $interval; $i++) {
            $result[] = [
                'harike'  => $i + 1,
                'tanggal' => $start->format('Y-m-d')
            ];
            $start->modify('+1 day');
        }

        return $result;
    }

    private function normalizeExcelString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $str = trim((string) $value);
        return $str === '' ? null : $str;
    }

    private function parseExcelDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        $str = $this->normalizeExcelString($value);
        if (!$str) {
            return null;
        }

        try {
            return Carbon::parse($str)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseExcelTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            $fraction = (float) $value;
            if ($fraction < 0 || $fraction >= 2) {
                return null;
            }
            $seconds = (int) round(($fraction - floor($fraction)) * 86400);
            $hours = (int) floor($seconds / 3600);
            $minutes = (int) floor(($seconds % 3600) / 60);
            $secs = (int) ($seconds % 60);
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }

        $str = $this->normalizeExcelString($value);
        if (!$str) {
            return null;
        }

        $str = str_replace('.', ':', $str);
        if (preg_match('/^\d{1,2}:\d{2}$/', $str)) {
            $str .= ':00';
        }
        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $str)) {
            return null;
        }

        [$h, $m, $s] = array_map('intval', explode(':', $str));
        if ($h < 0 || $h > 23 || $m < 0 || $m > 59 || $s < 0 || $s > 59) {
            return null;
        }

        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    public function editItenerary(Request $request)
    {
        $idpaket  = base64_decode($request->query('pkt'));
        $idtravel = base64_decode($request->query('trv'));
        $nourut   = base64_decode($request->query('nrt'));

        return view('admin-page.paket.tambah_rundown', [
            'title'    => 'Tambah Travel Itenerary',
            'menu'     => 'Manajemen Paket',
            'data'     => MstItenerary::where([
                'IDPaket'  => $idpaket,
                'IDTravel' => $idtravel,
                'NoUrut'   => $nourut
            ])->first(),
            'idpaket'  => $idpaket,
            'idtravel' => $idtravel
        ]);
    }

    public function downloadIteneraryTemplate(string $encodedIDPaket, string $encodedIDTravel)
    {
        $encodedIDPaket  = strtr($encodedIDPaket, '._-', '+/=');
        $encodedIDTravel = strtr($encodedIDTravel, '._-', '+/=');

        $IDPaket  = base64_decode($encodedIDPaket);
        $IDTravel = base64_decode($encodedIDTravel);

        $paket = MstPaket::where('IDPaket', $IDPaket)->where('IDTravel', $IDTravel)->first();
        if (!$paket) {
            abort(404);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Itenerary');

        $headings = ['HariKe', 'JamMulai', 'JamSelesai', 'Agenda', 'Lokasi'];
        $sheet->fromArray($headings, null, 'A1');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        $sheet->setCellValueExplicit('A2', 1, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $sheet->setCellValueExplicit('B2', '10:00', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('C2', '14:00', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('D2', 'Contoh Agenda', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('E2', 'Contoh Lokasi', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'format_itenerary_' . $IDPaket . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importIteneraryExcel(Request $request, string $encodedIDPaket, string $encodedIDTravel)
    {
        $encodedIDPaket  = strtr($encodedIDPaket, '._-', '+/=');
        $encodedIDTravel = strtr($encodedIDTravel, '._-', '+/=');

        $IDPaket  = base64_decode($encodedIDPaket);
        $IDTravel = base64_decode($encodedIDTravel);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $paket = MstPaket::where('IDPaket', $IDPaket)->where('IDTravel', $IDTravel)->first();
        if (!$paket) {
            return response()->json([
                'status' => false,
                'message' => 'Paket tidak ditemukan'
            ], 404);
        }

        $startBoundary = Carbon::parse($paket->TglBerangkat)->startOfDay();
        $endBoundary = Carbon::parse($paket->TglPulang)->endOfDay();

        $failed = [];
        $parsed = [];

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestDataRow();

            for ($row = 2; $row <= $highestRow; $row++) {
                $hariKeRaw = $sheet->getCell("A{$row}")->getValue();
                $mulaiRaw = $sheet->getCell("B{$row}")->getValue();
                $selesaiRaw = $sheet->getCell("C{$row}")->getValue();
                $agendaRaw = $sheet->getCell("D{$row}")->getValue();
                $lokasiRaw = $sheet->getCell("E{$row}")->getValue();

                $hariKeStr = $this->normalizeExcelString($hariKeRaw);
                $agenda = $this->normalizeExcelString($agendaRaw);
                $lokasi = $this->normalizeExcelString($lokasiRaw);

                $isEmptyRow = !$hariKeStr && !$this->normalizeExcelString($mulaiRaw) && !$this->normalizeExcelString($selesaiRaw) && !$agenda && !$lokasi;
                if ($isEmptyRow) {
                    continue;
                }

                $hariKe = $hariKeStr !== null && is_numeric($hariKeStr) ? (int) $hariKeStr : null;
                if (!$hariKe || $hariKe < 1) {
                    $failed[] = ['row' => $row, 'reason' => 'HariKe wajib angka >= 1'];
                    continue;
                }

                $jamMulai = $this->parseExcelTime($mulaiRaw);
                $jamSelesai = $this->parseExcelTime($selesaiRaw);
                if (!$jamMulai || !$jamSelesai) {
                    $failed[] = ['row' => $row, 'reason' => 'JamMulai/JamSelesai tidak valid (gunakan HH:MM atau HH:MM:SS)'];
                    continue;
                }

                $tanggalMulai = $startBoundary->copy()->addDays($hariKe - 1)->format('Y-m-d');
                try {
                    $mulaiC = Carbon::createFromFormat('Y-m-d H:i:s', $tanggalMulai . ' ' . $jamMulai);
                    $selesaiC = Carbon::createFromFormat('Y-m-d H:i:s', $tanggalMulai . ' ' . $jamSelesai);
                    if ($selesaiC->lessThanOrEqualTo($mulaiC)) {
                        $selesaiC = $selesaiC->addDay();
                    }
                    if ($mulaiC->lt($startBoundary) || $mulaiC->gt($endBoundary) || $selesaiC->lt($startBoundary) || $selesaiC->gt($endBoundary)) {
                        $failed[] = ['row' => $row, 'reason' => 'Waktu di luar rentang paket'];
                        continue;
                    }
                } catch (\Throwable) {
                    $failed[] = ['row' => $row, 'reason' => 'JamMulai/JamSelesai tidak valid'];
                    continue;
                }

                if (!$agenda) {
                    $failed[] = ['row' => $row, 'reason' => 'Agenda wajib diisi'];
                    continue;
                }

                if (!$lokasi) {
                    $failed[] = ['row' => $row, 'reason' => 'Lokasi wajib diisi'];
                    continue;
                }

                $parsed[] = [
                    'HariKe' => $hariKe,
                    'TglMulai' => $mulaiC->format('Y-m-d H:i:s'),
                    'TglSelesai' => $selesaiC->format('Y-m-d H:i:s'),
                    'Agenda' => $agenda,
                    'Lokasi' => $lokasi,
                ];
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membaca file excel'
            ], 422);
        }

        if (count($parsed) < 1) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada data yang bisa diimport',
                'success_count' => 0,
                'failed' => $failed
            ], 422);
        }

        usort($parsed, function ($a, $b) {
            if ($a['HariKe'] === $b['HariKe']) {
                if ($a['TglMulai'] === $b['TglMulai']) {
                    return strcmp($a['TglSelesai'], $b['TglSelesai']);
                }
                return strcmp($a['TglMulai'], $b['TglMulai']);
            }
            return $a['HariKe'] <=> $b['HariKe'];
        });

        $groups = [];
        foreach ($parsed as $item) {
            $key = (string) $item['HariKe'];
            $groups[$key][] = $item;
        }

        DB::beginTransaction();

        try {
            $hariKeList = array_values(array_unique(array_map(function ($v) {
                return (int) $v;
            }, array_keys($groups))));

            $existing = DB::table('mstitenerary')
                ->select('NoUrut', 'HariKe', 'TglMulai', 'TglSelesai')
                ->where('IDPaket', $IDPaket)
                ->where('IDTravel', $IDTravel)
                ->where('IsAktif', 1)
                ->whereIn('HariKe', $hariKeList)
                ->get();

            $existingMap = [];
            $maxNoUrut = 0;
            foreach ($existing as $row) {
                $maxNoUrut = max($maxNoUrut, (int) $row->NoUrut);
                $key = ((int) $row->HariKe) . '|' . Carbon::parse($row->TglMulai)->format('Y-m-d H:i:s') . '|' . Carbon::parse($row->TglSelesai)->format('Y-m-d H:i:s');
                $existingMap[$key] = (int) $row->NoUrut;
            }

            $noUrut = $maxNoUrut > 0 ? ($maxNoUrut + 1) : $this->MstItenerary->generateNoUrut($IDPaket, $IDTravel);
            $insertRows = [];
            $updatedCount = 0;
            foreach ($parsed as $item) {
                $key = ((int) $item['HariKe']) . '|' . $item['TglMulai'] . '|' . $item['TglSelesai'];
                $existingNoUrut = $existingMap[$key] ?? null;

                if ($existingNoUrut) {
                    DB::table('mstitenerary')
                        ->where('IDPaket', $IDPaket)
                        ->where('IDTravel', $IDTravel)
                        ->where('NoUrut', $existingNoUrut)
                        ->update([
                            'TglMulai' => $item['TglMulai'],
                            'TglSelesai' => $item['TglSelesai'],
                            'Lokasi' => $item['Lokasi'],
                            'Agenda' => $item['Agenda'],
                            'HariKe' => $item['HariKe'],
                            'IsAktif' => 1,
                        ]);
                    $updatedCount++;
                    continue;
                }

                $insertRows[] = [
                    'NoUrut' => $noUrut++,
                    'IDPaket' => $IDPaket,
                    'IDTravel' => $IDTravel,
                    'TglMulai' => $item['TglMulai'],
                    'TglSelesai' => $item['TglSelesai'],
                    'KoorLat' => null,
                    'KoorLong' => null,
                    'Lokasi' => $item['Lokasi'],
                    'Agenda' => $item['Agenda'],
                    'HariKe' => $item['HariKe'],
                    'IsAktif' => 1,
                ];
            }

            if (count($insertRows) > 0) {
                DB::table('mstitenerary')->insert($insertRows);
            }

            $notrans = $IDPaket . '_' . $IDTravel . '_IMPORT';
            $this->ServerLog->insertLog($notrans, 'Import', 'Travel Itenerary');

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Import itenerary berhasil',
                'success_count' => count($insertRows),
                'updated_count' => $updatedCount,
                'failed' => $failed
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat import itenerary'
            ], 500);
        }
    }

    public function simpanItenerary(
        Request $request,
        string $encodedIDPaket,
        string $encodedIDTravel
    ) {
        $encodedIDPaket  = strtr($encodedIDPaket, '._-', '+/=');
        $encodedIDTravel = strtr($encodedIDTravel, '._-', '+/=');

        $IDPaket  = base64_decode($encodedIDPaket);
        $IDTravel = base64_decode($encodedIDTravel);

        $hariKe  = $request->input('HariKe');

        DB::beginTransaction();

        try {
            $paket = MstPaket::where('IDPaket', $IDPaket)->where('IDTravel', $IDTravel)->first();
            if (!$paket) {
                return response()->json([
                    'status' => false,
                    'message' => 'Paket tidak ditemukan'
                ], 404);
            }

            $startBoundary = Carbon::parse($paket->TglBerangkat)->startOfDay();
            $endBoundary = Carbon::parse($paket->TglPulang)->endOfDay();

            $tanggalMulai = $startBoundary->copy()->addDays(((int) $hariKe) - 1)->format('Y-m-d');

            MstItenerary::where('IDPaket', $IDPaket)
                ->where('IDTravel', $IDTravel)
                ->where('HariKe', $hariKe)
                ->delete();

            foreach ($request->input('TglMulai') as $i => $mulaiRaw) {
                $jamMulai = $this->parseExcelTime($mulaiRaw);
                $jamSelesai = $this->parseExcelTime($request->input('TglSelesai')[$i] ?? null);
                if (!$jamMulai || !$jamSelesai) {
                    continue;
                }

                try {
                    $startDt = Carbon::createFromFormat('Y-m-d H:i:s', $tanggalMulai . ' ' . $jamMulai);
                    $endDt = Carbon::createFromFormat('Y-m-d H:i:s', $tanggalMulai . ' ' . $jamSelesai);
                    if ($endDt->lessThanOrEqualTo($startDt)) {
                        $endDt = $endDt->addDay();
                    }

                    if ($startDt->lt($startBoundary) || $startDt->gt($endBoundary) || $endDt->lt($startBoundary) || $endDt->gt($endBoundary)) {
                        continue;
                    }
                } catch (\Throwable) {
                    continue;
                }
                MstItenerary::create([
                    'IDPaket'   => $IDPaket,
                    'IDTravel'  => $IDTravel,
                    'NoUrut'    => $this->MstItenerary->generateNoUrut($IDPaket, $IDTravel),
                    'TglMulai'  => $startDt->format('Y-m-d H:i:s'),
                    'TglSelesai' => $endDt->format('Y-m-d H:i:s'),
                    'Agenda'    => $request->input('Agenda')[$i],
                    'Lokasi'    => $request->input('Lokasi')[$i],
                    'HariKe'    => $hariKe,
                    'IsAktif'   => 1,
                ]);
            }

            DB::commit();

            $notrans = $IDPaket . '_' . $IDTravel . '_' . $hariKe;
            $this->ServerLog->insertLog($notrans, 'Tambah', 'Travel Itenerary');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menambah data',
                'redirect' => route('paket.tambah_itenerary', [
                    'pkt' => base64_encode($IDPaket),
                    'trv' => base64_encode($IDTravel)
                ])
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan itenerary'
            ], 500);
        }
    }

    public function hapusItenerary(Request $request)
    {
        try {
            MstItenerary::where('IDPaket', $request->idpaket)
                ->where('IDTravel', $request->idtravel)
                ->where('NoUrut', $request->nourut)
                ->update(['IsAktif' => 0]);

            $notrans = $request->idpaket . '_' . $request->idtravel . '_' . $request->nourut;

            $this->ServerLog->insertLog($notrans, 'Hapus', 'Travel Itenerary');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus data'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data'
            ]);
        }
    }

    public function getIteneraryHari($encodedIDPaket, $encodedIDTravel, $HariKe)
    {
        $encodedIDPaket = strtr($encodedIDPaket, '._-', '+/=');
        $encodedIDTravel = strtr($encodedIDTravel, '._-', '+/=');

        $IDPaket = base64_decode($encodedIDPaket);
        $IDTravel = base64_decode($encodedIDTravel);

        $data = MstItenerary::where('IDPaket', $IDPaket)
            ->where('IDTravel', $IDTravel)
            ->where('HariKe', $HariKe)
            ->where('IsAktif', 1)
            ->get();

        if ($data->count()) {
            $data->transform(function ($row) {
                $row->TglMulai   = date('H:i:s', strtotime($row->TglMulai));
                $row->TglSelesai = date('H:i:s', strtotime($row->TglSelesai));
                return $row;
            });

            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        }

        return response()->json([
            'status' => false,
            'data' => []
        ]);
    }

    public function hapusSemuaItenerary(Request $request)
    {
        try {
            $idpaket = base64_decode(strtr($request->idpaket, '._-', '+/='));
            $idtravel = base64_decode(strtr($request->idtravel, '._-', '+/='));

            MstItenerary::where('IDPaket', $idpaket)
                ->where('IDTravel', $idtravel)
                ->update(['IsAktif' => 0]);

            $notrans = $idpaket . '_' . $idtravel;

            $this->ServerLog->insertLog($notrans, 'Hapus Semua Itenerary', 'Travel Itenerary');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus data'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data'
            ]);
        }
    }
}
