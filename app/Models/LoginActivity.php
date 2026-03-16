<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'identifier',
        'method',
        'is_success',
        'failure_reason',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'is_success' => 'bool',
            'created_at' => 'datetime',
        ];
    }
}

