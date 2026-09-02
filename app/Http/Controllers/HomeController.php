<?php

namespace App\Http\Controllers;

use App\Models\MstPaket;
use App\Models\MstBanner;
use App\Models\MstTravel;
use Illuminate\Http\Request;
use App\Models\TransaksiPromo;

class HomeController extends Controller
{
    protected $banner;
    protected $promo;
    protected $paket;
    protected $travel;

    public function __construct(
        MstBanner $banner,
        TransaksiPromo $promo,
        MstPaket $paket,
        MstTravel $travel
    ) {
        $this->banner = $banner;
        $this->promo = $promo;
        $this->paket = $paket;
        $this->travel = $travel;
    }

    public function index(Request $request)
    {
        $type = $request->query('type', '');

        $wherePaket = ['IsAktif' => 1];

        if ($type !== '') {
            $wherePaket['KategoriPaket'] = $type;
        }

        $data = [
            'banner' => $this->banner->where('IsAktif', 1)->get(),

            'promo'  => $this->promo->getPromoAktif(),

            'paket'  => $this->paket->getLatestPaket(8, $wherePaket),

            'travel' => $this->travel->where('IsShow', 1)->get(),

            'type'   => $type,
        ];

        return view('front-page.home', $data);
    }
}
