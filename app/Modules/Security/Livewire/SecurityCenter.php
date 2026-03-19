<?php

declare(strict_types=1);

namespace App\Modules\Security\Livewire;

use App\Models\User;
use App\Modules\Security\Models\LoginAttempt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('panel.layouts.app')]
#[Title('Security Center')]
class SecurityCenter extends Component
{
    public int $totalUsers       = 0;
    public int $activeUsers      = 0;
    public int $lockedAccounts   = 0;
    public int $failedToday      = 0;
    public int $successToday     = 0;
    public int $failedThisWeek   = 0;

    /** @var \Illuminate\Support\Collection<int, \App\Modules\Security\Models\LoginAttempt> */
    public $recentFailed;

    /** @var \Illuminate\Support\Collection<int, \App\Models\User> */
    public $lockedUsers;

    public function mount(): void
    {
        $this->loadStats();
    }

    public function loadStats(): void
    {
        $this->totalUsers    = User::count();
        $this->activeUsers   = User::where('is_active', true)->count();
        $this->lockedAccounts = User::where('is_locked', true)
            ->where(fn ($q) => $q->whereNull('locked_until')->orWhere('locked_until', '>', now()))
            ->count();

        $this->failedToday  = LoginAttempt::where('is_success', false)
            ->where('attempted_at', '>=', now()->startOfDay())
            ->count();

        $this->successToday = LoginAttempt::where('is_success', true)
            ->where('attempted_at', '>=', now()->startOfDay())
            ->count();

        $this->failedThisWeek = LoginAttempt::where('is_success', false)
            ->where('attempted_at', '>=', now()->startOfWeek())
            ->count();

        $this->recentFailed = LoginAttempt::where('is_success', false)
            ->where('attempted_at', '>=', now()->subHours(24))
            ->latest('attempted_at')
            ->limit(8)
            ->get();

        $this->lockedUsers = User::where('is_locked', true)
            ->where(fn ($q) => $q->whereNull('locked_until')->orWhere('locked_until', '>', now()))
            ->get();
    }

    public function unlockUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update([
            'is_locked'             => false,
            'locked_until'          => null,
            'failed_login_attempts' => 0,
            'last_failed_attempt'   => null,
        ]);
        $this->loadStats();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.modules.security.security-center');
    }
}
