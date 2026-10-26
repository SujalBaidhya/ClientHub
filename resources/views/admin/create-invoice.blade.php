<x-layouts.app title="Invoices — Admin">

    <style>
        .invoices-header {
            margin-bottom: 24px;
        }

        .invoice-form-group {
            margin-bottom: 16px;
        }

        .invoice-form-input,
        .invoice-form-select {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-size: 14px;
            font-family: inherit;
        }

        .invoice-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .invoice-card-custom {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            transition: border-color 0.15s ease;
        }

        .invoice-card-custom:hover {
            border-color: #cbd5e1;
        }

        .invoice-actions-footer {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 6px;
            padding-top: 14px;
            border-top: 1px solid var(--border);
        }

        @media (max-width: 768px) {
            .invoices-header h1 {
                font-size: 26px;
            }

            .invoice-grid-2 {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .invoice-card-custom {
                padding: 16px;
            }

            .invoice-actions-footer {
                grid-template-columns: 1fr;
            }

            .invoice-actions-footer .btn {
                width: 100%;
                text-align: center;
                justify-content: center;
                padding: 10px;
            }
        }
    </style>

    <div class="dashboard">

        <x-breadcrumbs :links="[
            ['label' => 'Projects', 'url' => route('admin.projects.index'), 'icon' => true],
            ['label' => $project->name, 'url' => route('admin.projects.edit', $project)],
            ['label' => 'Invoices'],
        ]" />

        <div class="invoices-header">
            <p class="dashboard-eyebrow">Admin Workspace</p>
            <h1 style="margin: 0 0 6px 0;">{{ $project->name }}</h1>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Add and manage billing invoices for this project.</p>
        </div>

        <x-card title="Add Invoice">

            <form method="POST" action="{{ route('admin.invoices.store', $project) }}">
                @csrf

                <div class="invoice-form-group">
                    <label style="display:block; margin-bottom: 6px; font-weight: 600; font-size: 14px;">Invoice Number</label>
                    <input type="text" name="invoice_number" required placeholder="INV-2026-003" class="invoice-form-input">
                </div>

                <div class="invoice-grid-2">
                    <div>
                        <label style="display:block; margin-bottom: 6px; font-weight: 600; font-size: 14px;">Amount (NPR)</label>
                        <input type="number" step="0.01" name="amount" required placeholder="0.00" class="invoice-form-input">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom: 6px; font-weight: 600; font-size: 14px;">Status</label>
                        <select name="status" required class="invoice-form-select">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                </div>

                <div class="invoice-grid-2">
                    <div>
                        <label style="display:block; margin-bottom: 6px; font-weight: 600; font-size: 14px;">Issued Date</label>
                        <input type="date" name="issued_at" class="invoice-form-input">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom: 6px; font-weight: 600; font-size: 14px;">Due Date</label>
                        <input type="date" name="due_date" class="invoice-form-input">
                    </div>
                </div>

                <button type="submit" class="btn btn-success" style="padding: 10px 18px; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Add Invoice
                </button>

            </form>

        </x-card>

        <x-card title="Existing Invoices">

            @if($invoices->isEmpty())
                <x-empty-state
                    title="No invoices yet"
                    message="Add one above to get started."
                />
            @else
                <div class="client-invoice-list" style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach($invoices as $invoice)
                        <div class="invoice-card-custom">

                            <form id="update-invoice-{{ $invoice->id }}" method="POST" action="{{ route('admin.invoices.update', [$project, $invoice]) }}">
                                @csrf
                                @method('PUT')

                                <div class="invoice-form-group">
                                    <label style="display:block; margin-bottom: 4px; font-size: 12px; font-weight: 600; color: var(--muted);">INVOICE NO.</label>
                                    <input type="text" name="invoice_number" required value="{{ $invoice->invoice_number }}" class="invoice-form-input" style="font-weight: 600;">
                                </div>

                                <div class="invoice-grid-2">
                                    <div>
                                        <label style="display:block; margin-bottom: 4px; font-size: 12px; font-weight: 600; color: var(--muted);">AMOUNT (NPR)</label>
                                        <input type="number" step="0.01" name="amount" required value="{{ $invoice->amount }}" class="invoice-form-input">
                                    </div>
                                    <div>
                                        <label style="display:block; margin-bottom: 4px; font-size: 12px; font-weight: 600; color: var(--muted);">STATUS</label>
                                        <select name="status" required class="invoice-form-select">
                                            <option value="pending" @selected($invoice->status === 'pending')>Pending</option>
                                            <option value="paid" @selected($invoice->status === 'paid')>Paid</option>
                                            <option value="overdue" @selected($invoice->status === 'overdue')>Overdue</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="invoice-grid-2">
                                    <div>
                                        <label style="display:block; margin-bottom: 4px; font-size: 12px; font-weight: 600; color: var(--muted);">ISSUED DATE</label>
                                        <input type="date" name="issued_at" value="{{ $invoice->issued_at?->format('Y-m-d') }}" class="invoice-form-input">
                                    </div>
                                    <div>
                                        <label style="display:block; margin-bottom: 4px; font-size: 12px; font-weight: 600; color: var(--muted);">DUE DATE</label>
                                        <input type="date" name="due_date" value="{{ $invoice->due_date?->format('Y-m-d') }}" class="invoice-form-input">
                                    </div>
                                </div>
                            </form>

                            <div class="invoice-actions-footer">
                                <button type="submit" form="update-invoice-{{ $invoice->id }}" class="btn btn-small" style="padding: 10px 14px;">
                                    Save Changes
                                </button>

                                <form method="POST" action="{{ route('admin.invoices.destroy', [$project, $invoice]) }}" onsubmit="return confirm('Delete this invoice?');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-small" style="background: #dc2626; color: white; border: none; width: 100%; padding: 10px 14px;">
                                        Delete Invoice
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif

        </x-card>

    </div>

</x-layouts.app>
