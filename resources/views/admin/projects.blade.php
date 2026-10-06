<x-layouts.app title="Projects — Admin">

    <div class="dashboard">

              <div class="dashboard-welcome">
            <div>
                <p class="dashboard-eyebrow">Admin</p>
                <h1>All Projects</h1>
                <p>Select a project to manage its milestones.</p>
            </div>
            <a href="{{ route('admin.projects.create') }}" class="btn btn-success">+ New Project</a>
        </div>

        <form method="GET" action="{{ route('admin.projects.index') }}" style="margin-bottom: 20px; display: flex; gap: 10px;">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search by project or client name..."
                style="flex: 1; max-width: 400px; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);"
            >
            <button type="submit" class="btn">Search</button>
            @if($search)
                <a href="{{ route('admin.projects.index') }}" class="btn">Clear</a>
            @endif
        </form>

        <x-card>

            @if($projects->isEmpty())
                <x-empty-state
                    title="No projects yet"
                    message="Create your first project to get started."
                />
            @else
                <div class="file-list">
                    @foreach($projects as $project)
                        <div class="file-item">
                            <div>
                                <strong>{{ $project->name }}</strong>
                                <span>Client: {{ $project->client->name ?? 'N/A' }}</span>
                            </div>
                         <a href="{{ route('admin.files.create', $project) }}" class="btn btn-small">
                            Manage Files
                        </a>
                        <a href="{{ route('admin.milestones.create', $project) }}" class="btn btn-small">
                            Manage Milestones
                        </a>
                        <a href="{{ route('admin.invoices.create', $project) }}" class="btn btn-small">
                            Manage Invoices
                        </a>
                        <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-small">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" onsubmit="return confirm('Delete this project? This will also delete all its milestones, files, and invoices. This cannot be undone.');" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-small" style="background: #dc2626; color: white; border: none;">
                                Delete
                            </button>
                        </form>
                        </div>
                    @endforeach
                </div>
                    <<div style="
                        margin-top: 24px;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: 6px;
                    ">
                        {{-- Previous --}}
                        @if($projects->onFirstPage())
                            <span style="
                                padding: 8px 12px;
                                border: 1px solid var(--border);
                                border-radius: 8px;
                                color: #999;
                                background: #f5f5f5;
                            ">
                                Previous
                            </span>
                        @else
                            <a
                                href="{{ $projects->previousPageUrl() }}"
                                style="
                                    padding: 8px 12px;
                                    border: 1px solid var(--border);
                                    border-radius: 8px;
                                    text-decoration: none;
                                    color: inherit;
                                    background: white;
                                "
                            >
                                Previous
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach($projects->getUrlRange(1, $projects->lastPage()) as $page => $url)
                            @if($page == $projects->currentPage())
                                <span style="
                                    padding: 8px 12px;
                                    border: 1px solid #2563eb;
                                    border-radius: 8px;
                                    background: #2563eb;
                                    color: white;
                                    font-weight: 600;
                                ">
                                    {{ $page }}
                                </span>
                            @else
                                <a
                                    href="{{ $url }}"
                                    style="
                                        padding: 8px 12px;
                                        border: 1px solid var(--border);
                                        border-radius: 8px;
                                        text-decoration: none;
                                        color: inherit;
                                        background: white;
                                    "
                                >
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Next --}}
                        @if($projects->hasMorePages())
                            <a
                                href="{{ $projects->nextPageUrl() }}"
                                style="
                                    padding: 8px 12px;
                                    border: 1px solid var(--border);
                                    border-radius: 8px;
                                    text-decoration: none;
                                    color: inherit;
                                    background: white;
                                "
                            >
                                Next
                            </a>
                        @else
                            <span style="
                                padding: 8px 12px;
                                border: 1px solid var(--border);
                                border-radius: 8px;
                                color: #999;
                                background: #f5f5f5;
                            ">
                                Next
                            </span>
                        @endif
                    </div>
            @endif

        </x-card>

    </div>

</x-layouts.app>