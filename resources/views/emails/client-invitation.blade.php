<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f8fafc; margin: 0; padding: 24px; color: #1e293b; }
        .card { max-width: 540px; margin: 0 auto; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .btn { display: inline-block; background-color: #4f46e5; color: #ffffff !important; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 24px 0; }
        .footer { font-size: 12px; color: #64748b; margin-top: 24px; border-top: 1px solid #e2e8f0; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="margin-top: 0; color: #0f172a;">Welcome to ClientHub</h2>
        <p>Hello {{ $user->name }},</p>
        <p>An account has been set up for you. Click the button below to set your account password and access your dashboard:</p>
        
        <p style="text-align: center;">
            <a href="{{ $inviteUrl }}" class="btn">Set Up Your Password</a>
        </p>

        <p style="font-size: 13px; color: #64748b;">Or copy and paste this URL into your browser:<br>{{ $inviteUrl }}</p>

        <div class="footer">
            If you did not expect this invitation, you can safely ignore this message.
        </div>
    </div>
</body>
</html>