<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f5f7;
            margin: 0;
            padding: 20px;
            color: #333333;
        }
        .container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }
        .header {
            background-color: #1e1b4b;
            color: #ffffff;
            padding: 28px 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-family: Georgia, serif;
            font-weight: normal;
        }
        .content {
            padding: 24px;
        }
        .receipt-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 16px;
            margin: 20px 0;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #edf2f7;
        }
        .item-row:last-child {
            border-bottom: none;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            padding-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Received</h1>
        </div>
        <div class="content">
            <p>Hi {{ $invoice->project->client->name }},</p>
            <p>Thank you for your payment. Your invoice has been marked as <strong>Paid</strong>.</p>

            <div class="receipt-card">
                <div class="item-row">
                    <span>Invoice Number:</span>
                    <span>#{{ $invoice->invoice_number }}</span>
                </div>
                <div class="item-row">
                    <span>Project:</span>
                    <span>{{ $invoice->project->name }}</span>
                </div>
                <div class="item-row">
                    <span>Amount Paid:</span>
                    <span style="font-family: Georgia, serif; color: #a16207; font-size: 15px;">NPR {{ number_format($invoice->amount, 2) }}</span>
                </div>
                <div class="item-row">
                    <span>Status:</span>
                    <span style="color: #16a34a;">PAID</span>
                </div>
            </div>

            <p>Your paid invoice receipt has been generated and attached to this email as a PDF. You can also view and download it anytime from your dashboard.</p>
        </div>
        <div class="footer">
            &copy; ClientHub. All rights reserved.
        </div>
    </div>
</body>
</html>