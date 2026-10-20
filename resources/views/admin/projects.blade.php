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
            background: #ffffff;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .projects-search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .view-toggle-group {
            display: inline-flex;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 3px;
            gap: 2px;
        }

        .view-toggle-btn {
            background: none;
            border: none;
            padding: 6px 12px;
            border-radius: 7px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }

        .view-toggle-btn.active {
            background: #1e1b4b;
            color: #ffffff;
        }

        /* GRID VIEW LAYOUT */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 20px;
        }

        .project-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, border-color 0.2s ease;
            position: relative;
        }

        .project-card:hover {
            transform: translateY(-2px);
            border-color: #cbd5e1;
            box-shadow: 0 12px 24px -8px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.03);
        }

        .project-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .project-card-title {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            line-height: 1.3;
            text-decoration: none;
        }

        .project-card-title a {
            color: inherit;
            text-decoration: none;
        }

        .project-card-title a:hover {
            color: var(--primary);
        }

        .project-client-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #475569;
        }

        .client-avatar-circle {
            width: 28px;
            height: 28px;
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

        .project-dates-row {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 12.5px;
            color: #64748b;
        }

        .date-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* PROGRESS TRACKER */
        .progress-box {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 10px;
            padding: 12px 14px;
        }

        .progress-box-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12.5px;
            margin-bottom: 8px;
        }

        .progress-bar-track {
            width: 100%;
            height: 7px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
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

        /* STATS PILLS */
        .project-stats-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 500;
            color: #475569;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 6px;
        }

        /* ACTION BAR */
        .project-card-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
            margin-top: auto;
        }

        .project-card-actions .btn-small {
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 7px;
        }

        /* LIST VIEW LAYOUT */
        .projects-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .projects-list .project-card {
            padding: 16px 20px;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .projects-list .project-list-main {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
            min-width: 0;
        }

        .projects-list .project-list-info {
            min-width: 220px;
            max-width: 280px;
        }

        .projects-list .progress-box {
            width: 220px;
            padding: 8px 12px;
            flex-shrink: 0;
        }

        .projects-list .project-card-actions {
            border-top: none;
            padding-top: 0;
            margin-top: 0;
            flex-shrink: 0;
        }

        .pagination-container {
            margin-top: 28px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        @media (max-width: 900px) {
            .projects-list .project-card {
                flex-direction: column;
                align-items: stretch;
            }

            .projects-list .project-list-main {
                flex-direction: column;
                align-items: stretch;
            }

            .projects-list .project-list-info,
            .projects-list .progress-box {
                width: 100%;
                max-width: 100%;
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

            .project-card-actions {
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
                <p style="margin: 0; color: #6b7280; font-size: 14px;">Select a project to manage its milestones, deliverables, and invoices.</p>
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
                       style="padding: 7px 14px; border: 1px solid {{ $view === 'active' ? '#1e1b4b' : 'var(--border)' }}; border-radius: 999px; text-decoration: none; font-size: 13px; font-weight: 500; background: {{ $view === 'active' ? '#1e1b4b' : '#ffffff' }}; color: {{ $view === 'active' ? '#ffffff' : 'inherit' }}; display: inline-flex; align-items: center; gap: 6px;">
                        <span>Active</span>
                        <span style="background: {{ $view === 'active' ? 'rgba(255,255,255,0.2)' : '#f1f5f9' }}; padding: 1px 7px; border-radius: 999px; font-size: 11px;">{{ $activeCount }}</span>
                    </a>
                    <a href="{{ route('admin.projects.index', ['view' => 'archived']) }}"
                       style="padding: 7px 14px; border: 1px solid {{ $view === 'archived' ? '#1e1b4b' : 'var(--border)' }}; border-radius: 999px; text-decoration: none; font-size: 13px; font-weight: 500; background: {{ $view === 'archived' ? '#1e1b4b' : '#ffffff' }}; color: {{ $view === 'archived' ? '#ffffff' : 'inherit' }}; display: inline-flex; align-items: center; gap: 6px;">
                        <span>Archived</span>
                        <span style="background: {{ $view === 'archived' ? 'rgba(255,255,255,0.2)' : '#f1f5f9' }}; padding: 1px 7px; border-radius: 999px; font-size: 11px;">{{ $archivedCount }}</span>
                    </a>
                </div>

                {{-- Grid / List View Switcher --}}
                <div class="view-toggle-group">
                    <button type="button" class="view-toggle-btn active" id="btnGridView" onclick="switchView('grid')" title="Grid View">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        <span>Grid</span>
                    </button>
                    <button type="button" class="view-toggle-btn" id="btnListView" onclick="switchView('list')" title="List View">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        <span>List</span>
                    </button>
                </div>
            </div>
        </div>

        @if(!empty($status))
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; background: #ffffff; border: 1px solid var(--border); padding: 10px 16px; border-radius: 10px; font-size: 13.5px; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
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
                        <div class="project-card-header">
                            <div style="min-width: 0;">
                                <h3 class="project-card-title">
                                    <a href="{{ route('admin.milestones.create', $project) }}" title="{{ $project->name }}">
                                        {{ $project->name }}
                                    </a>
                                </h3>
                                <div class="project-client-badge" style="margin-top: 6px;">
                                    <span class="client-avatar-circle" title="Client avatar">{{ $clientInitials }}</span>
                                    <span style="font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        Client: {{ $project->client->name ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            <x-status-badge :status="$project->status" />
                        </div>

                        {{-- Dates & Timing --}}
                        <div class="project-dates-row">
                            <span class="date-item" title="Due Date">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                @if($project->due_date)
                                    Due {{ $project->due_date->format('M d, Y') }}
                                @else
                                    <span style="color: #94a3b8; font-style: italic;">No due date</span>
                                @endif
                            </span>
                        </div>

                        {{-- Milestone Progress Bar --}}
                        <div class="progress-box">
                            <div class="progress-box-header">
                                <span style="font-weight: 600; color: #334155; display: inline-flex; align-items: center; gap: 5px;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                                    Milestones
                                </span>
                                <span style="font-weight: 600; color: {{ $percent === 100 ? '#10b981' : '#4f46e5' }};">
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
                    <span style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; color: #999; background: #f5f5f5;">
                        Previous
                    </span>
                @else
                    <a href="{{ $projects->previousPageUrl() }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">
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
                        <a href="{{ $url }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($projects->hasMorePages())
                    <a href="{{ $projects->nextPageUrl() }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">
                        Next
                    </a>
                @else
                    <span style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; color: #999; background: #f5f5f5;">
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