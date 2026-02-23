<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f9; font-family:Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0; background-color:#f4f6f9;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:8px; padding:40px;">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding-bottom:30px;">
                            <h2 style="margin:0; color:#2d3748;">
                                Password Reset Request
                            </h2>
                        </td>
                    </tr>

                    <!-- Greeting -->
                    <tr>
                        <td style="color:#4a5568; font-size:16px; line-height:1.6;">
                            Hello <strong>{{ $user->name }}</strong>,
                            <br><br>
                            We received a request to reset your account password.
                            If you made this request, please click the button below to set a new password.
                        </td>
                    </tr>

                    <!-- Button -->
                    <tr>
                        <td align="center" style="padding:30px 0;">
                            <a href="{{ url('/reset-password/'.$token) }}"
                                style="background-color:#2563eb;
                                  color:#ffffff;
                                  padding:12px 24px;
                                  text-decoration:none;
                                  border-radius:6px;
                                  font-weight:bold;
                                  display:inline-block;">
                                Reset Password
                            </a>
                        </td>
                    </tr>

                    <!-- Expiration Notice -->
                    <tr>
                        <td style="color:#4a5568; font-size:14px; line-height:1.6;">
                            This password reset link will expire in 60 minutes.
                            <br><br>
                            If you did not request a password reset, no further action is required.
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding:30px 0;">
                            <hr style="border:none; border-top:1px solid #e2e8f0;">
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="color:#a0aec0; font-size:12px; text-align:center;">
                            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.

                              
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>