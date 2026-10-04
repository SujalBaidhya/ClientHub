<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; color: #1e1b4b; margin: 0; padding: 0; background: #f6f7fb; }
        .header { background: #1e1b4b; color: white; padding: 34px 40px; }
        .header h1 { margin: 0; font-size: 24px; font-family: Georgia, serif; font-weight: normal; }
        .content { background: white; padding: 30px 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        td { padding: 10px 0; border-bottom: 1px solid #e4e4f0; font-size: 14px; }
        .label-cell { color: #6b7280; }
        .value-cell { font-weight: bold; text-align: right; }
                .total { font-size: 22px; font-family: Georgia, serif; color: #a16207; }
        .btn { display: inline-block; margin-top: 24px; padding: 12px 24px; background: #4f46e5; color: white !important; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 14px; }
        .footer { padding: 20px 40px; color: #6b7280; font-size: 11px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Invoice</h1>
    </div>
    <div class="content">
        <p>A new invoice has been added to your project <strong>{{ $invoice->project->name }}</strong>.</p>

        <table>
            <tr>
                <td class="label-cell">Invoice Number</td>
                <td class="value-cell">{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td class="label-cell">Status</td>
                <td class="value-cell">{{ ucfirst($invoice->status) }}</td>
            </tr>
            <tr>
                <td class="label-cell">Due Date</td>
                <td class="value-cell">{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Amount</td>
                <td class="value-cell total">NPR {{ number_format($invoice->amount, 2) }}</td>
            </tr>
        </table>

        <a href="{{ url('/dashboard') }}" class="btn">View on Dashboard</a>
    </div>
    <div class="footer">ClientHub &middot; This is an automated message.</div>
</body>
</html>