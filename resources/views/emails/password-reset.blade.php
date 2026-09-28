<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your DELIVER Password</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: Arial, sans-serif;">

    <div style="max-width: 560px; margin: 0 auto; padding: 40px 20px;">

        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 32px;">

            <h1 style="margin: 0; color: #0f172a; font-size: 24px;">
                Reset Your Password
            </h1>

            <p style="margin-top: 20px; color: #334155; line-height: 1.6;">
                We received a request to reset your DELIVER account password.
            </p>

            <p style="color: #334155; line-height: 1.6;">
                Click the button below to choose a new password.
            </p>

            <div style="margin: 30px 0;">
                <a
                    href="{{ $resetUrl }}"
                    style="
                        display: inline-block;
                        padding: 14px 24px;
                        background-color: #2563eb;
                        color: #ffffff;
                        text-decoration: none;
                        border-radius: 10px;
                        font-weight: 600;
                    "
                >
                    Reset Password
                </a>
            </div>

            <p style="color: #64748b; font-size: 14px; line-height: 1.6;">
                This link will expire after 60 minutes.
            </p>

            <p style="color: #64748b; font-size: 14px; line-height: 1.6;">
                If you did not request a password reset, you can safely ignore this email.
            </p>

            <hr style="margin: 28px 0; border: 0; border-top: 1px solid #e2e8f0;">

            <p style="margin: 0; color: #94a3b8; font-size: 12px;">
                DELIVER
            </p>

        </div>

    </div>

</body>
</html>