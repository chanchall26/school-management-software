<?php

declare(strict_types=1);

namespace App\Livewire\Panel;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PanelHeader extends Component
{
    public string $schoolName  = '';
    public string $userName    = '';
    public string $userInitials = '';
    public string $userRole    = '';

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->userName = $user?->name ?? 'Admin';

        // Build initials from up to 2 words of the name
        $this->userInitials = collect(explode(' ', $this->userName))
            ->take(2)
            ->map(fn (string $word) => strtoupper($word[0] ?? ''))
            ->implode('');

        try {
            $this->userRole = $user?->getRoleNames()->first() ?? 'Admin';
        } catch (\Throwable) {
            $this->userRole = 'Admin';
        }

        $this->schoolName = tenancy()->initialized ? (tenant('name') ?? 'School') : 'School';
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/admin/login', navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.panel.panel-header');
    }
}
