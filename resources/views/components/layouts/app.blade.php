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
            -webkit-text-size-adjust: 100%;
        }

        /* Responsive Navigation Bar */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            color: var(--text);
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 2px rgba(79, 70, 229, 0.04);
            gap: 12px;
        }

        .navbar h2 {
            margin: 0;
            font-size: 20px;
            color: var(--primary);
            font-weight: 800;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .logout-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: transparent;
            color: var(--text);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s ease, border-color 0.15s ease;
            white-space: nowrap;
        }

        .logout-button:hover {
            background: #eef2ff;
            border-color: #c7d2fe;
        }

        /* Container & Card adjustments for phones */
        .container {
            width: 100%;
            max-width: 1250px;
            margin: 0 auto;
            padding: 20px 16px 40px;
        }

        .card {
            background: var(--surface);
            padding: 20px 16px;
            margin-bottom: 18px;
            border-radius: 14px;
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
            gap: 16px;
        }

        /* Inputs (prevents iOS auto-zoom by setting 16px on mobile) */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            font-size: 16px !important;
            padding: 10px 12px;
            border-radius: 8px !important;
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

        /* Responsive Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
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
            padding: 24px 16px;
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
            gap: 12px;
            padding: 12px 14px;
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
            gap: 12px;
            min-width: 0;
        }

        .file-icon {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: white;
            color: var(--text);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s ease, border-color 0.15s ease;
        }

        .btn:hover {
            background: #f3f4f6;
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

        .milestone-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .milestone-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
        }

        .milestone-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px;
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
            gap: 8px;
            margin-bottom: 4px;
            flex-wrap: wrap;
        }

        .milestone-title strong {
            font-size: 0.95rem;
        }

        .milestone-summary {
            margin: 0;
            color: #64748b;
            font-size: 0.85rem;
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .milestone-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .milestone-chevron {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
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
            flex-wrap: wrap;
            gap: 20px;
            padding: 14px 16px;
            border-top: 1px solid #e5e7eb;
            background: #f8fafc;
        }

        .milestone-detail {
            display: flex;
            flex-direction: column;
            gap: 2px;
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

        .dashboard-welcome {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
            margin-bottom: 24px;
        }

        .dashboard-eyebrow {
            margin: 0 0 6px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: #2563eb;
        }

        .dashboard-welcome h1 {
            margin: 0;
            font-size: 26px;
            line-height: 1.2;
            color: #111827;
        }

        .dashboard-welcome p:not(.dashboard-eyebrow) {
            margin: 6px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .dashboard-date {
            padding: 8px 12px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            color: #6b7280;
            font-size: 12px;
            font-weight: 600;
            align-self: flex-start;
        }

        .current-project-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .current-project-header {
            padding: 20px;
        }

        .project-label {
            color: #2563eb;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.12em;
        }

        .current-project-title {
            margin-top: 6px;
            font-size: 24px;
            color: #111827;
        }

        .current-project-description {
            color: #6b7280;
            font-size: 14px;
        }

        .project-info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }

        .project-info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 16px 20px;
        }

        .project-info-item:not(:last-child) {
            border-right: 1px solid #e5e7eb;
        }

        .info-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
        }

        .project-info-item strong {
            font-size: 14px;
        }

        .project-progress-section {
            padding: 20px;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .dashboard-grid .card > h2,
        .dashboard-grid .card > h3 {
            padding: 18px 18px 0;
            margin-bottom: 16px;
        }

        .client-invoice-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .client-invoice-card {
            padding: 20px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .invoice-card-header {
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .invoice-card-details {
            display: flex;
            gap: 30px;
            margin-top: 18px;
        }

        .invoice-card-details > div {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .invoice-card-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid #e5e7eb;
        }

        /* ====================================================
           PHONE & TABLET BREAKPOINTS (Screen Width <= 768px)
           ==================================================== */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: stretch;
                padding: 12px 16px;
            }

            .navbar h2 {
                text-align: center;
                margin-bottom: 8px;
            }

            .nav-actions {
                justify-content: center;
            }

            .dashboard-welcome {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .dashboard-welcome h1 {
                font-size: 24px;
            }

            .grid,
            .dashboard-grid,
            .client-invoice-list {
                grid-template-columns: 1fr;
            }

            .project-info-grid {
                grid-template-columns: 1fr;
            }

            .project-info-item:not(:last-child) {
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
            }

            .file-item {
                flex-direction: column;
                align-items: stretch;
            }

            .file-item form,
            .file-item .btn {
                width: 100%;
                text-align: center;
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
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ auth()->user()?->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}" style="text-decoration: none; color: inherit;">
            <h2>ClientHub</h2>
        </a>
        <div class="nav-actions">
            <a href="{{ route('account.edit') }}" class="logout-button">Account</a>   
            @if(auth()->user()?->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="logout-button">Overview</a>
                <a href="{{ route('admin.projects.index') }}" class="logout-button">Projects</a>
                <a href="{{ route('admin.clients.create') }}" class="logout-button">Clients</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
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