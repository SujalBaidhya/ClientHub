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
                        <div class="milestone-list">
                            @foreach($milestones as $milestone)
                                <details class="milestone-card" style="border: 1px solid var(--border); border-radius: 10px; margin-bottom: 12px; background: #ffffff; padding: 14px;">
                                    <summary style="list-style: none; cursor: pointer;">
                                        <div class="milestone-summary-row">
                                            <div style="min-width: 0; flex: 1;">
                                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                    <strong style="font-size: 14.5px; color: #1e1b4b;">{{ $milestone->title }}</strong>
                                                    <x-status-badge :status="$milestone->status" />
                                                </div>
                                                @if($milestone->description)
                                                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        {{ $milestone->description }}
                                                    </p>
                                                @endif
                                            </div>

                                            <span style="font-size: 12px; color: #94a3b8; margin-left: 8px;">▼</span>
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
                                        <div style="margin-top: 4px; border-top: 1px solid var(--border); padding-top: 12px;">
                                            <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1e1b4b; letter-spacing: 0.03em;">Comments</span>

                                            @forelse($milestone->comments as $comment)
                                                <div style="margin-top: 8px; background: #f6f7fb; border-radius: 8px; padding: 10px 12px;">
                                                    <div style="font-size: 12px; font-weight: 700; color: var(--primary);">
                                                        {{ $comment->user->name }}
                                                        <span style="font-weight: normal; color: var(--muted);">
                                                            &middot; {{ $comment->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                    <div style="font-size: 13px; margin-top: 4px; color: #374151;">
                                                        {{ $comment->body }}
                                                    </div>
                                                </div>
                                            @empty
                                                <p style="font-size: 13px; color: var(--muted); margin: 6px 0 0;">No comments yet.</p>
                                            @endforelse

                                            <form method="POST" action="{{ route('comments.store', $milestone) }}" style="margin-top: 12px;">
                                                @csrf
                                                <textarea name="body" rows="2" placeholder="Write a comment..." required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border); font-size: 13px; box-sizing: border-box;"></textarea>
                                                <button type="submit" class="btn btn-small" style="margin-top: 8px;">Post Comment</button>
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
                        <form method="POST" action="{{ route('files.store', $project) }}" enctype="multipart/form-data" style="margin-bottom: 18px; display: flex; gap: 10px; align-items: center;">
                            @csrf
                            <input type="file" name="file" required style="flex: 1; padding: 8px; border-radius: 8px; border: 1px solid var(--border); background: white;">
                            <button type="submit" class="btn btn-success">Upload</button>
                        </form>
                    @endif

                    @if($files->isEmpty())
                        <x-empty-state
                            title="No files available"
                            message="Project files will appear here."
                        />
                    @else
                        <div class="file-list">
                            @foreach($files as $file)
                                <div class="file-item">
                                    <a href="{{ route('files.view', $file) }}" title="View file" style="text-decoration: none; color: inherit; flex: 1; min-width: 0;">
                                    <div class="file-info">
                                        <div class="file-icon">
                                            {{ strtoupper(pathinfo($file->original_name, PATHINFO_EXTENSION)) ?: 'FILE' }}
                                        </div>
                                        <div>
                                            <strong>{{ $file->original_name }}</strong>
                                            <span>
                                                Uploaded {{ $file->created_at->format('M d, Y') }}
                                                @if($file->uploaded_by === auth()->id())
                                                    · by you
                                                @elseif($file->uploadedBy)
                                                    · by {{ $file->uploadedBy->name }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    </a>
                                    <a href="{{ route('files.download', $file) }}" class="btn btn-small">
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