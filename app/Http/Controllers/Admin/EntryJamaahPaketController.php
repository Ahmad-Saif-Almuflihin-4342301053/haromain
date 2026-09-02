<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JamaahPaket;
use App\Models\MstPaket;
use App\Models\MstCabang;
use App\Models\MstPerson;
use App\Models\MstTravel;
use App\Models\Lokasi;
use App\Models\ServerLog;
use App\Models\AgenTravel;
use App\Traits\DatatablesResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\JamaahPaketTemplateExport;
use App\Imports\JamaahPaketImport;
use Maatwebsite\Excel\Facades\Excel;

class EntryJamaahPaketController extends Controller
{
    use DatatablesResponse;

    protected $IDTravel;
    protected $lokasi;
    protected $MstPerson;
    protected $agenTravel;
    protected $jamaahPaket;
    protected $MstPaket;
    protected $MstCabang;
    protected $ServerLog;
    protected $MstTravel;

    public function __construct(
        Lokasi $lokasi,
        MstPerson $MstPerson,
        AgenTravel $agenTravel,
        JamaahPaket $jamaahPaket,
        MstPaket $MstPaket,
        MstCabang $MstCabang,
        ServerLog $ServerLog,
        MstTravel $MstTravel
    ) {
        $this->IDTravel = Auth::user()->IDTravel;
        $this->lokasi = $lokasi;
        $this->MstPerson = $MstPerson;
        $this->agenTravel = $agenTravel;
        $this->jamaahPaket = $jamaahPaket;
        $this->MstPaket = $MstPaket;
        $this->MstCabang = $MstCabang;
        $this->ServerLog = $ServerLog;
        $this->MstTravel = $MstTravel;
    }

    public function index(Request $request)
    {
        $IDPerson = $request->query('jamaah');
        $IDPerson = $IDPerson ? base64_decode($IDPerson) : null;

        $no_hp = $request->query('no_hp');
        $jamaah = null;

        if ($no_hp) {
            $jamaah = MstPerson::where('NoHP', $no_hp)->first();
            if ($jamaah) $IDPerson = $jamaah->IDPerson;
        } elseif ($IDPerson) {
            $jamaah = MstPerson::where('IDPerson', $IDPerson)->first();
        }

        $paket = MstPaket::where('IsAktif', 1)
            ->where('IDTravel', $this->IDTravel)
            ->get();

        $cabang = MstCabang::where('IsAktif', 1)
            ->where('IDTravel', $this->IDTravel)
            ->get();

        $agentravel = $this->agenTravel->getJoinPerson([
            'IDTravel' => $this->IDTravel,
            'agentravel.IsAktif' => 1
        ]);

        $data = [
            'menu' => 'Daftarkan Paket',
            'paket' => $paket,
            'cabang' => $cabang,
            'jamaah' => $jamaah,
            'agentravel' => $agentravel
        ];

        if ($IDPerson) {
            $data['prov'] = $this->lokasi->findProvinsi();
            if (isset($jamaah->IDProv)) {
                $data['kab'] = $this->lokasi->setIDProv($jamaah->IDProv)->setIDKab(null)->findKabupaten();
                if (isset($jamaah->IDKab)) {
                    $data['kec'] = $this->lokasi->setIDProv($jamaah->IDProv)->setIDKab($jamaah->IDKab)->setIDKec(null)->findKecamatan();
                    if (isset($jamaah->IDKec)) {
                        $data['desa'] = $this->lokasi->setIDProv($jamaah->IDProv)->setIDKab($jamaah->IDKab)->setIDKec($jamaah->IDKec)->setIDDesa(null)->findDesa();
                    }
                }
            }
        }

        return view('admin-page.jamaah.tambah-jamaahpaket', $data);
    }

    public function simpan(Request $request)
    {
        $IDPerson = $request->id;

        $exists = JamaahPaket::where('IDPerson', $IDPerson)
            ->where('IDPaket', $request->IDPaket)
            ->first();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Jamaah sudah terdaftar di paket ini'
            ]);
        }

        $jamaah_paket = [
            'IDPaket' => $request->IDPaket,
            'IDPerson' => $IDPerson,
            'IDTravel' => $this->IDTravel,
            'IDCabang' => $request->IDCabang,
            'IsPaid' => 0,
            'IsAlert' => 0,
            'IDPersonAgen' => $request->IDPersonAgen,
            'StatusJamaah' => 'Jamaah',
            'TanggalBooking' => now(),
            'JumlahTrip' => $request->JumlahTrip,
            'InfoTagihan' => $this->getInfoTagihan($request->IDPaket, $request->JumlahTrip),
            'InfoSisaTagihan' => $this->getInfoTagihan($request->IDPaket, $request->JumlahTrip)
        ];

        JamaahPaket::create($jamaah_paket);

        $this->ServerLog->insertLog($IDPerson, 'Tambah jamaah paket', 'Jamaah');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil menambah data jamaah paket',
            'redirect' => route('admin.jamaah.index'),
            'print_url' => route('admin.entryjamaahpaket.cetak_nota', [
                'IDPaket' => base64_encode($request->IDPaket),
                'IDPerson' => base64_encode($IDPerson)
            ])
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt',
            'IDPaket' => 'required',
            'IDCabang' => 'required',
            'JumlahTrip' => 'required',
            'IDPersonAgen' => 'required',
        ]);

        $file = $request->file('file');
        
        $data = Excel::toArray(new JamaahPaketImport, $file);
        $data = $data[0] ?? []; // First sheet
        
        // Remove header if it contains "No HP"
        if (isset($data[0]) && stripos(json_encode($data[0]), 'No HP') !== false) {
            array_shift($data);
        }

        $success = 0;
        $failed = [];
        
        // Pre-fetch package info for tagihan
        $biaya = 0;
        $paket = MstPaket::where('IDPaket', $request->IDPaket)->first();
        if ($paket) {
             $column = match ($request->JumlahTrip) {
                '2' => 'BiayaDouble',
                '3' => 'BiayaTriple',
                '4' => 'BiayaQuad',
                default => null
            };
            if ($column && !empty($paket->$column)) {
                $biaya = $paket->$column;
            }
        }

        foreach ($data as $row) {
            if (count($row) < 2) continue;

            $noHP = trim($row[1]);
            
            if (empty($noHP)) continue;

            $jamaah = $this->MstPerson->where('NoHP', $noHP)->first();

            if (!$jamaah) {
                $failed[] = [
                    'no_hp' => $noHP,
                    'nama' => $row[2] ?? '',
                    'reason' => 'Jamaah belum terdaftar (No HP tidak ditemukan)'
                ];
                continue;
            }

            // Check existing registration
            $exists = JamaahPaket::where('IDPerson', $jamaah->IDPerson)
                ->where('IDPaket', $request->IDPaket)
                ->exists();

            if ($exists) {
                $failed[] = [
                    'no_hp' => $noHP,
                    'nama' => $jamaah->NamaJamaah,
                    'reason' => 'Sudah terdaftar di paket ini'
                ];
                continue;
            }

            try {
                JamaahPaket::create([
                    'IDPaket' => $request->IDPaket,
                    'IDPerson' => $jamaah->IDPerson,
                    'IDTravel' => $this->IDTravel,
                    'IDCabang' => $request->IDCabang,
                    'IsPaid' => 0,
                    'IsAlert' => 0,
                    'IDPersonAgen' => $request->IDPersonAgen,
                    'StatusJamaah' => 'Jamaah',
                    'TanggalBooking' => now(),
                    'JumlahTrip' => $request->JumlahTrip,
                    'InfoTagihan' => $biaya,
                    'InfoSisaTagihan' => $biaya
                ]);

                $this->ServerLog->insertLog($jamaah->IDPerson, 'Import Jamaah Paket', 'Jamaah');
                $success++;
            } catch (\Exception $e) {
                $failed[] = [
                    'no_hp' => $noHP,
                    'nama' => $jamaah->NamaJamaah,
                    'reason' => 'Error: ' . $e->getMessage()
                ];
            }
        }

        return response()->json([
            'status' => true,
            'success_count' => $success,
            'failed' => $failed
        ]);
    }

    public function downloadTemplate()
    {
        return Excel::download(new JamaahPaketTemplateExport, 'template_jamaah_paket.xlsx');
    }

    public function cetakNota($IDPaket, $IDPerson)
    {
        $IDPerson = base64_decode($IDPerson);
        $IDPaket = base64_decode($IDPaket);

        $data = $this->jamaahPaket->getDetail($this->IDTravel, $IDPaket, $IDPerson);
        $travel = $this->MstTravel->detail($this->IDTravel);

        return view('admin-page.jamaah.cetak-nota', compact('data', 'travel'));
    }

    private function getInfoTagihan($IDPaket, $JumlahTrip)
    {
        $column = match ($JumlahTrip) {
            '2' => 'BiayaDouble',
            '3' => 'BiayaTriple',
            '4' => 'BiayaQuad',
            default => null
        };

        if (!$column) return 0;

        $paket = MstPaket::where('IDPaket', $IDPaket)->first();
        if (!$paket || empty($paket->$column)) return 0;
        return $paket->$column;
    }

    public function get(Request $request)
    {
        $where = [
            'jamaahpaket.IDTravel' => $this->IDTravel,
        ];

        $list = $this->jamaahPaket->get_datatables($where);

         return $this->datatablesResponse(
            $request,
            $list,
            $this->jamaahPaket->count_all(),
            $this->jamaahPaket->count_filtered($where)
        );
    }
}
