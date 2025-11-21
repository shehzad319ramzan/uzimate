@php
    $filters = $filters ?? [];
    $filterOptions = $filterOptions ?? [];
    $stats = $stats ?? [];
    $chartData = $stats['chart'] ?? ['labels' => [], 'values' => [], 'title' => 'Activity'];
    $chartId = 'dashboardChart_' . uniqid();
    $currentDateRange = $filters['date_range'] ?? 'all';
    $startDate = $filters['start_date'] ?? '';
    $endDate = $filters['end_date'] ?? '';
@endphp

{{-- Date Range Filter Buttons --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    {{-- Date Range Buttons --}}
                    <div class="btn-group" role="group" aria-label="Date Range Filter">
                        <a href="{{ route('auth', ['date_range' => 'all'] + request()->except(['date_range', 'start_date', 'end_date'])) }}" 
                           class="btn {{ $currentDateRange === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                            All Results
                        </a>
                        <a href="{{ route('auth', ['date_range' => 'this_month'] + request()->except(['date_range', 'start_date', 'end_date'])) }}" 
                           class="btn {{ $currentDateRange === 'this_month' ? 'btn-primary' : 'btn-outline-primary' }}">
                            This Month
                        </a>
                        <a href="{{ route('auth', ['date_range' => 'this_year'] + request()->except(['date_range', 'start_date', 'end_date'])) }}" 
                           class="btn {{ $currentDateRange === 'this_year' ? 'btn-primary' : 'btn-outline-primary' }}">
                            This Year
                        </a>
                        <button type="button" class="btn {{ $currentDateRange === 'custom' ? 'btn-primary' : 'btn-outline-primary' }}" 
                                data-bs-toggle="collapse" data-bs-target="#customDateRange" aria-expanded="false">
                            Custom Range
                        </button>
                    </div>

                    {{-- Other Filters --}}
                    <div class="d-flex flex-wrap gap-2 ms-auto">
                        @if (!empty($filterOptions['merchants']))
                            <form method="GET" action="{{ route('auth') }}" class="d-inline-block">
                                @foreach (request()->except('merchant_id') as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <select name="merchant_id" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                                    <option value="">All Merchants</option>
                                    @foreach ($filterOptions['merchants'] as $merchant)
                                        <option value="{{ $merchant->id }}" {{ ($filters['merchant_id'] ?? '') == $merchant->id ? 'selected' : '' }}>
                                            {{ $merchant->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @endif

                        @if (!empty($filterOptions['modules']))
                            <form method="GET" action="{{ route('auth') }}" class="d-inline-block">
                                @foreach (request()->except('module') as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <select name="module" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                                    @foreach ($filterOptions['modules'] as $value => $label)
                                        <option value="{{ $value }}" {{ ($filters['module'] ?? 'merchants') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Custom Date Range Collapse --}}
                <div class="collapse mt-3 {{ $currentDateRange === 'custom' ? 'show' : '' }}" id="customDateRange">
                    <form method="GET" action="{{ route('auth') }}" class="row g-3">
                        <input type="hidden" name="date_range" value="custom">
                        @foreach (request()->except(['start_date', 'end_date', 'date_range']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control" required />
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Apply</button>
                            <a href="{{ route('auth', ['date_range' => 'all'] + request()->except(['date_range', 'start_date', 'end_date'])) }}" 
                               class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="row">
    {{-- Top Row Cards --}}
    <div class="col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">Merchants</h6>
            </div>
            <div class="dashboard-card-value">{{ $stats['merchants']['total'] ?? 0 }}</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">This Month</span>
                    <span class="dashboard-metric-value">{{ $stats['merchants']['this_month'] ?? 0 }}</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">This Year</span>
                    <span class="dashboard-metric-value">{{ $stats['merchants']['this_year'] ?? 0 }}</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Sites</span>
                    <span class="dashboard-metric-value">{{ $stats['merchants']['sites'] ?? 0 }}</span>
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
                    <span class="dashboard-metric-label">Merchants</span>
                    <span class="dashboard-metric-value">{{ $stats['site_users']['merchants'] ?? 0 }}</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Admins</span>
                    <span class="dashboard-metric-value">{{ $stats['site_users']['admins'] ?? 0 }}</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Super Admins</span>
                    <span class="dashboard-metric-value">{{ $stats['site_users']['super_admins'] ?? 0 }}</span>
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
                <button class="chart-tab active">{{ $chartData['title'] ?? 'Activity' }}</button>
            </div>

            <div class="chart-filters">
                <span class="text-muted small">Trend based on selected module</span>
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
            const canvas = document.getElementById(canvasId);
            if (!canvas || !window.Chart) return;

            const ctx = canvas.getContext('2d');

            // Destroy existing chart instance on same canvas to avoid duplication
            if (canvas._chartInstance) {
                canvas._chartInstance.destroy();
            }

            canvas._chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: payload.labels ?? [],
                    datasets: [{
                        label: payload.title ?? 'Activity',
                        data: payload.values ?? [],
                        borderColor: '#4A148D',
                        backgroundColor: 'rgba(74, 20, 141, 0.15)',
                        tension: 0.4,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
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
                document.getElementById('chartjs-script')
                    .addEventListener('load', renderChart, {
                        once: true
                    });
            }
        }

        loadChartJsAndRender();
    })();
</script>

{{-- @endpush --}}
