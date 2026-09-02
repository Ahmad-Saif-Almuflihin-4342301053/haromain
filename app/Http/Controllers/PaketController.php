<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\MstPaket;
use App\Models\SyaratPaket;
use App\Models\MstItenerary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaketController extends Controller
{
    public function __construct(
        protected MstPaket $MstPaket,
        protected MstItenerary $MstItenerary,
        protected SyaratPaket $SyaratPaket
    ) {}

    public function index(Request $request)
    {
        $type = $request->query('type', '');
        $wherePaket = $type !== '' ? ['KategoriPaket' => $type] : [];

        $perPage = 12;
        $page = (int) $request->query('page', 1);

        $offset = ($page - 1) * $perPage;

        $paket = $this->MstPaket->getLatestPaketPaging($perPage, $offset, $wherePaket);

        $totalRows = $this->MstPaket->totalLatestPaket($wherePaket);
        $totalPages = ceil($totalRows / $perPage);

        return view('front-page.paket', [
            'paket' => $paket,
            'type' => $type,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'totalRows' => $totalRows,
        ]);
    }

    public function detail(Request $request)
    {
        $IDPaket = base64_decode($request->query('id'));

        $paket = $this->MstPaket->findJoinTravelLokasi(['IDPaket' => $IDPaket]);

        if (!$paket) {
            abort(404, 'Paket tidak ditemukan');
        }

        $itenerary = $this->MstItenerary
            ->setIDTravel($paket->IDTravel)
            ->setIDPaket($IDPaket)
            ->findAll(['HariKe' => 1]);

        $datapaket = $this->MstPaket->findJoinTravel(['IDPaket' => $IDPaket]);

        $syaratpaket = $this->SyaratPaket->getByPaket($IDPaket);

        $jumlahhari = $this->countDaysBetweenDates(
            $paket->TglBerangkat,
            $paket->TglPulang
        );

        $datahari = $this->generateDateOptions(
            $datapaket->TglBerangkat,
            $datapaket->TglPulang
        );

        return view('front-page.paket-detail', [
            'menu' => 'Monitoring',
            'paket' => $paket,
            'itenerary' => $itenerary,
            'syaratpaket' => $syaratpaket,
            'IDPaket' => $IDPaket,
            'jumlahhari' => $jumlahhari,
            'datahari' => $datahari,
        ]);
    }


    public function getIteneraryHari($encodedIDPaket, $encodedIDTravel, $HariKe)
    {
        try {
            $encodedIDPaket = strtr($encodedIDPaket, '._-', '+/=');
            $encodedIDTravel = strtr($encodedIDTravel, '._-', '+/=');

            $IDPaket = base64_decode($encodedIDPaket);
            $IDTravel = base64_decode($encodedIDTravel);

            // Get itenerary for specific day
            $data = $this->MstItenerary
                ->setIDTravel($IDTravel)
                ->setIDPaket($IDPaket)
                ->findAll(['HariKe' => $HariKe]);

            if ($data && count($data) > 0) {
                // Format times for display
                foreach ($data as $i => $dt) {
                    $data[$i]->TglMulai = date('H:i:s', strtotime($dt->TglMulai));
                    $data[$i]->TglSelesai = date('H:i:s', strtotime($dt->TglSelesai));
                }

                return response()->json([
                    'status' => true,
                    'data' => $data
                ]);
            }

            return response()->json([
                'status' => false,
                'data' => []
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting itenerary hari: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil data itenerary',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get all itenerary for a paket (AJAX)
     */
    public function getItenerary(Request $request)
    {
        try {
            $IDPaket = base64_decode($request->query('id'));

            $result = $this->MstItenerary
                ->setIDPaket($IDPaket)
                ->findAll();

            return response()->json([
                'status' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting itenerary: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil data itenerary',
                'data' => []
            ], 500);
        }
    }

    /**
     * Generate array of date options between start and end date
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function generateDateOptions($startDate, $endDate)
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $interval = $end->diff($start)->days + 1;

        $dateOptions = [];

        for ($i = 0; $i < $interval; $i++) {
            $currentDate = $start->format('Y-m-d');
            $dateOptions[] = [
                'harike' => $i + 1,
                'tanggal' => $currentDate
            ];
            $start->modify('+1 day');
        }

        return $dateOptions;
    }

    /**
     * Count days between two dates
     *
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    private function countDaysBetweenDates($startDate, $endDate)
    {
        if ($startDate != '0000-00-00 00:00:00' && $endDate != '0000-00-00 00:00:00') {
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            $interval = $start->diff($end);
            return $interval->days + 1;
        }

        return 0;
    }
}
