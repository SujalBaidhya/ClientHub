<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ClientHub' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f6f7fb;
            --surface: #ffffff;
            --text: #1e1b4b;
            --muted: #6b7280;
            --border: #e4e4f0;
            --primary: #4f46e5;
            --accent-gold: #a16207;
            --sidebar-bg: #1e1b4b;
            --sidebar-text: rgba(255, 255, 255, 0.75);
            --sidebar-text-active: #ffffff;
            --success-bg: #dcfce7;
            --success-text: #166534;
            --warning-bg: #fef3c7;
            --warning-text: #92400e;
            --danger-bg: #fee2e2;
            --danger-text: #991b1b;
            --neutral-bg: #e5e7eb;
            --neutral-text: #374151;
            --font-display: 'Fraunces', serif;
            --font-body: 'Inter', -apple-system, sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--font-body);
            margin: 0;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3 {
            font-family: var(--font-display);
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        /* ===== App Shell: Sidebar + Main Content ===== */

        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            flex-shrink: 0;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            padding: 24px 16px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            transition: transform 0.25s ease;
        }

        .sidebar-brand {
            display: block;
            text-decoration: none;
            color: #ffffff;
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 600;
            padding: 8px 12px 28px;
            letter-spacing: -0.01em;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 10px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.15s ease, color 0.15s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.06);
            color: var(--sidebar-text-active);
        }

        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: var(--sidebar-text-active);
            border-left-color: var(--accent-gold);
        }

        .sidebar-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 14px 4px;
        }
                .sidebar-section-label {
            padding: 20px 12px 6px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.04em;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
        }

        .sidebar-footer {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sidebar-footer form {
            margin: 0;
        }

        .sidebar-footer button {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            min-width: 0;
        }

        /* ===== Mobile menu toggle ===== */

        .mobile-topbar {
            display: none;
        }

        .sidebar-backdrop {
            display: none;
        }

        @media (max-width: 900px) {
            .mobile-topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: var(--sidebar-bg);
                padding: 14px 18px;
                position: sticky;
                top: 0;
                z-index: 90;
            }

            .mobile-topbar .sidebar-brand {
                padding: 0;
                font-size: 19px;
            }

            .mobile-menu-button {
                background: rgba(255, 255, 255, 0.1);
                border: none;
                color: white;
                width: 38px;
                height: 38px;
                border-radius: 8px;
                font-size: 18px;
                cursor: pointer;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.is-open {
                transform: translateX(0);
            }

            .sidebar-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.4);
                z-index: 99;
            }

            .sidebar-backdrop.is-visible {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* ===== Shared components (cards, badges, forms, etc.) ===== */

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
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: none;
        }

        .card h2, .card h3 { margin-top: 0; }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        input[type="text"], input[type="email"], input[type="password"],
        input[type="number"], input[type="date"], select, textarea {
            border-radius: 10px !important;
            border: 1px solid var(--border) !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            font-family: inherit;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus,
        input[type="number"]:focus, input[type="date"]:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid var(--border); }

        .plain-list { list-style: none; margin: 0; padding: 0; }
        .plain-list li { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--border); }
        .plain-list li:last-child { border-bottom: none; }

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

        a { color: var(--primary); }

        .file-list { display: flex; flex-direction: column; gap: 10px; }

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
        .file-item:hover { border-color: #cbd5e1; }

        .file-info { display: flex; align-items: center; gap: 14px; min-width: 0; }

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

        .file-info > div { display: flex; flex-direction: column; gap: 3px; min-width: 0; }
        .file-info strong { font-size: 14px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .file-info span { font-size: 12px; color: var(--muted); }

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
        .btn:hover { background: #f3f4f6; transform: translateY(-1px); }
        .btn-small { padding: 6px 12px; font-size: 12px; }
        .btn-success { background: var(--success-bg); color: var(--success-text); border-color: transparent; }
        .btn-success:hover { background: #bbf7d0; }
        .btn-success:disabled { opacity: 0.7; cursor: default; }

        .milestone-list { display: flex; flex-direction: column; gap: 12px; }

        .milestone-card {
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: 14px;
            background: var(--card-background, #fff);
            overflow: hidden;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }
        .milestone-card:hover { border-color: #c7d2fe; box-shadow: 0 4px 14px rgba(79, 70, 229, 0.08); transform: translateY(-1px); }
        .milestone-card[open] { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04); }

        .milestone-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 18px 20px;
            cursor: pointer;
            list-style: none;
        }
        .milestone-card-header::-webkit-details-marker { display: none; }

        .milestone-main { min-width: 0; flex: 1; }
        .milestone-title { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
        .milestone-title strong { font-size: 0.95rem; }
        .milestone-summary { margin: 0; color: #64748b; font-size: 0.875rem; line-height: 1.5; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .milestone-actions { display: flex; align-items: center; gap: 14px; flex-shrink: 0; }

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
        .milestone-card[open] .milestone-chevron { transform: rotate(180deg); }

        .milestone-card-details {
            display: flex;
            align-items: center;
            gap: 40px;
            padding: 16px 20px 18px;
            border-top: 1px solid var(--border-color, #e5e7eb);
            background: #f8fafc;
        }

        .milestone-detail { display: flex; flex-direction: column; gap: 4px; }
        .milestone-detail .detail-label { font-size: 0.8rem; text-transform: none; letter-spacing: 0; color: var(--muted); }
        .milestone-detail strong { font-size: 0.875rem; color: #334155; }
        .milestone-review { margin-left: auto; }
        .milestone-completed { font-size: 0.8rem; font-weight: 600; }
        .milestone-table-link, .milestone-review a { font-size: 0.875rem; font-weight: 600; }
        .milestone-card button { position: relative; z-index: 2; }

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
        .invoice-create h3, .invoice-list h3 { margin: 0; }
        .invoice-create p { margin: 6px 0 0; color: #64748b; font-size: 14px; }
        .invoice-form { display: flex; align-items: flex-end; gap: 15px; }
        .amount-input { display: flex; flex-direction: column; gap: 7px; }
        .amount-input label, .invoice-label { font-size: 13px; font-weight: 500; color: var(--muted); text-transform: none; }
        .amount-input input { width: 180px; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 7px; }

        .create-invoice-button, .download-invoice-button {
            display: inline-block;
            padding: 10px 16px;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            cursor: pointer;
            font-weight: 600;
        }

        .invoice-list { margin-top: 20px; }
        .invoice-table-wrapper { overflow-x: auto; margin-top: 15px; }
        .invoice-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        .invoice-table th { padding: 10px 15px; text-align: left; font-size: 13px; color: var(--muted); text-transform: none; font-weight: 500; }
        .invoice-table td { padding: 15px; background: white; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; }
        .invoice-table td:first-child { border-left: 1px solid #e5e7eb; border-radius: 8px 0 0 8px; }
        .invoice-table td:last-child { border-right: 1px solid #e5e7eb; border-radius: 0 8px 8px 0; }

        .client-invoice-list { display: flex; flex-direction: column; gap: 14px; }
        .client-invoice-card { padding: 22px; border: 1px solid #e5e7eb; border-radius: 10px; background: white; }
        .invoice-card-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; }
        .invoice-number { font-weight: 700; font-size: 16px; }
        .invoice-card-header p { margin: 5px 0 0; color: #64748b; font-size: 14px; }
        .invoice-card-details { display: flex; gap: 80px; margin-top: 24px; }
        .invoice-card-details > div { display: flex; flex-direction: column; gap: 7px; }
        .invoice-amount { font-family: var(--font-display); font-size: 20px; font-weight: 600; color: var(--accent-gold); }
        .invoice-card-actions { display: flex; justify-content: flex-end; margin-top: 22px; padding-top: 18px; border-top: 1px solid #e5e7eb; }

        .current-project-card {
            padding: 0;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid var(--border);
            border-top: 3px solid var(--primary);
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(30, 27, 75, 0.09);
            margin-bottom: 28px;
        }

        .current-project-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; padding: 30px 32px; }
        .project-title-section { max-width: 750px; }
        .project-label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 600; letter-spacing: 0; color: var(--primary); }
        .current-project-title { margin: 0; font-size: 30px; line-height: 1.2; font-weight: 600; color: var(--text); font-family: var(--font-display); }
        .current-project-description { max-width: 650px; margin: 10px 0 0; font-size: 15px; line-height: 1.6; color: #6b7280; }
        .project-status { flex-shrink: 0; padding-top: 4px; }

        .project-info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            margin-top: 0;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
        }
        .project-info-item { display: flex; flex-direction: column; gap: 8px; padding: 20px 28px; }
        .project-info-item:not(:last-child) { border-right: 1px solid #e5e7eb; }
        .info-label { font-size: 13px; font-weight: 500; text-transform: none; letter-spacing: 0; color: var(--muted); }
        .project-info-item strong { font-size: 15px; }

        .project-progress-section { padding: 24px 32px 30px; }
        .progress-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 10px; font-size: 14px; font-weight: 500; }
        .progress-header strong { font-family: var(--font-display); font-size: 20px; font-weight: 600; color: var(--primary); }
        .progress-bar { width: 100%; height: 10px; overflow: hidden; border-radius: 999px; background: #e5e7eb; }
        .progress-bar-fill { height: 100%; border-radius: inherit; background: var(--primary); transition: width 0.3s ease; }

        .dashboard-welcome { display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; margin-bottom: 28px; }
        .dashboard-eyebrow { margin: 0 0 8px; font-size: 14px; font-weight: 600; letter-spacing: 0; color: var(--primary); }
        .dashboard-welcome h1 { margin: 0; font-size: 34px; line-height: 1.2; color: var(--text); }
        .dashboard-welcome p:not(.dashboard-eyebrow) { margin: 8px 0 0; color: #6b7280; font-size: 15px; }
        .dashboard-date { padding: 10px 14px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; color: #6b7280; font-size: 13px; font-weight: 600; }

        .dashboard-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 24px; margin-bottom: 28px; }
        .dashboard-grid > * { min-width: 0; }
        .dashboard-grid .card { background: #ffffff; border: 1px solid var(--border); border-radius: 14px; box-shadow: none; overflow: hidden; }
        .dashboard-grid .card > h2, .dashboard-grid .card > h3 { padding: 24px 24px 0; margin-bottom: 20px; color: #111827; }
        .milestone-list { padding: 0 24px 24px; }
        .file-list { padding: 0 24px 24px; }
        .file-item { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px; }
        .file-item:hover { background: #f1f5f9; border-color: #cbd5e1; }

        .client-invoice-list { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
        .download-invoice-button { background: #111827; color: #ffffff; padding: 9px 15px; border-radius: 8px; font-size: 13px; }
        .download-invoice-button:hover { background: #2563eb; }

        @media (max-width: 850px) {
            .container { padding: 20px 14px 40px; }
            .grid { grid-template-columns: 1fr !important; }
            .dashboard-welcome { flex-direction: column; align-items: flex-start; }
            .current-project-header { flex-direction: column; padding: 20px; }
            .current-project-title { font-size: 24px; }
            .project-info-grid { grid-template-columns: 1fr; }
            .project-info-item { padding: 16px 20px; }
            .project-info-item:not(:last-child) { border-right: none; border-bottom: 1px solid #e5e7eb; }
            .project-progress-section { padding: 20px; }
            .dashboard-grid { grid-template-columns: 1fr; }
            .client-invoice-list { grid-template-columns: 1fr; }
            .invoice-card-details { gap: 20px; flex-wrap: wrap; }
            .invoice-create { flex-direction: column; align-items: stretch; }
            .invoice-form { flex-direction: column; align-items: stretch; }
            .amount-input input { width: 100%; }
            .file-item { flex-direction: column; align-items: flex-start; }
            .file-item .btn { align-self: flex-end; }
            .milestone-card-header { flex-direction: column; align-items: stretch; }
            .milestone-main { width: 100%; }
            .milestone-title { flex-wrap: wrap; width: 100%; }
            .milestone-actions { width: 100%; justify-content: space-between; margin-top: 10px; }
            .milestone-card-details { flex-direction: column; align-items: flex-start; gap: 16px; }
            .milestone-review { margin-left: 0; }
        }
    </style>
</head>
<body>

    @php
        $isGuest = auth()->guest();
        $isAdmin = !$isGuest && auth()->user()->role === 'admin';
        $homeRoute = $isGuest ? url('/login') : ($isAdmin ? route('admin.dashboard') : route('dashboard'));
        $current = request()->path();
    @endphp

    <div class="app-shell">

        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <aside class="sidebar" id="sidebar">
            <a href="{{ $homeRoute }}" class="sidebar-brand">ClientHub</a>

                                   @unless($isGuest)
                <div style="display: flex; align-items: center; gap: 10px; padding: 0 12px 20px;">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.12); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div style="min-width: 0;">
                        <div style="color: white; font-size: 14px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ auth()->user()->name }}
                        </div>
                        <div style="color: var(--sidebar-text); font-size: 12px;">
                            {{ $isAdmin ? 'Administrator' : 'Client' }}
                        </div>
                    </div>
                </div>
            @endunless

            <nav class="sidebar-nav">
                @unless($isGuest)

                @if($isAdmin)
                    <a href="{{ route('admin.projects.create') }}" class="btn btn-success" style="justify-content: center; text-align: center; margin: 0 0 18px; display: block;">
                        + New Project
                    </a>
                @endif

                <div class="sidebar-section-label">Workspace</div>

                @if(!$isAdmin)
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') || request()->routeIs('dashboard.project') ? 'active' : '' }}">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        Overview
                    </a>
                    <a href="{{ route('admin.projects.index') }}" class="sidebar-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                        Projects
                    </a>
                    <a href="{{ route('admin.clients.create') }}" class="sidebar-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
                        Clients
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                        Payments
                    </a>
                @endif

                <div class="sidebar-section-label">Account</div>

                <a href="{{ route('account.edit') }}" class="sidebar-link {{ request()->routeIs('account.edit') ? 'active' : '' }}">
                    Settings
                </a>

                @if(!$isAdmin)
                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=hello.clienthub@gmail.com" target="_blank" class="sidebar-link">
                        Contact Support
                    </a>
                @endif
                @endunless

            </nav>
            <div class="sidebar-divider"></div>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-link">Logout</button>
                </form>
            </div>
        </aside>

        <div class="main-content">

            <div class="mobile-topbar">
                <a href="{{ $homeRoute }}" class="sidebar-brand" style="color: white;">ClientHub</a>
                <button class="mobile-menu-button" id="mobileMenuButton" aria-label="Open menu">&#9776;</button>
            </div>

            <main class="container">
                {{ $slot }}
            </main>

        </div>

    </div>

    <script>
        (function () {
            var sidebar = document.getElementById('sidebar');
            var backdrop = document.getElementById('sidebarBackdrop');
            var button = document.getElementById('mobileMenuButton');

            function openMenu() {
                sidebar.classList.add('is-open');
                backdrop.classList.add('is-visible');
            }

            function closeMenu() {
                sidebar.classList.remove('is-open');
                backdrop.classList.remove('is-visible');
            }

            if (button) {
                button.addEventListener('click', openMenu);
            }
            if (backdrop) {
                backdrop.addEventListener('click', closeMenu);
            }
        })();
    </script>

</body>
</html>