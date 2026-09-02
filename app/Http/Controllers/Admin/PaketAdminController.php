<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstPaket;
use App\Models\MstPerson;
use App\Models\MstCabang;
use App\Models\ServerLog;
use App\Models\AgenTravel;
use App\Models\JamaahPaket;
use Illuminate\Http\Request;
use App\Traits\DatatablesResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaketAdminController extends Controller
{
    use DatatablesResponse;

    protected $IDTravel;
    protected $MstPerson;
    protected $MstPaket;
    protected $MstCabang;
    protected $AgenTravel;
    protected $JamaahPaket;
    protected $ServerLog;

    public function __construct(
        MstPerson $MstPerson,
        MstPaket $MstPaket,
        MstCabang $MstCabang,
        AgenTravel $AgenTravel,
        JamaahPaket $JamaahPaket,
        ServerLog $ServerLog
    ) {
        $this->IDTravel = Auth::user()->IDTravel ?? null;
        $this->MstPerson = $MstPerson;
        $this->MstPaket = $MstPaket;
        $this->MstCabang = $MstCabang;
        $this->AgenTravel = $AgenTravel;
        $this->JamaahPaket = $JamaahPaket;
        $this->ServerLog = $ServerLog;
    }

    public function index()
    {
        return view('admin-page.paket-admin.index', [
            'menu' => 'Daftarkan Paket'
        ]);
    }

    public function get(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $filterSearch = $request->input('filter_search');

        $query = DB::table('mstperson')
            ->select(
                'mstperson.IDPerson',
                'mstperson.NamaJamaah',
                'mstperson.NIK',
                'mstperson.NoHP',
                'mstperson.Tgl_Register',
                'mstperson.Alamat',
                'mstperson.IsAktif',
                'mstprov.NamaProv',
                'mstkab.NamaKab'
            )
            ->leftJoin('mstprov', 'mstprov.IDProv', '=', 'mstperson.IDProv')
            ->leftJoin('mstkab', function ($join) {
                $join->on('mstkab.IDProv', '=', 'mstperson.IDProv')
                    ->on('mstkab.IDKab', '=', 'mstperson.IDKab');
            })
            ->where('mstperson.StatusPerson', 'Jamaah')
            ->where('mstperson.IsAktif', 1);

        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('DATE(mstperson.Tgl_Register)'), [$startDate, $endDate]);
        }

        if ($filterSearch) {
            $query->where(function ($q) use ($filterSearch) {
                $q->where('mstperson.NamaJamaah', 'LIKE', "%{$filterSearch}%")
                    ->orWhere('mstperson.NoHP', 'LIKE', "%{$filterSearch}%");
            });
        }

        // Search
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('mstperson.NamaJamaah', 'LIKE', "%{$search}%")
                    ->orWhere('mstperson.NIK', 'LIKE', "%{$search}%")
                    ->orWhere('mstperson.NoHP', 'LIKE', "%{$search}%")
                    ->orWhere('mstperson.Alamat', 'LIKE', "%{$search}%");
            });
        }

        $totalRecords = DB::table('mstperson')
            ->where('StatusPerson', 'Jamaah')
            ->where('IsAktif', 1)
            ->count();

        $filteredRecords = $query->count();

        // Order
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'desc');
        $columns = ['IDPerson', 'NamaJamaah', 'NIK', 'NoHP', 'Tgl_Register', 'Alamat'];
        
        if (isset($columns[$orderColumnIndex])) {
            $query->orderBy('mstperson.' . $columns[$orderColumnIndex], $orderDirection);
        }

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        
        if ($length != -1) {
            $query->offset($start)->limit($length);
        }

        $data = $query->get();

        // Format data for DataTables
        $result = [];
        $no = $start + 1;
        foreach ($data as $row) {
            $result[] = [
                'No' => $no++,
                'IDPerson' => $row->IDPerson,
                'NamaJamaah' => $row->NamaJamaah,
                'NIK' => $row->NIK ?? '-',
                'NoHP' => $row->NoHP,
                'Tgl_Register' => $row->Tgl_Register,
                'Alamat' => $row->Alamat ?? '-',
                'LokasiLengkap' => ($row->NamaKab ? $row->NamaKab . ', ' : '') . ($row->NamaProv ?? ''),
                'IsAktif' => $row->IsAktif
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $result
        ]);
    }

    public function getTravel(Request $request)
    {
        $travel = DB::table('msttravel')
            ->select('IDTravel', 'NamaTravel')
            ->where('IsVerified', 1)
            ->where('StatusAkun', 'Aktif')
            ->orderBy('NamaTravel', 'ASC')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $travel
        ]);
    }

    public function getPaket(Request $request)
    {
        $IDTravel = $request->input('IDTravel');
        
        $query = MstPaket::where('IsAktif', 1)
            //->whereRaw('DATE(TglBerangkat) > CURDATE()')
            ->orderBy('TglBerangkat', 'ASC');

        if ($IDTravel) {
            $query->where('IDTravel', $IDTravel);
        }

        $paket = $query->get(['IDPaket', 'NamaPaket', 'TglBerangkat', 'BiayaDouble', 'BiayaTriple', 'BiayaQuad']);

        return response()->json([
            'status' => true,
            'data' => $paket
        ]);
    }

    public function getCabang(Request $request)
    {
        $IDTravel = $request->input('IDTravel');
        
        $query = MstCabang::where('IsAktif', 1);

        if ($IDTravel) {
            $query->where('IDTravel', $IDTravel);
        }

        $cabang = $query->get(['IDCabang', 'NamaCabang']);

        return response()->json([
            'status' => true,
            'data' => $cabang
        ]);
    }

    public function getAgen(Request $request)
    {
        $IDTravel = $request->input('IDTravel');
        
        $query = DB::table('agentravel')
            ->select('agentravel.IDPerson', 'mstperson.NamaJamaah')
            ->join('mstperson', 'agentravel.IDPerson', '=', 'mstperson.IDPerson')
            ->where('agentravel.IsAktif', 1)
            ->whereRaw("
                (
                    IF(
                        agentravel.TglAkhirAgen IS NULL,
                        NOW() >= agentravel.TglMulaiAgen,
                        NOW() BETWEEN agentravel.TglMulaiAgen AND agentravel.TglAkhirAgen
                    )
                )
            ");

        if ($IDTravel) {
            $query->where('agentravel.IDTravel', $IDTravel);
        }

        $agen = $query->get();

        return response()->json([
            'status' => true,
            'data' => $agen
        ]);
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'IDTravel' => 'required',
            'IDPaket' => 'required',
            'IDCabang' => 'required',
            'JumlahTrip' => 'required|in:2,3,4',
            'jamaah_ids' => 'required|array|min:1',
            'jamaah_ids.*' => 'required|string'
        ]);

        $IDTravel = $request->IDTravel;
        $IDPaket = $request->IDPaket;
        $IDCabang = $request->IDCabang;
        $IDPersonAgen = $request->IDPersonAgen;
        $JumlahTrip = $request->JumlahTrip;
        $jamaahIds = $request->jamaah_ids;

        // Get package details for InfoTagihan calculation
        $paket = MstPaket::where('IDPaket', $IDPaket)->first();
        
        if (!$paket) {
            return response()->json([
                'status' => false,
                'message' => 'Paket tidak ditemukan'
            ]);
        }

        $infoTagihan = $this->getInfoTagihan($paket, $JumlahTrip);

        DB::beginTransaction();
        try {
            $registeredCount = 0;

            foreach ($jamaahIds as $IDPerson) {
                // Check if already registered
                $exists = JamaahPaket::where('IDPerson', $IDPerson)
                    ->where('IDPaket', $IDPaket)
                    ->where('IDTravel', $IDTravel)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $jamaahPaket = [
                    'IDPaket' => $IDPaket,
                    'IDPerson' => $IDPerson,
                    'IDTravel' => $IDTravel,
                    'IDCabang' => $IDCabang,
                    'IDPersonAgen' => $IDPersonAgen,
                    'IsPaid' => 0,
                    'IsAlert' => 0,
                    'StatusJamaah' => 'Jamaah',
                    'TanggalBooking' => now(),
                    'JumlahTrip' => $JumlahTrip,
                    'InfoTagihan' => $infoTagihan,
                    'InfoSisaTagihan' => $infoTagihan
                ];

                JamaahPaket::create($jamaahPaket);
                $this->ServerLog->insertLog($IDPerson, 'Tambah', 'Daftarkan Paket');
                $registeredCount++;
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Berhasil mendaftarkan {$registeredCount} jamaah ke paket",
                'count' => $registeredCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendaftarkan jamaah: ' . $e->getMessage()
            ]);
        }
    }

    private function getInfoTagihan($paket, $JumlahTrip)
    {
        $column = match ($JumlahTrip) {
            '2' => 'BiayaDouble',
            '3' => 'BiayaTriple',
            '4' => 'BiayaQuad',
            default => null
        };

        if (!$column || empty($paket->$column)) {
            return 0;
        }

        return $paket->$column;
    }
}
