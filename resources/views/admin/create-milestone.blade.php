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
            background: #f6f7fb;
            border-radius: 10px;
            padding: 12px 14px;
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

        <div class="milestones-header">
            <p class="dashboard-eyebrow">Admin</p>
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
                <button type="submit" class="btn btn-success" style="padding: 10px 18px;">
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

                            {{-- Comments Section --}}
                            <div class="milestone-comments-section">

                                <span style="font-size: 13px; font-weight: 700; color: #1e1b4b; text-transform: uppercase; letter-spacing: 0.03em;">Comments</span>

                                @forelse($milestone->comments as $comment)
                                    <div class="comment-bubble">
                                        <div style="font-size: 12px; font-weight: 700; color: var(--primary);">
                                            {{ $comment->user->name }}
                                            <span style="font-weight: normal; color: var(--muted);">
                                                &middot; {{ $comment->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <div style="font-size: 13.5px; margin-top: 4px; line-height: 1.5; color: #1f2937;">
                                            {{ $comment->body }}
                                        </div>
                                    </div>
                                @empty
                                    <p style="font-size: 13px; color: var(--muted); margin: 8px 0 0 0;">No comments yet.</p>
                                @endforelse

                                <form method="POST" action="{{ route('comments.store', $milestone) }}" style="margin-top: 14px;">
                                    @csrf
                                    <textarea name="body" rows="2" placeholder="Reply as admin..." required class="milestone-form-textarea" style="margin-bottom: 8px;"></textarea>
                                    <button type="submit" class="btn btn-small" style="padding: 8px 14px;">Post Comment</button>
                                </form>

                            </div>

                        </div>
                    @endforeach
                </div>
            @endif

        </x-card>

    </div>

</x-layouts.app>