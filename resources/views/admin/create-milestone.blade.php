<x-layouts.app title="Milestones — Admin">

    <style>
        .milestones-header {
            margin-bottom: 24px;
        }

        .milestone-form-group {
            margin-bottom: 16px;
        }

        .milestone-form-input,
        .milestone-form-select,
        .milestone-form-textarea {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-size: 14px;
            font-family: inherit;
        }

        .milestone-manage-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #ffffff;
            padding: 20px;
            margin-bottom: 16px;
        }

        .milestone-manage-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .milestone-status-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 240px;
        }

        .milestone-comments-section {
            margin-top: 18px;
            border-top: 1px solid var(--border);
            padding-top: 16px;
        }

        .comment-bubble {
            margin-top: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 14px;
            transition: border-color 0.15s ease;
        }

        .comment-bubble:hover {
            border-color: #cbd5e1;
        }

        @media (max-width: 768px) {
            .milestones-header h1 {
                font-size: 26px;
            }

            .milestone-manage-card {
                padding: 16px;
            }

            .milestone-manage-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .milestone-status-group {
                width: 100%;
                min-width: 0;
            }

            .milestone-status-group select {
                flex: 1;
            }

            .milestone-manage-card .delete-form {
                width: 100%;
            }

            .milestone-manage-card .delete-form button {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
        }
    </style>

    <div class="dashboard">

        <x-breadcrumbs :links="[
            ['label' => 'Projects', 'url' => route('admin.projects.index'), 'icon' => true],
            ['label' => $project->name, 'url' => route('admin.projects.edit', $project)],
            ['label' => 'Milestones'],
        ]" />

        <div class="milestones-header">
            <p class="dashboard-eyebrow">Admin Workspace</p>
            <h1 style="margin: 0 0 6px 0;">{{ $project->name }}</h1>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Add and manage milestones for this project.</p>
        </div>

        <x-card title="Add Milestone">

            <form method="POST" action="{{ route('admin.milestones.store', $project) }}">
                @csrf

                <div class="milestone-form-group">
                    <label style="display:block; margin-bottom: 6px; font-weight: 600; font-size: 14px;">Title</label>
                    <input type="text" name="title" required placeholder="e.g. Design Wireframes" class="milestone-form-input">
                </div>

                <div class="milestone-form-group">
                    <label style="display:block; margin-bottom: 6px; font-weight: 600; font-size: 14px;">Description</label>
                    <textarea name="description" rows="3" placeholder="Outline deliverables for this milestone..." class="milestone-form-textarea"></textarea>
                </div>

                <div class="milestone-form-group">
                    <label style="display:block; margin-bottom: 6px; font-weight: 600; font-size: 14px;">Status</label>
                    <select name="status" required class="milestone-form-select">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="milestone-form-group">
                    <label style="display:block; margin-bottom: 6px; font-weight: 600; font-size: 14px;">Due Date <span style="font-weight: normal; color: var(--muted);">(optional)</span></label>
                    <input type="date" name="due_date" class="milestone-form-input">
                </div>
                <button type="submit" class="btn btn-success" style="padding: 10px 18px; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Add Milestone
                </button>

            </form>

        </x-card>

        <x-card title="Existing Milestones">

            @if($milestones->isEmpty())
                <x-empty-state
                    title="No milestones yet"
                    message="Add one above to get started."
                />
            @else
                <div class="milestone-list">
                    @foreach($milestones as $milestone)
                        <div class="milestone-manage-card">

                            <form method="POST" action="{{ route('admin.milestones.update', [$project, $milestone]) }}">
                                @csrf
                                @method('PUT')

                                <div style="margin-bottom: 12px;">
                                    <input type="text" name="title" required value="{{ $milestone->title }}" class="milestone-form-input" style="font-weight: 600;">
                                </div>

                                <div style="margin-bottom: 12px;">
                                    <textarea name="description" rows="2" class="milestone-form-textarea">{{ $milestone->description }}</textarea>
                                </div>
                                <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                    <label style="font-size: 13px; font-weight: 600;">Due date</label>
                                    <input type="date" name="due_date" value="{{ $milestone->due_date?->format('Y-m-d') }}" class="milestone-form-input" style="width: auto;">
                                    @if($milestone->isOverdue())
                                        <span class="badge badge-danger">Overdue by {{ abs($milestone->daysUntilDue()) }} day(s)</span>
                                    @elseif($milestone->status !== 'completed' && $milestone->daysUntilDue() !== null && $milestone->daysUntilDue() <= 3)
                                        <span class="badge badge-warning">{{ $milestone->daysUntilDue() === 0 ? 'Due today' : 'Due in ' . $milestone->daysUntilDue() . ' day(s)' }}</span>
                                    @endif
                                </div>
                                <div class="milestone-manage-actions">
                                    <div class="milestone-status-group">
                                        <select name="status" required class="milestone-form-select" style="padding: 9px 12px;">
                                            <option value="pending" @selected($milestone->status === 'pending')>Pending</option>
                                            <option value="in_progress" @selected($milestone->status === 'in_progress')>In Progress</option>
                                            <option value="completed" @selected($milestone->status === 'completed')>Completed</option>
                                        </select>

                                        <button type="submit" class="btn btn-small" style="padding: 9px 16px;">
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('admin.milestones.destroy', [$project, $milestone]) }}" onsubmit="return confirm('Delete this milestone?');" class="delete-form" style="margin-top: 10px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-small" style="background: #dc2626; color: white; border: none; padding: 9px 16px;">
                                    Delete Milestone
                                </button>
                            </form>

                            {{-- Revision Notes Callout --}}
                            @if($milestone->client_notes && $milestone->status !== 'completed')
                                <div style="margin-top: 14px; background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 12px 14px;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                        <strong style="font-size: 13px; color: #92400e; text-transform: uppercase; letter-spacing: 0.04em;">Client Revision Notes</strong>
                                    </div>
                                    <div style="font-size: 13.5px; color: #78350f; line-height: 1.5; white-space: pre-wrap;">{{ $milestone->client_notes }}</div>
                                </div>
                            @endif

                            {{-- Modern Threaded Comments Section --}}
                            <div class="milestone-comments-section">

                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                    <span style="font-size: 13px; font-weight: 700; color: #1e1b4b; text-transform: uppercase; letter-spacing: 0.03em; display: inline-flex; align-items: center; gap: 6px;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                        Comments
                                    </span>
                                    <span style="font-size: 12px; color: var(--muted); font-weight: 500;">
                                        {{ $milestone->comments->count() }} {{ \Illuminate\Support\Str::plural('reply', $milestone->comments->count()) }}
                                    </span>
                                </div>

                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    @forelse($milestone->comments as $comment)
                                        @php
                                            $cInitials = collect(explode(' ', $comment->user->name ?? 'U'))
                                                ->filter()
                                                ->map(fn($p) => strtoupper(mb_substr($p, 0, 1)))
                                                ->take(2)
                                                ->join('') ?: 'U';
                                            $cIsAdmin = ($comment->user->role ?? null) === 'admin';
                                        @endphp
                                        <div class="comment-bubble">
                                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 6px;">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <span style="width: 26px; height: 26px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; background: {{ $cIsAdmin ? '#4f46e5' : '#0d9488' }}; color: white; flex-shrink: 0;">
                                                        {{ $cInitials }}
                                                    </span>
                                                    <strong style="font-size: 13px; color: #1e1b4b;">{{ $comment->user->name }}</strong>
                                                    <span style="font-size: 11px; font-weight: 600; padding: 1px 7px; border-radius: 999px; background: {{ $cIsAdmin ? '#e0e7ff' : '#ccfbf1' }}; color: {{ $cIsAdmin ? '#3730a3' : '#115e59' }};">
                                                        {{ $cIsAdmin ? 'Admin' : 'Client' }}
                                                    </span>
                                                </div>
                                                <span style="font-size: 12px; color: var(--muted);" title="{{ $comment->created_at->format('M d, Y h:i A') }}">
                                                    &middot; {{ $comment->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <div style="font-size: 13.5px; line-height: 1.5; color: #334155; padding-left: 34px; white-space: pre-wrap; word-break: break-word;">
                                                {{ $comment->body }}
                                            </div>
                                        </div>
                                    @empty
                                        <p style="font-size: 13px; color: var(--muted); margin: 4px 0 0 0;">No comments yet.</p>
                                    @endforelse
                                </div>

                                <form method="POST" action="{{ route('comments.store', $milestone) }}" style="margin-top: 14px;">
                                    @csrf
                                    <textarea name="body" rows="2" placeholder="Reply as admin..." required class="milestone-form-textarea" style="margin-bottom: 8px; font-size: 13.5px;"></textarea>
                                    <button type="submit" class="btn btn-small" style="padding: 8px 14px; display: inline-flex; align-items: center; gap: 6px;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                        Post Comment
                                    </button>
                                </form>

                            </div>

                        </div>
                    @endforeach
                </div>
            @endif

        </x-card>

    </div>

</x-layouts.app>