<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; color: #1e1b4b; margin: 0; padding: 0; background: #f6f7fb; }
        .header { background: #16a34a; color: white; padding: 34px 40px; }
        .header h1 { margin: 0; font-size: 24px; font-family: Georgia, serif; font-weight: normal; }
        .content { background: white; padding: 30px 40px; }
        .milestone-box { background: #f6f7fb; border-radius: 10px; padding: 16px; margin-top: 16px; }
        .milestone-title { font-weight: bold; font-size: 15px; }
        .milestone-desc { color: #6b7280; font-size: 13px; margin-top: 4px; }
        .btn { display: inline-block; margin-top: 24px; padding: 12px 24px; background: #4f46e5; color: white !important; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 14px; }
        .footer { padding: 20px 40px; color: #6b7280; font-size: 11px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✓ Milestone Completed</h1>
    </div>
    <div class="content">
        <p>Great news! A milestone on your project <strong>{{ $milestone->project->name }}</strong> has been marked as complete.</p>

        <div class="milestone-box">
            <div class="milestone-title">{{ $milestone->title }}</div>
            @if($milestone->description)
                <div class="milestone-desc">{{ $milestone->description }}</div>
            @endif
        </div>

        <a href="{{ url('/dashboard') }}" class="btn">View on Dashboard</a>
    </div>
    <div class="footer">ClientHub &middot; This is an automated message.</div>
</body>
</html>