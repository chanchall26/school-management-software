<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Login OTP</title>
</head>
<body style="margin:0;padding:0;background-color:#F8FAFC;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;padding:40px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;">

                    {{-- Header --}}
                    <tr>
                        <td align="center" style="padding-bottom:24px;">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background:linear-gradient(135deg,#14B8A6,#0D9488);border-radius:12px;padding:14px 20px;">
                                        <span style="color:#FFFFFF;font-size:20px;font-weight:700;letter-spacing:-0.5px;">SIMPTION</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Card --}}
                    <tr>
                        <td style="background:#FFFFFF;border-radius:16px;padding:40px 36px;box-shadow:0 4px 24px rgba(15,23,42,0.08);border:1px solid #E2E8F0;">

                            <p style="margin:0 0 6px;font-size:22px;font-weight:700;color:#0F172A;letter-spacing:-0.5px;">
                                Your Login OTP
                            </p>
                            <p style="margin:0 0 28px;font-size:14px;color:#64748B;">
                                {{ $schoolName }} — Admin Portal
                            </p>

                            {{-- OTP Box --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                <tr>
                                    <td align="center" style="background:#F0FDFA;border:2px solid #14B8A6;border-radius:12px;padding:24px 16px;">
                                        <p style="margin:0 0 4px;font-size:11px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#0D9488;">
                                            One-Time Password
                                        </p>
                                        <p style="margin:0;font-size:42px;font-weight:700;letter-spacing:12px;color:#0F172A;font-family:'Courier New',monospace;">
                                            {{ $otp }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Instructions --}}
                            <p style="margin:0 0 8px;font-size:14px;color:#475569;line-height:1.6;">
                                Enter this code on the login page to access your account.
                            </p>
                            <p style="margin:0 0 28px;font-size:14px;color:#475569;line-height:1.6;">
                                This OTP expires in <strong style="color:#0F172A;">{{ $expiryMinutes }} minutes</strong>.
                                Do not share it with anyone.
                            </p>

                            {{-- Warning box --}}
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background:#FFF7ED;border-left:3px solid #F59E0B;border-radius:6px;padding:12px 16px;">
                                        <p style="margin:0;font-size:12px;color:#92400E;line-height:1.5;">
                                            <strong>Security notice:</strong> Simption will never ask for your OTP over
                                            phone or email. If you did not request this code, ignore this email.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td align="center" style="padding-top:24px;">
                            <p style="margin:0;font-size:11px;color:#94A3B8;">
                                Powered by <strong style="color:#14B8A6;">SIMPTION</strong>
                                &nbsp;·&nbsp; School Management Platform
                            </p>
                            <p style="margin:6px 0 0;font-size:11px;color:#CBD5E1;">
                                This is an automated message. Please do not reply.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
