<x-layouts.app title="Financial Analytics — Admin">

    <style>
        .analytics-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .analytics-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .year-select-form select {
            padding: 9px 14px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 10px;
            background: var(--surface);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: relative;
            overflow: hidden;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .kpi-card:hover {
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.06);
        }

        [data-theme="dark"] .kpi-card:hover {
            border-color: #3b4d6e;
            box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.4);
        }

        .kpi-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .kpi-label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .kpi-icon-wrap {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .kpi-value {
            font-size: 24px;
            font-weight: 700;
            line-height: 1.2;
            margin: 0;
            color: var(--text);
            letter-spacing: -0.01em;
        }

        .kpi-footer {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }

        .trend-pill {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 11.5px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
            line-height: 1.2;
        }

        .trend-pill.positive {
            background: rgba(34, 197, 94, 0.14);
            color: #16a34a;
        }

        .trend-pill.negative {
            background: rgba(239, 68, 68, 0.14);
            color: #dc2626;
        }

        [data-theme="dark"] .trend-pill.positive {
            color: #4ade80;
        }

        [data-theme="dark"] .trend-pill.negative {
            color: #f87171;
        }

        /* Charts Layout */
        .analytics-charts-grid {
            display: grid;
            grid-template-columns: 1.65fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .chart-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px;
            display: flex;
            flex-direction: column;
        }

        .chart-box-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .chart-box-title {
            font-size: 17px;
            font-weight: 700;
            margin: 0 0 4px;
            color: var(--text);
        }

        .chart-box-subtitle {
            font-size: 13px;
            color: var(--muted);
            margin: 0;
        }

        .doughnut-layout {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            flex: 1;
            justify-content: center;
        }

        .doughnut-wrapper {
            position: relative;
            width: 220px;
            height: 220px;
        }

        .doughnut-center-info {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
        }

        .doughnut-center-amount {
            font-size: 18px;
            font-weight: 800;
            color: var(--text);
            line-height: 1.1;
        }

        .doughnut-center-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 3px;
        }

        .status-legend-cards {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .status-legend-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 12px;
            border-radius: 9px;
            background: rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border);
            font-size: 13px;
            transition: background 0.15s;
        }

        [data-theme="dark"] .status-legend-row {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Bottom Section: Top Clients & Monthly Table */
        .analytics-details-grid {
            display: grid;
            grid-template-columns: 1fr 1.8fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .top-client-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .top-client-item:last-child {
            border-bottom: none;
        }

        .top-client-rank {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background: #eef2f6;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11.5px;
            font-weight: 700;
            flex-shrink: 0;
        }

        [data-theme="dark"] .top-client-rank {
            background: #1e293b;
            color: #94a3b8;
        }

        .top-client-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .top-client-meta {
            flex: 1;
            min-width: 0;
        }

        .top-client-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            margin: 0 0 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .top-client-sub {
            font-size: 12px;
            color: var(--muted);
            margin: 0;
        }

        .top-client-revenue {
            text-align: right;
            flex-shrink: 0;
        }

        .top-client-rev-val {
            font-size: 14px;
            font-weight: 700;
            color: #10b981;
            margin: 0 0 2px;
        }

        .top-client-pct {
            font-size: 11px;
            color: var(--muted);
        }

        /* Monthly Ledger Table */
        .ledger-table-wrap {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid var(--border);
        }

        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .ledger-table th {
            background: rgba(0, 0, 0, 0.02);
            padding: 11px 14px;
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid var(--border);
            text-align: right;
        }

        [data-theme="dark"] .ledger-table th {
            background: rgba(255, 255, 255, 0.02);
        }

        .ledger-table th:first-child,
        .ledger-table td:first-child {
            text-align: left;
        }

        .ledger-table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            text-align: right;
        }

        .ledger-table tr:last-child td {
            border-bottom: none;
        }

        .ledger-table tr.summary-row td {
            font-weight: 700;
            background: rgba(0, 0, 0, 0.02);
            border-top: 2px solid var(--border);
        }

        [data-theme="dark"] .ledger-table tr.summary-row td {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Responsive */
        @media (max-width: 1100px) {
            .kpi-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            .analytics-charts-grid {
                grid-template-columns: 1fr;
            }
            .analytics-details-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="analytics-container">

        {{-- Breadcrumb Navigation --}}
        <x-breadcrumbs :links="[
            ['label' => 'Overview', 'url' => route('admin.dashboard')],
            ['label' => 'Financial Analytics']
        ]" />

        {{-- Header & Controls --}}
        <div class="analytics-header">
            <div>
                <p class="dashboard-eyebrow">Finance & Intelligence</p>
                <h1 style="margin: 0 0 6px 0;">Financial Analytics</h1>
                <p style="margin: 0; color: var(--muted); font-size: 14px;">
                    Cash flow analysis, collection performance, and billing metrics for {{ $selectedYear }}.
                </p>
            </div>

            <div class="analytics-actions">
                <form method="GET" action="{{ route('admin.analytics.index') }}" class="year-select-form">
                    <select name="year" onchange="this.form.submit()" aria-label="Select Year">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                Year {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('admin.analytics.export', ['year' => $selectedYear]) }}" class="btn" style="display: inline-flex; align-items: center; gap: 6px;">
                    <x-icon name="download" size="15" />
                    <span>Export CSV</span>
                </a>
            </div>
        </div>

        {{-- KPI Cards Grid --}}
        <div class="kpi-grid">

            {{-- 1. Total Collected --}}
            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-label">Collected</span>
                    <div class="kpi-icon-wrap" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                        <x-icon name="trending-up" size="18" />
                    </div>
                </div>
                <div class="kpi-value" style="color: #10b981;">
                    NPR {{ number_format($totalCollected, 0) }}
                </div>
                <div class="kpi-footer">
                    @if($momGrowth === null)
                        <span style="color: var(--muted);">—</span>
                    @elseif($momGrowth > 999)
                        <span class="trend-pill positive" title="+{{ number_format($momGrowth, 1) }}%">↑ >999%</span>
                    @elseif($momGrowth >= 0)
                        <span class="trend-pill positive">↑ {{ number_format($momGrowth, 1) }}%</span>
                    @else
                        <span class="trend-pill negative">↓ {{ number_format(abs($momGrowth), 1) }}%</span>
                    @endif
                    <span>vs last month</span>
                </div>
            </div>

            {{-- 2. Total Invoiced --}}
            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-label">Total Invoiced</span>
                    <div class="kpi-icon-wrap" style="background: rgba(99, 102, 241, 0.12); color: #6366f1;">
                        <x-icon name="file-text" size="18" />
                    </div>
                </div>
                <div class="kpi-value">
                    NPR {{ number_format($totalInvoiced, 0) }}
                </div>
                <div class="kpi-footer">
                    <span>Lifetime billing</span>
                </div>
            </div>

            {{-- 3. Collection Rate --}}
            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-label">Collection Rate</span>
                    <div class="kpi-icon-wrap" style="background: rgba(14, 165, 233, 0.12); color: #0284c7;">
                        <x-icon name="check-circle" size="18" />
                    </div>
                </div>
                <div class="kpi-value">
                    {{ $collectionRate }}%
                </div>
                <div class="kpi-footer">
                    <span>Collected vs billed ratio</span>
                </div>
            </div>

            {{-- 4. Outstanding & Overdue --}}
            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-label">Outstanding</span>
                    <div class="kpi-icon-wrap" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                        <x-icon name="credit-card" size="18" />
                    </div>
                </div>
                <div class="kpi-value" style="color: #ef4444;">
                    NPR {{ number_format($outstandingAmount, 0) }}
                </div>
                <div class="kpi-footer">
                    @if($overdueCount > 0)
                        <span class="trend-pill negative">{{ $overdueCount }} overdue</span>
                    @else
                        <span class="trend-pill positive">0 overdue</span>
                    @endif
                </div>
            </div>

            {{-- 5. Average Invoice --}}
            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-label">Avg. Invoice</span>
                    <div class="kpi-icon-wrap" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                        <x-icon name="calendar" size="18" />
                    </div>
                </div>
                <div class="kpi-value">
                    NPR {{ number_format($avgInvoiceValue, 0) }}
                </div>
                <div class="kpi-footer">
                    <span>Per invoice issued</span>
                </div>
            </div>

        </div>

        {{-- Charts Section --}}
        <div class="analytics-charts-grid">

            {{-- Billed vs Collected Trend --}}
            <div class="chart-box">
                <div class="chart-box-header">
                    <div>
                        <h2 class="chart-box-title">Billed vs Collected Trend ({{ $selectedYear }})</h2>
                        <p class="chart-box-subtitle">Monthly comparison of invoice amounts billed vs actual revenue collected</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 14px; font-size: 12.5px; font-weight: 600;">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="width: 10px; height: 10px; border-radius: 2px; background: #6366f1; display: inline-block;"></span>
                            <span>Billed</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="width: 10px; height: 10px; border-radius: 2px; background: #10b981; display: inline-block;"></span>
                            <span>Collected</span>
                        </div>
                    </div>
                </div>
                <div style="height: 310px; position: relative;">
                    <canvas id="billedVsCollectedChart"></canvas>
                </div>
            </div>

            {{-- Invoice Status Distribution --}}
            <div class="chart-box">
                <div class="chart-box-header">
                    <div>
                        <h2 class="chart-box-title">Invoice Status</h2>
                        <p class="chart-box-subtitle">Portfolio breakdown by payment status</p>
                    </div>
                </div>

                <div class="doughnut-layout">
                    <div class="doughnut-wrapper">
                        <canvas id="paymentStatusChart"></canvas>
                        <div class="doughnut-center-info">
                            <div class="doughnut-center-amount" id="statusCenterAmount">
                                NPR {{ number_format($totalCollected, 0) }}
                            </div>
                            <div class="doughnut-center-label" id="statusCenterLabel">
                                Collected
                            </div>
                        </div>
                    </div>

                    <div class="status-legend-cards">
                        <div class="status-legend-row">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                                <strong style="color: var(--text);">Paid</strong>
                                <span style="font-size: 11.5px; color: var(--muted);">({{ $statusBreakdown['paid']['count'] }})</span>
                            </div>
                            <span style="font-weight: 700; color: #10b981;">NPR {{ number_format($statusBreakdown['paid']['amount'], 0) }}</span>
                        </div>

                        <div class="status-legend-row">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></span>
                                <strong style="color: var(--text);">Pending</strong>
                                <span style="font-size: 11.5px; color: var(--muted);">({{ $statusBreakdown['pending']['count'] }})</span>
                            </div>
                            <span style="font-weight: 700; color: #f59e0b;">NPR {{ number_format($statusBreakdown['pending']['amount'], 0) }}</span>
                        </div>

                        <div class="status-legend-row">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></span>
                                <strong style="color: var(--text);">Overdue</strong>
                                <span style="font-size: 11.5px; color: var(--muted);">({{ $statusBreakdown['overdue']['count'] }})</span>
                            </div>
                            <span style="font-weight: 700; color: #ef4444;">NPR {{ number_format($statusBreakdown['overdue']['amount'], 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Bottom Grid: Top Clients & Monthly Breakdown Ledger --}}
        <div class="analytics-details-grid">

            {{-- Top Clients by Revenue --}}
            <div class="chart-box">
                <div class="chart-box-header">
                    <div>
                        <h2 class="chart-box-title">Top Revenue Clients</h2>
                        <p class="chart-box-subtitle">Ranked by total paid invoice volume</p>
                    </div>
                </div>

                @if($topClients->isEmpty())
                    <x-empty-state title="No payment records yet" message="Paid invoices will populate client revenue contributions here." />
                @else
                    <div style="display: flex; flex-direction: column;">
                        @foreach($topClients as $idx => $client)
                            <div class="top-client-item">
                                <div class="top-client-rank">{{ $idx + 1 }}</div>
                                <div class="top-client-avatar">
                                    {{ strtoupper(substr($client['name'], 0, 1)) }}
                                </div>
                                <div class="top-client-meta">
                                    <h4 class="top-client-name">{{ $client['name'] }}</h4>
                                    <p class="top-client-sub">{{ $client['projects_count'] }} project(s) • {{ $client['paid_invoices_count'] }} invoice(s)</p>
                                </div>
                                <div class="top-client-revenue">
                                    <div class="top-client-rev-val">NPR {{ number_format($client['revenue'], 0) }}</div>
                                    <div class="top-client-pct">{{ $client['share_percent'] }}% of total</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Monthly Financial Breakdown Ledger --}}
            <div class="chart-box">
                <div class="chart-box-header">
                    <div>
                        <h2 class="chart-box-title">Monthly Financial Ledger</h2>
                        <p class="chart-box-subtitle">Detailed breakdown across all 12 months for {{ $selectedYear }}</p>
                    </div>
                </div>

                <div class="ledger-table-wrap">
                    <table class="ledger-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Billed (NPR)</th>
                                <th>Collected (NPR)</th>
                                <th>Outstanding</th>
                                <th>Collection Rate</th>
                                <th>MoM Growth</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sumBilled = 0;
                                $sumCollected = 0;
                                $sumOutstanding = 0;
                            @endphp

                            @foreach($monthlyData as $row)
                                @php
                                    $sumBilled += $row['billed_amount'];
                                    $sumCollected += $row['collected_amount'];
                                    $sumOutstanding += $row['unpaid_amount'];
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $row['month'] }}</strong>
                                    </td>
                                    <td>
                                        {{ number_format($row['billed_amount'], 0) }}
                                        <span style="font-size: 11px; color: var(--muted);">({{ $row['billed_count'] }})</span>
                                    </td>
                                    <td style="color: #10b981; font-weight: 600;">
                                        {{ number_format($row['collected_amount'], 0) }}
                                        <span style="font-size: 11px; color: var(--muted);">({{ $row['collected_count'] }})</span>
                                    </td>
                                    <td style="{{ $row['unpaid_amount'] > 0 ? 'color: #ef4444;' : 'color: var(--muted);' }}">
                                        {{ number_format($row['unpaid_amount'], 0) }}
                                    </td>
                                    <td>
                                        @if($row['collection_rate'] === null)
                                            <span style="color: var(--muted);">—</span>
                                        @else
                                            <span class="badge {{ $row['collection_rate'] >= 80 ? 'badge-success' : ($row['collection_rate'] > 0 ? 'badge-warning' : 'badge-neutral') }}" style="font-size: 11.5px;">
                                                {{ $row['collection_rate'] }}%
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row['mom_growth'] === null)
                                            <span style="color: var(--muted);">—</span>
                                        @elseif($row['mom_growth'] > 999)
                                            <span style="color: #16a34a; font-weight: 600;" title="+{{ number_format($row['mom_growth'], 1) }}%">> +999%</span>
                                        @elseif($row['mom_growth'] >= 0)
                                            <span style="color: #16a34a; font-weight: 600;">+{{ number_format($row['mom_growth'], 1) }}%</span>
                                        @else
                                            <span style="color: #dc2626; font-weight: 600;">{{ number_format($row['mom_growth'], 1) }}%</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Yearly Summary Row --}}
                            <tr class="summary-row">
                                <td>Year Total</td>
                                <td>NPR {{ number_format($sumBilled, 0) }}</td>
                                <td style="color: #10b981;">NPR {{ number_format($sumCollected, 0) }}</td>
                                <td style="{{ $sumOutstanding > 0 ? 'color: #ef4444;' : '' }}">NPR {{ number_format($sumOutstanding, 0) }}</td>
                                <td>{{ $sumBilled > 0 ? round(($sumCollected / $sumBilled) * 100, 1) : 0 }}%</td>
                                <td>—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    {{-- Chart.js Scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const gridColor = isDark ? '#1f293d' : '#f1f0fb';
            const textColor = isDark ? '#94a3b8' : '#64748b';

            // 1. Billed vs Collected Trend Bar Chart
            const monthlyLabels = {!! json_encode(array_column($monthlyData, 'month')) !!};
            const billedData = {!! json_encode(array_column($monthlyData, 'billed_amount')) !!};
            const collectedData = {!! json_encode(array_column($monthlyData, 'collected_amount')) !!};

            const ctxTrend = document.getElementById('billedVsCollectedChart').getContext('2d');
            new Chart(ctxTrend, {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [
                        {
                            label: 'Billed',
                            data: billedData,
                            backgroundColor: 'rgba(99, 102, 241, 0.75)',
                            hoverBackgroundColor: '#6366f1',
                            borderRadius: 6,
                            barPercentage: 0.65,
                            categoryPercentage: 0.75
                        },
                        {
                            label: 'Collected',
                            data: collectedData,
                            backgroundColor: 'rgba(16, 185, 129, 0.85)',
                            hoverBackgroundColor: '#10b981',
                            borderRadius: 6,
                            barPercentage: 0.65,
                            categoryPercentage: 0.75
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function (context) {
                                    return context.dataset.label + ': NPR ' + Number(context.raw).toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: textColor,
                                callback: function (val) {
                                    if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                                    if (val >= 1000) return (val / 1000).toFixed(0) + 'k';
                                    return val;
                                }
                            },
                            grid: { color: gridColor }
                        },
                        x: {
                            ticks: { color: textColor },
                            grid: { display: false }
                        }
                    }
                }
            });

            // 2. Invoice Payment Status Doughnut
            const statusPaid = {{ (float) $statusBreakdown['paid']['amount'] }};
            const statusPending = {{ (float) $statusBreakdown['pending']['amount'] }};
            const statusOverdue = {{ (float) $statusBreakdown['overdue']['amount'] }};
            const hasData = (statusPaid + statusPending + statusOverdue) > 0;

            const ctxStatus = document.getElementById('paymentStatusChart').getContext('2d');
            const statusChart = new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Paid', 'Pending', 'Overdue'],
                    datasets: [{
                        data: hasData ? [statusPaid, statusPending, statusOverdue] : [1],
                        backgroundColor: hasData ? ['#10b981', '#f59e0b', '#ef4444'] : ['#e2e8f0'],
                        borderColor: isDark ? '#111827' : '#ffffff',
                        borderWidth: 3,
                        hoverOffset: hasData ? 6 : 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: hasData,
                            callbacks: {
                                label: function (context) {
                                    return context.label + ': NPR ' + Number(context.raw).toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

</x-layouts.app>
