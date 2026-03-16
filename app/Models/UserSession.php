<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'session_id',
        'method',
        'ip_address',
        'user_agent',
        'logged_in_at',
        'logged_out_at',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'logged_in_at' => 'datetime',
            'logged_out_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }
}

