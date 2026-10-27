@php
    $userName = auth()->user()->name ?? 'there';

    $milestones = $project?->milestones ?? collect();
    $files = $project?->files ?? collect();
    $invoices = $project?->invoices ?? collect();

    $totalMilestones = $milestones->count();
    $completedMilestones = $milestones->where('status', 'completed')->count();
    $progress = $totalMilestones > 0
        ? (int) round(($completedMilestones / $totalMilestones) * 100)
        : 0;

    $daysRemaining = null;
    if ($project?->due_date) {
        $daysRemaining = now()->startOfDay()->diffInDays($project->due_date->startOfDay(), false);
    }
@endphp

<x-layouts.app title="Client Dashboard — ClientHub">

    <style>
        .client-welcome {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
        }

        .project-selector-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 6px;
            -webkit-overflow-scrolling: touch;
        }

        /* --- Fix for label cutting through card border --- */
        .current-project-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding-top: 12px;
        }

        .project-label {
            position: static !important;
            display: inline-block !important;
            margin: 0 0 6px 0 !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.06em !important;
            color: var(--primary, #4338ca) !important;
            background: transparent !important;
            padding: 0 !important;
        }
        /* ------------------------------------------------- */

        .client-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin: 20px 0;
            padding: 16px 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .milestone-summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
        }

        .milestone-actions-bar {
            display: flex;
            gap: 10px;
            align-items: center;
            padding-top: 14px;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
        }

        .revision-popover {
            margin-top: 10px;
            padding: 14px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 10px;
            width: 100%;
            max-width: 360px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            box-sizing: border-box;
        }

        .client-file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #ffffff;
        }

        .invoice-card-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
        }

        /* ===== Milestone Roadmap Stepper & Cards ===== */
        .milestone-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 12px;
            background: #ffffff;
            padding: 16px 18px;
            border-left: 4px solid #cbd5e1;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .milestone-card[data-status="completed"] { border-left-color: #10b981; }
        .milestone-card[data-status="in_progress"] { border-left-color: #f59e0b; }
        .milestone-card[data-status="pending"] { border-left-color: #94a3b8; }

        .milestone-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            border-color: #cbd5e1;
        }

        .milestone-card.is-highlighted {
            animation: milestone-highlight 1.2s ease;
        }

        @keyframes milestone-highlight {
            0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4); border-color: #4f46e5; }
            50% { box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.25); border-color: #4f46e5; }
            100% { box-shadow: none; }
        }

        .milestone-chevron {
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, color 0.15s ease;
        }

        .milestone-card[open] .milestone-chevron {
            transform: rotate(180deg);
            color: #1e1b4b;
        }

        .roadmap-stepper-container {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }

        .roadmap-stepper-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .roadmap-stepper-title {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .roadmap-stepper-counter {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--primary);
            background: #eef2ff;
            padding: 2px 9px;
            border-radius: 999px;
            letter-spacing: 0.02em;
        }

        .roadmap-track-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            padding: 4px 2px 6px 2px;
            -webkit-overflow-scrolling: touch;
        }

        .roadmap-step-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.15s ease;
            background: #ffffff;
            border: 1px solid var(--border);
            color: var(--muted);
            flex-shrink: 0;
            user-select: none;
        }

        .roadmap-step-pill:hover {
            transform: translateY(-1px);
            border-color: #cbd5e1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            color: var(--text);
        }

        .roadmap-step-pill.is-done {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }

        .roadmap-step-pill.is-active {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1e40af;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
        }

        .roadmap-step-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            background: #e2e8f0;
            color: #475569;
            flex-shrink: 0;
        }

        .roadmap-step-pill.is-done .roadmap-step-dot {
            background: #10b981;
            color: #ffffff;
        }

        .roadmap-step-pill.is-active .roadmap-step-dot {
            background: #3b82f6;
            color: #ffffff;
            position: relative;
        }

        .roadmap-pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #ffffff;
            animation: pulse-dot 1.4s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.7; }
        }

        .roadmap-connector {
            flex: 1;
            min-width: 14px;
            height: 2px;
            background: #e2e8f0;
            flex-shrink: 0;
        }

        .roadmap-connector.is-done {
            background: #10b981;
        }

        @media (max-width: 768px) {
            .client-welcome {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .current-project-header-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                padding-top: 8px;
            }

            .client-info-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .milestone-actions-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .milestone-actions-bar form,
            .milestone-actions-bar details {
                width: 100%;
            }

            .milestone-actions-bar .btn,
            .milestone-actions-bar summary.btn {
                width: 100%;
                text-align: center;
                justify-content: center;
                padding: 10px;
                box-sizing: border-box;
            }

            .revision-popover {
                max-width: 100%;
            }

            .client-file-item {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .client-file-item .btn {
                width: 100%;
                text-align: center;
                justify-content: center;
            }

            .invoice-card-buttons {
                grid-template-columns: 1fr;
            }

            .invoice-card-buttons a {
                width: 100%;
                text-align: center;
                justify-content: center;
                padding: 10px;
                box-sizing: border-box;
            }
        }
        .dropzone-box {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            padding: 22px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }
        .dropzone-box:hover, .dropzone-box.is-dragover {
            border-color: var(--primary, #4f46e5);
            background: #eef2ff;
        }
        .dropzone-hidden-input {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
            z-index: 2;
        }
        .dropzone-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            pointer-events: none;
        }
        .dropzone-icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #e0e7ff;
            color: var(--primary, #4f46e5);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
        }
        .dropzone-box:hover .dropzone-icon-wrap {
            transform: translateY(-2px);
        }
        .dropzone-title {
            font-size: 13.5px;
            font-weight: 600;
            color: #1e1b4b;
            display: block;
        }
        .dropzone-hint {
            font-size: 11.5px;
            color: #6b7280;
            display: block;
            margin-top: 2px;
        }
        .dropzone-preview {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #ffffff;
            border: 1px solid var(--border, #e4e4f0);
            border-radius: 8px;
            padding: 10px 14px;
            text-align: left;
            position: relative;
            z-index: 3;
        }
        .dropzone-preview-info {
            flex: 1;
            min-width: 0;
        }
        .dropzone-preview-name {
            font-size: 13px;
            font-weight: 600;
            color: #1e1b4b;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dropzone-preview-size {
            font-size: 11px;
            color: #6b7280;
            display: block;
        }
        .dropzone-remove-btn {
            background: #fee2e2;
            color: #991b1b;
            border: none;
            border-radius: 6px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .dropzone-remove-btn:hover {
            background: #fecaca;
        }
    
        [data-theme="dark"] .dashboard .card h1,
        [data-theme="dark"] .dashboard .card h2,
        [data-theme="dark"] .dashboard .card h3,
        [data-theme="dark"] .dashboard .card-header,
        [data-theme="dark"] .dashboard .card-title,
        [data-theme="dark"] .dashboard .card-header h2,
        [data-theme="dark"] .dashboard .card-header h3 {
            color: #f1f5f9 !important;
        }

        [data-theme="dark"] .milestone-title,
        [data-theme="dark"] .milestone-card strong,
        [data-theme="dark"] .milestone-card h3,
        [data-theme="dark"] .milestone-card h4 {
            color: #f1f5f9 !important;
        }

        [data-theme="dark"] .milestone-card p,
        [data-theme="dark"] .milestone-card span {
            color: #cbd5e1;
        }

        [data-theme="dark"] .file-item strong,
        [data-theme="dark"] .file-info strong {
            color: #f1f5f9 !important;
        }

        [data-theme="dark"] .file-item span {
            color: #cbd5e1;
        }

        [data-theme="dark"] .file-item span[style*="background: #f1f5f9"],
        [data-theme="dark"] .file-item span[style*="background:#f1f5f9"] {
            background: #263449 !important;
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .file-item,
        [data-theme="dark"] .file-info {
            color: #cbd5e1;
        }

        [data-theme="dark"] .client-invoice-card {
            background: #111827 !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .client-invoice-card span[style*="#1e1b4b"],
        [data-theme="dark"] .client-invoice-card span[style*="#1E1B4B"] {
            color: #c7d2fe !important;
        }

        [data-theme="dark"] .client-invoice-card strong[style*="#1e1b4b"],
        [data-theme="dark"] .client-invoice-card strong[style*="#1E1B4B"] {
            color: #a5b4fc !important;
        }

        [data-theme="dark"] .client-invoice-card p,
        [data-theme="dark"] .client-invoice-card .invoice-label {
            color: #94a3b8 !important;
        }

        [data-theme="dark"] .client-invoice-card strong {
            color: #e2e8f0;
        }

        [data-theme="dark"] .roadmap-stepper-container,
        [data-theme="dark"] .roadmap-step-pill {
            background-color: #111827 !important;
            color: var(--text) !important;
            border-color: var(--border) !important;
        }

        [data-theme="dark"] .roadmap-stepper-counter {
            background-color: #252747 !important;
            color: #a5b4fc !important;
        }

        [data-theme="dark"] .roadmap-step-pill.is-done {
            background-color: #123329 !important;
            border-color: #166534 !important;
            color: #86efac !important;
        }

        [data-theme="dark"] .roadmap-step-pill.is-active {
            background-color: #172554 !important;
            border-color: #1d4ed8 !important;
            color: #bfdbfe !important;
        }

        [data-theme="dark"] .roadmap-step-dot {
            background-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        [data-theme="dark"] .client-invoice-card a.btn:not(.btn-success) {
            background: #1e293b !important;
            border-color: #475569 !important;
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .project-selector-bar a.btn:not([style*="var(--primary)"]) {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .file-item,
        [data-theme="dark"] .milestone-card {
            background: #111827 !important;
            border-color: #334155 !important;
        }

        [data-theme="dark"] .dashboard .muted,
        [data-theme="dark"] .dashboard small {
            color: #94a3b8;
        }
    </style>

    <div class="dashboard">
        <div class="client-welcome">
            <div>
                <p class="dashboard-eyebrow">Client Portal</p>
                <h1 style="margin: 0 0 6px 0;">Welcome back, {{ $userName }}</h1>
                <p style="margin: 0; color: #6b7280; font-size: 14px;">Here's an overview of your project, milestones, files, and invoices.</p>
            </div>

            <div class="dashboard-date" style="align-self: flex-start;">
                {{ now()->format('M d, Y') }}
            </div>
        </div>

        @if(!$project)
            <x-card>
                <x-empty-state
                    title="No project yet"
                    message="You don't have a project assigned yet. Your project manager will set one up soon."
                />
            </x-card>
        @else

            @if($allProjects->count() > 1)
                <div class="project-selector-bar">
                    @foreach($allProjects as $p)
                        <a
                            href="{{ route('dashboard.project', $p) }}"
                            class="btn btn-small"
                            style="{{ $p->id === $project->id ? 'background: var(--primary); color: white; border-color: var(--primary);' : 'background: white;' }}"
                        >
                            {{ $p->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Current Project --}}
            <x-card class="current-project-card">

                <div style="padding: 16px 24px;">

                    {{-- Top Section --}}
                    <div class="current-project-header-row">
                        <div style="min-width: 0; flex: 1;">
                            <span class="project-label">Current Project</span>
                            <h1 class="current-project-title" style="margin: 4px 0 8px 0; word-break: break-word;">{{ $project->name }}</h1>
                            @if($project->description)
                                <p class="current-project-description" style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.5;">{{ $project->description }}</p>
                            @endif
                        </div>

                        <div style="flex-shrink: 0;">
                            <x-status-badge :status="$project->status" />
                        </div>
                    </div>

                    {{-- Project Information --}}
                    <div class="client-info-grid">
                        <div>
                            <span class="info-label">Client</span>
                            <strong style="display: block; font-size: 15px; margin-top: 2px;">{{ $project->client->name ?? 'N/A' }}</strong>
                        </div>

                        <div>
                            <span class="info-label">Start Date</span>
                            <strong style="display: block; font-size: 15px; margin-top: 2px;">{{ $project->start_date?->format('M d, Y') ?? 'Not set' }}</strong>
                        </div>

                        <div>
                            <span class="info-label">Due Date</span>
                            <strong style="display: block; font-size: 15px; margin-top: 2px;">{{ $project->due_date?->format('M d, Y') ?? 'Not set' }}</strong>

                            @if($daysRemaining !== null)
                                <div style="margin-top: 6px;">
                                    @if($daysRemaining > 0)
                                        <x-status-badge status="pending" :label="$daysRemaining . ' day' . ($daysRemaining === 1 ? '' : 's') . ' left'" />
                                    @elseif($daysRemaining === 0)
                                        <x-status-badge status="in_progress" label="Due today" />
                                    @else
                                        <x-status-badge status="overdue" :label="'Overdue by ' . abs($daysRemaining) . ' day' . (abs($daysRemaining) === 1 ? '' : 's')" />
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div class="project-progress-section">
                        <div class="progress-header" style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13.5px;">
                            <span>Project Progress</span>
                            <strong>{{ $progress }}%</strong>
                        </div>

                        <div class="progress-bar" style="height: 8px; border-radius: 999px; background: #e5e7eb; overflow: hidden;">
                            <div class="progress-bar-fill" style="width: {{ $progress }}%; height: 100%; background: #2563eb; transition: width 0.3s ease;"></div>
                        </div>
                    </div>

                </div>

            </x-card>

            {{-- Dashboard Grid --}}
            <div class="dashboard-grid">

                {{-- Milestones --}}
                <x-card title="Project Milestones">
                    @if($milestones->isEmpty())
                        <x-empty-state
                            title="No milestones yet"
                            message="Milestones will appear here when they are added to the project."
                        />
                    @else
                        {{-- Visual Roadmap Stepper --}}
                        <div class="roadmap-stepper-container">
                            <div class="roadmap-stepper-header">
                                <span class="roadmap-stepper-title">
                                    <x-icon name="activity" size="14" />
                                    <span>Project Roadmap</span>
                                </span>
                                <span class="roadmap-stepper-counter">{{ $completedMilestones }} of {{ $totalMilestones }} completed ({{ $progress }}%)</span>
                            </div>
                            <div class="roadmap-track-wrapper">
                                @foreach($milestones as $idx => $m)
                                    @php
                                        $isDone = $m->status === 'completed';
                                        $isActive = $m->status === 'in_progress';
                                    @endphp
                                    <div class="roadmap-step-pill {{ $isDone ? 'is-done' : ($isActive ? 'is-active' : 'is-pending') }}"
                                         title="Click to jump to '{{ $m->title }}'"
                                         onclick="const d = document.getElementById('milestone-card-{{ $m->id }}'); if (d) { d.open = true; d.scrollIntoView({ behavior: 'smooth', block: 'center' }); d.classList.add('is-highlighted'); setTimeout(() => d.classList.remove('is-highlighted'), 1200); }">
                                        <span class="roadmap-step-dot">
                                            @if($isDone)
                                                <x-icon name="check" size="11" strokeWidth="3" />
                                            @elseif($isActive)
                                                <span class="roadmap-pulse-dot"></span>
                                            @else
                                                {{ $idx + 1 }}
                                            @endif
                                        </span>
                                        <span class="roadmap-step-name">{{ $m->title }}</span>
                                    </div>
                                    @if(!$loop->last)
                                        <div class="roadmap-connector {{ $isDone ? 'is-done' : '' }}"></div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        @php
                            $nextMilestone = $milestones->where('status', '!=', 'completed')->whereNotNull('due_date')->sortBy('due_date')->first();
                        @endphp
                        @if($nextMilestone)
                            <div style="margin-bottom: 14px; padding: 10px 14px; border-radius: 10px; background: #f1f0fb; font-size: 13.5px; color: #1e1b4b; display: flex; align-items: center; gap: 8px;">
                                <x-icon name="clock" size="16" style="color: var(--primary);" />
                                <div>
                                    <strong>Next up:</strong> {{ $nextMilestone->title }}
                                    &middot; {{ $nextMilestone->due_date->format('M d, Y') }}
                                    @if($nextMilestone->isOverdue())
                                        <span style="color: #dc2626; font-weight: 600;">(overdue)</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="milestone-list">
                            @foreach($milestones as $milestone)
                                <details id="milestone-card-{{ $milestone->id }}" class="milestone-card" data-status="{{ $milestone->status }}">
                                    <summary style="list-style: none; cursor: pointer;">
                                        <div class="milestone-summary-row">
                                            <div style="min-width: 0; flex: 1;">
                                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                    <strong style="font-size: 14.5px; color: #1e1b4b;">{{ $milestone->title }}</strong>
                                                    <x-status-badge :status="$milestone->status" />
                                                    @if($milestone->isOverdue())
                                                        <span class="badge badge-danger">Overdue by {{ abs($milestone->daysUntilDue()) }} day(s)</span>
                                                    @elseif($milestone->status !== 'completed' && $milestone->daysUntilDue() !== null && $milestone->daysUntilDue() <= 3)
                                                        <span class="badge badge-warning">{{ $milestone->daysUntilDue() === 0 ? 'Due today' : 'Due in ' . $milestone->daysUntilDue() . ' day(s)' }}</span>
                                                    @endif
                                                </div>
                                                @if($milestone->description)
                                                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        {{ $milestone->description }}
                                                    </p>
                                                @endif
                                            </div>

                                            <span class="milestone-chevron"><x-icon name="chevron-down" size="16" /></span>
                                        </div>
                                    </summary>

                                    <div style="margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: 14px;">

                                        {{-- Date Row --}}
                                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; font-size: 13px;">
                                            <div>
                                                <span class="detail-label" style="display: block; font-size: 11px; color: var(--muted); text-transform: uppercase;">Target Date</span>
                                                <strong>{{ $milestone->due_date ? $milestone->due_date->format('M d, Y') : 'No target date' }}</strong>
                                            </div>

                                            @if($milestone->completed_at ?? $milestone->approved_at)
                                                <div>
                                                    <span class="detail-label" style="display: block; font-size: 11px; color: var(--muted); text-transform: uppercase;">Completed On</span>
                                                    <strong style="color: var(--success-text);">
                                                        {{ ($milestone->completed_at ?? $milestone->approved_at)->format('M d, Y') }}
                                                    </strong>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Full Description --}}
                                        @if($milestone->description)
                                            <div style="font-size: 13.5px; color: #4b5563; line-height: 1.5;">
                                                {{ $milestone->description }}
                                            </div>
                                        @endif

                                        {{-- Revision Notes --}}
                                        @if($milestone->client_notes && $milestone->status !== 'completed')
                                            <div style="padding: 12px 14px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; font-size: 13px; color: #92400e;">
                                                <strong style="display: block; margin-bottom: 4px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Your Revision Notes</strong>
                                                {{ $milestone->client_notes }}
                                            </div>
                                        @endif

                                        {{-- Client Actions --}}
                                        @if($milestone->status !== 'completed')
                                            <div class="milestone-actions-bar">
                                                <form method="POST" action="{{ Route::has('client.milestones.approve') ? route('client.milestones.approve', $milestone) : route('milestones.complete', $milestone) }}" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-small btn-success" onclick="return confirm('Approve this milestone?')">
                                                        ✓ Approve Milestone
                                                    </button>
                                                </form>

                                                @if(Route::has('client.milestones.request-revision'))
                                                    <details style="position: relative;">
                                                        <summary class="btn btn-small" style="list-style: none; cursor: pointer; border-color: #cbd5e1; background: white;">
                                                            Request Changes
                                                        </summary>
                                                        <div class="revision-popover">
                                                            <form method="POST" action="{{ route('client.milestones.request-revision', $milestone) }}" style="margin: 0;">
                                                                @csrf
                                                                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">
                                                                    What needs revision?
                                                                </label>
                                                                <textarea name="client_notes" rows="3" style="width: 100%; padding: 8px 10px; font-size: 13px; margin-bottom: 10px; border: 1px solid var(--border); border-radius: 6px; box-sizing: border-box;" placeholder="e.g. Please update the button styles and spacing..." required>{{ old('client_notes', $milestone->client_notes) }}</textarea>
                                                                <button type="submit" class="btn btn-small" style="background: var(--warning-bg); color: var(--warning-text); border: 1px solid #fde68a; width: 100%;">
                                                                    Submit Feedback
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </details>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Comments --}}
                                        <div style="margin-top: 14px; border-top: 1px solid var(--border); padding-top: 14px;">
                                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                                                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1e1b4b; letter-spacing: 0.03em; display: inline-flex; align-items: center; gap: 5px;">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                                    Comments
                                                </span>
                                                <span style="font-size: 11.5px; color: var(--muted); font-weight: 500;">
                                                    {{ $milestone->comments->count() }} {{ \Illuminate\Support\Str::plural('reply', $milestone->comments->count()) }}
                                                </span>
                                            </div>

                                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                                @forelse($milestone->comments as $comment)
                                                    @php
                                                        $cInitials = collect(explode(' ', $comment->user->name ?? 'U'))
                                                            ->filter()
                                                            ->map(fn($p) => strtoupper(mb_substr($p, 0, 1)))
                                                            ->take(2)
                                                            ->join('') ?: 'U';
                                                        $cIsAdmin = ($comment->user->role ?? null) === 'admin';
                                                    @endphp
                                                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 9px; padding: 10px 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 5px;">
                                                            <div style="display: flex; align-items: center; gap: 7px;">
                                                                <span style="width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 10.5px; font-weight: 700; background: {{ $cIsAdmin ? '#4f46e5' : '#0d9488' }}; color: white; flex-shrink: 0;">
                                                                    {{ $cInitials }}
                                                                </span>
                                                                <strong style="font-size: 12.5px; color: #1e1b4b;">{{ $comment->user->name }}</strong>
                                                                <span style="font-size: 10.5px; font-weight: 600; padding: 1px 6px; border-radius: 999px; background: {{ $cIsAdmin ? '#e0e7ff' : '#ccfbf1' }}; color: {{ $cIsAdmin ? '#3730a3' : '#115e59' }};">
                                                                    {{ $cIsAdmin ? 'Admin' : 'Client' }}
                                                                </span>
                                                            </div>
                                                            <span style="font-size: 11.5px; color: var(--muted);" title="{{ $comment->created_at->format('M d, Y h:i A') }}">
                                                                &middot; {{ $comment->created_at->diffForHumans() }}
                                                            </span>
                                                        </div>
                                                        <div style="font-size: 13px; line-height: 1.5; color: #334155; padding-left: 31px; white-space: pre-wrap; word-break: break-word;">
                                                            {{ $comment->body }}
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p style="font-size: 13px; color: var(--muted); margin: 6px 0 0;">No comments yet.</p>
                                                @endforelse
                                            </div>

                                            <form method="POST" action="{{ route('comments.store', $milestone) }}" style="margin-top: 12px;">
                                                @csrf
                                                <textarea name="body" rows="2" placeholder="Write a comment..." required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border); font-size: 13px; box-sizing: border-box;"></textarea>
                                                <button type="submit" class="btn btn-small" style="margin-top: 8px; display: inline-flex; align-items: center; gap: 5px;">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                                    Post Comment
                                                </button>
                                            </form>
                                        </div>

                                    </div>
                                </details>
                            @endforeach
                        </div>
                    @endif
                </x-card>

                               {{-- Project Files --}}
                <x-card title="Project Files">
                    @if($project)
                        @error('file_name')
                            <div style="margin-bottom: 14px; padding: 12px 14px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; font-size: 13px; color: #991b1b;">
                                {{ $message }}
                            </div>
                        @enderror
                        <form method="POST" action="{{ route('files.store', $project) }}" enctype="multipart/form-data" style="margin-bottom: 20px;">
                            @csrf
                            <div class="dropzone-box" id="clientDropzoneBox">
                                <input type="file" name="file" id="clientDropzoneInput" required accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip" class="dropzone-hidden-input">
                                <div class="dropzone-content" id="clientDropzonePrompt">
                                    <div class="dropzone-icon-wrap">
                                        <x-icon name="upload-cloud" size="24" />
                                    </div>
                                    <div>
                                        <span class="dropzone-title">Drop your file here, or browse</span>
                                        <span class="dropzone-hint">PDF, Images, Word, Excel, ZIP up to 20MB</span>
                                    </div>
                                </div>
                                <div class="dropzone-preview" id="clientDropzonePreview" style="display: none;">
                                    <div style="color: var(--primary);"><x-icon name="file-text" size="22" /></div>
                                    <div class="dropzone-preview-info">
                                        <span id="clientDropzoneFileName" class="dropzone-preview-name"></span>
                                        <span id="clientDropzoneFileSize" class="dropzone-preview-size"></span>
                                    </div>
                                    <button type="button" id="clientDropzoneClearBtn" class="dropzone-remove-btn" title="Remove file">
                                        <x-icon name="x" size="14" />
                                    </button>
                                </div>
                            </div>
                            <div id="clientDropzoneActions" style="display: none; margin-top: 10px; justify-content: flex-end;">
                                <button type="submit" class="btn btn-success" style="padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px;">
                                    <x-icon name="upload-cloud" size="15" />
                                    <span>Upload Selected File</span>
                                </button>
                            </div>
                        </form>
                        <script>
                            (function() {
                                var box = document.getElementById("clientDropzoneBox");
                                var input = document.getElementById("clientDropzoneInput");
                                var prompt = document.getElementById("clientDropzonePrompt");
                                var preview = document.getElementById("clientDropzonePreview");
                                var nameEl = document.getElementById("clientDropzoneFileName");
                                var sizeEl = document.getElementById("clientDropzoneFileSize");
                                var clearBtn = document.getElementById("clientDropzoneClearBtn");
                                var actions = document.getElementById("clientDropzoneActions");

                                if (!input || !box) return;

                                function formatBytes(bytes) {
                                    if (bytes === 0) return "0 Bytes";
                                    var k = 1024, sizes = ["Bytes", "KB", "MB", "GB"];
                                    var i = Math.floor(Math.log(bytes) / Math.log(k));
                                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
                                }

                                input.addEventListener("change", function() {
                                    if (input.files && input.files[0]) {
                                        var file = input.files[0];
                                        nameEl.textContent = file.name;
                                        sizeEl.textContent = formatBytes(file.size);
                                        prompt.style.display = "none";
                                        preview.style.display = "flex";
                                        actions.style.display = "flex";
                                    }
                                });

                                if (clearBtn) {
                                    clearBtn.addEventListener("click", function(e) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        input.value = "";
                                        preview.style.display = "none";
                                        prompt.style.display = "flex";
                                        actions.style.display = "none";
                                    });
                                }

                                ["dragenter", "dragover"].forEach(function(evt) {
                                    box.addEventListener(evt, function(e) {
                                        e.preventDefault();
                                        box.classList.add("is-dragover");
                                    });
                                });

                                ["dragleave", "drop"].forEach(function(evt) {
                                    box.addEventListener(evt, function(e) {
                                        e.preventDefault();
                                        box.classList.remove("is-dragover");
                                    });
                                });
                            })();
                        </script>
                    @endif

                    @if($files->isEmpty())
                        <x-empty-state
                            title="No files available"
                            message="Project files will appear here."
                        />
                    @else
                        <div class="file-list">
                            @foreach($files as $file)
                                @php
                                    $ext = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                                    $typeConfig = match(true) {
                                        $ext === 'pdf' => ['bg' => '#fee2e2', 'color' => '#dc2626', 'label' => 'PDF'],
                                        in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg']) => ['bg' => '#d1fae5', 'color' => '#059669', 'label' => strtoupper($ext)],
                                        in_array($ext, ['doc', 'docx', 'txt', 'rtf']) => ['bg' => '#dbeafe', 'color' => '#2563eb', 'label' => strtoupper($ext)],
                                        in_array($ext, ['xls', 'xlsx', 'csv']) => ['bg' => '#fef3c7', 'color' => '#d97706', 'label' => strtoupper($ext)],
                                        in_array($ext, ['zip', 'rar', 'tar', 'gz', '7z']) => ['bg' => '#ede9fe', 'color' => '#7c3aed', 'label' => 'ZIP'],
                                        default => ['bg' => '#f1f5f9', 'color' => '#475569', 'label' => strtoupper($ext) ?: 'FILE'],
                                    };
                                    $formattedSize = $file->file_size ? ($file->file_size < 1048576 ? round($file->file_size / 1024, 1) . ' KB' : round($file->file_size / 1048576, 1) . ' MB') : null;
                                @endphp
                                <div class="file-item">
                                    <a href="{{ route('files.view', $file) }}" title="View file" style="text-decoration: none; color: inherit; flex: 1; min-width: 0;">
                                    <div class="file-info">
                                        <div class="file-icon" style="background: {{ $typeConfig['bg'] }}; color: {{ $typeConfig['color'] }}; font-weight: 700; font-size: 11px;">
                                            {{ $typeConfig['label'] }}
                                        </div>
                                        <div>
                                            <strong>{{ $file->original_name }}</strong>
                                            <span style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-top: 2px;">
                                                @if($formattedSize)
                                                    <span style="background: #f1f5f9; padding: 1px 6px; border-radius: 4px; font-size: 11px; font-weight: 500; color: #475569;">{{ $formattedSize }}</span>
                                                    <span>·</span>
                                                @endif
                                                <span>Uploaded {{ $file->created_at->format('M d, Y') }}</span>
                                                @if($file->uploaded_by === auth()->id())
                                                    <span>· by you</span>
                                                @elseif($file->uploadedBy)
                                                    <span>· by {{ $file->uploadedBy->name }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    </a>
                                    <a href="{{ route('files.download', $file) }}" class="btn btn-small" style="display: inline-flex; align-items: center; gap: 5px;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        Download
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>

            </div>

            {{-- Invoices --}}
            <x-card title="Invoices">
                @if($invoices->isEmpty())
                    <x-empty-state
                        title="No invoices yet"
                        message="Invoices for your project will appear here."
                    />
                @else
                    <div class="client-invoice-list" style="display: flex; flex-direction: column; gap: 14px;">
                        @foreach($invoices as $invoice)
                            <div class="client-invoice-card" style="border: 1px solid var(--border); border-radius: 10px; padding: 18px; background: #ffffff;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 12px;">
                                    <div>
                                        <span style="font-weight: 700; font-size: 15px; color: #1e1b4b;">{{ $invoice->invoice_number }}</span>
                                        <p style="margin: 2px 0 0; font-size: 13px; color: var(--muted);">{{ $project->name }}</p>
                                    </div>
                                    <x-status-badge :status="$invoice->status" />
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; font-size: 13px;">
                                    <div>
                                        <span class="invoice-label" style="display: block; font-size: 11px; color: var(--muted); text-transform: uppercase;">Issued</span>
                                        <strong>{{ $invoice->issued_at?->format('M d, Y') ?? '—' }}</strong>
                                    </div>

                                    <div style="text-align: right;">
                                        <span class="invoice-label" style="display: block; font-size: 11px; color: var(--muted); text-transform: uppercase;">Amount</span>
                                        <strong style="font-size: 15px; color: #1e1b4b;">NPR {{ number_format($invoice->amount, 2) }}</strong>
                                    </div>
                                </div>

                                <div class="invoice-card-buttons">
                                    <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-small" style="background: white; border: 1px solid var(--border); color: #374151;">
                                        Download PDF
                                    </a>

                                    @if($invoice->status !== 'paid')
                                        <a href="{{ route('payment.pay', $invoice) }}" class="btn btn-small btn-success">
                                            Pay via eSewa
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

        @endif
    </div>

</x-layouts.app>