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
            gap: 8px;
        }

        .stat-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .stat-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-number {
            font-size: 26px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
            word-break: break-word;
        }

        .stat-subtext {
            font-size: 11.5px;
            color: var(--muted);
            margin: 0;
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
            gap: 12px;
            flex: 1;
            min-width: 240px;
        }

        .status-legend-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 10px 14px;
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid var(--border);
            text-decoration: none;
            color: inherit;
            transition: all 0.18s ease;
            cursor: pointer;
        }

        .status-legend-item:hover,
        .status-legend-item.is-hovered {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateX(3px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .status-legend-item:hover .status-row-arrow,
        .status-legend-item.is-hovered .status-row-arrow {
            opacity: 1;
            transform: translateX(2px);
        }

        .status-row-arrow {
            color: var(--muted);
            opacity: 0.35;
            transition: all 0.18s ease;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            display: inline-block;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.04);
        }

        .status-count-val {
            font-weight: 700;
            font-size: 15px;
            color: var(--text);
        }

        .status-percent-pill {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--muted);
            background: #f1f5f9;
            padding: 2px 7px;
            border-radius: 999px;
        }

        .status-progress-track {
            height: 4px;
            width: 100%;
            background: #f1f5f9;
            border-radius: 999px;
            overflow: hidden;
        }

        .status-progress-fill {
            height: 100%;
            border-radius: 999px;
            transition: width 0.4s ease;
        }

        .chart-wrapper {
            width: 250px;
            height: 250px;
            position: relative;
            margin: 0 auto;
        }

        .chart-center-display {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 130px;
            transition: all 0.2s ease;
        }

        .chart-center-val {
            font-size: 30px;
            font-weight: 800;
            color: var(--text);
            line-height: 1;
            letter-spacing: -0.02em;
            transition: color 0.15s ease;
        }

        .chart-center-lbl {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-top: 4px;
            transition: color 0.15s ease;
        }

        .chart-center-pct {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 999px;
            margin-top: 4px;
            transition: all 0.15s ease;
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
            transition: border-color 0.15s ease, background 0.15s ease;
        }

        .recent-project-row:hover {
            border-color: #cbd5e1;
            background: #fafbfc;
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

        <div class="admin-dashboard-header">
            <p class="dashboard-eyebrow">Admin</p>
            <h1 style="margin: 0 0 6px 0;">Overview</h1>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">A snapshot of everything happening across ClientHub.</p>
        </div>

        @if($stats['overdue_invoices'] > 0)
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; background: #fff5f5; border: 1px solid #fecaca; border-radius: 12px; padding: 14px 20px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <x-icon name="alert-circle" size="20" class="text-danger" style="color: #dc2626;" />
                    <span style="color: #991b1b; font-weight: 600; font-size: 14.5px;">
                        {{ $stats['overdue_invoices'] }} invoice(s) overdue — clients have been notified automatically.
                    </span>
                </div>
                <form method="POST" action="{{ route('admin.invoices.check-overdue') }}">
                    @csrf
                    <button type="submit" class="btn btn-small" style="background: #dc2626; color: white; border: none;">Re-check Overdue Invoices</button>
                </form>
            </div>
        @endif

        {{-- Stat Cards --}}
        <div class="stats-grid">

            <x-card>
                <div class="stat-card-inner">
                    <div class="stat-card-top">
                        <span class="info-label">Total Clients</span>
                        <div class="stat-icon-wrap" style="background: #eef2ff; color: #4f46e5;">
                            <x-icon name="users" size="18" />
                        </div>
                    </div>
                    <h2 class="stat-number" style="color: var(--text);">{{ $stats['total_clients'] }}</h2>
                    <p class="stat-subtext">Active client accounts</p>
                </div>
            </x-card>

            <x-card>
                <div class="stat-card-inner">
                    <div class="stat-card-top">
                        <span class="info-label">Total Projects</span>
                        <div class="stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                            <x-icon name="folder" size="18" />
                        </div>
                    </div>
                    <h2 class="stat-number" style="color: var(--text);">{{ $stats['total_projects'] }}</h2>
                    <p class="stat-subtext">Across all clients</p>
                </div>
            </x-card>

            <x-card>
                <div class="stat-card-inner">
                    <div class="stat-card-top">
                        <span class="info-label">Revenue</span>
                        <div class="stat-icon-wrap" style="background: #fef3c7; color: #b45309;">
                            <x-icon name="trending-up" size="18" />
                        </div>
                    </div>
                    <h2 class="stat-number" style="font-family: var(--font-display); color: var(--accent-gold);">
                        NPR {{ number_format($stats['total_revenue'], 0) }}
                    </h2>
                    <p class="stat-subtext">Received payments</p>
                </div>
            </x-card>

            <x-card>
                <div class="stat-card-inner">
                    <div class="stat-card-top">
                        <span class="info-label">Outstanding</span>
                        <div class="stat-icon-wrap" style="background: #fee2e2; color: #dc2626;">
                            <x-icon name="credit-card" size="18" />
                        </div>
                    </div>
                    <h2 class="stat-number" style="color: #dc2626;">
                        NPR {{ number_format($stats['outstanding_amount'], 0) }}
                    </h2>
                    <p class="stat-subtext">Unpaid invoices</p>
                </div>
            </x-card>

        </div>

        {{-- Revenue Trend --}}
        <x-card title="Revenue — Last 6 Months">
            <div style="height: 220px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </x-card>

        @php
            $totalStatusProjects = $stats['pending_projects'] + $stats['in_progress_projects'] + $stats['completed_projects'];
            $pendingPercent = $totalStatusProjects > 0 ? round(($stats['pending_projects'] / $totalStatusProjects) * 100) : 0;
            $inProgressPercent = $totalStatusProjects > 0 ? round(($stats['in_progress_projects'] / $totalStatusProjects) * 100) : 0;
            $completedPercent = $totalStatusProjects > 0 ? round(($stats['completed_projects'] / $totalStatusProjects) * 100) : 0;
        @endphp

        {{-- Project Status Breakdown --}}
        <x-card title="Projects by Status">
            <div class="chart-breakdown-container">
                <div class="status-legend-list">
                    {{-- Pending Item --}}
                    <a href="{{ route('admin.projects.index', ['status' => 'pending']) }}"
                       class="status-legend-item"
                       data-index="0"
                       title="Click to view all pending projects">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; width: 100%;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="status-dot" style="background: #94a3b8;"></span>
                                <x-status-badge status="pending" />
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="status-count-val">{{ $stats['pending_projects'] }}</span>
                                <span class="status-percent-pill">{{ $pendingPercent }}%</span>
                                <x-icon name="chevron-right" size="14" class="status-row-arrow" />
                            </div>
                        </div>
                        <div class="status-progress-track">
                            <div class="status-progress-fill" style="width: {{ $pendingPercent }}%; background: #94a3b8;"></div>
                        </div>
                    </a>

                    {{-- In Progress Item --}}
                    <a href="{{ route('admin.projects.index', ['status' => 'in_progress']) }}"
                       class="status-legend-item"
                       data-index="1"
                       title="Click to view all in-progress projects">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; width: 100%;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="status-dot" style="background: #f59e0b;"></span>
                                <x-status-badge status="in_progress" />
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="status-count-val">{{ $stats['in_progress_projects'] }}</span>
                                <span class="status-percent-pill">{{ $inProgressPercent }}%</span>
                                <x-icon name="chevron-right" size="14" class="status-row-arrow" />
                            </div>
                        </div>
                        <div class="status-progress-track">
                            <div class="status-progress-fill" style="width: {{ $inProgressPercent }}%; background: #f59e0b;"></div>
                        </div>
                    </a>

                    {{-- Completed Item --}}
                    <a href="{{ route('admin.projects.index', ['status' => 'completed']) }}"
                       class="status-legend-item"
                       data-index="2"
                       title="Click to view all completed projects">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; width: 100%;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="status-dot" style="background: #10b981;"></span>
                                <x-status-badge status="completed" />
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="status-count-val">{{ $stats['completed_projects'] }}</span>
                                <span class="status-percent-pill">{{ $completedPercent }}%</span>
                                <x-icon name="chevron-right" size="14" class="status-row-arrow" />
                            </div>
                        </div>
                        <div class="status-progress-track">
                            <div class="status-progress-fill" style="width: {{ $completedPercent }}%; background: #10b981;"></div>
                        </div>
                    </a>
                </div>

                <div class="chart-wrapper">
                    <canvas id="statusChart"></canvas>
                    <div class="chart-center-display" id="chartCenterDisplay">
                        <div class="chart-center-val" id="centerVal">{{ $stats['total_projects'] }}</div>
                        <div class="chart-center-lbl" id="centerLbl">Total Projects</div>
                        <div class="chart-center-pct" id="centerPct" style="display: none;"></div>
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
                            <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                                <div style="width: 36px; height: 36px; border-radius: 8px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <x-icon name="folder" size="18" />
                                </div>
                                <div style="min-width: 0; display: flex; flex-direction: column; gap: 2px;">
                                    <strong style="font-size: 14.5px; color: #1e1b4b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <a href="{{ route('admin.milestones.create', $project) }}" style="color: inherit; text-decoration: none;">
                                            {{ $project->name }}
                                        </a>
                                    </strong>
                                    <span style="font-size: 12.5px; color: var(--muted);">Client: {{ $project->client->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
                                <x-status-badge :status="$project->status" />
                                <a href="{{ route('admin.milestones.create', $project) }}" class="btn btn-small" style="font-size: 12px; padding: 5px 12px; display: inline-flex; align-items: center; gap: 4px;">
                                    Manage <x-icon name="arrow-right" size="12" />
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </x-card>

        <div style="margin-top: 14px;">
            <a href="{{ route('admin.projects.index') }}" class="btn" style="display: inline-flex; align-items: center; gap: 8px; width: auto;">
                <span>View All Projects</span>
                <x-icon name="arrow-right" size="14" />
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
        const statusMeta = [
            { label: 'Pending', count: {{ (int) $stats['pending_projects'] }}, percent: '{{ $pendingPercent }}%', color: '#94a3b8' },
            { label: 'In Progress', count: {{ (int) $stats['in_progress_projects'] }}, percent: '{{ $inProgressPercent }}%', color: '#f59e0b' },
            { label: 'Completed', count: {{ (int) $stats['completed_projects'] }}, percent: '{{ $completedPercent }}%', color: '#10b981' }
        ];

        const totalProjectsCount = {{ (int) $stats['total_projects'] }};
        const hasStatusData = (statusMeta[0].count + statusMeta[1].count + statusMeta[2].count) > 0;

        const centerVal = document.getElementById('centerVal');
        const centerLbl = document.getElementById('centerLbl');
        const centerPct = document.getElementById('centerPct');
        const legendItems = document.querySelectorAll('.status-legend-item');

        function setCenterActive(index) {
            if (index !== null && statusMeta[index]) {
                const item = statusMeta[index];
                centerVal.textContent = item.count;
                centerVal.style.color = item.color;
                centerLbl.textContent = item.label;
                centerLbl.style.color = item.color;
                centerPct.textContent = item.percent + ' of total';
                centerPct.style.background = item.color + '18';
                centerPct.style.color = item.color;
                centerPct.style.display = 'inline-block';
            } else {
                centerVal.textContent = totalProjectsCount;
                centerVal.style.color = 'var(--text)';
                centerLbl.textContent = 'Total Projects';
                centerLbl.style.color = 'var(--muted)';
                centerPct.style.display = 'none';
            }
        }

        const chartData = hasStatusData 
            ? [statusMeta[0].count, statusMeta[1].count, statusMeta[2].count]
            : [1];
        const chartColors = hasStatusData 
            ? ['#94a3b8', '#f59e0b', '#10b981']
            : ['#e2e8f0'];
        const chartHoverColors = hasStatusData 
            ? ['#64748b', '#d97706', '#059669']
            : ['#e2e8f0'];

        const statusChart = new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In Progress', 'Completed'],
                datasets: [{
                    data: chartData,
                    backgroundColor: chartColors,
                    hoverBackgroundColor: chartHoverColors,
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: hasStatusData ? 6 : 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '74%',
                animation: {
                    duration: 600,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false } // Disabled in favor of interactive live center inspection
                },
                onHover: (event, activeElements) => {
                    if (!hasStatusData) return;
                    if (activeElements.length > 0) {
                        const idx = activeElements[0].index;
                        setCenterActive(idx);
                        legendItems.forEach((el, i) => {
                            el.classList.toggle('is-hovered', i === idx);
                        });
                    } else {
                        setCenterActive(null);
                        legendItems.forEach(el => el.classList.remove('is-hovered'));
                    }
                }
            }
        });

        // Two-way interaction: hovering legend item also highlights doughnut segment
        legendItems.forEach((item) => {
            const idx = parseInt(item.dataset.index, 10);
            item.addEventListener('mouseenter', () => {
                if (!hasStatusData) return;
                setCenterActive(idx);
                statusChart.setActiveElements([{ datasetIndex: 0, index: idx }]);
                statusChart.update('none');
            });
            item.addEventListener('mouseleave', () => {
                if (!hasStatusData) return;
                setCenterActive(null);
                statusChart.setActiveElements([]);
                statusChart.update('none');
            });
        });
    </script>
    <script>
        const revCanvas = document.getElementById('revenueChart');
        const revCtx = revCanvas.getContext('2d');
        const revGradient = revCtx.createLinearGradient(0, 0, 0, 220);
        revGradient.addColorStop(0, 'rgba(79, 70, 229, 0.22)');
        revGradient.addColorStop(1, 'rgba(79, 70, 229, 0.00)');

        new Chart(revCanvas, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyRevenue->pluck('label')) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($monthlyRevenue->pluck('total')) !!},
                    borderColor: '#4f46e5',
                    backgroundColor: revGradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2.5,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#4f46e5',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2,
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
                        displayColors: false,
                        callbacks: {
                            title: function(items) {
                                return items[0].label;
                            },
                            label: function(context) {
                                return 'Revenue: NPR ' + Number(context.raw).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return 'NPR ' + Number(value).toLocaleString(); }
                        },
                        grid: { color: '#f1f0fb' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>

</x-layouts.app>