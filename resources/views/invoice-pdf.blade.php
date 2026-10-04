<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #1e1b4b;
            padding: 0;
            margin: 0;
            font-size: 13px;
        }
        .header {
            background: #1e1b4b;
            color: white;
            padding: 34px 40px;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            font-family: 'Georgia', serif;
            font-weight: normal;
        }
        .header p {
            margin: 4px 0 0;
            opacity: 0.85;
            font-size: 13px;
        }
        .content {
            padding: 30px 40px;
        }
        .invoice-meta {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .invoice-meta-left, .invoice-meta-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .invoice-meta-right {
            text-align: right;
        }
        .label {
            color: #6b7280;
            font-size: 11px;
            text-transform: none;
            letter-spacing: 0;
        }
        .value {
            font-size: 14px;
            font-weight: bold;
            margin-top: 2px;
        }
        table.line-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.line-items th {
            text-align: left;
            background: #f6f7fb;
            padding: 10px 12px;
            font-size: 11px;
            text-transform: none;
            letter-spacing: 0;
            color: #6b7280;
            border-bottom: 2px solid #e4e4f0;
        }
        table.line-items td {
            padding: 14px 12px;
            border-bottom: 1px solid #e4e4f0;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-pending { background: #e5e7eb; color: #374151; }
        .status-overdue { background: #fee2e2; color: #991b1b; }
        .total-box {
            margin-top: 20px;
            text-align: right;
        }
        .total-box .total-label {
            font-size: 12px;
            color: #6b7280;
        }
        .total-box .total-amount {
            font-size: 28px;
            font-weight: bold;
            font-family: 'Georgia', serif;
            color: #a16207;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e4e4f0;
            color: #6b7280;
            font-size: 11px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>ClientHub</h1>
        <p>Invoice {{ $invoice->invoice_number }}</p>
    </div>

    <div class="content">

        <div class="invoice-meta">
            <div class="invoice-meta-left">
                <div class="label">Billed To</div>
                <div class="value">{{ $invoice->project->client->name ?? 'N/A' }}</div>
            </div>
            <div class="invoice-meta-right">
                <div class="label">Status</div>
                <div style="margin-top: 4px;">
                    <span class="status-badge status-{{ $invoice->status }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
            </div>
        </div>

        <table class="line-items">
            <tr>
                <th>Project</th>
                <th>Issued</th>
                <th>Due Date</th>
            </tr>
            <tr>
                <td>{{ $invoice->project->name }}</td>
                <td>{{ $invoice->issued_at?->format('M d, Y') ?? '—' }}</td>
                <td>{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</td>
            </tr>
        </table>

        <div class="total-box">
            <div class="total-label">Total Amount</div>
            <div class="total-amount">NPR {{ number_format($invoice->amount, 2) }}</div>
        </div>

        <div class="footer">
            Thank you for your business. If you have any questions about this invoice, please contact your project manager.
        </div>

    </div>

</body>
</html>