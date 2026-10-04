<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; color: #1e1b4b; margin: 0; padding: 0; background: #f6f7fb; }
        .header { background: #1e1b4b; color: white; padding: 34px 40px; }
        .header h1 { margin: 0; font-size: 24px; font-family: Georgia, serif; font-weight: normal; }
        .content { background: white; padding: 30px 40px; }
        .label { color: #6b7280; font-size: 12px; text-transform: none; letter-spacing: 0; }
        .value { font-size: 15px; font-weight: bold; margin: 2px 0 16px; }
        .btn { display: inline-block; padding: 12px 24px; background: #4f46e5; color: white !important; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px; }
        .footer { padding: 20px 40px; color: #6b7280; font-size: 11px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to ClientHub</h1>
    </div>
    <div class="content">
        <p>Hi {{ $client->name }},</p>
        <p>An account has been created for you on ClientHub, where you can track your project's progress, milestones, files, and invoices in one place.</p>

        <div class="label">Login Email</div>
        <div class="value">{{ $client->email }}</div>

        <a href="{{ url('/login') }}" class="btn">Log In to ClientHub</a>

        <p style="margin-top: 24px; color: #6b7280; font-size: 13px;">If you have any questions, please reach out to your project manager.</p>
    </div>
    <div class="footer">ClientHub &middot; This is an automated message.</div>
</body>
</html>