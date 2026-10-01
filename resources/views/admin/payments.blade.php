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
                                </span>
                            </div>
                            <strong style="color: #16a34a;">NPR {{ number_format($invoice->amount, 2) }}</strong>
                        </div>
                    @endforeach
                </div>
            @endif

        </x-card>

    </div>

</x-layouts.app>