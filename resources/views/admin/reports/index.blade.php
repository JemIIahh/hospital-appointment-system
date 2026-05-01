<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 mb-0">Reports</h2>
            <p class="text-muted small mb-0">Hospital activity at a glance</p>
        </div>
    </x-slot>

    {{-- KPI cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card card-stat" style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Total Appointments</div>
                        <div class="stat-number">{{ number_format($kpis['total']) }}</div>
                    </div>
                    <i class="bi bi-calendar-check stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-stat" style="background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">This Month</div>
                        <div class="stat-number">{{ number_format($kpis['this_month']) }}</div>
                    </div>
                    <i class="bi bi-calendar-month stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-stat" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Completion Rate</div>
                        <div class="stat-number">{{ $kpis['completion_rate'] }}%</div>
                    </div>
                    <i class="bi bi-check2-circle stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-stat" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">No-Show Rate</div>
                        <div class="stat-number">{{ $kpis['no_show_rate'] }}%</div>
                    </div>
                    <i class="bi bi-person-x stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    @if($kpis['total'] === 0)
        <div class="card">
            <div class="empty-state">
                <i class="bi bi-bar-chart"></i>
                <p class="text-muted mb-1">No appointments yet — charts will appear once bookings are made.</p>
                <p class="text-muted small mb-0">Run <code>php artisan migrate:fresh --seed</code> to populate demo appointment data.</p>
            </div>
        </div>
    @else
        {{-- Charts row 1 --}}
        <div class="row g-3 mb-3">
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <strong>Appointments by Status</strong>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="240"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <strong>Appointments by Department</strong>
                    </div>
                    <div class="card-body">
                        <canvas id="departmentChart" height="240"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts row 2 --}}
        <div class="row g-3 mb-3">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <strong>Bookings &mdash; Last 30 Days</strong>
                    </div>
                    <div class="card-body">
                        <canvas id="last30Chart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <strong>Top 5 Doctors</strong>
                    </div>
                    <div class="card-body">
                        <canvas id="topDoctorsChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
        <script>
            (function() {
                Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
                Chart.defaults.color = '#64748b';

                const palette = {
                    indigo:  '#4f46e5',
                    blue:    '#3b82f6',
                    cyan:    '#06b6d4',
                    green:   '#10b981',
                    yellow:  '#f59e0b',
                    red:     '#ef4444',
                    grey:    '#94a3b8',
                };

                // Pie: status
                new Chart(document.getElementById('statusChart'), {
                    type: 'doughnut',
                    data: {
                        labels: @json($statusBreakdown['labels']),
                        datasets: [{
                            data: @json($statusBreakdown['data']),
                            backgroundColor: [palette.yellow, palette.cyan, palette.green, palette.grey, palette.red],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom', labels: { padding: 14, usePointStyle: true } }
                        }
                    }
                });

                // Bar: department
                new Chart(document.getElementById('departmentChart'), {
                    type: 'bar',
                    data: {
                        labels: @json($byDepartment['labels']),
                        datasets: [{
                            label: 'Appointments',
                            data: @json($byDepartment['data']),
                            backgroundColor: palette.indigo,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 } },
                            x: { grid: { display: false } }
                        }
                    }
                });

                // Line: last 30 days
                new Chart(document.getElementById('last30Chart'), {
                    type: 'line',
                    data: {
                        labels: @json($last30Days['labels']),
                        datasets: [{
                            label: 'Bookings',
                            data: @json($last30Days['data']),
                            borderColor: palette.indigo,
                            backgroundColor: 'rgba(79, 70, 229, 0.08)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 } },
                            x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10 } }
                        }
                    }
                });

                // Horizontal bar: top doctors
                new Chart(document.getElementById('topDoctorsChart'), {
                    type: 'bar',
                    data: {
                        labels: @json($topDoctors['labels']),
                        datasets: [{
                            label: 'Appointments',
                            data: @json($topDoctors['data']),
                            backgroundColor: palette.cyan,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { beginAtZero: true, ticks: { precision: 0 } },
                            y: { grid: { display: false } }
                        }
                    }
                });
            })();
        </script>
    @endif
</x-app-layout>
