<x-layouts.app title="Clients — Admin">

    <style>
        .clients-header {
            margin-bottom: 24px;
        }

        .client-form-group {
            margin-bottom: 16px;
        }

        .client-form-input {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-size: 14px;
        }

        .client-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fafafa;
        }

        .client-info {
            min-width: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .client-name-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .client-email {
            font-size: 13px;
            color: #4b5563;
            word-break: break-all;
        }

        .client-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
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
    
        [data-theme="dark"] .client-row {
            background: #162032;
            border-color: #223048;
        }

        [data-theme="dark"] .client-row:hover {
            border-color: #3b4d6e;
        }

        [data-theme="dark"] .client-email {
            color: #94a3b8;
        }

        [data-theme="dark"] .client-form-input {
            background: #162032;
            color: var(--text);
            border-color: #243048;
        }

        [data-theme="dark"] .client-form-input::placeholder {
            color: #64748b;
        }

        [data-theme="dark"] .client-form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        [data-theme="dark"] .clients-header p:not(.dashboard-eyebrow) {
            color: var(--muted) !important;
        }

        [data-theme="dark"] .client-name-badge strong {
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .client-name-badge span[style*="fef3c7"] {
            background: #422f12 !important;
            color: #fbbf24 !important;
        }

        [data-theme="dark"] .client-name-badge span[style*="dcfce7"] {
            background: #123b30 !important;
            color: #6ee7b7 !important;
        }

        [data-theme="dark"] .client-actions .btn[style*="eef2ff"] {
            background: #242b4a !important;
            color: #a5b4fc !important;
            border-color: #373f68 !important;
        }

        [data-theme="dark"] .pagination-container a,
        [data-theme="dark"] .pagination-container span {
            background: #162032 !important;
            border-color: #243048 !important;
            color: #cbd5e1 !important;
        }

        [data-theme="dark"] .pagination-container span[style*="2563eb"] {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #ffffff !important;
        }
        @media (max-width: 768px) {
            .client-row {
                flex-direction: column;
                align-items: stretch;
                gap: 14px;
            }

            .client-actions {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                width: 100%;
            }

            .client-actions form {
                width: 100%;
                display: flex;
            }

            .client-actions .btn {
                width: 100%;
                text-align: center;
                justify-content: center;
                padding: 9px 12px;
            }

            /* If only delete button is present, make it full-width */
            .client-actions form:only-child {
                grid-column: span 2;
            }
        }
    </style>

    <div class="dashboard">

        <div class="clients-header">
            <p class="dashboard-eyebrow">Admin</p>
            <h1 style="margin: 0 0 6px 0;">Client Accounts</h1>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Invite clients to set up their own login accounts.</p>
        </div>

        <x-card title="Invite Client">

            <form method="POST" action="{{ route('admin.clients.store') }}">
                @csrf

                <div class="client-form-group">
                    <label style="display:block; margin-bottom: 6px; font-weight: 600; font-size: 14px;">Full Name</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Sujal Shrestha" class="client-form-input">
                </div>

                <div class="client-form-group">
                    <label style="display:block; margin-bottom: 6px; font-weight: 600; font-size: 14px;">Email Address</label>
                    <input type="email" name="email" required value="{{ old('email') }}" placeholder="client@example.com" class="client-form-input">
                    <small style="color: #64748b; margin-top: 6px; display: block; font-size: 12px; line-height: 1.4;">
                        An invitation link will be sent to this email so they can set their password.
                    </small>
                </div>

                <button type="submit" class="btn btn-success" style="padding: 10px 18px; width: auto;">
                    Send Invitation
                </button>

            </form>

        </x-card>

        <x-card title="Existing Clients">

            @if($clients->isEmpty())
                <x-empty-state
                    title="No clients yet"
                    message="Add one above to get started."
                />
            @else
                <div class="file-list">
                    @foreach($clients as $client)
                        <div class="client-row">
                            <div class="client-info">
                                <div class="client-name-badge">
                                    <strong style="font-size: 15px; color: #1e1b4b;">{{ $client->name }}</strong>
                                    @if($client->invitation_token)
                                        <span style="font-size: 11px; padding: 2px 8px; border-radius: 9999px; background: #fef3c7; color: #b45309; font-weight: 600;">
                                            Pending Setup
                                        </span>
                                    @else
                                        <span style="font-size: 11px; padding: 2px 8px; border-radius: 9999px; background: #dcfce7; color: #15803d; font-weight: 600;">
                                            Active
                                        </span>
                                    @endif
                                </div>

                                <span class="client-email">{{ $client->email }}</span>

                                <div style="margin-top: 4px; font-size: 13px; color: var(--muted);">
                                    @if($client->projects_count > 0)
                                        {{ $client->projects_count }} {{ Str::plural('project', $client->projects_count) }} assigned
                                    @else
                                        No projects assigned yet
                                    @endif
                                </div>
                            </div>

                            <div class="client-actions">
                                @if($client->invitation_token)
                                    <form method="POST" action="{{ route('admin.clients.resend-invite', $client) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-small" style="background: #eef2ff; color: var(--primary); border-color: #c7d2fe;">
                                            Resend Invite
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" onsubmit="return confirm('Delete {{ $client->name }}? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-small" style="background: #dc2626; color: white; border: none;">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="pagination-container">
                    {{-- Previous --}}
                    @if($clients->onFirstPage())
                        <span style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; color: #999; background: #f5f5f5;">
                            Previous
                        </span>
                    @else
                        <a href="{{ $clients->previousPageUrl() }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">
                            Previous
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach($clients->getUrlRange(1, $clients->lastPage()) as $page => $url)
                        @if($page == $clients->currentPage())
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
                    @if($clients->hasMorePages())
                        <a href="{{ $clients->nextPageUrl() }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">
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