<x-layouts.app title="Clients — Admin">

    <div class="dashboard">

        <div class="dashboard-welcome">
            <div>
                <p class="dashboard-eyebrow">Admin</p>
                <h1>Client Accounts</h1>
                <p>Invite clients to set up their own login accounts.</p>
            </div>
        </div>

        @if(session('success'))
            <x-card>
                <p style="color: #16a34a; font-weight: bold;">{{ session('success') }}</p>
            </x-card>
        @endif

        @if($errors->any())
            <x-card>
                <p style="color: #dc2626; font-weight: bold;">{{ $errors->first() }}</p>
            </x-card>
        @endif

        <x-card title="Invite Client">

            <form method="POST" action="{{ route('admin.clients.store') }}">
                @csrf

                <div style="margin-bottom: 16px;">
                    <label style="display:block; margin-bottom:4px; font-weight:bold;">Full Name</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Sujal Shrestha" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border);">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display:block; margin-bottom:4px; font-weight:bold;">Email Address</label>
                    <input type="email" name="email" required value="{{ old('email') }}" placeholder="client@example.com" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border);">
                    <small style="color: #64748b; margin-top: 4px; display: block;">An invitation link will be sent to this email so they can set their password.</small>
                </div>

                <button type="submit" class="btn btn-success">Send Invitation</button>

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
                        <div class="file-item">
                            <div>
                                <strong>{{ $client->name }}</strong>
                                <span>{{ $client->email }}</span>
                                @if($client->invitation_token)
                                    <span style="display: inline-block; font-size: 11px; padding: 2px 8px; border-radius: 9999px; background: #fef3c7; color: #b45309; margin-left: 8px;">Pending Setup</span>
                                @else
                                    <span style="display: inline-block; font-size: 11px; padding: 2px 8px; border-radius: 9999px; background: #dcfce7; color: #15803d; margin-left: 8px;">Active</span>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" onsubmit="return confirm('Delete {{ $client->name }}? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-small" style="background: #dc2626; color: white; border: none;">
                                    Delete
                                </button>
                            </form>
                            @if($client->invitation_token)
                            <form method="POST" action="{{ route('admin.clients.resend-invite', $client) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-small" style="background: #eef2ff; color: var(--primary); border-color: #c7d2fe;">
                                    Resend Invite
                                </button>
                            </form>
                        @endif
                        </div>
                    @endforeach
                </div>
                <div style="
                    margin-top: 24px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    gap: 6px;
                ">
                    {{-- Previous --}}
                    @if($clients->onFirstPage())
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
                            href="{{ $clients->previousPageUrl() }}"
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
                    @foreach($clients->getUrlRange(1, $clients->lastPage()) as $page => $url)
                        @if($page == $clients->currentPage())
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
                    @if($clients->hasMorePages())
                        <a
                            href="{{ $clients->nextPageUrl() }}"
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