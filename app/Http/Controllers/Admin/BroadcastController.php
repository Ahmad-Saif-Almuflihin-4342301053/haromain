<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lokasi;
use App\Models\MstBanner;
use App\Models\MstKonten;
use App\Models\ServerLog;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use App\Traits\DatatablesResponse;
use App\Http\Controllers\Controller;
use App\Services\Firebase\FirebaseNotificationService;

class BroadcastController extends Controller
{
    use DatatablesResponse;

    protected $banner;
    protected $konten;
    protected $pengumuman;
    protected $lokasi;

    public function __construct()
    {
        $this->banner = new MstBanner();
        $this->konten = new MstKonten();
        $this->pengumuman = new Pengumuman();
        $this->lokasi = new Lokasi();
    }

    public function index()
    {
        return view('admin-page.broadcast.index', [
            'menu' => 'Broadcast'
        ]);
    }

    public function tambah()
    {
        return view('admin-page.broadcast.tambah', [
            'menu'       => 'Broadcast',
            'KodeBanner' => $this->banner->generateID()
        ]);
    }

    public function edit(Request $request)
    {
        $id = e(base64_decode($request->query('id')));

        return view('admin-page.broadcast.tambah', [
            'menu'        => 'Broadcast',
            'pengumuman'  => $this->pengumuman->find($id),
            'prov'        => $this->lokasi->setIDProv(null)->findProvinsi()
        ]);
    }

    public function simpan(Request $request,FirebaseNotificationService $firebase)
    {
        $data = $request->all();
        $isUpdate = !empty($data['id']);
        $IDPengumuman = e($data['id'] ?? null);

        $datainsert = [
            'IDProv' => e($data['prov']),
            'Isi'    => e($data['narasi'])
        ];

        $IDProv = $datainsert['IDProv'];
        $Isi    = $datainsert['Isi'];

        if ($isUpdate) {

            Pengumuman::where('IDPengumuman', $IDPengumuman)
                ->update($datainsert);

            (new ServerLog)->insertLog($IDPengumuman, 'Edit', 'Broadcast');

            return response()->json([
                'status'   => true,
                'message'  => 'Berhasil mengedit data',
                'redirect' => url('admin/Broadcast')
            ]);
        }

        $datainsert['DiBaca']  = "0";
        $datainsert['Tanggal'] = now();

        Pengumuman::create($datainsert);

        $kode = $this->pengumuman->getIdLast();

        (new ServerLog)->insertLog($kode, 'Tambah', 'Broadcast');

        $topic = 'prov_' . $IDProv;

       $t =  $firebase->sendNotifBroadcast(
            'pengumuman',
            $Isi,
            $topic
        );

        return response()->json([
            'status'   => true,
            'message'  => 'Berhasil menambah data',
            'redirect' => url('admin/Broadcast'),
            't'    => $t,
        ]);
    }

    public function hapus(Request $request)
    {
        try {
            $id = e(base64_decode($request->query('id')));

            Pengumuman::where('IDPengumuman', $id)->delete();

            (new ServerLog)->insertLog($id, 'Hapus', 'Banner');

            return response()->json([
                'status'  => true,
                'message' => 'Berhasil menghapus data'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus data'
            ]);
        }
    }

    public function get(Request $request)
    {
        $where = 'pengumuman.IDProv IS NOT NULL';

        $list = $this->pengumuman->get_datatables($where);

         return $this->datatablesResponse(
            $request,
            $list,
            $this->pengumuman->count_all($where),
            $this->pengumuman->count_filtered($where)
        );
    }


}
