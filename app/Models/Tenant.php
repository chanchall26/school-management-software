<?php

declare(strict_types=1);

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;
    use HasDomains;

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
        ];
    }

    public function getLogoUrl(): ?string
    {
        // Stancl/Tenancy v3: non-custom-column keys from the data JSON
        // are exposed as direct model attributes — use $this->logo, NOT $this->data['logo']
        $logo = $this->logo ?? null;

        if (! $logo) {
            return null;
        }

        // Full URL (CDN / S3) — return as-is
        if (filter_var($logo, FILTER_VALIDATE_URL)) {
            return $logo;
        }

        // Relative path — verify file exists before returning URL
        if (! file_exists(public_path('storage/' . $logo))) {
            return null; // Missing file → blade shows initial avatar
        }

        return asset('storage/' . $logo);
    }
}

