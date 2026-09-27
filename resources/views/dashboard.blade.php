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

    <div class="dashboard">
        <div class="dashboard-welcome">
            <div>
                <p class="dashboard-eyebrow">Client Portal</p>
                <h1>Welcome back, {{ $userName }}</h1>
                <p>Here's an overview of your project, milestones, files, and invoices.</p>
            </div>

            <div class="dashboard-date">
                {{ now()->format('M d, Y') }}
            </div>
        </div>

        @if(session('success'))
            <x-card>
                <p style="color: #16a34a; font-weight: bold;">{{ session('success') }}</p>
            </x-card>
        @endif

        @if(session('error'))
            <x-card>
                <p style="color: #dc2626; font-weight: bold;">{{ session('error') }}</p>
            </x-card>
        @endif

        @if(!$project)
            <x-card>
                <x-empty-state
                    title="No project yet"
                    message="You don't have a project assigned yet. Your project manager will set one up soon."
                />
            </x-card>
        @else

            @if($allProjects->count() > 1)
                <div style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                    @foreach($allProjects as $p)
                        <a
                            href="{{ route('dashboard.project', $p) }}"
                            class="btn btn-small"
                            style="{{ $p->id === $project->id ? 'background: var(--primary); color: white; border-color: var(--primary);' : '' }}"
                        >
                            {{ $p->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Current Project --}}
            <x-card class="current-project-card">

                {{-- Top Section --}}
                <div class="current-project-header">
                    <div class="project-title-section">
                        <span class="project-label">Current Project</span>
                        <h1 class="current-project-title">{{ $project->name }}</h1>
                        @if($project->description)
                            <p class="current-project-description">{{ $project->description }}</p>
                        @endif
                    </div>

                    <div class="project-status">
                        <x-status-badge :status="$project->status" />
                    </div>
                </div>

                {{-- Project Information --}}
                <div class="project-info-grid">
                    <div class="project-info-item">
                        <span class="info-label">Client</span>
                        <strong>{{ $project->client->name ?? 'N/A' }}</strong>
                    </div>

                    <div class="project-info-item">
                        <span class="info-label">Start Date</span>
                        <strong>{{ $project->start_date?->format('M d, Y') ?? 'Not set' }}</strong>
                    </div>

                    <div class="project-info-item">
                        <span class="info-label">Due Date</span>
                        <strong>{{ $project->due_date?->format('M d, Y') ?? 'Not set' }}</strong>

                        @if($daysRemaining !== null)
                            @if($daysRemaining > 0)
                                <div style="margin-top: 4px;"><x-status-badge status="pending" :label="$daysRemaining . ' day' . ($daysRemaining === 1 ? '' : 's') . ' left'" /></div>
                            @elseif($daysRemaining === 0)
                                <div style="margin-top: 4px;"><x-status-badge status="in_progress" label="Due today" /></div>
                            @else
                                <div style="margin-top: 4px;"><x-status-badge status="overdue" :label="'Overdue by ' . abs($daysRemaining) . ' day' . (abs($daysRemaining) === 1 ? '' : 's')" /></div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Progress --}}
                <div class="project-progress-section">
                    <div class="progress-header">
                        <span>Project Progress</span>
                        <strong>{{ $progress }}%</strong>
                    </div>

                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: {{ $progress }}%"></div>
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
                                <details class="milestone-card">
                                    <summary class="milestone-card-header">
                                        <div class="milestone-main">
                                            <div class="milestone-title">
                                                <strong>{{ $milestone->title }}</strong>
                                                <x-status-badge :status="$milestone->status" />
                                            </div>
                                            @if($milestone->description)
                                                <p class="milestone-summary">{{ $milestone->description }}</p>
                                            @endif
                                        </div>

                                        <div class="milestone-actions">
                                            @if($milestone->status === 'completed')
                                                <span class="milestone-completed">✓ Completed</span>
                                            @endif
                                            <span class="milestone-chevron">▼</span>
                                        </div>
                                    </summary>

                                    <div class="milestone-card-details" style="flex-direction: column; align-items: stretch; gap: 14px;">
                                        
                                        {{-- Date Row --}}
                                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                                            <div class="milestone-detail">
                                                <span class="detail-label">Target Completion</span>
                                                <strong>{{ $milestone->due_date ? $milestone->due_date->format('M d, Y') : 'No target date' }}</strong>
                                            </div>

                                            @if($milestone->completed_at ?? $milestone->approved_at)
                                                <div class="milestone-detail">
                                                    <span class="detail-label">Completed On</span>
                                                    <strong style="color: var(--success-text);">
                                                        {{ ($milestone->completed_at ?? $milestone->approved_at)->format('M d, Y') }}
                                                    </strong>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Full Description --}}
                                        @if($milestone->description)
                                            <div style="font-size: 14px; color: #4b5563; line-height: 1.5; padding-top: 4px;">
                                                {{ $milestone->description }}
                                            </div>
                                        @endif

                                        {{-- Active Revision Notes --}}
                                        @if($milestone->client_notes && $milestone->status !== 'completed')
                                            <div style="padding: 12px 14px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; font-size: 13px; color: #92400e;">
                                                <strong style="display: block; margin-bottom: 4px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Your Revision Notes</strong>
                                                {{ $milestone->client_notes }}
                                            </div>
                                        @endif

                                        {{-- Client Action Buttons (Approve / Request Changes) --}}
                                        @if($milestone->status !== 'completed')
                                            <div style="display: flex; gap: 10px; align-items: center; padding-top: 10px; border-top: 1px solid var(--border); flex-wrap: wrap;">
                                                
                                                {{-- Approve Button --}}
                                                <form method="POST" action="{{ Route::has('client.milestones.approve') ? route('client.milestones.approve', $milestone) : route('milestones.complete', $milestone) }}" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-small btn-success" onclick="return confirm('Approve this milestone?')">
                                                        ✓ Approve Milestone
                                                    </button>
                                                </form>

                                                {{-- Request Changes Dropdown Form --}}
                                                @if(Route::has('client.milestones.request-revision'))
                                                    <details style="position: relative;">
                                                        <summary class="btn btn-small" style="list-style: none; cursor: pointer; border-color: #cbd5e1;">
                                                            Request Changes
                                                        </summary>
                                                        <div style="margin-top: 10px; padding: 14px; background: #ffffff; border: 1px solid var(--border); border-radius: 10px; width: min(100%, 340px); box-shadow: 0 4px 14px rgba(0,0,0,0.06);">
                                                            <form method="POST" action="{{ route('client.milestones.request-revision', $milestone) }}" style="margin: 0;">
                                                                @csrf
                                                                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">
                                                                    What needs revision?
                                                                </label>
                                                                <textarea name="client_notes" rows="3" style="width: 100%; padding: 8px 10px; font-size: 13px; margin-bottom: 10px;" placeholder="e.g. Please update the button styles and spacing..." required>{{ old('client_notes', $milestone->client_notes) }}</textarea>
                                                                <button type="submit" class="btn btn-small" style="background: var(--warning-bg); color: var(--warning-text); border: 1px solid #fde68a; width: 100%;">
                                                                    Submit Feedback
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </details>
                                                @endif

                                            </div>
                                        @endif

                                        {{-- Comments Section --}}
                                        <div style="margin-top: 10px; border-top: 1px solid var(--border); padding-top: 14px;">
                                            <span class="detail-label">Comments</span>

                                            @forelse($milestone->comments as $comment)
                                                <div style="margin-top: 10px; background: #f6f7fb; border-radius: 10px; padding: 10px 14px;">
                                                    <div style="font-size: 12px; font-weight: bold; color: var(--primary);">
                                                        {{ $comment->user->name }}
                                                        <span style="font-weight: normal; color: var(--muted);">
                                                            &middot; {{ $comment->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                    <div style="font-size: 13px; margin-top: 4px;">
                                                        {{ $comment->body }}
                                                    </div>
                                                </div>
                                            @empty
                                                <p style="font-size: 13px; color: var(--muted); margin-top: 6px;">No comments yet.</p>
                                            @endforelse

                                            <form method="POST" action="{{ route('comments.store', $milestone) }}" style="margin-top: 12px;">
                                                @csrf
                                                <textarea name="body" rows="2" placeholder="Write a comment..." required style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid var(--border);"></textarea>
                                                <button type="submit" class="btn btn-small" style="margin-top: 6px;">Post Comment</button>
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
                    @if($files->isEmpty())
                        <x-empty-state
                            title="No files available"
                            message="Project files will appear here."
                        />
                    @else
                        <div class="file-list">
                            @foreach($files as $file)
                                <div class="file-item">
                                    <div class="file-info">
                                        <div class="file-icon">
                                            {{ strtoupper(pathinfo($file->original_name, PATHINFO_EXTENSION)) ?: 'FILE' }}
                                        </div>
                                        <div>
                                            <strong>{{ $file->original_name }}</strong>
                                            <span>
                                                Uploaded {{ $file->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>
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
                    <div class="client-invoice-list">
                        @foreach($invoices as $invoice)
                            <div class="client-invoice-card">
                                <div class="invoice-card-header">
                                    <div>
                                        <span class="invoice-number">{{ $invoice->invoice_number }}</span>
                                        <p>{{ $project->name }}</p>
                                    </div>
                                    <x-status-badge :status="$invoice->status" />
                                </div>

                                <div class="invoice-card-details">
                                    <div>
                                        <span class="invoice-label">Issued</span>
                                        <strong>{{ $invoice->issued_at?->format('M d, Y') ?? '—' }}</strong>
                                    </div>

                                    <div>
                                        <span class="invoice-label">Amount</span>
                                        <strong class="invoice-amount">NPR {{ number_format($invoice->amount, 2) }}</strong>
                                    </div>
                                </div>

                                <div class="invoice-card-actions" style="display: flex; gap: 10px;">
                                    <a href="{{ route('invoices.download', $invoice) }}" class="download-invoice-button">
                                        Download Invoice
                                    </a>

                                    @if($invoice->status !== 'paid')
                                        <a href="{{ route('payment.pay', $invoice) }}" class="btn btn-success">
                                            Pay Now
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