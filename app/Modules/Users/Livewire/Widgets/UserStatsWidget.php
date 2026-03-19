<?php

declare(strict_types=1);

namespace App\Modules\Users\Livewire\Widgets;

use App\Models\User;
use Livewire\Component;

class UserStatsWidget extends Component
{
    public int $totalUsers = 0;
    public int $activeUsers = 0;
    public int $lockedUsers = 0;
    public int $staffCount = 0;
    public int $teacherCount = 0;

    public function mount(): void
    {
        $this->totalUsers   = User::count();
        $this->activeUsers  = User::where('is_active', true)->count();
        $this->lockedUsers  = User::where('is_locked', true)->count();
        $this->staffCount   = User::where('role_type', 'staff')->count();
        $this->teacherCount = User::where('role_type', 'teacher')->count();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.modules.users.widgets.user-stats-widget');
    }
}
