<x-layouts.app title="Payments — Admin">

    <div class="dashboard">

        <div class="dashboard-welcome">
            <div>
                <p class="dashboard-eyebrow">Admin</p>
                <h1>Payment History</h1>
                <p>All payments received across every client and project.</p>
            </div>
        </div>

        <x-card>
            <span class="info-label">Total Received</span>
            <h2 style="margin: 6px 0 0; font-size: 28px; color: #16a34a;">
                NPR {{ number_format($totalReceived, 2) }}
            </h2>
        </x-card>

       <form method="GET" action="{{ route('admin.payments.index') }}" style="margin-bottom: 20px; display: flex; gap: 10px;">
    <input
        type="text"
        name="search"
        value="{{ $search }}"
        placeholder="Search by invoice number or client name..."
        style="flex: 1; max-width: 400px; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);"
    >
    <button type="submit" class="btn">Search</button>
    @if($search)
        <a href="{{ route('admin.payments.index') }}" class="btn">Clear</a>
    @endif
    <a href="{{ route('admin.payments.export', ['search' => $search]) }}" class="btn btn-success" style="margin-left: auto;">Export CSV</a>
</form>

        <x-card title="Payments">

            @if($payments->isEmpty())
                <x-empty-state
                    title="No payments yet"
                    message="Paid invoices will appear here once clients make payments."
                />
            @else
                <div class="file-list">
                    @foreach($payments as $invoice)
                        <div class="file-item">
                            <div>
                                <strong>{{ $invoice->invoice_number }}</strong>
                                <span>
                                    {{ $invoice->project->client->name ?? 'N/A' }}
                                    &middot; {{ $invoice->project->name ?? 'N/A' }}
                                    &middot; Paid {{ $invoice->paid_at?->format('M d, Y') }}
&middot; {{ $invoice->transaction_code ? 'eSewa' : 'Manual' }}
                                </span>
                            </div>
                            <strong style="color: #16a34a;">NPR {{ number_format($invoice->amount, 2) }}</strong>
                        </div>
                    @endforeach
                </div>

                @if($payments->hasPages())
                    <div style="margin-top: 20px; display: flex; justify-content: center; gap: 6px; flex-wrap: wrap;">
                        @if($payments->onFirstPage())
                            <span style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; color: #999; background: #f5f5f5;">Previous</span>
                        @else
                            <a href="{{ $payments->previousPageUrl() }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">Previous</a>
                        @endif

                        @foreach($payments->getUrlRange(1, $payments->lastPage()) as $page => $url)
                            @if($page == $payments->currentPage())
                                <span style="padding: 8px 12px; border: 1px solid var(--primary); border-radius: 8px; background: var(--primary); color: white; font-weight: 600;">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($payments->hasMorePages())
                            <a href="{{ $payments->nextPageUrl() }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">Next</a>
                        @else
                            <span style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; color: #999; background: #f5f5f5;">Next</span>
                        @endif
                    </div>
                @endif
            @endif

        </x-card>

    </div>

</x-layouts.app>
