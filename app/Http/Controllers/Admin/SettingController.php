<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SistemSetting;
use App\Traits\DatatablesResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use DatatablesResponse;

    protected SistemSetting $sistemsetting;

    public function __construct(SistemSetting $sistemsetting)
    {
        $this->sistemsetting = $sistemsetting;
    }

    public function index()
    {
        return view('admin-page.sistemsetting.index', [
            'menu' => 'Sistem Setting'
        ]);
    }

    public function get(Request $request)
    {
        $list = $this->sistemsetting->getDatatables($request);

         return $this->datatablesResponse(
            $request,
            $list,
            $this->sistemsetting->countAll(),
            $this->sistemsetting->countFiltered($request)
        );
    }

    public function simpan(Request $request)
    {
        $nosetting = $request->input('nosetting');
        $value = $request->input('value1');
        $value1 = $request->input('value3');

        $data = [];
        $data['Value'] = $this->replaceInputNominal($value);

        if (in_array($nosetting, [1, 2, 3])) {
            $data['Value1'] = null;
        } else {
            $data['Value1'] = $value1 == 1 ? 'AKTIF' : 'NON AKTIF';
        }

        $this->sistemsetting
            ->where('KodeSetting', $nosetting)
            ->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengubah setting'
        ]);
    }

    private function replaceInputNominal($value)
    {
        if ($value === null) return null;
        return str_replace(['.', ','], ['', '.'], $value);
    }
}
