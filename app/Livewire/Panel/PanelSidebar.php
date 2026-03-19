<?php

declare(strict_types=1);

namespace App\Livewire\Panel;

use App\Support\ModuleRegistry;
use Illuminate\Support\Collection;
use Livewire\Component;

class PanelSidebar extends Component
{
    /** @var Collection<string, class-string> Enabled module classes keyed by module ID */
    public Collection $modules;

    public string $schoolName = '';

    public function mount(): void
    {
        $this->modules    = ModuleRegistry::enabled();
        $this->schoolName = tenancy()->initialized ? (tenant('name') ?? 'School') : 'School';
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.panel.panel-sidebar');
    }
}
