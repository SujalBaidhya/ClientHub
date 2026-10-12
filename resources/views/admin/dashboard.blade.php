<x-layouts.app title="Admin Dashboard — ClientHub">

    <style>
        .admin-dashboard-header {
            margin-bottom: 24px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .stat-card-inner {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .stat-number {
            font-size: 26px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
            word-break: break-word;
        }

        .chart-breakdown-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 32px;
            flex-wrap: wrap;
        }

        .status-legend-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            flex: 1;
            min-width: 200px;
        }

        .status-legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .chart-wrapper {
            width: 250px;
            height: 250px;
            position: relative;
            margin: 0 auto;
        }

        .recent-project-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #ffffff;
        }

        @media (max-width: 900px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .chart-breakdown-container {
                flex-direction: column-reverse;
                align-items: center;
                gap: 24px;
            }

            .status-legend-list {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .admin-dashboard-header h1 {
                font-size: 26px;
            }

            .stat-number {
                font-size: 20px;
            }

            .chart-wrapper {
                width: 220px;
                height: 220px;
            }

            .recent-project-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>

    <div class="dashboard">

       <div class="admin-dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
    <div>
        <p class="dashboard-eyebrow">Admin</p>
        <h1 style="margin: 0 0 6px 0;">Overview</h1>
        <p style="margin: 0; color: #6b7280; font-size: 14px;">A snapshot of everything happening across ClientHub.</p>
    </div>

    <form method="POST" action="{{ route('admin.invoices.check-overdue') }}">
        @csrf
        <button type="submit" class="btn btn-small">Check Overdue Invoices</button>
    </form>
</div>

        {{-- Stat Cards --}}
        <div class="stats-grid">

            <x-card>
                <div class="stat-card-inner">
                    <span class="info-label">Total Clients</span>
                    <h2 class="stat-number" style="color: var(--text);">{{ $stats['total_clients'] }}</h2>
                </div>
            </x-card>

            <x-card>
                <div class="stat-card-inner">
                    <span class="info-label">Total Projects</span>
                    <h2 class="stat-number" style="color: var(--text);">{{ $stats['total_projects'] }}</h2>
                </div>
            </x-card>

            <x-card>
                <div class="stat-card-inner">
                    <span class="info-label">Revenue</span>
                    <h2 class="stat-number" style="font-family: var(--font-display); color: var(--accent-gold);">
                        NPR {{ number_format($stats['total_revenue'], 0) }}
                    </h2>
                </div>
            </x-card>

            <x-card>
                <div class="stat-card-inner">
                    <span class="info-label">Outstanding</span>
                    <h2 class="stat-number" style="color: #dc2626;">
                        NPR {{ number_format($stats['outstanding_amount'], 0) }}
                    </h2>
                </div>
            </x-card>

        </div>
                {{-- Revenue Trend --}}
        <x-card title="Revenue — Last 6 Months">
            <div style="height: 220px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </x-card>

        {{-- Project Status Breakdown --}}
        <x-card title="Projects by Status">
            <div class="chart-breakdown-container">
                <div class="status-legend-list">
                    <div class="status-legend-item">
                        <x-status-badge status="pending" />
                        <strong>{{ $stats['pending_projects'] }}</strong>
                    </div>
                    <div class="status-legend-item">
                        <x-status-badge status="in_progress" />
                        <strong>{{ $stats['in_progress_projects'] }}</strong>
                    </div>
                    <div class="status-legend-item">
                        <x-status-badge status="completed" />
                        <strong>{{ $stats['completed_projects'] }}</strong>
                    </div>
                    @if($stats['overdue_invoices'] > 0)
                        <div class="status-legend-item" style="border-color: #fecaca; background: #fff5f5;">
                            <x-status-badge status="overdue" />
                            <strong style="color: #dc2626;">{{ $stats['overdue_invoices'] }} invoice(s)</strong>
                        </div>
                    @endif
                </div>

                <div class="chart-wrapper">
                    <canvas id="statusChart"></canvas>
                    <div id="chartCenterLabel" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                        <div style="font-size: 26px; font-weight: 800; color: var(--text); line-height: 1;">{{ $stats['total_projects'] }}</div>
                        <div style="font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px;">Total</div>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Recent Projects --}}
        <x-card title="Recent Projects">

            @if($recentProjects->isEmpty())
                <x-empty-state
                    title="No projects yet"
                    message="Create your first project to get started."
                />
            @else
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($recentProjects as $project)
                        <div class="recent-project-row">
                            <div style="min-width: 0; display: flex; flex-direction: column; gap: 2px;">
                                <strong style="font-size: 14.5px; color: #1e1b4b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $project->name }}
                                </strong>
                                <span style="font-size: 12.5px; color: var(--muted);">Client: {{ $project->client->name ?? 'N/A' }}</span>
                            </div>
                            <x-status-badge :status="$project->status" />
                        </div>
                    @endforeach
                </div>
            @endif

        </x-card>

        <div style="margin-top: 14px;">
            <a href="{{ route('admin.projects.index') }}" class="btn" style="display: inline-block; width: auto;">
                View All Projects &rarr;
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
        const statusTotal = {{ $stats['pending_projects'] + $stats['in_progress_projects'] +$stats['completed_projects'] }};

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In Progress', 'Completed'],
                datasets: [{
                    data: [
                        {{ $stats['pending_projects'] }},
                        {{ $stats['in_progress_projects'] }},
                        {{ $stats['completed_projects'] }}
                    ],
                    backgroundColor: ['#c7d2fe', '#4f46e5', '#16a34a'],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const percent = statusTotal > 0 ? Math.round((value / statusTotal) * 100) : 0;
                                return ` ${context.label}: ${value} (${percent}%)`;
                            }
                        },
                        backgroundColor: '#1e1b4b',
                        padding: 10,
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        cornerRadius: 8,
                    }
                }
            }
        });
    </script>
        <script>
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyRevenue->pluck('label')) !!},
                datasets: [{
                    label: 'Revenue (NPR)',
                    data: {!! json_encode($monthlyRevenue->pluck('total')) !!},
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.08)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#4f46e5',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e1b4b',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ' NPR ' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return 'NPR ' + value.toLocaleString(); }
                        },
                        grid: { color: '#f1f0fb' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>

</x-layouts.app>