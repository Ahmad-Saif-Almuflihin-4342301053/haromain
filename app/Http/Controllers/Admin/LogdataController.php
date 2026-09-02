<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LocationLog;

class LogdataController extends Controller
{
    public function __construct(
        protected LocationLog $LocationLog
    ) {}


    public function index(Request $request)
    {
        $date = date("j.n.Y");


        if (!$this->LocationLog->logFileExists($date)) {

            if ($request->has('show') || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'File log tidak ditemukan'
                ], 404);
            }


            return view('admin-page.monitoring.logdata', [
                'menu' => 'Log Tracking Hari Ini',
                'groupedData' => []
            ]);
        }

        $groupedData = $this->LocationLog->getGroupedLogData($date);

        $colors = ['#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#FF00FF', '#00FFFF', '#FFA500', '#800080', '#008000', '#FFC0CB'];
        foreach ($groupedData as $key => $person) {
            $groupedData[$key]['colour'] = $colors[$key % count($colors)];
        }

        if ($request->has('show') || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'data' => $groupedData,
                'statistics' => $this->LocationLog->getLogStatistics($date)
            ], 200);
        }

        return view('admin-page.monitoring.logdata', [
            'menu' => 'Log Tracking Hari Ini',
            'groupedData' => $groupedData
        ]);
    }


    public function getAvailableDates(Request $request)
    {
        $days = $request->query('days', 30);

        $dates = $this->LocationLog->getAvailableLogDates($days);

        return response()->json([
            'status' => true,
            'data' => $dates
        ], 200);
    }

    public function getStatistics(Request $request)
    {
        $date = $request->query('date');

        $statistics = $this->LocationLog->getLogStatistics($date);

        return response()->json([
            'status' => true,
            'data' => $statistics
        ], 200);
    }
}
