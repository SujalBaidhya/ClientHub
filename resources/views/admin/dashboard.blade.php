<x-layouts.app title="Admin Dashboard — ClientHub">

    <div class="dashboard">

        <div class="dashboard-welcome">
            <div>
                <p class="dashboard-eyebrow">Admin</p>
                <h1>Overview</h1>
                <p>A snapshot of everything happening across ClientHub.</p>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px;">

            <x-card>
                <span class="info-label">Total Clients</span>
                <h2 style="margin: 6px 0 0; font-size: 28px;">{{ $stats['total_clients'] }}</h2>
            </x-card>

            <x-card>
                <span class="info-label">Total Projects</span>
                <h2 style="margin: 6px 0 0; font-size: 28px;">{{ $stats['total_projects'] }}</h2>
            </x-card>

                       <x-card>
                <span class="info-label">Revenue Collected</span>
                <h2 style="margin: 6px 0 0; font-size: 30px; font-family: var(--font-display); color: var(--accent-gold);">
                    NPR {{ number_format($stats['total_revenue'], 0) }}
                </h2>
            </x-card>

            <x-card>
                <span class="info-label">Outstanding</span>
                <h2 style="margin: 6px 0 0; font-size: 28px; color: #dc2626;">
                    NPR {{ number_format($stats['outstanding_amount'], 0) }}
                </h2>
            </x-card>

        </div>

               {{-- Project Status Breakdown --}}
        <x-card title="Projects by Status">
            <div style="display: flex; gap: 40px; flex-wrap: wrap; align-items: center;">
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div>
                        <x-status-badge status="pending" />
                        <strong style="margin-left: 8px;">{{ $stats['pending_projects'] }}</strong>
                    </div>
                    <div>
                        <x-status-badge status="in_progress" />
                        <strong style="margin-left: 8px;">{{ $stats['in_progress_projects'] }}</strong>
                    </div>
                    <div>
                        <x-status-badge status="completed" />
                        <strong style="margin-left: 8px;">{{ $stats['completed_projects'] }}</strong>
                    </div>
                    @if($stats['overdue_invoices'] > 0)
                        <div>
                            <x-status-badge status="overdue" />
                            <strong style="margin-left: 8px;">{{ $stats['overdue_invoices'] }} invoice(s)</strong>
                        </div>
                    @endif
                </div>

                              <div style="width: 260px; height: 260px; position: relative;">
                    <canvas id="statusChart"></canvas>
                    <div id="chartCenterLabel" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                                            <div style="font-size: 28px; font-weight: 800; color: var(--text); line-height: 1;">{{ $stats['total_projects'] }}</div>
                        <div style="font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px;">Total</div> </div>
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
                <div class="file-list">
                    @foreach($recentProjects as $project)
                        <div class="file-item">
                            <div>
                                <strong>{{ $project->name }}</strong>
                                <span>Client: {{ $project->client->name ?? 'N/A' }}</span>
                            </div>
                            <x-status-badge :status="$project->status" />
                        </div>
                    @endforeach
                </div>
            @endif

        </x-card>

        <div style="margin-top: 10px;">
            <a href="{{ route('admin.projects.index') }}" class="btn">View All Projects →</a>
        </div>
    </div>

      <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
        const statusTotal = {{ $stats['pending_projects'] + $stats['in_progress_projects'] + $stats['completed_projects'] }};

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
                    hoverOffset: 10,
                }]
            },
            options: {
                cutout: '80%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            font: { size: 13, weight: '600' },
                            usePointStyle: true,
                            pointStyle: 'circle',
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const percent = statusTotal > 0 ? Math.round((value / statusTotal) * 100) : 0;
                                return ` ${context.label}: ${value} (${percent}%)`;
                            }
                        },
                        backgroundColor: '#1e1b4b',
                        padding: 12,
                        titleFont: { size: 13 },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                }
            }
        });
    </script>

</x-layouts.app>