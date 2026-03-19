<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SmsService — Plug-and-play SMS abstraction.
 *
 * To activate SMS, set SMS_PROVIDER in .env:
 *
 *   SMS_PROVIDER=fast2sms   → set SMS_FAST2SMS_API_KEY
 *   SMS_PROVIDER=msg91      → set SMS_MSG91_API_KEY, SMS_MSG91_SENDER_ID, SMS_MSG91_TEMPLATE_ID
 *   SMS_PROVIDER=twilio     → set SMS_TWILIO_SID, SMS_TWILIO_TOKEN, SMS_TWILIO_FROM
 *   SMS_PROVIDER=none       → SMS disabled (default — returns false)
 */
class SmsService
{
    /**
     * Send an OTP SMS. Returns true on success, false on failure.
     */
    public function sendOtp(string $phone, string $otp, string $schoolName): bool
    {
        $message = "Your {$schoolName} login OTP is: {$otp}. Valid for 10 minutes. Do not share.";

        return $this->send($phone, $message);
    }

    /**
     * Send any SMS message. Returns true on success, false on failure.
     */
    public function send(string $phone, string $message): bool
    {
        $provider = config('services.sms.provider', 'none');

        return match ($provider) {
            'fast2sms' => $this->sendFast2Sms($phone, $message),
            'msg91'    => $this->sendMsg91($phone, $message),
            'twilio'   => $this->sendTwilio($phone, $message),
            default    => false,
        };
    }

    /**
     * Fast2SMS — India (cheapest, easiest).
     * Docs: https://docs.fast2sms.com
     * Get API key: https://www.fast2sms.com/dashboard/apikey
     */
    private function sendFast2Sms(string $phone, string $message): bool
    {
        $apiKey = config('services.sms.fast2sms.api_key');

        if (! $apiKey) {
            Log::warning('SmsService: SMS_FAST2SMS_API_KEY not set.');
            return false;
        }

        // Strip country code for Fast2SMS (India only — expects 10-digit number)
        $phone = preg_replace('/^\+?91/', '', preg_replace('/\s+/', '', $phone));

        try {
            $response = Http::withHeaders(['authorization' => $apiKey])
                ->post('https://www.fast2sms.com/dev/bulkV2', [
                    'route'    => 'q',
                    'message'  => $message,
                    'language' => 'english',
                    'flash'    => 0,
                    'numbers'  => $phone,
                ]);

            if ($response->successful() && ($response->json('return') === true)) {
                return true;
            }

            Log::warning('SmsService Fast2SMS failed.', ['body' => $response->body()]);
            return false;

        } catch (\Throwable $e) {
            Log::error('SmsService Fast2SMS exception.', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * MSG91 — India (popular, requires DLT registered template).
     * Docs: https://docs.msg91.com/reference/send-otp
     * Get API key: https://msg91.com/dashboard
     */
    private function sendMsg91(string $phone, string $message): bool
    {
        $apiKey     = config('services.sms.msg91.api_key');
        $senderId   = config('services.sms.msg91.sender_id', 'SIMPTION');
        $templateId = config('services.sms.msg91.template_id');

        if (! $apiKey || ! $templateId) {
            Log::warning('SmsService: MSG91 credentials not fully set.');
            return false;
        }

        // MSG91 expects 91 prefix for India
        $phone = preg_replace('/\s+/', '', $phone);
        if (! str_starts_with($phone, '91') && ! str_starts_with($phone, '+91')) {
            $phone = '91' . ltrim($phone, '+');
        }
        $phone = ltrim($phone, '+');

        try {
            $response = Http::withHeaders([
                'authkey'      => $apiKey,
                'content-type' => 'application/json',
            ])->post('https://control.msg91.com/api/v5/flow/', [
                'template_id' => $templateId,
                'sender'      => $senderId,
                'mobiles'     => $phone,
                'VAR1'        => $message, // map to your DLT template variable
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('SmsService MSG91 failed.', ['body' => $response->body()]);
            return false;

        } catch (\Throwable $e) {
            Log::error('SmsService MSG91 exception.', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Twilio — International (premium).
     * Docs: https://www.twilio.com/docs/sms
     * Get credentials: https://console.twilio.com
     */
    private function sendTwilio(string $phone, string $message): bool
    {
        $sid   = config('services.sms.twilio.sid');
        $token = config('services.sms.twilio.token');
        $from  = config('services.sms.twilio.from');

        if (! $sid || ! $token || ! $from) {
            Log::warning('SmsService: Twilio credentials not fully set.');
            return false;
        }

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'To'   => $phone,
                    'From' => $from,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('SmsService Twilio failed.', ['body' => $response->body()]);
            return false;

        } catch (\Throwable $e) {
            Log::error('SmsService Twilio exception.', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Whether any SMS provider is configured.
     */
    public function isConfigured(): bool
    {
        return config('services.sms.provider', 'none') !== 'none';
    }
}
