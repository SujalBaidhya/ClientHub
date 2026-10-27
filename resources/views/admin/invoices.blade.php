<x-layouts.app title="Invoices — Admin">

    <style>
        .inv-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
        .inv-filters { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .inv-tabs { display: flex; gap: 6px; flex-wrap: wrap; }
        .inv-tab { padding: 8px 14px; border: 1px solid var(--border); border-radius: 999px; text-decoration: none; color: inherit; background: white; font-size: 13px; }
        .inv-tab.active { background: #1e1b4b; color: white; border-color: #1e1b4b; }
        .inv-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 16px; border: 1px solid var(--border); border-radius: 10px; background: #fafafa; }
        .inv-row.is-overdue { border-color: #fca5a5; background: #fef2f2; }
        .inv-right { display: flex; align-items: center; gap: 14px; text-align: right; }
        .inv-main { min-width: 0; display: flex; flex-direction: column; gap: 4px; }
        [data-theme="dark"] .inv-tab {
            background: var(--surface);
            color: var(--text);
            border-color: var(--border);
        }

        [data-theme="dark"] .inv-tab.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        [data-theme="dark"] .inv-row {
            background: var(--surface);
            border-color: var(--border);
            color: var(--text);
        }

        [data-theme="dark"] .inv-row.is-overdue {
            background: rgba(239, 68, 68, 0.10);
            border-color: #7f1d1d;
        }

        [data-theme="dark"] .inv-main > strong {
            color: var(--text) !important;
        }

        [data-theme="dark"] .inv-right > strong {
            color: var(--text);
        }
        [data-theme="dark"] .inv-right .btn[style] {
            background: var(--surface) !important;
            color: var(--text) !important;
            border-color: var(--border) !important;
        }

        [data-theme="dark"] .inv-row .badge {
            color: inherit;
        }

        [data-theme="dark"] .inv-filters input {
            background: #0c121e !important;
            color: var(--text) !important;
            border-color: var(--border) !important;
        }

        [data-theme="dark"] .inv-filters input::placeholder {
            color: var(--muted);
        }

        [data-theme="dark"] .inv-row ~ div a {
            background: var(--surface) !important;
            color: var(--text) !important;
            border-color: var(--border) !important;
        }
        @media (max-width: 768px) {
            .inv-stats { grid-template-columns: 1fr; }
            .inv-row { flex-direction: column; align-items: stretch; }
            .inv-right { justify-content: space-between; text-align: left; }
        }
    </style>

    <div class="dashboard">

        <div class="dashboard-welcome">
            <div>
                <p class="dashboard-eyebrow">Admin</p>
                <h1>All Invoices</h1>
                <p>Every invoice across all clients and projects.</p>
            </div>
        </div>

        @php
            $paid = $totals->get('paid');
            $pending = $totals->get('pending');
            $overdue = $totals->get('overdue');
            $outstanding = ($pending->amount ?? 0) + ($overdue->amount ?? 0);
        @endphp

        <div class="inv-stats">
            <x-card>
                <span class="info-label">Outstanding</span>
                <h2 style="margin: 6px 0 0; font-size: 26px;">NPR {{ number_format($outstanding, 2) }}</h2>
                <span style="font-size: 12px; color: var(--muted);">{{ ($pending->total ?? 0) + ($overdue->total ?? 0) }} unpaid invoice(s)</span>
            </x-card>
            <x-card>
                <span class="info-label">Overdue</span>
                <h2 style="margin: 6px 0 0; font-size: 26px; color: #dc2626;">NPR {{ number_format($overdue->amount ?? 0, 2) }}</h2>
                <span style="font-size: 12px; color: var(--muted);">{{ $overdue->total ?? 0 }} invoice(s)</span>
            </x-card>
            <x-card>
                <span class="info-label">Paid</span>
                <h2 style="margin: 6px 0 0; font-size: 26px; color: #16a34a;">NPR {{ number_format($paid->amount ?? 0, 2) }}</h2>
                <span style="font-size: 12px; color: var(--muted);">{{ $paid->total ?? 0 }} invoice(s)</span>
            </x-card>
        </div>

        <form method="GET" action="{{ route('admin.invoices.index') }}" class="inv-filters">
            <input type="text" id="invFilterInput" name="search" value="{{ $search }}" placeholder="Live filter or press Enter to search all..."
                   style="flex: 1; min-width: 200px; max-width: 400px; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <button type="submit" class="btn">Search</button>
            @if($search)
                <a href="{{ route('admin.invoices.index', array_filter(['status' => $status])) }}" class="btn">Clear</a>
            @endif
            <a href="{{ route('admin.invoices.export', array_filter(['search' => $search, 'status' => $status])) }}" class="btn btn-success" style="margin-left: auto;">Export CSV</a>
        </form>

        <div class="inv-tabs" style="margin-bottom: 16px;">
            @foreach([null => 'All', 'overdue' => 'Overdue', 'pending' => 'Pending', 'paid' => 'Paid'] as $key => $label)
                <a href="{{ route('admin.invoices.index', array_filter(['status' => $key, 'search' => $search])) }}"
                   class="inv-tab {{ $status === $key ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>

        <x-card>
            @if($invoices->isEmpty())
                <x-empty-state title="No invoices found" message="Try a different search or filter." />
            @else
                <div class="file-list">
                    @foreach($invoices as $invoice)
                        @php
                            $daysLate = ($invoice->status === 'overdue' && $invoice->due_date)
                                ? (int) $invoice->due_date->startOfDay()->diffInDays(now()->startOfDay())
                                : null;
                        @endphp
                        <div class="inv-row {{ $invoice->status === 'overdue' ? 'is-overdue' : '' }}">
                            <div class="inv-main">
                                <strong style="color: #1e1b4b;">{{ $invoice->invoice_number }}</strong>
                                <span style="font-size: 13px; color: var(--muted);">
                                    {{ $invoice->project->client->name ?? 'N/A' }} &middot; {{ $invoice->project->name ?? 'N/A' }}
                                </span>
                                <span style="font-size: 12px; color: var(--muted);">
                                    @if($invoice->status === 'paid')
                                        Paid {{ $invoice->paid_at?->format('M d, Y') ?? '—' }}
                                    @elseif($invoice->due_date)
                                        Due {{ $invoice->due_date->format('M d, Y') }}
                                        @if($daysLate) &middot; <strong style="color:#dc2626;">{{ $daysLate }} day(s) late</strong> @endif
                                    @else
                                        No due date
                                    @endif
                                </span>
                            </div>
                            <div class="inv-right">
                                <strong>NPR {{ number_format($invoice->amount, 2) }}</strong>
                                <x-status-badge :status="$invoice->status" />
                                <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-small" style="background: white; border: 1px solid var(--border); color: #374151;">PDF</a>
                                @if($invoice->project)
                                    <a href="{{ route('admin.invoices.create', $invoice->project) }}" class="btn btn-small">Manage</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($invoices->hasPages())
                    <div style="margin-top: 20px; display: flex; justify-content: center; gap: 6px; flex-wrap: wrap;">
                        @if($invoices->onFirstPage())
                            <span style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; color: #999; background: #f5f5f5;">Previous</span>
                        @else
                            <a href="{{ $invoices->previousPageUrl() }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">Previous</a>
                        @endif
                        <span style="padding: 8px 12px; color: var(--muted);">Page {{ $invoices->currentPage() }} of {{ $invoices->lastPage() }}</span>
                        @if($invoices->hasMorePages())
                            <a href="{{ $invoices->nextPageUrl() }}" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">Next</a>
                        @else
                            <span style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; color: #999; background: #f5f5f5;">Next</span>
                        @endif
                    </div>
                @endif
            @endif
        </x-card>

    </div>

    <script>
        (function() {
            var input = document.getElementById('invFilterInput');
            var rows = document.querySelectorAll('.inv-row');
            if (!input || !rows.length) return;

            input.addEventListener('input', function() {
                var q = input.value.trim().toLowerCase();
                rows.forEach(function(row) {
                    var text = row.textContent.toLowerCase();
                    row.style.display = (q === '' || text.includes(q)) ? '' : 'none';
                });
            });
        })();
    </script>
</x-layouts.app>