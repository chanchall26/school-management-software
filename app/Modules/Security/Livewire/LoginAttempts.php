<?php

declare(strict_types=1);

namespace App\Modules\Security\Livewire;

use App\Modules\Security\Models\LoginAttempt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('panel.layouts.app')]
#[Title('Login Attempts')]
class LoginAttempts extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $filter      = 'all';   // all | failed | success
    public string $period      = '24h';   // 24h | 7d | 30d

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPeriod(): void
    {
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        $since = match ($this->period) {
            '7d'  => now()->subDays(7),
            '30d' => now()->subDays(30),
            default => now()->subHours(24),
        };

        $attempts = LoginAttempt::with('user')
            ->where('attempted_at', '>=', $since)
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('email', 'like', '%' . $this->search . '%')
                  ->orWhere('ip_address', 'like', '%' . $this->search . '%');
            }))
            ->when($this->filter === 'failed', fn ($q) => $q->where('is_success', false))
            ->when($this->filter === 'success', fn ($q) => $q->where('is_success', true))
            ->latest('attempted_at')
            ->paginate(20);

        return view('livewire.modules.security.login-attempts', compact('attempts'));
    }
}
