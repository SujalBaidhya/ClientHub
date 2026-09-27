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
                        </div>
                    @endforeach
                </div>
            @endif

        </x-card>

    </div>

</x-layouts.app>