<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Services\Landing\BerandaService;

new #[Layout('layouts::landing')] class extends Component {
    #[Computed]
    public function globalSetting()
    {
        return app(BerandaService::class)->getGlobalSetting();
    }
};
