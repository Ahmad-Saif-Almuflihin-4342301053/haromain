<?php

namespace App\Models;

use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasDatatableParams;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\Traits\HasDatatablesPagination;
use App\Traits\HasSequentialId;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class MstPerson extends Authenticatable
{
    use HasFactory, HasDatatableParams, HasApiTokens, Notifiable, HasDatatablesPagination, HasSequentialId;

    protected $table = 'mstperson';
    protected $primaryKey = 'IDPerson';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDPerson',
        'NamaJamaah',
        'NIK',
        'TempatLahir',
        'TanggalLahir',
        'NoHP',
        'NoPasspor',
        'Password',
        'Keterangan',
        'IsAktif',
        'LastModified',
        'Tgl_Register',
        'JenisKelamin',
        'FotoProfil',
        'IDDesa',
        'IDKec',
        'IDKab',
        'IDProv',
        'StatusPerson',
        'IDPersonReferral',
        'KodeReferral',
        'KodeVerifikasi',
        'KoorLat',
        'KoorLong',
        'Email',
        'UserName',
        'Token',
        'IsVerified',
    ];

    protected $hidden = [
        'Password',
    ];

    protected array $columnOrder = [
        null,
        'NamaJamaah',
        'UserName',
        'NoHP'
    ];

    protected array $columnSearch = [
        'mstperson.NamaJamaah',
        'mstperson.UserName',
        'mstperson.NoHP'
    ];

    protected array $defaultOrder = [
        'mstperson.NamaJamaah' => 'asc'
    ];

    private function baseDatatableQuery(array $where = [])
    {
        return $this->getDatatablesQuery($where);
    }

    private function getDatatablesQuery($where = [])
    {
        $where = is_array($where) ? $where : [];
        $query = DB::table('mstperson')
            ->selectRaw("
            mstperson.IDPerson,
            mstperson.NamaJamaah,
            mstperson.Tgl_Register,
            mstperson.NoHP,
            mstperson.FotoProfil,
            mstperson.IsAktif,
            cb.NamaCabang,
            GROUP_CONCAT(DISTINCT msttravel.NamaTravel SEPARATOR ', ') AS NamaTravel,
            GROUP_CONCAT(DISTINCT mstpaket.NamaPaket SEPARATOR ', ') AS NamaPaket
        ")
            ->leftJoin('jamaahpaket', 'jamaahpaket.IDPerson', '=', 'mstperson.IDPerson')
            ->leftJoin('mstcabang as cb', 'cb.IDCabang', '=', 'jamaahpaket.IDCabang')
            ->leftJoin('mstpaket', 'mstpaket.IDPaket', '=', 'jamaahpaket.IDPaket')
            ->leftJoin('msttravel', 'msttravel.IDTravel', '=', 'jamaahpaket.IDTravel');

        if (!empty($where)) {
            $query->where($where);
        }

        $query->groupBy(
            'mstperson.IDPerson',
            'mstperson.NamaJamaah',
            'mstperson.Tgl_Register',
            'mstperson.NoHP',
            'mstperson.FotoProfil',
            'mstperson.IsAktif',
            'cb.NamaCabang'
        );

        $search = request('search.value') ?: request('searchKeyword');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                foreach ($this->columnSearch as $i => $column) {
                    if ($i === 0) {
                        $q->where($column, 'like', "%{$search}%");
                    } else {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                }
            });
        }

        if (request()->has('order.0.column')) {
            $orderColumn = $this->columnOrder[request('order.0.column')] ?? 'mstperson.NamaJamaah';
            $orderDir = request('order.0.dir') ?? 'asc';
            $query->orderBy($orderColumn, $orderDir);
        } else {
            foreach ($this->defaultOrder as $col => $dir) {
                $query->orderBy($col, $dir);
            }
        }

        return $query;
    }


    public function countFiltered(array $where = [])
    {
        $query = $this->getDatatablesQuery($where);
        return $query->get()->count();
    }

    public function countAll(array $where = [])
    {
        $query = DB::table('mstperson');
        if (!empty($where)) {
            $query->leftJoin('jamaahpaket', 'jamaahpaket.IDPerson', '=', 'mstperson.IDPerson')
                  ->where($where);
            return $query->distinct('mstperson.IDPerson')->count('mstperson.IDPerson');
        }
        return $query->count();
    }

    public function generateID()
    {
        $today  = now('Asia/Jakarta')->year;
        $prefix = 'PRS-' . $today . '-';

        return $this->generateSequentialId([
            'column' => 'IDPerson',
            'prefix' => $prefix,
            'pad'    => 10,
            'scope'  => function ($q) use ($prefix) {
                $q->where('IDPerson', 'like', $prefix . '%');
            }
        ]);
    }


    public function findJoinJamaahPaket(array $where)
    {
        return DB::table('mstperson')
            ->select([
                'mstperson.IDPerson',
                'NamaJamaah',
                'NIK',
                'TempatLahir',
                'TanggalLahir',
                'Alamat',
                'NoHP',
                'NoPasspor',
                'UserName',
                'JenisKelamin',
                'FotoProfil',
                'IDDesa',
                'IDKec',
                'IDKab',
                'IDProv',
                'StatusPerson',
                'Email',
                'jamaahpaket.IDPaket',
                'jamaahpaket.IDTravel',
                'jamaahpaket.IDCabang',
                'jamaahpaket.IDPersonAgen',
                'jamaahpaket.JumlahTrip',
                'jamaahpaket.InfoTagihan',
                'jamaahpaket.InfoTerbayar',
            ])
            ->join('jamaahpaket', 'jamaahpaket.IDPerson', '=', 'mstperson.IDPerson')
            ->where($where)
            ->first();
    }

    public function total(array $where = []): int
    {
        return DB::table($this->table)
            ->where($where)
            ->count();
    }

    public function findJamaahPaket($IDTravel, $IDPaket, $IDPerson)
    {
        return DB::table('jamaahpaket')
            ->where([
                'IDTravel' => $IDTravel,
                'IDPaket'  => $IDPaket,
                'IDPerson' => $IDPerson,
            ])
            ->first();
    }

    public function getDetail($IDPerson)
    {
        $result = DB::table('mstperson as p')
            ->where('p.IDPerson', $IDPerson);

        $data = $this->findAll($result);
        return count($data) > 0 ? $data[0] : null;
    }

    public function getByEmail($Email, $IsAktif = null)
    {
        $query = DB::table('mstperson as p')
            ->where('p.Email', $Email);

        if ($IsAktif !== null) {
            $query->where('p.IsAktif', $IsAktif);
        }

        return $this->findAll($query);
    }

    public function getByReferral($KodeReferral, $IsAktif = null)
    {
        $query = DB::table('mstperson as p')
            ->where('p.KodeReferral', $KodeReferral);

        if ($IsAktif !== null) {
            $query->where('p.IsAktif', $IsAktif);
        }

        return $this->findAll($query);
    }

    public function getByNoHP($NoHP, $IsAktif = null)
    {
        $query = DB::table('mstperson as p')
            ->where('p.NoHP', $NoHP);

        if ($IsAktif !== null) {
            $query->where('p.IsAktif', $IsAktif);
        }

        return $this->findAll($query);
    }

    public function getByNIK($NIK, $IsAktif = null)
    {
        $query = DB::table('mstperson as p')
            ->where('p.NIK', $NIK);

        if ($IsAktif !== null) {
            $query->where('p.IsAktif', $IsAktif);
        }

        return $this->findAll($query);
    }

    public function getByLogin($UserNameOrNoHP, $Password, $StatusPerson = null)
    {
        $data = [];
        $query = MstPerson::where(function ($q) use ($UserNameOrNoHP) {
            $q->where('UserName', $UserNameOrNoHP)
                ->orWhere('NoHP', $UserNameOrNoHP);
        });

        if ($StatusPerson !== null) {
            $query->where('StatusPerson', $StatusPerson);
        }

        $user = $query->first();

        if (!$user) {
            return null;
        }

        if (Hash::needsRehash($user->Password) === false) {
            if (!Hash::check($Password, $user->Password)) {
                return null;
            }
        } else {
            if ($user->Password === md5($Password)) {
                $user->update([
                    'Password' => Hash::make($Password)
                ]);
            } else {
                return null;
            }
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $query = DB::table('mstperson as p')
            ->where('p.IDPerson', $user->IDPerson);

        $data = [
            'data' => $this->findAll($query)[0],
            'token' => $token
        ];
        return count($data) > 0 ? $data : null;
    }

    private function findAll($query)
    {
        if ($this->getCari()) {
            $query->where(function ($q) {
                $q->where('p.NamaJamaah', 'like', '%' . $this->getCari() . '%')
                    ->orWhere('p.Alamat', 'like', '%' . $this->getCari() . '%')
                    ->orWhere('p.Keterangan', 'like', '%' . $this->getCari() . '%')
                    ->orWhere('prov.NamaProv', 'like', '%' . $this->getCari() . '%')
                    ->orWhere('kab.NamaKab', 'like', '%' . $this->getCari() . '%')
                    ->orWhere('kec.NamaKec', 'like', '%' . $this->getCari() . '%')
                    ->orWhere('desa.NamaDesa', 'like', '%' . $this->getCari() . '%');
            });
        }

        if ($this->getLimit() > 0) {
            $query->limit($this->getLimit())
                ->offset($this->getPage());
        }

        return $query
            ->select(
                'p.*',
                'prov.NamaProv',
                'kab.NamaKab',
                'kec.NamaKec',
                'desa.NamaDesa'
            )
            ->leftJoin('mstprov as prov', 'prov.IDProv', '=', 'p.IDProv')
            ->leftJoin('mstkab as kab', function ($j) {
                $j->on('kab.IDKab', '=', 'p.IDKab')
                    ->on('kab.IDProv', '=', 'p.IDProv');
            })
            ->leftJoin('mstkec as kec', function ($j) {
                $j->on('kec.IDKec', '=', 'p.IDKec')
                    ->on('kec.IDKab', '=', 'p.IDKab')
                    ->on('kec.IDProv', '=', 'p.IDProv');
            })
            ->leftJoin('mstdesa as desa', function ($j) {
                $j->on('desa.IDDesa', '=', 'p.IDDesa')
                    ->on('desa.IDKec', '=', 'p.IDKec')
                    ->on('desa.IDKab', '=', 'p.IDKab')
                    ->on('desa.IDProv', '=', 'p.IDProv');
            })
            ->get()
            ->toArray();
    }

    public function findTokenPerson(array $somePerson)
    {
        return self::whereIn('IDPerson', $somePerson)
            ->pluck('Token')
            ->filter() // buang null
            ->values()
            ->toArray();
    }


    public static function generateRandomString(int $length = 6): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function createPerson(array $data)
    {
        DB::table('mstperson')->insert($data);
        return $data['IDPerson'] ?? null;
    }

    public function updatePerson(string $IDPerson, array $data)
    {
        return DB::table('mstperson')
            ->where('IDPerson', $IDPerson)
            ->update($data);
    }

    // CI-compatible aliases and helpers
    public function insert(array $data)
    {
        return $this->createPerson($data);
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Support CI-style call: update(['IDPerson'=>..., 'Field'=>...])
        if (isset($attributes['IDPerson'])) {
            $IDPerson = $attributes['IDPerson'];
            $payload = $attributes;
            unset($payload['IDPerson']);
            return $this->updatePerson($IDPerson, $payload);
        }
        // Fallback to Eloquent update signature
        return parent::update($attributes, $options);
    }

    public function generateKodeReferral(): string
    {
        do {
            $code = 'REF-' . strtoupper(substr(self::generateRandomString(8), 0, 8));
            $exists = DB::table('mstperson')->where('KodeReferral', $code)->exists();
        } while ($exists);
        return $code;
    }



    public function verifikasi($IDPerson, $KodeVerifikasi)
    {
        return DB::table('mstperson')
            ->where('IDPerson', $IDPerson)
            ->where('KodeVerifikasi', $KodeVerifikasi)
            ->update(['IsVerified' => 1]);
    }

    public function updatePassword($IDPerson, $Password)
    {
        return DB::table('mstperson')
            ->where('IDPerson', $IDPerson)
            ->update(['Password' => md5($Password)]);
    }

    public function updatePersonData($IDPerson, array $data)
    {
        unset($data['IDPerson']); // Remove IDPerson from data to update
        return DB::table('mstperson')
            ->where('IDPerson', $IDPerson)
            ->update($data);
    }

    public function deactivatePerson($IDPerson)
    {
        return DB::table('mstperson')
            ->where('IDPerson', $IDPerson)
            ->update(['IsAktif' => 0]);
    }
}
