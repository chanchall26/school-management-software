<?php

declare(strict_types=1);

namespace App\Livewire\Shared;

use App\Modules\Users\Actions\CreateUser;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateUserForm extends Component
{
    use WithFileUploads;

    public bool $show = false;
    public int  $step = 1;

    // ── Step 1 — Basic Info ───────────────────────────────────────────────────

    public string $role_type             = 'teacher';
    public string $name                  = '';
    public string $email                 = '';
    public string $phone                 = '';
    public $avatar                       = null;
    public string $password              = '';
    public string $password_confirmation = '';

    // ── Step 2 — Restrictions ─────────────────────────────────────────────────

    public bool $is_active        = true;
    public bool $can_login_app    = true;
    public bool $time_restriction = false;
    public bool $multi_login      = true;   // true = allow multiple sessions

    // ── Open / Close ─────────────────────────────────────────────────────────

    #[On('open-create-user-form')]
    public function open(): void
    {
        $this->reset(['name', 'email', 'phone', 'avatar', 'password', 'password_confirmation']);
        $this->role_type       = 'teacher';
        $this->is_active       = true;
        $this->can_login_app   = true;
        $this->time_restriction = false;
        $this->multi_login     = true;
        $this->step            = 1;
        $this->show            = true;
        $this->resetValidation();
    }

    public function close(): void
    {
        $this->show = false;
        $this->resetValidation();
    }

    // ── Step navigation ───────────────────────────────────────────────────────

    public function nextStep(): void
    {
        $this->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'avatar'   => 'nullable|image|max:2048',
            'password' => 'required|min:8|confirmed',
        ]);

        $this->step = 2;
    }

    public function prevStep(): void
    {
        $this->step = 1;
    }

    // ── Save ─────────────────────────────────────────────────────────────────

    public function save(): void
    {
        (new CreateUser)->handle([
            'name'              => $this->name,
            'email'             => $this->email,
            'phone'             => $this->phone ?: null,
            'avatar'            => $this->avatar,
            'role_type'         => $this->role_type,
            'password'          => $this->password,
            'is_active'         => $this->is_active,
            'can_login_app'     => $this->can_login_app,
            'time_restriction'  => $this->time_restriction,
            'restrict_access'   => ! $this->multi_login,
            'show_login_status' => true,
        ]);

        $this->show = false;
        $this->dispatch('user-created');
        $this->resetValidation();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getAvatarPreview(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        try {
            return $this->avatar->temporaryUrl();
        } catch (\Throwable) {
            return null;
        }
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        return view('livewire.shared.create-user-form');
    }
}
