<?php

declare(strict_types=1);

namespace App\Modules\Users\Livewire;

use App\Models\User;
use App\Modules\Users\Actions\ToggleUserStatus;
use App\Modules\Users\Actions\UpdateUser;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('panel.layouts.app')]
#[Title('Users')]
class UserList extends Component
{
    use WithPagination, WithFileUploads;

    // ── List state ────────────────────────────────────────────────────────────

    public string $search = '';
    public string $filterRole = 'all';
    public string $filterStatus = 'all';

    // ── Preview panel ─────────────────────────────────────────────────────────

    public bool $showPreview = false;
    public ?int $previewId = null;

    // ── Form slide-over state ─────────────────────────────────────────────────

    public bool $showForm = false;
    public int $step = 1;
    public ?int $editingId = null;

    // ── Import panel state ────────────────────────────────────────────────────

    public bool $showImport = false;
    public string $importEmails = '';

    // ── Step 1 fields ─────────────────────────────────────────────────────────

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public $avatar = null;
    public string $role_type = 'staff';
    public string $role_label = '';          // used when role_type = 'other'
    public string $password = '';
    public string $password_confirmation = '';

    // ── Step 2 fields ─────────────────────────────────────────────────────────

    public bool $restrict_access = false;
    public bool $can_login_app = true;
    public bool $show_login_status = true;
    public bool $time_restriction = false;

    // ── List watchers ─────────────────────────────────────────────────────────

    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingFilterRole(): void  { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    // ── Preview panel ─────────────────────────────────────────────────────────

    public function openPreview(int $id): void
    {
        $this->previewId = $id;
        $this->showPreview = true;
        $this->showForm = false;
        $this->showImport = false;
    }

    public function closePreview(): void
    {
        $this->showPreview = false;
        $this->previewId = null;
    }

    // ── Form actions ──────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->dispatch('open-create-user-form');
    }

    #[On('user-created')]
    public function onUserCreated(): void
    {
        $this->resetPage();
    }

    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);

        $this->editingId           = $id;
        $this->name                = $user->name;
        $this->email               = $user->email;
        $this->phone               = $user->phone ?? '';
        $this->avatar              = null;
        $this->role_type           = $user->role_type ?? 'staff';
        $this->role_label          = $user->role_label ?? '';
        $this->password            = '';
        $this->password_confirmation = '';
        $this->restrict_access     = (bool) $user->restrict_access;
        $this->can_login_app       = (bool) $user->can_login_app;
        $this->show_login_status   = (bool) $user->show_login_status;
        $this->time_restriction    = ! empty($user->allowed_access_times);

        $this->step        = 1;
        $this->showForm    = true;
        $this->showPreview = false;
        $this->showImport  = false;
    }

    public function nextStep(): void
    {
        $uniqueRule = 'required|email|unique:users,email' . ($this->editingId ? ",{$this->editingId}" : '');

        $rules = [
            'name'      => 'required|string|max:100',
            'email'     => $uniqueRule,
            'phone'     => 'nullable|string|max:20',
            'avatar'    => 'nullable|image|max:2048',
            'role_type' => 'required|in:staff,teacher,other',
            'password'  => $this->editingId
                ? 'nullable|min:8|confirmed'
                : 'required|min:8|confirmed',
        ];

        if ($this->role_type === 'other') {
            $rules['role_label'] = 'required|string|max:100';
        }

        $this->validate($rules);
        $this->step = 2;
    }

    public function prevStep(): void
    {
        $this->step = 1;
    }

    public function save(): void
    {
        $this->validate([
            'restrict_access'   => 'boolean',
            'can_login_app'     => 'boolean',
            'show_login_status' => 'boolean',
            'time_restriction'  => 'boolean',
        ]);

        $data = [
            'name'               => $this->name,
            'email'              => $this->email,
            'phone'              => $this->phone ?: null,
            'avatar'             => $this->avatar,
            'role_type'          => $this->role_type,
            'role_label'         => $this->role_label ?: null,
            'password'           => $this->password,
            'restrict_access'    => $this->restrict_access,
            'can_login_app'      => $this->can_login_app,
            'show_login_status'  => $this->show_login_status,
            'time_restriction'   => $this->time_restriction,
        ];

        if ($this->editingId) {
            (new UpdateUser)->handle(User::findOrFail($this->editingId), $data);
        }

        $this->cancelForm();
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    // ── Import panel ──────────────────────────────────────────────────────────

    public function openImport(): void
    {
        $this->showImport  = true;
        $this->showForm    = false;
        $this->showPreview = false;
        $this->importEmails = '';
    }

    public function closeImport(): void
    {
        $this->showImport   = false;
        $this->importEmails = '';
    }

    // ── Delete user ───────────────────────────────────────────────────────────

    public function deleteUser(int $id): void
    {
        User::findOrFail($id)->delete();

        if ($this->previewId === $id) {
            $this->showPreview = false;
            $this->previewId   = null;
        }
    }

    // ── Toggle active ─────────────────────────────────────────────────────────

    public function toggleActive(int $id): void
    {
        (new ToggleUserStatus)->handle($id);
        // refresh preview if that user is being previewed
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        $query = User::query();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterRole !== 'all') {
            $query->where('role_type', $this->filterRole);
        }

        if ($this->filterStatus === 'active') {
            $query->where('is_active', true)->where('is_locked', false);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('is_active', false);
        } elseif ($this->filterStatus === 'locked') {
            $query->where('is_locked', true);
        }

        $users = $query->orderBy('name')->paginate(15);

        $previewUser = $this->previewId ? User::find($this->previewId) : null;

        return view('livewire.modules.users.user-list', [
            'users'       => $users,
            'previewUser' => $previewUser,
        ]);
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

    public function getEditingUserAvatar(): ?string
    {
        if (! $this->editingId) {
            return null;
        }

        $user = User::find($this->editingId);

        return ($user && $user->avatar)
            ? \Illuminate\Support\Facades\Storage::url($user->avatar)
            : null;
    }

    private function resetForm(): void
    {
        $this->step                  = 1;
        $this->editingId             = null;
        $this->name                  = '';
        $this->email                 = '';
        $this->phone                 = '';
        $this->avatar                = null;
        $this->role_type             = 'staff';
        $this->role_label            = '';
        $this->password              = '';
        $this->password_confirmation = '';
        $this->restrict_access       = false;
        $this->can_login_app         = true;
        $this->show_login_status     = true;
        $this->time_restriction      = false;
        $this->resetValidation();
    }
}
