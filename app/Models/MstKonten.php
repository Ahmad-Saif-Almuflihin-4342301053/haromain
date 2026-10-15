<?php

namespace App\Models;

use App\Traits\HasDatatableParams;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\HasDatatablesPagination;
use App\Traits\HasSequentialId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstKonten extends Model
{
    use HasFactory, HasDatatableParams, HasDatatablesPagination, HasSequentialId;

    protected $table = 'mstkonten';
    protected $primaryKey = 'IDKonten';
    public $incrementing = false;
    public $timestamps = false;

    private $IDKonten;
    private $JenisKonten;
    private $IDKatKonten;
    private $Lat;
    private $Lng;
    private $column_order;
    private $column_search;
    private $IDKota;

    protected $fillable = [
        'IDKonten',
        'IDKatKonten',
        'JudulKonten',
        'IsiKonten',
        'JenisKonten',
        'GambarKonten',
        'Author',
        'TanggalKonten',
        'IsAktif',
        'IsRekomendasi',
        'IDKota',
        'Lat',
        'Lng',
        'NoUrut'
    ];

    protected $columnOrder = [
        null,
        'JudulKonten',
        'Author',
        'TanggalKonten',
        'JenisKonten',
        'IsAktif',
        'IsRekomendasi'
    ];

    protected $columnSearch = [
        'k.JudulKonten',
        'k.IsiKonten',
        'k.Author',
        'k.JenisKonten',
        'kk.NamaKategori'
    ];

    protected $order = ['TanggalKonten' => 'desc'];

    public function generateID()
    {
        $today = date('Ymd');
        $prefix = 'KTN-' . $today . '-';

        return $this->generateSequentialId([
            'column' => 'IDKonten',
            'prefix' => $prefix,
            'pad' => 4,
            'scope' => function ($q) use ($prefix) {
                $q->where('IDKonten', 'like', $prefix . '%');
            }
        ]);
    }


    public static function findKonten($idKonten = null, $lat = null, $lng = null)
    {
        $query = DB::table('mstkonten as k')
            ->leftJoin('mstkategorikonten as kk', 'kk.IDKatKonten', '=', 'k.IDKatKonten')
            ->leftJoin('mstkota as kt', 'kt.IDKota', '=', 'k.IDKota');

        if ($lat && $lng) {
            $distance = "
                ROUND(
                    (6371 * acos(
                        cos(radians($lat)) *
                        cos(radians(k.Lat)) *
                        cos(radians(k.Lng) - radians($lng)) +
                        sin(radians($lat)) *
                        sin(radians(k.Lat))
                    )), 2
                )";

            $query->selectRaw("
                k.IDKonten,
                k.JudulKonten,
                k.IsiKonten,
                k.GambarKonten,
                k.Author,
                k.TanggalKonten,
                k.JenisKonten,
                k.IsAktif,
                k.IsRekomendasi,
                k.Lat,
                k.Lng,
                k.IDKatKonten,
                kk.NamaKategori,
                k.IDKota,
                kt.NamaKota,
                {$distance} AS Jarak
            ");
        } else {
            $query->select(
                'k.IDKonten',
                'k.JudulKonten',
                'k.IsiKonten',
                'k.GambarKonten',
                'k.Author',
                'k.TanggalKonten',
                'k.JenisKonten',
                'k.IsAktif',
                'k.IsRekomendasi',
                'k.Lat',
                'k.Lng',
                'k.IDKatKonten',
                'kk.NamaKategori',
                'k.IDKota',
                'kt.NamaKota'
            );
        }

        if ($idKonten) {
            $query->where('k.IDKonten', $idKonten);
        }

        return $query->first();
    }

    public function findAll()
    {
        $query = DB::table('mstkonten as k')
            ->leftJoin('mstkategorikonten as kk', 'kk.IDKatKonten', '=', 'k.IDKatKonten')
            ->leftJoin('mstkota as kt', 'kt.IDKota', '=', 'k.IDKota');

        // SEARCH
        if ($this->getCari()) {
            $query->where(function ($q) {
                $q->where('k.JudulKonten', 'like', '%' . $this->getCari() . '%');
            });
        }

        // FILTER
        if ($this->getIDKonten()) {
            $query->where('k.IDKonten', $this->getIDKonten());
        }

        if ($this->getIDKatKonten()) {
            $query->where('k.IDKatKonten', $this->getIDKatKonten());
        }

        if ($this->getJenisKonten()) {
            $query->where('k.JenisKonten', $this->getJenisKonten());
        }

        // HAVERSINE (JARAK)
        if ($this->getLat() && $this->getLng()) {
            $lat = $this->getLat();
            $lng = $this->getLng();

            $distanceFormula = "
            ROUND(
                (6371 * acos(
                    cos(radians($lat)) *
                    cos(radians(k.Lat)) *
                    cos(radians(k.Lng) - radians($lng)) +
                    sin(radians($lat)) *
                    sin(radians(k.Lat))
                )), 2
            )
        ";

            $query->selectRaw("
            k.IDKonten, k.JudulKonten, k.IsiKonten, k.GambarKonten,
            k.Author, k.TanggalKonten, k.JenisKonten, k.IsAktif,
            k.IsRekomendasi, k.Lat, k.Lng, k.IDKatKonten,
            kk.NamaKategori, k.IDKota, kt.NamaKota,
            $distanceFormula AS Jarak
        ");

            $query->whereNotNull('k.Lat')
                ->whereNotNull('k.Lng')
                ->orderBy('Jarak', 'ASC');
        } else {
            $query->select(
                'k.IDKonten',
                'k.JudulKonten',
                'k.IsiKonten',
                'k.GambarKonten',
                'k.Author',
                'k.TanggalKonten',
                'k.JenisKonten',
                'k.IsAktif',
                'k.IsRekomendasi',
                'k.Lat',
                'k.Lng',
                'k.IDKatKonten',
                'kk.NamaKategori',
                'k.IDKota',
                'kt.NamaKota'
            );

            $query->orderBy('k.TanggalKonten', 'DESC');
        }

        // PAGINATION (page * limit)
        if ($this->getLimit() > 0) {
            $offset = $this->getPage() * $this->getLimit();
            $query->limit($this->getLimit())->offset($offset);
        }

        return $query->get()->toArray();
    }

    public function countAll()
    {
        $query = DB::table('mstkonten as k')
            ->leftJoin('mstkategorikonten as kk', 'kk.IDKatKonten', '=', 'k.IDKatKonten')
            ->leftJoin('mstkota as kt', 'kt.IDKota', '=', 'k.IDKota');

        if ($this->getCari()) {
            $query->where(function ($q) {
                $q->where('k.JudulKonten', 'like', '%' . $this->getCari() . '%');
            });
        }

        if ($this->getIDKonten()) {
            $query->where('k.IDKonten', $this->getIDKonten());
        }

        if ($this->getJenisKonten()) {
            $query->where('k.JenisKonten', $this->getJenisKonten());
        }

        if ($this->getLat() && $this->getLng()) {
            $query->whereNotNull('k.Lat')
                ->whereNotNull('k.Lng');
        }

        return $query->count();
    }


    private function getDatatablesQuery()
    {
        $query = DB::table('mstkonten as k')
            ->select(
                'k.IDKonten',
                'k.JudulKonten',
                'k.IsiKonten',
                'k.GambarKonten',
                'k.Author',
                'k.TanggalKonten',
                'k.JenisKonten',
                'k.IsAktif',
                'k.IsRekomendasi',
                'k.Lat',
                'k.Lng',
                'k.IDKatKonten',
                'kk.NamaKategori',
                'k.IDKota',
                'kt.NamaKota',
                'k.NoUrut'
            )
            ->leftJoin('mstkategorikonten as kk', 'kk.IDKatKonten', '=', 'k.IDKatKonten')
            ->leftJoin('mstkota as kt', 'kt.IDKota', '=', 'k.IDKota');

        if ($this->getJenisKonten()) {
            $query->where('k.JenisKonten', $this->getJenisKonten());
        }

        if ($this->getIDKatKonten()) {
            $query->where('k.IDKatKonten', $this->getIDKatKonten());
        }

        if ($this->getIDKota()) {
            $query->where('k.IDKota', $this->getIDKota());
        }

        if (isset($_POST['search']['value']) && $_POST['search']['value']) {
            $query->where(function ($q) {
                foreach ($this->column_search as $item) {
                    $q->orWhere($item, 'like', '%' . $_POST['search']['value'] . '%');
                }
            });
        }

        if (isset($_POST['searchKeyword']) && $_POST['searchKeyword']) {
            $query->where(function ($q) {
                $q->where('k.JudulKonten', 'like', '%' . $_POST['searchKeyword'] . '%')
                    ->orWhere('k.IsiKonten', 'like', '%' . $_POST['searchKeyword'] . '%')
                    ->orWhere('k.Author', 'like', '%' . $_POST['searchKeyword'] . '%');
            });
        }

        if (isset($_POST['order'])) {
            $columnIndex = $_POST['order'][0]['column'];
            $dir = $_POST['order'][0]['dir'];
            if (isset($this->column_order[$columnIndex])) {
                $query->orderBy($this->column_order[$columnIndex], $dir);
            }
        } else {
            $order = $this->order;
            $query->orderBy(key($order), $order[key($order)]);
        }

        return $query;
    }

    public function count_filtered()
    {
        return $this->getDatatablesQuery()->count();
    }

    public function count_all_datatables()
    {
        $query = DB::table($this->table);

        if ($this->getJenisKonten()) {
            $query->where('JenisKonten', $this->getJenisKonten());
        }

        return $query->count();
    }

    public function getIDKonten()
    {
        return $this->IDKonten;
    }

    public function setIDKonten($IDKonten)
    {
        $this->IDKonten = $IDKonten;
        return $this;
    }

    public function getJenisKonten()
    {
        return $this->JenisKonten;
    }

    public function setJenisKonten($JenisKonten)
    {
        $this->JenisKonten = $JenisKonten;
        return $this;
    }

    public function getIDKatKonten()
    {
        return $this->IDKatKonten;
    }

    public function setIDKatKonten($IDKatKonten)
    {
        $this->IDKatKonten = $IDKatKonten;
        return $this;
    }

    public function getLat()
    {
        return $this->Lat;
    }

    public function setLat($Lat)
    {
        $this->Lat = $Lat;
        return $this;
    }

    public function getLng()
    {
        return $this->Lng;
    }

    public function setLng($Lng)
    {
        $this->Lng = $Lng;
        return $this;
    }

    public function getIDKota()
    {
        return $this->IDKota;
    }

    public function setIDKota($IDKota)
    {
        $this->IDKota = $IDKota;
        return $this;
    }

    public function findArtikel($page = 0, $limit = 0, $cari = null, $IDKatKonten = null)
    {
        $this->setJenisKonten('ARTIKEL')
            ->setLimit($limit)
            ->setPage($page)
            ->setCari($cari);

        if ($IDKatKonten) {
            $this->setIDKatKonten($IDKatKonten);
        }

        $result = $this->findAll();
        $total = $this->countAll();

        return [
            'data' => $result,
            'total' => $total
        ];
    }

    public function findLokasi($page = 0, $limit = 10, $cari = null, $IDKatKonten = null, $IDKota = null, $lat = null, $lng = null)
    {
        $this->setJenisKonten('LOKASI')
            ->setLimit($limit)
            ->setPage($page)
            ->setCari($cari);

        if ($IDKatKonten) {
            $this->setIDKatKonten($IDKatKonten);
        }

        if ($IDKota) {
            $this->setIDKota($IDKota);
        }

        if ($lat) {
            $this->setLat($lat);
        }

        if ($lng) {
            $this->setLng($lng);
        }

        $result = $this->findAll();
        $total = $this->countAll();

        return [
            'data' => $result,
            'total' => $total
        ];
    }

    public function findSlider($IDKatKonten = null)
    {
        $this->setJenisKonten('SLIDER');

        if ($IDKatKonten) {
            $this->setIDKatKonten($IDKatKonten);
        }

        $result = $this->findAll();
        $total = $this->countAll();

        return [
            'data' => $result,
            'total' => $total
        ];
    }


    public static function getLandingSlider()
    {
        return self::where('JenisKonten', 'SLIDER')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->get()
            ->toArray();
    }

    public static function getLandingMemahamiTentang()
    {
        return self::where('JenisKonten', 'LANDING_MEMAHAMI_TENTANG')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingPeranHaromain()
    {
        return self::where('JenisKonten', 'LANDING_PERAN_HAROMAIN')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingPeranHaromainList()
    {
        return self::where('JenisKonten', 'LANDING_PERAN_HAROMAIN_LIST')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'ASC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->get()
            ->toArray();
    }

    public static function getLandingPeranHaromainFooter()
    {
        return self::where('JenisKonten', 'LANDING_PERAN_HAROMAIN_FOOTER')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingKenalLebih()
    {
        return self::where('JenisKonten', 'LANDING_KENAL_LEBIH')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingHaromainBersama()
    {
        return self::where('JenisKonten', 'LANDING_HAROMAIN_BERSAMA')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingFiturUnggulan()
    {
        return self::where('JenisKonten', 'LANDING_FITUR_UNGGULAN')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingFiturUnggulanList()
    {
        return self::where('JenisKonten', 'LANDING_FITUR_UNGGULAN_LIST')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'ASC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->get()
            ->toArray();
    }

    public static function getLandingSiapaYang()
    {
        return self::where('JenisKonten', 'LANDING_SIAPA_YANG')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingSiapaYangList()
    {
        return self::where('JenisKonten', 'LANDING_SIAPA_YANG_LIST')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'ASC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->get()
            ->toArray();
    }

    public static function getLandingKondisiPengguna()
    {
        return self::where('JenisKonten', 'LANDING_KONDISI_PENGGUNA')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingMitraTravelList()
    {
        return self::where('JenisKonten', 'LANDING_MITRA_TRAVEL_LIST')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'ASC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->get()
            ->toArray();
    }

    public static function getLandingTestimonial()
    {
        return self::where('JenisKonten', 'LANDING_TESTIMONIAL')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingTestimonialList()
    {
        return self::where('JenisKonten', 'LANDING_TESTIMONIAL_LIST')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'ASC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->get()
            ->toArray();
    }

    public static function getLandingPertanyaan()
    {
        return self::where('JenisKonten', 'LANDING_PERTANYAAN')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingPertanyaanList()
    {
        return self::where('JenisKonten', 'LANDING_PERTANYAAN_LIST')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'ASC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->get()
            ->toArray();
    }

    public static function getLandingActionButton()
    {
        return self::where('JenisKonten', 'LANDING_ACTION_BUTTON')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingCaraMemulai()
    {
        return self::where('JenisKonten', 'LANDING_CARA_MEMULAI')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingKatalogHeader()
    {
        return self::where('JenisKonten', 'LANDING_KATALOG_HEADER')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingKatalogFooter()
    {
        return self::where('JenisKonten', 'LANDING_KATALOG_FOOTER')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingGaleriHeader()
    {
        return self::where('JenisKonten', 'LANDING_GALERI_HEADER')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }


    public static function getLandingGaleriFooter()
    {
        return self::where('JenisKonten', 'LANDING_GALERI_FOOTER')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

    public static function getLandingGaleriTitle()
    {
        return self::where('JenisKonten', 'LANDING_GALERI_TITLE')
            ->where('IsAktif', 1)
            ->orderBy('TanggalKonten', 'DESC')
            ->select('JudulKonten', 'IsiKonten', 'GambarKonten')
            ->first();
    }

}
