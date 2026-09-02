<?php

namespace App\Http\Controllers\Admin;

use DateTime;
use Carbon\Carbon;
use App\Models\MstPaket;

use App\Models\MstPerson;
use App\Models\AgenTravel;
use App\Models\JamaahPaket;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    protected $jamaah;
    protected $paket;
    protected $agentravel;
    protected $jamaahpaket;
    protected $IDTravel;

    public function __construct(
        MstPerson $jamaah,
        MstPaket $paket,
        Agentravel $agentravel,
        Jamaahpaket $jamaahpaket
    ) {

        $this->jamaah = $jamaah;
        $this->paket = $paket;
        $this->agentravel = $agentravel;
        $this->jamaahpaket = $jamaahpaket;
        $this->IDTravel = Auth::user()->IDTravel;
    }

    public function index()
    {
        $today = date('Y-m-d');
        $lastMonthDate = $this->getLastMonth('d');
        $lastMonth = $this->getLastMonth('m');
        $lastMonthYear = $this->getLastMonth('Y');

        $wheretotalJamaah = [];
        $wheretotalJamaahTracking = [
            ['NoInvoice', '<>', null]
        ];

        $wheretotalBookingBulanIni = [
            ['MONTH(TanggalBooking)', date('m')],
            ['YEAR(TanggalBooking)', date('Y')]
        ];

        $wheretotalBookingBulanLalu = [
            ['MONTH(TanggalBooking)', $lastMonth],
            ['YEAR(TanggalBooking)', $lastMonthYear]
        ];

        $wheretotalTrackingBulanIni = [
            ['MONTH(trkastravel.TanggalInvoice)', date('m')],
            ['YEAR(trkastravel.TanggalInvoice)', date('Y')]
        ];

        $wheretotalTrackingBulanLalu = [
            ['MONTH(trkastravel.TanggalInvoice)', $lastMonth],
            ['YEAR(trkastravel.TanggalInvoice)', $lastMonthYear]
        ];

        $wheretotalPersiapan = ['DATE(TglBerangkat) > ' => $today,  'IsAktif' => 1,];

        $wheretotalPersiapanBulanLalu = ['DATE(TglBerangkat) > ' => $lastMonthDate,  'IsAktif' => 1,];

        $wheretotalPerjalanan = [
            'DATE(TglBerangkat) <= ' => $today,
            'DATE(TglPulang) >= ' => $today,
            'IsAktif' => 1,
        ];
        $wheretotalPerjalananBulanLalu = [
            'MONTH(TglBerangkat) <= ' => $lastMonth,
            'YEAR(TglBerangkat) <= ' => $lastMonthYear,
            'MONTH(TglPulang) >= ' => $lastMonth,
            'YEAR(TglPulang) >= ' => $lastMonthYear,
            'IsAktif' => 1,
        ];

        $wheretotalSelesai = ['DATE(TglPulang) < ' => $today,  'IsAktif' => 1,];
        $wheretotalSelesaiBulanLalu = ['DATE(TglPulang) < ' => $lastMonthDate,  'IsAktif' => 1,];

        // dd(session('LevelID'));
        if (Auth::user()->LevelID == 1) {
            $totalAgen = $this->jamaah->total(['StatusPerson' => 'Agen']);
        } elseif (Auth::user()->LevelID == 2) {
            $totalAgen = $this->agentravel->total(['IDTravel' => $this->IDTravel]);
            $wheretotalJamaah['IDTravel'] = $this->IDTravel;
            $wheretotalJamaahTracking['IDTravel'] = $this->IDTravel;

            $wheretotalBookingBulanIni[] =
                ['IDTravel', $this->IDTravel];

            $wheretotalBookingBulanLalu[] = ['IDTravel', $this->IDTravel];

            $wheretotalTrackingBulanIni[] = ['jamaahpaket.IDTravel', $this->IDTravel];

            $wheretotalTrackingBulanLalu[] =
                ['jamaahpaket.IDTravel', $this->IDTravel];

            $wheretotalPersiapan['IDTravel'] = $this->IDTravel;
            $wheretotalPersiapanBulanLalu['IDTravel'] = $this->IDTravel;

            $wheretotalPerjalanan['IDTravel'] = $this->IDTravel;
            $wheretotalPerjalananBulanLalu['IDTravel'] = $this->IDTravel;

            $wheretotalSelesai['IDTravel'] = $this->IDTravel;
            $wheretotalSelesaiBulanLalu['IDTravel'] = $this->IDTravel;
        }

        // dd($wheretotalTrackingBulanIni);

        $totalJamaah = $this->jamaahpaket->total($wheretotalJamaah);
        $totalJamaahTracking = $this->jamaahpaket->total($wheretotalJamaahTracking);

        $totalBookingBulanIni = $this->jamaahpaket->totalBooking($wheretotalBookingBulanIni);
        $totalBookingBulanLalu = $this->jamaahpaket->totalBooking($wheretotalBookingBulanLalu);

        $totalTrackingBulanIni = $this->jamaahpaket->totalTracking($wheretotalTrackingBulanIni);
        $totalTrackingBulanLalu = $this->jamaahpaket->totalTracking($wheretotalTrackingBulanLalu);

        $totalPersiapan = $this->paket->total($wheretotalPersiapan);
        // dd([$wheretotalPersiapan,$wheretotalPersiapanBulanLalu]);
        $totalPersiapanBulanLalu = $this->paket->total($wheretotalPersiapanBulanLalu);

        $totalPerjalanan = $this->paket->total($wheretotalPerjalanan);
        $totalPerjalananBulanLalu = $this->paket->total($wheretotalPerjalananBulanLalu);

        $totalSelesai = $this->paket->total($wheretotalSelesai);
        $totalSelesaiBulanLalu = $this->paket->total($wheretotalSelesaiBulanLalu);


        return view('admin-page.dashboard', [
            'menu' => 'Dashboard',
            'totalAgen' => $totalAgen,
            'totalJamaah' => $totalJamaah,
            'totalJamaahTracking' => $totalJamaahTracking,
            'selisihBooking' => $totalBookingBulanIni - $totalBookingBulanLalu,
            'selisihTracking' => $totalTrackingBulanIni - $totalTrackingBulanLalu,
            'totalPersiapan' => $totalPersiapan,
            'selisihPersiapan' => $totalPersiapan - $totalPersiapanBulanLalu,
            'totalPerjalanan' => $totalPerjalanan,
            'selisihPerjalanan' => $totalPerjalanan - $totalPerjalananBulanLalu,
            'totalSelesai' => $totalSelesai,
            'selisihSelesai' => $totalSelesai - $totalSelesaiBulanLalu
        ]);
    }

    private function getLastMonth($format)
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $lastMonth = ($currentMonth == 1) ? 12 : ($currentMonth - 1);
        $lastMonthYear = ($currentMonth == 1) ? ($currentYear - 1) : $currentYear;

        if ($format == 'm') {
            return $lastMonth;
        } elseif ($format == 'Y') {
            return $lastMonthYear;
        } elseif ($format == 'd') {
            $date = new DateTime($lastMonthYear . '-' . $lastMonth . '-01');
            $date->modify('last day of this month');
            return $date->format('Y-m-d');
        }
    }

    public function grafikPerjalananBulanan()
    {
        $currentYear = date('Y');
        $data = [];

        for ($month = 1; $month <= 12; $month++) {

            $where = [
                'MONTH(TglBerangkat)' => $month,
                'YEAR(TglBerangkat)' => $currentYear,
                'IsAktif' => 1
            ];

            if ($this->IDTravel != '') {
                $where['IDTravel'] = [$this->IDTravel];
            }

            $data[] = $this->paket->total($where);
        }

        return response()->json($data);
    }
}
