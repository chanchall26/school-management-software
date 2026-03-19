<?php

declare(strict_types=1);

namespace App\Modules\Security\Services;

use App\Models\User;
use App\Modules\Security\Models\DeviceFingerprint;

class DeviceFingerprintService
{
    public function generateFingerprint(array $deviceData): string
    {
        return hash('sha256', json_encode($deviceData));
    }

    public function extractDeviceInfo(): array
    {
        return [
            'ip_address'  => request()->ip(),
            'browser'     => $this->getBrowser(),
            'os'          => $this->getOperatingSystem(),
            'device_name' => $this->getDeviceName(),
            'user_agent'  => request()->userAgent(),
        ];
    }

    public function getOrCreateFingerprint(User $user): DeviceFingerprint
    {
        $deviceInfo  = $this->extractDeviceInfo();
        $fingerprint = $this->generateFingerprint($deviceInfo);

        return DeviceFingerprint::findOrCreate($user, $fingerprint, $deviceInfo);
    }

    public function isTrustedDevice(User $user): bool
    {
        $deviceInfo  = $this->extractDeviceInfo();
        $fingerprint = $this->generateFingerprint($deviceInfo);

        $device = DeviceFingerprint::where('fingerprint_hash', $fingerprint)
            ->where('user_id', $user->id)
            ->first();

        return $device && $device->is_trusted;
    }

    public function trustDevice(User $user): void
    {
        $fingerprint = $this->getOrCreateFingerprint($user);
        $fingerprint->update(['is_trusted' => true]);
    }

    public function revokeDeviceTrust(User $user, int $deviceId): bool
    {
        return (bool) DeviceFingerprint::where('id', $deviceId)
            ->where('user_id', $user->id)
            ->update(['is_trusted' => false]);
    }

    public function getTrustedDevices(User $user): array
    {
        return DeviceFingerprint::where('user_id', $user->id)
            ->where('is_trusted', true)
            ->get()
            ->toArray();
    }

    private function getBrowser(): string
    {
        $ua = (string) request()->userAgent();

        if (str_contains($ua, 'Edg'))     return 'Edge';
        if (str_contains($ua, 'Chrome'))  return 'Chrome';
        if (str_contains($ua, 'Safari'))  return 'Safari';
        if (str_contains($ua, 'Firefox')) return 'Firefox';
        if (str_contains($ua, 'MSIE') || str_contains($ua, 'Trident')) return 'Internet Explorer';

        return 'Unknown';
    }

    private function getOperatingSystem(): string
    {
        $ua = (string) request()->userAgent();

        if (str_contains($ua, 'Android')) return 'Android';
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) return 'iOS';
        if (str_contains($ua, 'Windows')) return 'Windows';
        if (str_contains($ua, 'Mac'))     return 'macOS';
        if (str_contains($ua, 'Linux'))   return 'Linux';

        return 'Unknown';
    }

    private function getDeviceName(): string
    {
        $ua = (string) request()->userAgent();

        if (str_contains($ua, 'Mobile'))  return 'Mobile Device';
        if (str_contains($ua, 'Tablet'))  return 'Tablet';

        return 'Desktop';
    }
}
