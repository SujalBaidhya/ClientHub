<x-layouts.app title="Projects — Admin">

    <style>
        .projects-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
        }

        .projects-search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .projects-search-input {
            flex: 1;
            min-width: 200px;
            max-width: 400px;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
        }

        .project-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fafafa;
        }

        .project-meta {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .project-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pagination-container {
            margin-top: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .projects-header {
                flex-direction: column;
                align-items: stretch;
            }

            .projects-header .btn-success {
                text-align: center;
            }

            .projects-search-form {
                flex-direction: column;
            }

            .projects-search-input {
                max-width: 100%;
            }

            .project-row {
                flex-direction: column;
                align-items: stretch;
                gap: 14px;
            }

            .project-actions {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                width: 100%;
            }

            .project-actions .btn {
                text-align: center;
                justify-content: center;
                padding: 8px 10px;
            }

            .project-actions form {
                grid-column: span 2;
                display: flex;
            }

            .project-actions form button {
                width: 100%;
                text-align: center;
            }
        }
    </style>

    <div class="dashboard">

        <div class="projects-header">
            <div>
                <p class="dashboard-eyebrow">Admin</p>
                <h1 style="margin: 0 0 6px 0;">All Projects</h1>
                <p style="margin: 0; color: #6b7280; font-size: 14px;">Select a project to manage its milestones.</p>
            </div>
            <a href="{{ route('admin.projects.create') }}" class="btn btn-success">+ New Project</a>
        </div>

        <form method="GET" action="{{ route('admin.projects.index') }}" class="projects-search-form">
                        @if($view === 'archived')
                <input type="hidden" name="view" value="archived">
            @endif
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search by project or client name..."
                class="projects-search-input"
            >
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn" style="flex: 1;">Search</button>
                @if($search)
                    <a href="{{ route('admin.projects.index', $view === 'archived' ? ['view' => 'archived'] : []) }}" class="btn">Clear</a>
                @endif
            </div>
        </form>
            <div style="display: flex; gap: 6px; margin-bottom: 16px; flex-wrap: wrap;">
            <a href="{{ route('admin.projects.index') }}"
               style="padding: 8px 14px; border: 1px solid {{ $view === 'active' ? '#1e1b4b' : 'var(--border)' }}; border-radius: 999px; text-decoration: none; font-size: 13px; background: {{ $view === 'active' ? '#1e1b4b' : 'white' }}; color: {{ $view === 'active' ? 'white' : 'inherit' }};">
                Active ({{ $activeCount }})
            </a>
            <a href="{{ route('admin.projects.index', ['view' => 'archived']) }}"
               style="padding: 8px 14px; border: 1px solid {{ $view === 'archived' ? '#1e1b4b' : 'var(--border)' }}; border-radius: 999px; text-decoration: none; font-size: 13px; background: {{ $view === 'archived' ? '#1e1b4b' : 'white' }}; color: {{ $view === 'archived' ? 'white' : 'inherit' }};">
                Archived ({{ $archivedCount }})
            </a>
        </div>
        <x-card>

                       @if($projects->isEmpty())
                @if($search)
                    <x-empty-state
                        title="No matches found"
                        message="No projects or clients matched '{{ $search }}'. Try a different search."
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
            @else
                <div class="file-list">
                    @foreach($projects as $project)
                        <div class="project-row">
                            <div class="project-meta">
                                <strong style="font-size: 15px; color: #1e1b4b;">{{ $project->name }}</strong>
                                <span style="font-size: 13px; color: var(--muted);">Client: {{ $project->client->name ?? 'N/A' }}</span>
                            </div>

                            <div class="project-actions">
                                <a href="{{ route('admin.files.create', $project) }}" class="btn btn-small">
                                    Files
                                </a>
                                <a href="{{ route('admin.milestones.create', $project) }}" class="btn btn-small">
                                    Milestones
                                </a>
                                <a href="{{ route('admin.invoices.create', $project) }}" class="btn btn-small">
                                    Invoices
                                </a>
                                <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-small">
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

        </x-card>

    </div>

</x-layouts.app>