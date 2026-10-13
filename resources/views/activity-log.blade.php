<x-layouts.app title="Notifications">
 
    <style>
        .activity-page-header {
            margin-bottom: 24px;
        }
 
        .activity-card-inner {
            padding: 4px 0;
        }
 
        .activity-timeline {
            position: relative;
            padding-left: 8px;
        }
 
        .activity-item {
            display: flex;
            gap: 14px;
            padding-bottom: 22px;
            position: relative;
        }
 
        .activity-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 18px;
            bottom: -2px;
            width: 1px;
            background: var(--border, #e4e4f0);
        }
 
        .activity-dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: #1e1b4b;
            margin-top: 5px;
            flex-shrink: 0;
        }
 
        .activity-item.is-unread .activity-dot {
            background: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
        }
 
        .activity-content {
            min-width: 0;
            flex: 1;
        }
 
        .activity-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }
 
        .activity-link:hover .activity-description {
            text-decoration: underline;
        }
 
        .activity-description {
            margin: 0 0 6px;
            font-size: 14px;
            color: #1e1b4b;
            font-weight: 500;
            line-height: 1.5;
            word-break: break-word;
        }
 
        .activity-item.is-unread .activity-description {
            font-weight: 700;
        }
 
        .activity-project {
            display: inline-block;
            font-size: 11.5px;
            color: #4b5563;
            background: #f3f4f6;
            border-radius: 999px;
            padding: 2px 8px;
            margin-bottom: 6px;
        }
 
        .activity-new-badge {
            display: inline-block;
            font-size: 10.5px;
            font-weight: 600;
            color: #fff;
            background: #2563eb;
            border-radius: 999px;
            padding: 1px 7px;
            margin-left: 6px;
            vertical-align: middle;
        }
 
        .activity-time {
            display: block;
            font-size: 12px;
            color: var(--muted, #6b7280);
            line-height: 1.4;
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
            .activity-page-header h1 {
                font-size: 26px;
            }
 
            .activity-timeline {
                padding-left: 2px;
            }
 
            .activity-item {
                gap: 12px;
                padding-bottom: 18px;
            }
 
            .activity-description {
                font-size: 13.5px;
            }
 
            .activity-time {
                font-size: 11.5px;
            }
        }
    </style>
 
    @php
        $unreadIds = $unreadIds ?? [];
    @endphp
 
    <div class="dashboard">
 
        <div class="activity-page-header">
            <p class="dashboard-eyebrow">Client</p>
            <h1 style="margin: 0 0 6px 0;">Notifications</h1>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Updates on your projects, milestones, and invoices.</p>
        </div>
 
        <x-card>
            @if($logs->isEmpty())
                <x-empty-state
                    title="No notifications yet"
                    message="Updates about your projects, such as milestone changes and new invoices, will show up here."
                />
            @else
                <div class="activity-card-inner">
                    <div class="activity-timeline">
                        @foreach($logs as $log)
                            @php $isUnread = in_array($log->id, $unreadIds); @endphp
 
                            <div class="activity-item {{ $isUnread ? 'is-unread' : '' }}">
                                <div class="activity-dot"></div>
                                <div class="activity-content">
                                    @if($log->project)
                                        <span class="activity-project">{{ $log->project->name }}</span>
                                    @endif

                                    <p class="activity-description">
                                        {{ $log->description }}
                                        @if($isUnread)
                                            <span class="activity-new-badge">New</span>
                                        @endif
                                    </p>

                                    <span class="activity-time">
                                        {{ $log->created_at->format('M d, Y \a\t g:i A') }}
                                        &middot; {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($logs->hasPages())
                    <div class="pagination-container">
        
                        @if($logs->onFirstPage())
                            <span style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; color: #999; background: #f5f5f5;">
                                Previous
                            </span>
                        @else
                            <a href="{{ $logs->previousPageUrl() }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">
                                Previous
                            </a>
                        @endif

                        @foreach($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                            @if($page == $logs->currentPage())
                                <span style="padding: 8px 12px; border: 1px solid #2563eb; border-radius: 8px; background: #2563eb; color: white; font-weight: 600;">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if($logs->hasMorePages())
                            <a href="{{ $logs->nextPageUrl() }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">
                                Next
                            </a>
                        @else
                            <span style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; color: #999; background: #f5f5f5;">
                                Next
                            </span>
                        @endif
                    </div>
                @endif
            @endif
        </x-card>
 
    </div>
 
</x-layouts.app>