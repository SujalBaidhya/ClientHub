<x-layouts.app title="Search Results">
    <div class="dashboard">
        <div class="dashboard-welcome">
            <div>
                <p class="dashboard-eyebrow">Admin</p>
                <h1>Search Results</h1>
                <p>Showing results for "{{ $q }}"</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.search') }}" style="margin-bottom: 28px; display: flex; gap: 10px; max-width: 500px;">
            <input type="text" name="q" value="{{ $q }}" placeholder="Search projects, clients, invoices..."
                   style="flex: 1; padding: 10px 14px;" autofocus>
            <button type="submit" class="btn btn-success">Search</button>
        </form>

        @if(strlen($q) < 2)
            <div class="empty-state">
                <div class="empty-state-icon">&#128269;</div>
                <div class="empty-state-title">Type to search</div>
                <div class="empty-state-message">Enter at least 2 characters to search across projects, clients, and invoices.</div>
            </div>
        @elseif($projects->isEmpty() && $invoices->isEmpty() && $clients->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">&#128269;</div>
                <div class="empty-state-title">No results found</div>
                <div class="empty-state-message">Nothing matched "{{ $q }}". Try a different search term.</div>
            </div>
        @else
            @if($projects->isNotEmpty())
                <div class="card" style="padding: 0; overflow: hidden;">
                    <h2 style="padding: 22px 24px 14px; margin: 0; font-size: 18px;">Projects <span style="color: var(--muted); font-weight: 500; font-size: 14px;">({{ $projects->count() }})</span></h2>
                    <div>
                        @foreach($projects as $project)
                            <a href="{{ route('admin.projects.edit', $project) }}" class="search-result-row">
                                <div class="search-result-icon" style="background: #eef2ff; color: var(--primary);">&#128194;</div>
                                <div class="search-result-info">
                                    <strong>{{ $project->name }}</strong>
                                    <span>{{ $project->client->name ?? 'No client' }}</span>
                                </div>
                                <span class="badge badge-neutral">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($invoices->isNotEmpty())
                <div class="card" style="padding: 0; overflow: hidden;">
                    <h2 style="padding: 22px 24px 14px; margin: 0; font-size: 18px;">Invoices <span style="color: var(--muted); font-weight: 500; font-size: 14px;">({{ $invoices->count() }})</span></h2>
                    <div>
                        @foreach($invoices as $invoice)
                            <a href="{{ route('admin.invoices.create', $invoice->project) }}" class="search-result-row">
                                <div class="search-result-icon" style="background: #fef3c7; color: #92400e;">&#128179;</div>
                                <div class="search-result-info">
                                    <strong>{{ $invoice->invoice_number }}</strong>
                                    <span>{{ $invoice->project->client->name ?? 'Unknown' }} — {{ $invoice->project->name }}</span>
                                </div>
                                <x-status-badge :status="$invoice->status" />
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($clients->isNotEmpty())
                <div class="card" style="padding: 0; overflow: hidden;">
                    <h2 style="padding: 22px 24px 14px; margin: 0; font-size: 18px;">Clients <span style="color: var(--muted); font-weight: 500; font-size: 14px;">({{ $clients->count() }})</span></h2>
                    <div>
                        @foreach($clients as $client)
                            <a href="{{ route('admin.clients.create') }}" class="search-result-row">
                                <div class="search-result-icon" style="background: #dcfce7; color: #166534; font-weight: 700; font-size: 13px;">
                                    {{ strtoupper(substr($client->name, 0, 1)) }}
                                </div>
                                <div class="search-result-info">
                                    <strong>{{ $client->name }}</strong>
                                    <span>{{ $client->email }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>