<?php

declare(strict_types=1);

namespace App\Modules\Security\Services;

/**
 * Simple math-based CAPTCHA — no external dependencies.
 * Default CAPTCHA provider for the Security module.
 */
class MathCaptchaService
{
    private const SESSION_KEY = 'security_captcha_answer';

    public function generateCaptcha(): array
    {
        $num1     = random_int(1, 20);
        $num2     = random_int(1, 10);
        $operator = ['+', '-'][random_int(0, 1)];
        $answer   = $operator === '+' ? $num1 + $num2 : $num1 - $num2;

        session([self::SESSION_KEY => hash('sha256', (string) $answer)]);

        return [
            'question' => "What is {$num1} {$operator} {$num2}?",
            'token'    => null, // no token needed for math CAPTCHA
        ];
    }

    public function validateCaptcha(string $answer): bool
    {
        $stored = session(self::SESSION_KEY);

        if (! $stored) {
            return false;
        }

        session()->forget(self::SESSION_KEY);

        return hash('sha256', trim($answer)) === $stored;
    }
}
