<?php

declare(strict_types=1);

namespace App\Livewire\Panel;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('panel.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.panel.dashboard');
    }
}
