<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ClientHub' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        (function () {
            try {
                var theme = localStorage.getItem('clienthub_theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {}
        })();
    </script>
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

        [data-theme="dark"] {
            --bg: #090d16;
            --surface: #111827;
            --text: #f3f4f6;
            --muted: #94a3b8;
            --border: #1f293d;
            --primary: #6366f1;
            --accent-gold: #fbbf24;
            --sidebar-bg: #070a12;
            --sidebar-text: rgba(255, 255, 255, 0.72);
            --sidebar-text-active: #ffffff;
            --success-bg: rgba(34, 197, 94, 0.16);
            --success-text: #4ade80;
            --warning-bg: rgba(245, 158, 11, 0.16);
            --warning-text: #fbbf24;
            --danger-bg: rgba(239, 68, 68, 0.16);
            --danger-text: #f87171;
            --neutral-bg: rgba(148, 163, 184, 0.15);
            --neutral-text: #cbd5e1;
        }

        html, body, .card, .sidebar, .btn, .search-result-row, .milestone-card, .file-item, .project-card {
            transition: background-color 0.25s ease, border-color 0.25s ease, color 0.2s ease, box-shadow 0.25s ease;
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
        .activity-timeline {
    position: relative;
    padding-left: 6px;
}

.activity-item {
    display: flex;
    gap: 14px;
    padding-bottom: 20px;
    position: relative;
}

.activity-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 16px;
    bottom: -4px;
    width: 1px;
    background: var(--border, #e4e4f0);
}

.activity-dot {
    width: 11px;
    height: 11px;
    border-radius: 50%;
    background: #1e1b4b;
    margin-top: 4px;
    flex-shrink: 0;
}

.activity-content { min-width: 0; }

.activity-description {
    margin: 0 0 4px;
    font-size: 14px;
    color: #1e1b4b;
    font-weight: 500;
}

.activity-time {
    font-size: 12px;
    color: var(--muted, #6b7280);
}
.activity-bell-wrapper {
    position: relative;
    margin-bottom: 14px;
}

.activity-bell-button {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.08);
    border: none;
    color: white;
    padding: 10px 12px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    position: relative;
}

.activity-bell-button:hover {
    background: rgba(255, 255, 255, 0.14);
}

.activity-badge {
    margin-left: auto;
    background: #dc2626;
    color: white;
    font-size: 11px;
    font-weight: 700;
    padding: 1px 7px;
    border-radius: 9999px;
}

.activity-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    width: 300px;
    max-height: 360px;
    overflow-y: auto;
    background: white;
    border-radius: 10px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
    z-index: 200;
}

.activity-dropdown.is-open { display: block; }

.activity-dropdown-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f0f0f5;
}

.activity-dropdown-item p {
    margin: 0 0 4px;
    font-size: 13px;
    color: #1e1b4b;
    font-weight: 500;
}

.activity-dropdown-item span {
    font-size: 11px;
    color: #6b7280;
}

.activity-dropdown-footer {
    display: block;
    text-align: center;
    padding: 12px;
    font-size: 13px;
    font-weight: 600;
    color: #1e1b4b;
    text-decoration: none;
}

.activity-dropdown-footer:hover { background: #f6f7fb; }

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
        .search-result-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 24px;
    border-top: 1px solid var(--border);
    text-decoration: none;
    color: var(--text);
    transition: background 0.15s ease;
}
.search-result-row:hover { background: #f8fafc; }
.search-result-icon {
    width: 36px;
    height: 36px;
    flex-shrink: 0;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}
.search-result-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.search-result-info strong {
    font-size: 14px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.search-result-info span {
    font-size: 12px;
    color: var(--muted);
}
        /* ===== Toast Notifications System ===== */
        .toast-stack {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
            max-width: 380px;
            width: calc(100% - 48px);
        }

        .toast-item {
            pointer-events: auto;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 13px 16px;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            animation: toast-in 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            position: relative;
            overflow: hidden;
            transition: transform 0.15s ease, opacity 0.15s ease;
        }

        .toast-item.toast-success {
            border-left: 4px solid #10b981;
        }

        .toast-item.toast-error {
            border-left: 4px solid #ef4444;
        }

        .toast-icon-wrap {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .toast-success .toast-icon-wrap {
            background: #d1fae5;
            color: #059669;
        }

        .toast-error .toast-icon-wrap {
            background: #fee2e2;
            color: #dc2626;
        }

        .toast-body {
            flex: 1;
            min-width: 0;
        }

        .toast-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #1e1b4b;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .toast-message {
            font-size: 12.5px;
            color: #4b5563;
            line-height: 1.4;
            word-break: break-word;
        }

        .toast-close-btn {
            background: transparent;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease, background 0.15s ease;
            flex-shrink: 0;
        }

        .toast-close-btn:hover {
            color: #1e1b4b;
            background: #f3f4f6;
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            background: rgba(0, 0, 0, 0.04);
        }

        .toast-progress-bar {
            height: 100%;
            width: 100%;
            transform-origin: left;
            animation: toast-timer 3.8s linear forwards;
        }

        .toast-success .toast-progress-bar { background: #10b981; }
        .toast-error .toast-progress-bar { background: #ef4444; }

        .toast-item.toast-hide {
            animation: toast-out 0.25s cubic-bezier(0.4, 0, 1, 1) forwards;
        }

        @keyframes toast-in {
            from { transform: translateX(30px) scale(0.96); opacity: 0; }
            to { transform: translateX(0) scale(1); opacity: 1; }
        }

        @keyframes toast-out {
            from { transform: translateX(0) scale(1); opacity: 1; }
            to { transform: translateX(30px) scale(0.96); opacity: 0; }
        }

        @keyframes toast-timer {
            from { transform: scaleX(1); }
            to { transform: scaleX(0); }
        }

        .btn-spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: btn-spin 0.6s linear infinite;
            margin-right: 6px;
            vertical-align: middle;
        }

        @keyframes btn-spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 900px) {
            .toast-stack {
                top: 72px;
                right: 14px;
                left: 14px;
                width: auto;
                max-width: none;
            }
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

.empty-state {
    text-align: center;
    padding: 36px 20px;
}

.empty-state-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #f1f0fb;
    color: #4f46e5;
    margin-bottom: 14px;
}

.empty-state-title {
    font-family: Georgia, serif;
    font-size: 15px;
    font-weight: 600;
    color: #1e1b4b;
    margin: 0 0 4px;
}

.empty-state-message {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
    max-width: 320px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.5;
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
.invoice-edit-row {
    display: flex;
    gap: 10px;
}
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
            .invoice-edit-row {
    flex-direction: column;
}

.invoice-status-select {
    width: 100%;
}
            .milestone-actions { width: 100%; justify-content: space-between; margin-top: 10px; }
            .milestone-card-details { flex-direction: column; align-items: flex-start; gap: 16px; }
            .milestone-review { margin-left: 0; }
        }

        /* ===== Command Palette (Linear / Stripe style) ===== */
        .sidebar-search-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.05);
            color: var(--sidebar-text);
            font-size: 13px;
            font-family: inherit;
            cursor: pointer;
            margin-bottom: 14px;
            transition: all 0.15s ease;
            text-align: left;
        }
        .sidebar-search-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
        }
        .sidebar-search-btn .cmd-kbd {
            margin-left: auto;
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.18);
            color: rgba(255, 255, 255, 0.8);
        }
        .cmd-palette-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 1000;
            align-items: flex-start;
            justify-content: center;
            padding: 12vh 16px 20px;
        }
        .cmd-palette-backdrop.is-open {
            display: flex;
            animation: cmdFadeIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes cmdFadeIn {
            from { opacity: 0; transform: scale(0.97); }
            to { opacity: 1; transform: scale(1); }
        }
        .cmd-palette-modal {
            width: 100%;
            max-width: 580px;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.28), 0 0 0 1px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .cmd-palette-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
        }
        .cmd-palette-input {
            flex: 1;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            font-size: 15px;
            background: transparent;
            color: var(--text);
            padding: 0;
            font-family: inherit;
        }
        .cmd-kbd {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            font-size: 11px;
            font-family: inherit;
            font-weight: 600;
            color: #64748b;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 2px 6px;
            line-height: 1.2;
        }
        .cmd-palette-body {
            max-height: 380px;
            overflow-y: auto;
            padding: 8px;
        }
        .cmd-palette-group-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            padding: 8px 10px 4px;
        }
        .cmd-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--text);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.1s ease;
        }
        .cmd-item:hover, .cmd-item.is-selected {
            background: #f1f5f9;
        }
        .cmd-item-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: #eef2ff;
            color: var(--primary);
            flex-shrink: 0;
        }
        .cmd-item-info {
            flex: 1;
            min-width: 0;
        }
        .cmd-item-title {
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cmd-item-subtitle {
            font-size: 12px;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cmd-palette-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 18px;
            background: #f8fafc;
            border-top: 1px solid var(--border);
            font-size: 11.5px;
            color: #64748b;
        }


        /* ===== Ultra-Premium Theme Switcher ===== */
        .theme-toggle-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            color: var(--sidebar-text);
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            width: 100%;
            text-align: left;
            margin-bottom: 6px;
        }

        .theme-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.16);
        }

        .theme-text {
            flex: 1;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .theme-toggle-pill {
            width: 36px;
            height: 20px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.18);
            position: relative;
            padding: 2px;
            transition: background 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            flex-shrink: 0;
        }

        .theme-toggle-circle {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease;
            transform: translateX(0);
        }

        [data-theme="dark"] .theme-toggle-pill {
            background: #6366f1;
        }

        [data-theme="dark"] .theme-toggle-circle {
            transform: translateX(16px);
            background: #ffffff;
        }

        .theme-icon-dark { display: none; }
        .theme-icon-light { display: inline-flex; align-items: center; color: #fbbf24; }

        [data-theme="dark"] .theme-icon-light { display: none; }
        [data-theme="dark"] .theme-icon-dark { display: inline-flex; align-items: center; color: #a5b4fc; }

        .theme-toggle-mobile {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        /* ===== Dark Mode Component Overrides ===== */
        [data-theme="dark"] input[type="text"],
        [data-theme="dark"] input[type="email"],
        [data-theme="dark"] input[type="password"],
        [data-theme="dark"] input[type="number"],
        [data-theme="dark"] input[type="date"],
        [data-theme="dark"] select,
        [data-theme="dark"] textarea {
            background: #0c121e !important;
            color: #f1f5f9 !important;
            border-color: #232f48 !important;
        }

        [data-theme="dark"] input::placeholder,
        [data-theme="dark"] textarea::placeholder {
            color: #64748b;
        }

        [data-theme="dark"] .card {
            background: var(--surface);
            border-color: var(--border);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.45);
        }

        [data-theme="dark"] .current-project-card {
            background: var(--surface);
            border-color: var(--border);
        }

        [data-theme="dark"] .current-project-title {
            color: var(--text);
        }

        [data-theme="dark"] .project-info-grid {
            background: #0c121e;
            border-color: var(--border);
        }

        [data-theme="dark"] .project-info-item:not(:last-child) {
            border-right-color: var(--border);
        }

        [data-theme="dark"] .progress-bar {
            background: #1e293b;
        }

        [data-theme="dark"] .dashboard-date {
            background: var(--surface);
            border-color: var(--border);
            color: var(--muted);
        }

        [data-theme="dark"] .file-item {
            background: #0c121e;
            border-color: var(--border);
        }

        [data-theme="dark"] .file-item:hover {
            background: #131b2c;
            border-color: #334155;
        }

        [data-theme="dark"] .file-icon {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
        }

        [data-theme="dark"] .milestone-card {
            background: var(--surface);
            border-color: var(--border);
        }

        [data-theme="dark"] .milestone-card:hover {
            border-color: #4f46e5;
        }

        [data-theme="dark"] .milestone-card-details {
            background: #0c121e;
            border-color: var(--border);
        }

        [data-theme="dark"] .milestone-chevron {
            background: #1e293b;
            color: #cbd5e1;
        }

        [data-theme="dark"] .milestone-summary {
            color: #94a3b8;
        }

        [data-theme="dark"] .milestone-detail strong {
            color: #f1f5f9;
        }

        [data-theme="dark"] .invoice-create {
            background: #0c121e;
            border-color: var(--border);
        }

        [data-theme="dark"] .invoice-table td {
            background: var(--surface);
            border-color: var(--border);
            color: var(--text);
        }

        [data-theme="dark"] .client-invoice-card {
            background: var(--surface);
            border-color: var(--border);
        }

        [data-theme="dark"] .empty-state {
            background: #0c121e;
            border-color: var(--border);
        }

        [data-theme="dark"] .empty-state-icon {
            background: rgba(99, 102, 241, 0.12);
            color: #818cf8;
        }

        [data-theme="dark"] .empty-state-title {
            color: var(--text);
        }

        [data-theme="dark"] .btn {
            background: var(--surface);
            color: var(--text);
            border-color: var(--border);
        }

        [data-theme="dark"] .btn:hover {
            background: #1e293b;
        }

        [data-theme="dark"] .btn-success {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border-color: rgba(34, 197, 94, 0.3);
        }

        [data-theme="dark"] .btn-success:hover {
            background: rgba(34, 197, 94, 0.3);
        }

        [data-theme="dark"] table th,
        [data-theme="dark"] table td {
            border-bottom-color: var(--border);
            color: var(--text);
        }

        [data-theme="dark"] .plain-list li {
            border-bottom-color: var(--border);
        }

        [data-theme="dark"] .activity-dropdown {
            background: #111827;
            border: 1px solid var(--border);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.55);
        }

        [data-theme="dark"] .activity-dropdown-item {
            border-bottom-color: #1f293d !important;
        }

        [data-theme="dark"] .activity-dropdown-item p {
            color: #f1f5f9;
        }

        [data-theme="dark"] .activity-dropdown-footer {
            color: #a5b4fc;
        }

        [data-theme="dark"] .activity-dropdown-footer:hover {
            background: #1a243b;
        }

        [data-theme="dark"] .activity-description {
            color: #f1f5f9;
        }

        [data-theme="dark"] .activity-dot {
            background: var(--primary);
        }

        [data-theme="dark"] .activity-item:not(:last-child)::before {
            background: var(--border);
        }

        [data-theme="dark"] .toast-item {
            background: #111827;
            border-color: var(--border);
            color: #f1f5f9;
            box-shadow: 0 16px 36px rgba(0,0,0,0.5);
        }

        [data-theme="dark"] .toast-title {
            color: #f1f5f9;
        }

        [data-theme="dark"] .toast-message {
            color: #94a3b8;
        }

        [data-theme="dark"] .toast-close-btn:hover {
            color: #f1f5f9;
            background: #1f293d;
        }

        [data-theme="dark"] .cmd-palette-modal {
            background: #111827;
            border: 1px solid var(--border);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65);
        }

        [data-theme="dark"] .cmd-palette-header {
            border-bottom-color: var(--border);
        }

        [data-theme="dark"] .cmd-palette-footer {
            background: #0c121e;
            border-top-color: var(--border);
            color: #94a3b8;
        }

        [data-theme="dark"] .cmd-item:hover,
        [data-theme="dark"] .cmd-item.is-selected {
            background: #1a243b;
        }

        [data-theme="dark"] .cmd-item-title {
            color: #f1f5f9;
        }

        [data-theme="dark"] .cmd-kbd {
            background: #0c121e;
            border-color: #232f48;
            color: #94a3b8;
        }

        [data-theme="dark"] .search-result-row {
            border-top-color: var(--border);
            color: var(--text);
        }

        [data-theme="dark"] .search-result-row:hover {
            background: #1a243b;
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

    <div class="toast-stack" id="toastStack">
        @if(session('success'))
            <div class="toast toast-item toast-success">
                <div class="toast-icon-wrap">
                    <x-icon name="check" size="14" strokeWidth="2.5" />
                </div>
                <div class="toast-body">
                    <div class="toast-title">Success</div>
                    <div class="toast-message">{{ session('success') }}</div>
                </div>
                <button type="button" class="toast-close-btn" onclick="this.closest('.toast-item').classList.add('toast-hide'); setTimeout(() => this.closest('.toast-item')?.remove(), 260);" aria-label="Close">
                    <x-icon name="x" size="14" />
                </button>
                <div class="toast-progress"><div class="toast-progress-bar"></div></div>
            </div>
        @endif

        @if(session('error'))
            <div class="toast toast-item toast-error">
                <div class="toast-icon-wrap">
                    <x-icon name="alert-circle" size="14" strokeWidth="2.5" />
                </div>
                <div class="toast-body">
                    <div class="toast-title">Notice</div>
                    <div class="toast-message">{{ session('error') }}</div>
                </div>
                <button type="button" class="toast-close-btn" onclick="this.closest('.toast-item').classList.add('toast-hide'); setTimeout(() => this.closest('.toast-item')?.remove(), 260);" aria-label="Close">
                    <x-icon name="x" size="14" />
                </button>
                <div class="toast-progress"><div class="toast-progress-bar"></div></div>
            </div>
        @endif

        @if($errors->any())
            <div class="toast toast-item toast-error">
                <div class="toast-icon-wrap">
                    <x-icon name="alert-circle" size="14" strokeWidth="2.5" />
                </div>
                <div class="toast-body">
                    <div class="toast-title">Please review</div>
                    <div class="toast-message">{{ $errors->first() }}</div>
                </div>
                <button type="button" class="toast-close-btn" onclick="this.closest('.toast-item').classList.add('toast-hide'); setTimeout(() => this.closest('.toast-item')?.remove(), 260);" aria-label="Close">
                    <x-icon name="x" size="14" />
                </button>
                <div class="toast-progress"><div class="toast-progress-bar"></div></div>
            </div>
        @endif
    </div>

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

    <div class="activity-bell-wrapper">
        <button type="button" class="activity-bell-button" id="activityBellButton">
            <span>&#128276;</span> Notifications
            @php
            $activityIndexRoute = $isAdmin ? route('admin.activity.index') : route('client.activity.index');
    $unreadActivityCount = $unreadActivityCount ?? 0;
    $recentActivity = $recentActivity ?? [];
@endphp
            @if($unreadActivityCount > 0)
                <span class="activity-badge">{{ $unreadActivityCount > 9 ? '9+' : $unreadActivityCount }}</span>
            @endif
        </button>

        <div class="activity-dropdown" id="activityDropdown">
            @forelse($recentActivity as $item)
                @php
                    if ($isAdmin) {
                        $targetUrl = $item->url ?? $activityIndexRoute;
                    } else {
                        if ($item->url && !str_contains($item->url, '/admin')) {
                            $targetUrl = $item->url;
                        } elseif ($item->project_id) {
                            $targetUrl = route('dashboard.project', $item->project_id);
                        } else {
                            $targetUrl = route('client.activity.index');
                        }
                    }
                @endphp
                <a href="{{ $targetUrl }}" class="activity-dropdown-item" style="text-decoration: none; display: block; color: inherit;background: rgba(0,0,0,0.03); font-weight: 500;">
                    <p>{{ $item->description }}</p>
                    <span style="font-size: 0.75rem; color: var(--muted);">{{ $item->created_at->diffForHumans() }}</span>
                </a>
            @empty
                <div class="activity-dropdown-item">
                    <p style="color: var(--muted);">No activity yet.</p>
                </div>
            @endforelse


            <a href="{{ $activityIndexRoute }}" class="activity-dropdown-footer">
                View all activity
            </a>
        </div>
    </div>
       @if($isAdmin)

    <button type="button" class="sidebar-search-btn" id="openCommandPalette">
        <x-icon name="search" size="14" />
        <span>Quick search...</span>
        <kbd class="cmd-kbd">Ctrl K</kbd>
    </button>

    <a href="{{ route('admin.projects.create') }}" class="btn btn-success" style="justify-content: center; text-align: center; margin: 0 0 18px; display: flex; align-items: center; gap: 6px;">
        <x-icon name="plus" size="16" />
        New Project
    </a>
@endif

                <div class="sidebar-section-label">Workspace</div>

                @if(!$isAdmin)
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') || request()->routeIs('dashboard.project') ? 'active' : '' }}">
                        <x-icon name="layout-dashboard" size="18" />
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('client.activity.index') }}" class="sidebar-link {{ request()->routeIs('client.activity.*') ? 'active' : '' }}">
                        <x-icon name="activity" size="18" />
                        <span>Activity Log</span>
                    </a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <x-icon name="layout-dashboard" size="18" />
                        <span>Overview</span>
                    </a>
                    <a href="{{ route('admin.projects.index') }}" class="sidebar-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                        <x-icon name="folder" size="18" />
                        <span>Projects</span>
                    </a>
                    <a href="{{ route('admin.clients.create') }}" class="sidebar-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
                        <x-icon name="users" size="18" />
                        <span>Clients</span>
                    </a>
                    <a href="{{ route('admin.invoices.index') }}" class="sidebar-link {{ request()->routeIs('admin.invoices.index') ? 'active' : '' }}">
                        <x-icon name="file-text" size="18" />
                        <span>Invoices</span>
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                        <x-icon name="credit-card" size="18" />
                        <span>Payments</span>
                    </a>
                    <a href="{{ route('admin.activity.index') }}" class="sidebar-link {{ request()->routeIs('admin.activity.*') ? 'active' : '' }}">
                        <x-icon name="activity" size="18" />
                        <span>Activity Log</span>
                    </a>   
                @endif

                <div class="sidebar-section-label">Account</div>

                <a href="{{ route('account.edit') }}" class="sidebar-link {{ request()->routeIs('account.edit') ? 'active' : '' }}">
                    <x-icon name="settings" size="18" />
                    <span>Settings</span>
                </a>

                @if(!$isAdmin)
                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=hello.clienthub@gmail.com" target="_blank" class="sidebar-link">
                        <x-icon name="external-link" size="18" />
                        <span>Contact Support</span>
                    </a>
                @endif
                @endunless

                <div class="sidebar-divider"></div>

                <div class="sidebar-footer">
                    <button type="button" id="themeToggleBtn" class="theme-toggle-btn" aria-label="Toggle Dark Mode" onclick="toggleTheme()">
                        <span class="theme-icon-light"><x-icon name="sun" size="16" /></span>
                        <span class="theme-icon-dark"><x-icon name="moon" size="16" /></span>
                        <span class="theme-text">
                            <span>Dark Theme</span>
                            <span class="theme-toggle-pill">
                                <span class="theme-toggle-circle"></span>
                            </span>
                        </span>
                    </button>

                    @unless($isGuest)
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="sidebar-link">
                                <x-icon name="log-out" size="18" />
                                <span>Logout</span>
                            </button>
                        </form>
                    @endunless
                </div>
        </aside>

        <div class="main-content">

            <div class="mobile-topbar">
                <a href="{{ $homeRoute }}" class="sidebar-brand" style="color: white;">ClientHub</a>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button type="button" class="mobile-menu-button theme-toggle-mobile" onclick="toggleTheme()" aria-label="Toggle Theme">
                        <span class="theme-icon-light"><x-icon name="sun" size="16" /></span>
                        <span class="theme-icon-dark"><x-icon name="moon" size="16" /></span>
                    </button>
                    <button class="mobile-menu-button" id="mobileMenuButton" aria-label="Open menu">&#9776;</button>
                </div>
            </div>

            <main class="container">
                {{ $slot }}
            </main>

        </div>

    </div>
    <!-- ===== Command Palette Modal ===== -->
    <div id="commandPalette" class="cmd-palette-backdrop">
        <div class="cmd-palette-modal" id="commandPaletteModal">
            <div class="cmd-palette-header">
                <x-icon name="search" size="18" style="color: var(--muted);" />
                <input type="text" id="cmdPaletteInput" class="cmd-palette-input" placeholder="Search projects, clients, invoices, or jump to..." autocomplete="off">
                <kbd class="cmd-kbd" id="closeCmdPaletteBtn" style="cursor: pointer;">ESC</kbd>
            </div>
            <div id="cmdPaletteResults" class="cmd-palette-body">
                <!-- Dynamically populated -->
            </div>
            <div class="cmd-palette-footer">
                <span>Navigate <kbd class="cmd-kbd">↑</kbd> <kbd class="cmd-kbd">↓</kbd></span>
                <span>Select <kbd class="cmd-kbd">↵</kbd></span>
                <span>Close <kbd class="cmd-kbd">esc</kbd></span>
            </div>
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

        // Auto-dismiss initial toasts & setup dismiss timers
        function initToast(toast, delay = 3800) {
            let timer = setTimeout(function () {
                toast.classList.add('toast-hide');
                setTimeout(() => toast.remove(), 260);
            }, delay);

            toast.addEventListener('mouseenter', () => clearTimeout(timer));
            toast.addEventListener('mouseleave', () => {
                timer = setTimeout(function () {
                    toast.classList.add('toast-hide');
                    setTimeout(() => toast.remove(), 260);
                }, 2000);
            });
        }

        document.querySelectorAll('.toast-item').forEach(function (toast, i) {
            initToast(toast, 3800 + i * 300);
        });

        // Global toast trigger for interactive client feedback
        window.showToast = function (message, type = 'success', title = null) {
            let stack = document.getElementById('toastStack');
            if (!stack) {
                stack = document.createElement('div');
                stack.id = 'toastStack';
                stack.className = 'toast-stack';
                document.body.appendChild(stack);
            }

            const item = document.createElement('div');
            item.className = `toast toast-item toast-${type}`;
            const isSuccess = type === 'success';
            const iconSvg = isSuccess 
                ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>'
                : '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';

            item.innerHTML = `
                <div class="toast-icon-wrap">${iconSvg}</div>
                <div class="toast-body">
                    <div class="toast-title">${title || (isSuccess ? 'Success' : 'Notice')}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button type="button" class="toast-close-btn" aria-label="Close">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
                <div class="toast-progress"><div class="toast-progress-bar"></div></div>
            `;

            item.querySelector('.toast-close-btn').addEventListener('click', () => {
                item.classList.add('toast-hide');
                setTimeout(() => item.remove(), 260);
            });

            stack.appendChild(item);
            initToast(item, 3800);
        };

        // Form submit loading states with clean micro-spinner
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function () {
                var btn = form.querySelector('button[type="submit"]');
                if (btn && !btn.disabled && !form.hasAttribute('data-no-loader')) {
                    btn.disabled = true;
                    btn.dataset.originalText = btn.innerHTML;
                    btn.innerHTML = '<span class="btn-spinner"></span> Processing...';
                    btn.style.opacity = '0.85';
                    btn.style.cursor = 'wait';
                }
            });
        });

        var bellButton = document.getElementById('activityBellButton');
        var dropdown = document.getElementById('activityDropdown');

        if (bellButton && dropdown) {
            bellButton.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdown.classList.toggle('is-open');

                // If dropdown just opened, clear the badge and update the database
                var badge = bellButton.querySelector('.activity-badge');
                if (dropdown.classList.contains('is-open') && badge) {
                    badge.remove();

                    fetch("{{$isAdmin ? route('admin.activity_logs.mark_read')  : route('client.activity.mark_read')}}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json",
                            "Accept": "application/json"
                        }
                    }).catch(function (err) {
                        console.error("Failed to mark notifications read:", err);
                    });
                }
            });

            document.addEventListener('click', function (e) {
                if (!dropdown.contains(e.target) && e.target !== bellButton) {
                    dropdown.classList.remove('is-open');
                }
            });
        }
        // ===== Command Palette Logic =====
        var cmdPalette = document.getElementById("commandPalette");
        var cmdInput = document.getElementById("cmdPaletteInput");
        var cmdResults = document.getElementById("cmdPaletteResults");
        var openCmdBtn = document.getElementById("openCommandPalette");
        var closeCmdBtn = document.getElementById("closeCmdPaletteBtn");

        var defaultCommands = [
            @if($isAdmin)
            { title: "Create New Project", subtitle: "Assign project and milestones to client", url: "{{ route('admin.projects.create') }}", icon: "plus" },
            { title: "All Projects", subtitle: "View and filter all client projects", url: "{{ route('admin.projects.index') }}", icon: "folder" },
            { title: "All Invoices", subtitle: "Manage project invoices and CSV export", url: "{{ route('admin.invoices.index') }}", icon: "file-text" },
            { title: "Payment History", subtitle: "View verified client payments", url: "{{ route('admin.payments.index') }}", icon: "credit-card" },
            { title: "Client Accounts", subtitle: "Invite new clients and manage accounts", url: "{{ route('admin.clients.create') }}", icon: "users" },
            { title: "Activity Audit Log", subtitle: "Recent actions across projects", url: "{{ route('admin.activity.index') }}", icon: "activity" },
            @else
            { title: "Project Dashboard", subtitle: "View your active project and milestones", url: "{{ route('dashboard') }}", icon: "layout-dashboard" },
            { title: "Recent Updates & Notifications", subtitle: "View project activity feed", url: "{{ route('client.activity.index') }}", icon: "activity" },
            { title: "Account Settings", subtitle: "Manage profile and password", url: "{{ route('account.edit') }}", icon: "settings" },
            @endif
            { title: "Account Settings", subtitle: "Update email preferences & security", url: "{{ route('account.edit') }}", icon: "settings" }
        ];

        function renderDefaultCommands() {
            if (!cmdResults) return;
            var html = '<div class="cmd-palette-group-title">Quick Actions</div>';
            defaultCommands.forEach(function (cmd, idx) {
                html += '<a href="' + cmd.url + '" class="cmd-item ' + (idx === 0 ? 'is-selected' : '') + '" data-idx="' + idx + '"><div class="cmd-item-icon">★</div><div class="cmd-item-info"><div class="cmd-item-title">' + cmd.title + '</div><div class="cmd-item-subtitle">' + cmd.subtitle + '</div></div><span style="color: var(--muted); font-size: 11px;">Jump →</span></a>';
            });
            cmdResults.innerHTML = html;
            attachCmdItemEvents();
        }

        function openPalette() {
            if (!cmdPalette) return;
            cmdPalette.classList.add("is-open");
            cmdInput.value = "";
            renderDefaultCommands();
            setTimeout(function () { cmdInput.focus(); }, 50);
        }

        function closePalette() {
            if (!cmdPalette) return;
            cmdPalette.classList.remove("is-open");
        }

        if (openCmdBtn) openCmdBtn.addEventListener("click", openPalette);
        if (closeCmdBtn) closeCmdBtn.addEventListener("click", closePalette);

        if (cmdPalette) {
            cmdPalette.addEventListener("click", function (e) {
                if (e.target === cmdPalette) closePalette();
            });
        }

        document.addEventListener("keydown", function (e) {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === "k") {
                e.preventDefault();
                if (cmdPalette && cmdPalette.classList.contains("is-open")) {
                    closePalette();
                } else {
                    openPalette();
                }
            } else if (e.key === "Escape" && cmdPalette && cmdPalette.classList.contains("is-open")) {
                closePalette();
            }
        });

        var debounceTimer = null;
        if (cmdInput) {
            cmdInput.addEventListener("input", function () {
                var query = cmdInput.value.trim();
                clearTimeout(debounceTimer);

                if (query.length < 2) {
                    renderDefaultCommands();
                    return;
                }

                debounceTimer = setTimeout(function () {
                    var searchUrl = @if($isAdmin) "{{ route('admin.search') }}" @else "{{ url('/search') }}" @endif;
                    fetch(searchUrl + "?q=" + encodeURIComponent(query), {
                        headers: { "Accept": "application/json" }
                    })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        var html = "";
                        var total = 0;

                        if (data.projects && data.projects.length) {
                            html += '<div class="cmd-palette-group-title">Projects</div>';
                            data.projects.forEach(function (p) {
                                total++;
                                html += '<a href="' + p.url + '" class="cmd-item ' + (total === 1 ? 'is-selected' : '') + '"><div class="cmd-item-icon">📁</div><div class="cmd-item-info"><div class="cmd-item-title">' + p.title + '</div><div class="cmd-item-subtitle">' + p.subtitle + '</div></div><span style="font-size: 11px; color: var(--muted); text-transform: capitalize;">' + p.status + '</span></a>';
                            });
                        }

                        if (data.invoices && data.invoices.length) {
                            html += '<div class="cmd-palette-group-title">Invoices</div>';
                            data.invoices.forEach(function (inv) {
                                total++;
                                html += '<a href="' + inv.url + '" class="cmd-item ' + (total === 1 ? 'is-selected' : '') + '"><div class="cmd-item-icon">💳</div><div class="cmd-item-info"><div class="cmd-item-title">' + inv.title + '</div><div class="cmd-item-subtitle">' + inv.subtitle + '</div></div><span style="font-size: 12px; font-weight: 600; color: #16a34a;">' + inv.amount + '</span></a>';
                            });
                        }

                        if (data.clients && data.clients.length) {
                            html += '<div class="cmd-palette-group-title">Clients</div>';
                            data.clients.forEach(function (c) {
                                total++;
                                html += '<a href="' + c.url + '" class="cmd-item ' + (total === 1 ? 'is-selected' : '') + '"><div class="cmd-item-icon">👤</div><div class="cmd-item-info"><div class="cmd-item-title">' + c.title + '</div><div class="cmd-item-subtitle">' + c.subtitle + '</div></div><span style="font-size: 11px; color: var(--muted);">Client</span></a>';
                            });
                        }

                        if (!total) {
                            html = '<div style="padding: 24px; text-align: center; color: var(--muted); font-size: 13.5px;">No results found for "' + query + '"</div>';
                        }

                        cmdResults.innerHTML = html;
                        attachCmdItemEvents();
                    })
                    .catch(function (err) {
                        console.error("Command palette search error:", err);
                    });
                }, 180);
            });

            cmdInput.addEventListener("keydown", function (e) {
                var items = cmdResults.querySelectorAll(".cmd-item");
                if (!items.length) return;

                var selected = cmdResults.querySelector(".cmd-item.is-selected");
                var currentIndex = Array.prototype.indexOf.call(items, selected);

                if (e.key === "ArrowDown") {
                    e.preventDefault();
                    if (selected) selected.classList.remove("is-selected");
                    var nextIndex = (currentIndex + 1) % items.length;
                    items[nextIndex].classList.add("is-selected");
                    items[nextIndex].scrollIntoView({ block: "nearest" });
                } else if (e.key === "ArrowUp") {
                    e.preventDefault();
                    if (selected) selected.classList.remove("is-selected");
                    var prevIndex = (currentIndex - 1 + items.length) % items.length;
                    items[prevIndex].classList.add("is-selected");
                    items[prevIndex].scrollIntoView({ block: "nearest" });
                } else if (e.key === "Enter" && selected) {
                    e.preventDefault();
                    selected.click();
                }
            });
        }

        function attachCmdItemEvents() {
            var items = cmdResults.querySelectorAll(".cmd-item");
            items.forEach(function (item) {
                item.addEventListener("mouseenter", function () {
                    var cur = cmdResults.querySelector(".cmd-item.is-selected");
                    if (cur) cur.classList.remove("is-selected");
                    item.classList.add("is-selected");
                });
            });
        }
        window.toggleTheme = function () {
            var current = document.documentElement.getAttribute('data-theme') || 'light';
            var next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            try {
                localStorage.setItem('clienthub_theme', next);
            } catch (e) {}
        };
    })();
</script>
    

</body>
</html>
