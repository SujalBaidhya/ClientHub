<x-layouts.app title="Projects — Admin">

    <style>
        .projects-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
        }

        .projects-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .projects-search-form {
            display: flex;
            gap: 10px;
            flex: 1;
            min-width: 260px;
            max-width: 480px;
        }

        .projects-search-input {
            flex: 1;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-size: 14px;
            background: var(--surface);
            color: var(--text);
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .projects-search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        /* PREMIUM SEGMENTED VIEW SWITCHER */
        .view-segmented-control {
            display: inline-flex;
            background: #eef2f6;
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 3px;
            gap: 3px;
            position: relative;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        [data-theme="dark"] .view-segmented-control {
            background: #182234;
            border-color: #243048;
        }

        .view-seg-btn {
            border: none;
            background: transparent;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            user-select: none;
        }

        .view-seg-btn:hover {
            color: var(--text);
        }

        .view-seg-btn.active {
            background: var(--surface);
            color: var(--text);
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        [data-theme="dark"] .view-seg-btn.active {
            background: #243048;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        /* BASE CARD ARCHITECTURE */
        .project-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .project-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.03);
        }

        [data-theme="dark"] .project-card:hover {
            border-color: #3b4d6e;
            box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.5);
        }

        .project-card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
            line-height: 1.3;
            letter-spacing: -0.01em;
        }

        .project-card-title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .project-card-title a:hover {
            color: var(--primary);
        }

        .client-avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #3730a3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: -0.02em;
            flex-shrink: 0;
        }

        [data-theme="dark"] .client-avatar-circle {
            background: linear-gradient(135deg, #312e81 0%, #3730a3 100%);
            color: #e0e7ff;
        }

        .date-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12.5px;
            color: var(--muted);
        }

        /* PROGRESS TRACKER */
        .progress-box {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 10px;
            padding: 10px 12px;
        }

        [data-theme="dark"] .progress-box {
            background: #162032;
            border-color: #223048;
        }

        .progress-box-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            margin-bottom: 7px;
        }

        .progress-bar-track {
            width: 100%;
            height: 6px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
        }

        [data-theme="dark"] .progress-bar-track {
            background: #243048;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 999px;
            transition: width 0.4s ease;
        }

        .progress-bar-fill.complete {
            background: linear-gradient(90deg, #10b981, #059669);
        }

        .progress-bar-fill.in-progress {
            background: linear-gradient(90deg, #6366f1, #4f46e5);
        }

        .progress-bar-fill.empty {
            background: #cbd5e1;
        }

        [data-theme="dark"] .progress-bar-fill.empty {
            background: #334155;
        }

        /* STATS PILLS */
        .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 500;
            color: var(--muted);
            background: #f1f5f9;
            padding: 4px 9px;
            border-radius: 6px;
        }

        [data-theme="dark"] .stat-pill {
            background: #1a2538;
            color: #94a3b8;
        }

        /* ============================================================
           GRID VIEW SPECIFICS
           ============================================================ */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 20px;
        }

        .projects-grid .project-card {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .projects-grid .project-card:hover {
            transform: translateY(-2px);
        }

        .projects-grid .project-top-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .projects-grid .project-card-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            padding-top: 12px;
            border-top: 1px solid var(--border);
            margin-top: auto;
        }

        /* ============================================================
           PREMIUM LIST VIEW (ALIGNED MODERN SAAS ROWS)
           ============================================================ */
        .projects-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .projects-list .project-card {
            padding: 14px 20px;
            display: grid;
            grid-template-columns: minmax(240px, 1.8fr) minmax(130px, 1fr) minmax(180px, 1.2fr) minmax(140px, 1fr) auto auto;
            align-items: center;
            gap: 16px;
        }

        .projects-list .project-top-row {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .projects-list .project-top-meta {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .projects-list .project-dates-row {
            display: flex;
            align-items: center;
        }

        .projects-list .progress-box {
            padding: 7px 11px;
        }

        .projects-list .project-stats-row {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .projects-list .project-card-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            justify-content: flex-end;
        }

        .pagination-container {
            margin-top: 28px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        @media (max-width: 1050px) {
            .projects-list .project-card {
                grid-template-columns: 1fr 1fr;
                gap: 14px;
            }

            .projects-list .project-card-actions {
                grid-column: span 2;
                justify-content: flex-start;
                border-top: 1px solid var(--border);
                padding-top: 10px;
            }
        }

        @media (max-width: 768px) {
            .projects-header {
                flex-direction: column;
                align-items: stretch;
            }

            .projects-header .btn-success {
                text-align: center;
            }

            .projects-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .projects-search-form {
                max-width: 100%;
            }

            .projects-list .project-card {
                display: flex;
                flex-direction: column;
                align-items: stretch;
            }

            .projects-grid .project-card-actions,
            .projects-list .project-card-actions {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                width: 100%;
            }

            .project-card-actions .btn {
                text-align: center;
                justify-content: center;
                padding: 8px 10px;
            }

            .project-card-actions form {
                grid-column: span 2;
                display: flex;
            }

            .project-card-actions form button {
                width: 100%;
                text-align: center;
            }
        }
    </style>

    <div class="dashboard">

        <div class="projects-header">
            <div>
                <p class="dashboard-eyebrow">Admin Workspace</p>
                <h1 style="margin: 0 0 6px 0;">All Projects</h1>
                <p style="margin: 0; color: var(--muted); font-size: 14px;">Select a project to manage milestones, deliverables, and invoices.</p>
            </div>
            <a href="{{ route('admin.projects.create') }}" class="btn btn-success" style="display: inline-flex; align-items: center; gap: 6px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                New Project
            </a>
        </div>

        <div class="projects-toolbar">
            <form method="GET" action="{{ route('admin.projects.index') }}" class="projects-search-form">
                @if($view === 'archived')
                    <input type="hidden" name="view" value="archived">
                @endif
                @if(!empty($status))
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search by project or client name..."
                    class="projects-search-input"
                >
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn">Search</button>
                    @if($search || !empty($status))
                        <a href="{{ route('admin.projects.index', $view === 'archived' ? ['view' => 'archived'] : []) }}" class="btn">Clear</a>
                    @endif
                </div>
            </form>

            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <div style="display: flex; gap: 6px;">
                    <a href="{{ route('admin.projects.index') }}"
                       style="padding: 7px 14px; border: 1px solid {{ $view === 'active' ? '#1e1b4b' : 'var(--border)' }}; border-radius: 999px; text-decoration: none; font-size: 13px; font-weight: 500; background: {{ $view === 'active' ? '#1e1b4b' : 'var(--surface)' }}; color: {{ $view === 'active' ? '#ffffff' : 'inherit' }}; display: inline-flex; align-items: center; gap: 6px;">
                        <span>Active</span>
                        <span style="background: {{ $view === 'active' ? 'rgba(255,255,255,0.2)' : 'var(--border)' }}; padding: 1px 7px; border-radius: 999px; font-size: 11px;">{{ $activeCount }}</span>
                    </a>
                    <a href="{{ route('admin.projects.index', ['view' => 'archived']) }}"
                       style="padding: 7px 14px; border: 1px solid {{ $view === 'archived' ? '#1e1b4b' : 'var(--border)' }}; border-radius: 999px; text-decoration: none; font-size: 13px; font-weight: 500; background: {{ $view === 'archived' ? '#1e1b4b' : 'var(--surface)' }}; color: {{ $view === 'archived' ? '#ffffff' : 'inherit' }}; display: inline-flex; align-items: center; gap: 6px;">
                        <span>Archived</span>
                        <span style="background: {{ $view === 'archived' ? 'rgba(255,255,255,0.2)' : 'var(--border)' }}; padding: 1px 7px; border-radius: 999px; font-size: 11px;">{{ $archivedCount }}</span>
                    </a>
                </div>

                {{-- LUXURY SEGMENTED VIEW SWITCHER --}}
                <div class="view-segmented-control" role="group" aria-label="Layout view">
                    <button type="button" class="view-seg-btn active" id="btnGridView" onclick="switchView('grid')" title="Grid View">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect></svg>
                        <span>Grid</span>
                    </button>
                    <button type="button" class="view-seg-btn" id="btnListView" onclick="switchView('list')" title="List View">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        <span>List</span>
                    </button>
                </div>
            </div>
        </div>

        @if(!empty($status))
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; background: var(--surface); border: 1px solid var(--border); padding: 10px 16px; border-radius: 10px; font-size: 13.5px; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
                <span style="color: var(--muted); font-size: 13px;">Filtered by status:</span>
                <x-status-badge :status="$status" />
                <a href="{{ route('admin.projects.index', array_filter(['view' => $view === 'archived' ? 'archived' : null, 'search' => $search])) }}" style="margin-left: auto; color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                    Clear status filter &times;
                </a>
            </div>
        @endif

        @if($projects->isEmpty())
            <x-card>
                @if($search)
                    <x-empty-state
                        title="No matches found"
                        message="No projects or clients matched your current search/filters."
                    />
                @elseif(!empty($status))
                    <x-empty-state
                        title="No {{ ucfirst(str_replace('_', ' ', $status)) }} projects"
                        message="There are currently no active projects with this status."
                    />
                @elseif($view === 'archived')
                    <x-empty-state
                        title="No archived projects"
                        message="Projects you archive will appear here and can be restored any time."
                    />
                @else
                    <x-empty-state
                        title="No projects yet"
                        message="Create your first project to get started."
                    />
                @endif
            </x-card>
        @else
            <div id="projectsContainer" class="projects-grid">
                @foreach($projects as $project)
                    @php
                        $totalMilestones = $project->milestones ? $project->milestones->count() : 0;
                        $completedMilestones = $project->milestones ? $project->milestones->where('status', 'completed')->count() : 0;
                        $percent = $totalMilestones > 0 ? (int) round(($completedMilestones / $totalMilestones) * 100) : 0;
                        $clientName = $project->client->name ?? 'Unassigned';
                        $clientInitials = collect(explode(' ', $clientName))
                            ->filter()
                            ->map(fn($p) => strtoupper(mb_substr($p, 0, 1)))
                            ->take(2)
                            ->join('') ?: 'NA';
                        $filesCount = $project->files ? $project->files->count() : 0;
                        $invoicesCount = $project->invoices ? $project->invoices->count() : 0;
                    @endphp

                    <div class="project-card">
                        {{-- Identity / Top Info --}}
                        <div class="project-top-row">
                            <span class="client-avatar-circle" title="Client avatar">{{ $clientInitials }}</span>
                            <div class="project-top-meta">
                                <h3 class="project-card-title">
                                    <a href="{{ route('admin.milestones.create', $project) }}" title="{{ $project->name }}">
                                        {{ $project->name }}
                                    </a>
                                </h3>
                                <span style="font-size: 13px; color: var(--muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    Client: {{ $project->client->name ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="project-status-wrapper">
                                <x-status-badge :status="$project->status" />
                            </div>
                        </div>

                        {{-- Dates & Timing --}}
                        <div class="project-dates-row">
                            <span class="date-item" title="Due Date">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                @if($project->due_date)
                                    Due {{ $project->due_date->format('M d, Y') }}
                                @else
                                    <span style="color: var(--muted); font-style: italic;">No due date</span>
                                @endif
                            </span>
                        </div>

                        {{-- Milestone Progress Bar --}}
                        <div class="progress-box">
                            <div class="progress-box-header">
                                <span style="font-weight: 600; color: var(--text); display: inline-flex; align-items: center; gap: 5px;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                                    Milestones
                                </span>
                                <span style="font-weight: 600; color: {{ $percent === 100 ? '#10b981' : 'var(--primary)' }};">
                                    {{ $completedMilestones }}/{{ $totalMilestones }} ({{ $percent }}%)
                                </span>
                            </div>
                            <div class="progress-bar-track" title="{{ $percent }}% completed">
                                <div
                                    class="progress-bar-fill {{ $percent === 100 ? 'complete' : ($percent > 0 ? 'in-progress' : 'empty') }}"
                                    style="width: {{ $percent }}%;"
                                ></div>
                            </div>
                        </div>

                        {{-- Quick Metrics Tags --}}
                        <div class="project-stats-row">
                            <span class="stat-pill" title="Project Deliverables">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                {{ $filesCount }} {{ \Illuminate\Support\Str::plural('File', $filesCount) }}
                            </span>
                            <span class="stat-pill" title="Project Invoices">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                {{ $invoicesCount }} {{ \Illuminate\Support\Str::plural('Invoice', $invoicesCount) }}
                            </span>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="project-card-actions">
                            <a href="{{ route('admin.files.create', $project) }}" class="btn btn-small" title="Manage Files">
                                Files
                            </a>
                            <a href="{{ route('admin.milestones.create', $project) }}" class="btn btn-small" title="Manage Milestones">
                                Milestones
                            </a>
                            <a href="{{ route('admin.invoices.create', $project) }}" class="btn btn-small" title="Manage Invoices">
                                Invoices
                            </a>
                            <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-small" title="Edit Project">
                                Edit
                            </a>

                            @if($project->isArchived())
                                <form method="POST" action="{{ route('admin.projects.restore', $project) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-small btn-success" style="width: 100%;">Restore</button>
                                </form>
                                <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" onsubmit="return confirm('Permanently delete this project? This will also delete all its milestones, files, and invoices. This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-small" style="background: #dc2626; color: white; border: none; width: 100%;">
                                        Delete Forever
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.projects.archive', $project) }}" onsubmit="return confirm('Archive this project? It will be hidden from the client and from reminders, but nothing is deleted. You can restore it later.');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-small" style="background: #b45309; color: white; border: none; width: 100%;">
                                        Archive
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination Links --}}
            <div class="pagination-container">
                {{-- Previous --}}
                @if($projects->onFirstPage())
                    <span style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; color: #999; background: var(--surface);">
                        Previous
                    </span>
                @else
                    <a href="{{ $projects->previousPageUrl() }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: var(--surface);">
                        Previous
                    </a>
                @endif

                {{-- Page Numbers --}}
                @foreach($projects->getUrlRange(1, $projects->lastPage()) as $page => $url)
                    @if($page == $projects->currentPage())
                        <span style="padding: 8px 12px; border: 1px solid #2563eb; border-radius: 8px; background: #2563eb; color: white; font-weight: 600;">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: var(--surface);">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($projects->hasMorePages())
                    <a href="{{ $projects->nextPageUrl() }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: var(--surface);">
                        Next
                    </a>
                @else
                    <span style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; color: #999; background: var(--surface);">
                        Next
                    </span>
                @endif
            </div>
        @endif

    </div>

    <script>
        function switchView(mode) {
            const container = document.getElementById('projectsContainer');
            const btnGrid = document.getElementById('btnGridView');
            const btnList = document.getElementById('btnListView');

            if (!container) return;

            if (mode === 'list') {
                container.classList.remove('projects-grid');
                container.classList.add('projects-list');
                btnList?.classList.add('active');
                btnGrid?.classList.remove('active');
                localStorage.setItem('clienthub_projects_view', 'list');
            } else {
                container.classList.remove('projects-list');
                container.classList.add('projects-grid');
                btnGrid?.classList.add('active');
                btnList?.classList.remove('active');
                localStorage.setItem('clienthub_projects_view', 'grid');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const savedView = localStorage.getItem('clienthub_projects_view') || 'grid';
            if (savedView === 'list') {
                switchView('list');
            }
        });
    </script>

</x-layouts.app>
