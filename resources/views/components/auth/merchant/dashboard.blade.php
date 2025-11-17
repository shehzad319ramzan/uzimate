@php
    $stats = $stats ?? [];
    $filters = $filters ?? [];
    $filterOptions = $filterOptions ?? [];
    $chartData = $stats['chart'] ?? ['labels' => [], 'values' => [], 'title' => 'Sites created'];
    $chartId = 'dashboardChart_' . uniqid();
    $startDate = $filters['start_date'] ?? now()->subDays(29)->format('Y-m-d');
    $endDate = $filters['end_date'] ?? now()->format('Y-m-d');
@endphp

<form method="GET" action="{{ route('auth') }}" class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">Start Date</label>
        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control" />
    </div>
    <div class="col-md-4">
        <label class="form-label">End Date</label>
        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control" />
    </div>
    <div class="col-md-3">
        <label class="form-label">Site</label>
        <select name="site_id" class="form-select">
            <option value="all">All Sites</option>
            @foreach($filterOptions['sites'] ?? [] as $site)
                <option value="{{ $site->id }}" {{ ($filters['site_id'] ?? 'all') == $site->id ? 'selected' : '' }}>
                    {{ $site->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-1 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Apply</button>
    </div>
    @if(request()->query())
        <div class="col-12 text-end">
            <a href="{{ route('auth') }}" class="btn btn-link p-0">Reset Filters</a>
        </div>
    @endif
</form>



<div class="row">
    {{-- Top Row Cards --}}
    <div class="col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">My Sites</h6>
            </div>
            <div class="dashboard-card-value">{{ $stats['sites']['total'] ?? 0 }}</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">This Month</span>
                    <span class="dashboard-metric-value">{{ $stats['sites']['this_month'] ?? 0 }}</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">This Year</span>
                    <span class="dashboard-metric-value">{{ $stats['sites']['this_year'] ?? 0 }}</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Site Users</span>
                    <span class="dashboard-metric-value">{{ $stats['sites']['site_users'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">Site Users</h6>
            </div>
            <div class="dashboard-card-value">{{ $stats['site_users']['total'] ?? 0 }}</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">This Month</span>
                    <span class="dashboard-metric-value">{{ $stats['site_users']['this_month'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Middle Row Cards --}}
    <div class="col-md-6 mb-4">
        <div class="dashboard-card purple">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">Offers</h6>
            </div>
            <div class="dashboard-card-value">{{ $stats['offers']['total'] ?? 0 }}</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Active</span>
                    <span class="dashboard-metric-value">{{ $stats['offers']['active'] ?? 0 }}</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Expired</span>
                    <span class="dashboard-metric-value">{{ $stats['offers']['expired'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="dashboard-card green">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">Customers</h6>
            </div>
            <div class="dashboard-card-value">{{ $stats['customers']['total'] ?? 0 }}</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Active</span>
                    <span class="dashboard-metric-value">{{ $stats['customers']['active'] ?? 0 }}</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Inactive</span>
                    <span class="dashboard-metric-value">{{ $stats['customers']['inactive'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="col-12">
        <div class="chart-section">
            <div class="chart-tabs">
                <button class="chart-tab active">{{ $chartData['title'] ?? 'Sites created' }}</button>
            </div>

            <div class="chart-filters">
                <span class="text-muted small">Sites trend within selected range</span>
            </div>

            <div class="chart-container" style="min-height: 300px; position: relative;">
                <canvas id="{{ $chartId }}"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- @push('auth_scripts') --}}
<script>
(function() {
    const canvasId = '{{ $chartId }}';
    const payload = @json($chartData);

    function renderChart() {
        const ctx = document.getElementById(canvasId);
        if (!ctx || !window.Chart) {
            return;
        }
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: payload.labels || [],
                datasets: [{
                    label: payload.title || 'Activity',
                    data: payload.values || [],
                    borderColor: '#4A148D',
                    backgroundColor: 'rgba(74, 20, 141, 0.15)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    function loadChartJsAndRender() {
        if (window.Chart) {
            renderChart();
            return;
        }
        if (!document.getElementById('chartjs-script')) {
            const script = document.createElement('script');
            script.id = 'chartjs-script';
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = renderChart;
            document.body.appendChild(script);
        } else {
            document.getElementById('chartjs-script').addEventListener('load', renderChart, { once: true });
        }
    }

    loadChartJsAndRender();
})();
</script>
{{-- @endpush --}}

