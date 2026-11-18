<?php

namespace App\Livewire;

use App\Services\TableauDeBordService;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tableau de bord')]
class Dashboard extends Component
{
    public function render(TableauDeBordService $service)
    {
        return view('livewire.dashboard', ['data' => $service->pour(auth()->user())]);
    }
}
