<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\Landing\BerandaService;
use Livewire\Attributes\Computed;

// intro
// layout : untuk menentukan layout mana yang akan digunakan pada folder layouts pada livewire
// https://livewire.laravel.com/docs/4.x/attribute-layout#basic-usage
// computed : utnuk menampilkan data yang hanya read only di livewire agar lebih optimal
// https://livewire.laravel.com/docs/4.x/attribute-computed#basic-usage

new #[Layout('layouts::landing')] class extends Component {

    #[Computed]
    public function listSlider()
    {
        return app(BerandaService::class)->getSlider();
    }

    #[Computed]
    public function memahamiTentang()
    {
        return app(BerandaService::class)->getMemahamiTentang();
    }

    #[Computed]
    public function peranHaromain()
    {
        return app(BerandaService::class)->getPeranHaromain();
    }

    #[Computed]
    public function peranHaromainList()
    {
        return app(BerandaService::class)->getPeranHaromainList();
    }

    #[Computed]
    public function peranHaromainFooter()
    {
        return app(BerandaService::class)->getPeranHaromainFooter();
    }

    #[Computed]
    public function kenalLebih()
    {
        return app(BerandaService::class)->getKenalLebih();
    }

    #[Computed]
    public function haromainBersama()
    {
        return app(BerandaService::class)->getHaromainBersama();
    }

    #[Computed]
    public function fiturUnggulan()
    {
        return app(BerandaService::class)->getFiturUnggulan();
    }

    #[Computed]
    public function fiturUnggulanList()
    {
        return app(BerandaService::class)->getFiturUnggulanList();
    }

    #[Computed]
    public function siapaYang()
    {
        return app(BerandaService::class)->getSiapaYang();
    }

    #[Computed]
    public function siapaYangList()
    {
        return app(BerandaService::class)->siapaYangList();
    }

    #[Computed]
    public function kondisiPengguna()
    {
        return app(BerandaService::class)->getKondisiPengguna();
    }

    #[Computed]
    public function mitraTravelHeader()
    {
        return app(BerandaService::class)->getMitraTravelHeader();
    }

    #[Computed]
    public function mitraTravelList()
    {
        return app(BerandaService::class)->getMitraTravelList();
    }

    #[Computed]
    public function testimonial()
    {
        return app(BerandaService::class)->getTestimonial();
    }

    #[Computed]
    public function testimonialList()
    {
        return app(BerandaService::class)->getTestimonialList();
    }

    #[Computed]
    public function pertanyaan()
    {
        return app(BerandaService::class)->getPertanyaan();
    }

    #[Computed]
    public function pertanyaanList()
    {
        return app(BerandaService::class)->getPertanyaanList();
    }

    #[Computed]
    public function actionButton()
    {
        return app(BerandaService::class)->getActionButton();
    }

    #[Computed]
    public function caraMemulai()
    {
        return app(BerandaService::class)->getCaraMemulai();
    }


};
