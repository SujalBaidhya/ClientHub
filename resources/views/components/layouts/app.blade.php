<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ClientHub' }}</title>

    <style>
        :root {
            --bg: #f6f7fb;
            --surface: #ffffff;
            --text: #1e1b4b;
            --muted: #6b7280;
            --border: #e4e4f0;
            --primary: #4f46e5;
            --success-bg: #dcfce7;
            --success-text: #166534;
            --warning-bg: #fef3c7;
            --warning-text: #92400e;
            --danger-bg: #fee2e2;
            --danger-text: #991b1b;
            --neutral-bg: #e5e7eb;
            --neutral-text: #374151;
        }

        * { box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
            margin: 0;
            background: var(--bg);
            color: var(--text);
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            color: var(--text);
            padding: 18px 40px;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 2px rgba(79, 70, 229, 0.04);
        }

        .navbar h2 {
            margin: 0;
            font-size: 20px;
            color: var(--primary);
            font-weight: 800;
        }

        .logout-button {
            display: inline-block;
            padding: 8px 16px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: transparent;
            color: var(--text);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s ease, border-color 0.15s ease;
        }

        .logout-button:hover {
            background: #eef2ff;
            border-color: #c7d2fe;
        }

        .container {
            width: 100%;
            max-width: 1250px;
            margin: 0 auto;
            padding: 40px 28px 60px;
        }

        .card {
            background: var(--surface);
            padding: 26px;
            margin-bottom: 20px;
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.06);
        }

        .card h2,
        .card h3 {
            margin-top: 0;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            border-radius: 10px !important;
            border: 1px solid var(--border) !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            font-family: inherit;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid var(--border);
        }

        .plain-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .plain-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }

        .plain-list li:last-child {
            border-bottom: none;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }
        .badge-success { background: var(--success-bg); color: var(--success-text); }
        .badge-warning { background: var(--warning-bg); color: var(--warning-text); }
        .badge-danger { background: var(--danger-bg); color: var(--danger-text); }
        .badge-neutral { background: var(--neutral-bg); color: var(--neutral-text); }

        .empty-state {
            color: var(--muted);
            font-size: 14px;
            padding: 28px 20px;
            margin: 0;
            text-align: center;
            background: #fafafa;
            border: 1px dashed var(--border);
            border-radius: 10px;
        }

        a {
            color: var(--primary);
        }

        .file-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fafafa;
            transition: border-color 0.15s ease;
        }

        .file-item:hover {
            border-color: #cbd5e1;
        }

        .file-info {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .file-icon {
            flex-shrink: 0;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #eef2ff;
            color: var(--primary);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .file-info > div {
            display: flex;
            flex-direction: column;
            gap: 3px;
            min-width: 0;
        }

        .file-info strong {
            font-size: 14px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .file-info span {
            font-size: 12px;
            color: var(--muted);
        }

        .btn {
            display: inline-block;
            flex-shrink: 0;
            padding: 8px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: white;
            color: var(--text);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s ease, border-color 0.15s ease, transform 0.1s ease;
        }

        .btn:hover {
            background: #f3f4f6;
            transform: translateY(-1px);
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-success {
            background: var(--success-bg);
            color: var(--success-text);
            border-color: transparent;
        }

        .btn-success:hover {
            background: #bbf7d0;
        }

        .btn-success:disabled {
            opacity: 0.7;
            cursor: default;
        }

        .milestone-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .milestone-card {
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: 14px;
            background: var(--card-background, #fff);
            overflow: hidden;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .milestone-card:hover {
            border-color: #c7d2fe;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.08);
            transform: translateY(-1px);
        }

        .milestone-card[open] {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .milestone-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 18px 20px;
            cursor: pointer;
            list-style: none;
        }

        .milestone-card-header::-webkit-details-marker {
            display: none;
        }

        .milestone-main {
            min-width: 0;
            flex: 1;
        }

        .milestone-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }

        .milestone-title strong {
            font-size: 0.95rem;
        }

        .milestone-summary {
            margin: 0;
            color: #64748b;
            font-size: 0.875rem;
            line-height: 1.5;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .milestone-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }

        .milestone-chevron {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #f1f5f9;
            font-size: 0.8rem;
            transition: transform 0.2s ease;
        }

        .milestone-card[open] .milestone-chevron {
            transform: rotate(180deg);
        }

        .milestone-card-details {
            display: flex;
            align-items: center;
            gap: 40px;
            padding: 16px 20px 18px;
            border-top: 1px solid var(--border-color, #e5e7eb);
            background: #f8fafc;
        }

        .milestone-detail {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .milestone-detail .detail-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #94a3b8;
        }

        .milestone-detail strong {
            font-size: 0.875rem;
            color: #334155;
        }

        .milestone-review {
            margin-left: auto;
        }

        .milestone-completed {
            font-size: 0.8rem;
            font-weight: 600;
        }

        .milestone-table-link,
        .milestone-review a {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .milestone-card button {
            position: relative;
            z-index: 2;
        }

        .invoice-create {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 30px;
            padding: 22px;
            margin-bottom: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fafafa;
        }

        .invoice-create h3,
        .invoice-list h3 {
            margin: 0;
        }

        .invoice-create p {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 14px;
        }

        .invoice-form {
            display: flex;
            align-items: flex-end;
            gap: 15px;
        }

        .amount-input {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .amount-input label,
        .invoice-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
        }

        .amount-input input {
            width: 180px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
        }

        .create-invoice-button,
        .download-invoice-button {
            display: inline-block;
            padding: 10px 16px;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            cursor: pointer;
            font-weight: 600;
        }

        .invoice-list {
            margin-top: 20px;
        }

        .invoice-table-wrapper {
            overflow-x: auto;
            margin-top: 15px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .invoice-table th {
            padding: 10px 15px;
            text-align: left;
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
        }

        .invoice-table td {
            padding: 15px;
            background: white;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }

        .invoice-table td:first-child {
            border-left: 1px solid #e5e7eb;
            border-radius: 8px 0 0 8px;
        }

        .invoice-table td:last-child {
            border-right: 1px solid #e5e7eb;
            border-radius: 0 8px 8px 0;
        }

        .client-invoice-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .client-invoice-card {
            padding: 22px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: white;
        }

        .invoice-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .invoice-number {
            font-weight: 700;
            font-size: 16px;
        }

        .invoice-card-header p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 14px;
        }

        .invoice-card-details {
            display: flex;
            gap: 80px;
            margin-top: 24px;
        }

        .invoice-card-details > div {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .invoice-amount {
            font-size: 18px;
        }

        .invoice-card-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid #e5e7eb;
        }

        .current-project-card {
            padding: 0;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
            margin-bottom: 28px;
        }

        .current-project-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            padding: 30px 32px;
        }

        .project-title-section {
            max-width: 750px;
        }

        .project-label {
            display: block;
            margin-bottom: 10px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.12em;
            color: #2563eb;
        }

        .current-project-title {
            margin: 0;
            font-size: 28px;
            line-height: 1.2;
            font-weight: 700;
            color: #111827;
        }

        .current-project-description {
            max-width: 650px;
            margin: 10px 0 0;
            font-size: 15px;
            line-height: 1.6;
            color: #6b7280;
        }

        .project-status {
            flex-shrink: 0;
            padding-top: 4px;
        }

        .project-info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            margin-top: 0;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
        }

        .project-info-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 20px 28px;
        }

        .project-info-item:not(:last-child) {
            border-right: 1px solid #e5e7eb;
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }

        .project-info-item strong {
            font-size: 15px;
        }

        .project-progress-section {
            padding: 24px 32px 30px;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 600;
        }

        .progress-bar {
            width: 100%;
            height: 10px;
            overflow: hidden;
            border-radius: 999px;
            background: #e5e7eb;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: inherit;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .dashboard-welcome {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            margin-bottom: 28px;
        }

        .dashboard-eyebrow {
            margin: 0 0 8px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: #2563eb;
        }

        .dashboard-welcome h1 {
            margin: 0;
            font-size: 32px;
            line-height: 1.2;
            color: #111827;
        }

        .dashboard-welcome p:not(.dashboard-eyebrow) {
            margin: 8px 0 0;
            color: #6b7280;
            font-size: 15px;
        }

        .dashboard-date {
            padding: 10px 14px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            color: #6b7280;
            font-size: 13px;
            font-weight: 600;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
        }

        .dashboard-grid > * {
            min-width: 0;
        }

        .dashboard-grid .card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .dashboard-grid .card > h2,
        .dashboard-grid .card > h3 {
            padding: 24px 24px 0;
            margin-bottom: 20px;
            color: #111827;
        }

        .milestone-list {
            padding: 0 24px 24px;
        }

        .file-list {
            padding: 0 24px 24px;
        }

        .file-item {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
        }

        .file-item:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .client-invoice-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .download-invoice-button {
            background: #111827;
            color: #ffffff;
            padding: 9px 15px;
            border-radius: 8px;
            font-size: 13px;
        }

        .download-invoice-button:hover {
            background: #2563eb;
        }

        /* =========================================================
           MOBILE & TABLET RESPONSIVE RULES (Max Width: 850px)
           ========================================================= */
        @media (max-width: 850px) {
            html, body {
                overflow-x: hidden;
            }

            .navbar {
                padding: 14px 18px;
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .navbar > div {
                justify-content: flex-start;
                flex-wrap: wrap !important;
                gap: 8px !important;
            }

            .container {
                padding: 20px 14px 40px;
            }

            .grid {
                grid-template-columns: 1fr !important;
            }

            .dashboard-welcome {
                flex-direction: column;
                align-items: flex-start;
            }

            .current-project-header {
                flex-direction: column;
                padding: 20px;
            }

            .current-project-title {
                font-size: 24px;
            }

            .project-info-grid {
                grid-template-columns: 1fr;
            }

            .project-info-item {
                padding: 16px 20px;
            }

            .project-info-item:not(:last-child) {
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
            }

            .project-progress-section {
                padding: 20px;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .client-invoice-list {
                grid-template-columns: 1fr;
            }

            .invoice-card-details {
                gap: 20px;
                flex-wrap: wrap;
            }

            .invoice-create {
                flex-direction: column;
                align-items: stretch;
            }

            .invoice-form {
                flex-direction: column;
                align-items: stretch;
            }

            .amount-input input {
                width: 100%;
            }

            .file-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .file-item .btn {
                align-self: flex-end;
            }

            .milestone-card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .milestone-actions {
                width: 100%;
                justify-content: space-between;
                margin-top: 10px;
            }

            .milestone-card-details {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .milestone-review {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ auth()->user()?->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}" style="text-decoration: none; color: inherit;">
            <h2>ClientHub</h2>
        </a>
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('account.edit') }}" class="logout-button" style="text-decoration: none;">
                Account
            </a>   
            @if(auth()->user()?->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="logout-button" style="text-decoration: none;">
                    Overview
                </a>
                <a href="{{ route('admin.projects.index') }}" class="logout-button" style="text-decoration: none;">
                    Projects
                </a>
                <a href="{{ route('admin.clients.create') }}" class="logout-button" style="text-decoration: none;">
                    Clients
                </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="logout-form" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </div>
    </nav>   
    <main class="container">
        {{ $slot }}
    </main>
</body>
</html>