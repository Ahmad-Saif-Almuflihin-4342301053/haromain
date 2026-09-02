<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstPaket;

use App\Models\MstTravel;
use App\Models\HotelPaket;
use App\Models\JamaahPaket;
use App\Models\SyaratPaket;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\DatatablesResponse;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    use DatatablesResponse;

    protected $IDTravel;
    protected $monitoring;
    protected $travel;
    protected $paket;
    protected $syaratpaket;
    protected $hotelpaket;
    protected $jamaahpaket;

    public function __construct(
        MstPaket $monitoring,
        MstTravel $travel,
        MstPaket $paket,
        SyaratPaket $syaratpaket,
        HotelPaket $hotelpaket,
        JamaahPaket $jamaahpaket
    ) {
        $this->monitoring = $monitoring;
        $this->travel = $travel;
        $this->paket = $paket;
        $this->syaratpaket = $syaratpaket;
        $this->hotelpaket = $hotelpaket;
        $this->jamaahpaket = $jamaahpaket;

        $this->IDTravel = Auth::user()->IDTravel ?? null;
    }

    public function index(Request $request)
    {
        $type = $request->query('type', 'Persiapan');

        if ($this->IDTravel) {
            $travel = $this->travel->where('IsShow', 1)
                ->where('IDTravel', $this->IDTravel)
                ->get();
        } else {
            $travel = $this->travel->where('IsShow', 1)->get();
        }

        return view('admin-page.monitoring.index', [
            'menu' => 'Monitoring',
            'type' => $type,
            'IDTravel' => $this->IDTravel,
            'travel' => $travel
        ]);
    }

    public function show(Request $request)
    {
        $type = $request->query('type', 'Detail');
        $IDPaket = base64_decode($request->query('id'));
        $IDPaket = e($IDPaket); // escape

        $view = match ($type) {
            'Detail' => 'admin-page.monitoring.detail',
            'Tracking' => 'admin-page.monitoring.tracking',
            'Jamaah' => 'admin-page.monitoring.jamaah',
            'Pembayaranjamaah' => 'admin-page.monitoring.pembayaran-jamaah',
            'Pembayaranharomain' => 'admin-page.monitoring.pembayaran-haromain',
            default => 'admin-page.monitoring.detail',
        };

        $jamaahpaket = $this->jamaahpaket->getJoinPerson(['IDPaket' => $IDPaket]);

        foreach ($jamaahpaket as $jamaah) {
            $jamaah->Dokumen = $this->jamaahpaket->getDokumenSyarat(
                $jamaah->IDTravel,
                $IDPaket,
                $jamaah->IDPerson
            );
        }

        return view($view, [
            'menu' => 'Monitoring',
            'type' => $type,
            'paket' => $this->paket->find($IDPaket),
            'syaratpaket' => $this->syaratpaket->where('IDPaket', $IDPaket)->get(),
            'hotelpaket' => $this->hotelpaket->where('IDPaket', $IDPaket)->get(),
            'tracking' => $this->getTracking($IDPaket),
            'jamaah' => $jamaahpaket,
            'IDPaket' => $IDPaket
        ]);
    }

    public function setPaid(Request $request)
    {
        $data = $request->all();

        foreach ($data['IDPerson'] as $IDPerson => $value) {
            $IsPaid = isset($data['IsPaid'][$IDPerson]) ? 1 : 0;

            $this->jamaahpaket->where('IDPerson', $IDPerson)
                ->where('IDPaket', $data['IDPaket'])
                ->update([
                    'IsPaid' => $IsPaid,
                    'VerifyPaidDate' => now(),
                    'VerifyPaidUser' => Auth::user()->UserName,
                ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengubah status pembayaran'
        ]);
    }

    public function getJamaah(Request $request)
    {
        $IDPaket = $request->query('id');
        $where = ['jamaahpaket.IDPaket' => $IDPaket];

        $list = $this->jamaahpaket->get_datatables($where);

         return $this->datatablesResponse(
            $request,
            $list,
            $this->jamaahpaket->count_all(),
            $this->jamaahpaket->count_filtered($where),
            function($row) use($IDPaket){
                $row->Dokumen = $this->jamaahpaket->getDokumenSyarat(
                $row->IDTravel,
                $IDPaket,
                $row->IDPerson
            );
            }
        );
    }

    public function get(Request $request)
    {
        $type = $request->query('type');
        $today = date('Y-m-d');
        $IDTravel = $this->IDTravel ?: $request->query('trv');

        $where = [];
        if ($type == 'Persiapan') {
            $where[] = ['TglBerangkat', '>', $today];
            $where[] = ['TglPulang', '>', $today];
        } elseif ($type == 'Perjalanan') {
            $where[] = ['TglBerangkat', '<=', $today];
            $where[] = ['TglPulang', '>=', $today];
        } elseif ($type == 'History') {
            $where[] = ['TglBerangkat', '<', $today];
            $where[] = ['TglPulang', '<', $today];
        }

        if ($IDTravel) {
            $where[] = ['mstpaket.IDTravel', '=', $IDTravel];
        }

        $list = $this->monitoring->getDatatables($where);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->monitoring->countAllMonitor(),
            $this->monitoring->countFilteredMonitor($where),
            function ($row) {
                $row->JumlahJamaah = $this->monitoring->getJumlahJamaahMonitor([
                    'IDPaket' => $row->IDPaket,
                    'IDTravel' => $row->IDTravel
                ]);

                $row->JumlahTracking = $this->monitoring->getJumlahJamaahMonitor([
                    'IDPaket' => $row->IDPaket,
                    'IDTravel' => $row->IDTravel,
                ], true);
            }
        );
    }

    public function getTracking($IDPaket)
    {
        $serviceAccountPath = storage_path('app/firebase/haromain-afindo-firebase-adminsdk-qzlal-87b11d086c.json');

        if (!file_exists($serviceAccountPath)) {
            return response()->json([
                'status' => false,
                'message' => 'Firebase service account tidak ditemukan'
            ]);
        }

        $factory = (new Factory)
            ->withServiceAccount($serviceAccountPath)
            ->withDatabaseUri('https://haromain-afindo-default-rtdb.asia-southeast1.firebasedatabase.app');

        $database = $factory->createDatabase();
        $persons = $database->getReference('person')->getValue();

        $jamaahs = $this->jamaahpaket->getTracking([
            'IDPaket' => $IDPaket,
            'IsPaid' => 1
        ]);

        if ($persons && is_array($persons)) {
            foreach ($persons as $userKey => $userData) {
                if (!empty($userData['location']['latitude']) && !empty($userData['location']['longitude'])) {
                    $IDPerson = $userKey;
                    $latitude = $userData['location']['latitude'];
                    $longitude = $userData['location']['longitude'];

                    foreach ($jamaahs as $jamaah) {
                        if ($IDPerson == $jamaah->IDPerson) {
                            $jamaah->KoorLat = (string)$latitude;
                            $jamaah->KoorLong = (string)$longitude;
                            $jamaah->IsBantuan = $userData['IsBantuan'] ?? false;
                        }
                    }
                }
            }
        }

        return $jamaahs;
    }
}
