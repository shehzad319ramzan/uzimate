<div class="row">
    {{-- Top Row Cards --}}
    <div class="col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">My Sites</h6>
            </div>
            <div class="dashboard-card-value">{{ $data['sites']['total'] ?? 0 }}</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">This Month</span>
                    <span class="dashboard-metric-value">{{ $data['sites']['this_month'] ?? 0 }}</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">This Year</span>
                    <span class="dashboard-metric-value">{{ $data['sites']['this_year'] ?? 0 }}</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Site Users</span>
                    <span class="dashboard-metric-value">{{ $data['sites']['site_users'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">Site Users</h6>
            </div>
            <div class="dashboard-card-value">{{ $data['site_users']['total'] ?? 0 }}</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">This Month</span>
                    <span class="dashboard-metric-value">{{ $data['site_users']['this_month'] ?? 0 }}</span>
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
            <div class="dashboard-card-value">{{ $data['offers']['total'] ?? 0 }}</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Active</span>
                    <span class="dashboard-metric-value">{{ $data['offers']['active'] ?? 0 }}</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Expired</span>
                    <span class="dashboard-metric-value">{{ $data['offers']['expired'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="dashboard-card green">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">Customers</h6>
            </div>
            <div class="dashboard-card-value">{{ $data['customers']['total'] ?? 0 }}</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Active</span>
                    <span class="dashboard-metric-value">{{ $data['customers']['active'] ?? 0 }}</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Inactive</span>
                    <span class="dashboard-metric-value">{{ $data['customers']['inactive'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="col-12">
        <div class="chart-section">
            <div class="chart-tabs">
                <button class="chart-tab active">Offers</button>
                <button class="chart-tab">Customers</button>
            </div>
            
            <div class="chart-filters">
                <select class="form-select" style="max-width: 150px;">
                    <option>Nov</option>
                </select>
                <select class="form-select" style="max-width: 150px;">
                    <option>2025</option>
                </select>
                <select class="form-select" style="max-width: 150px;">
                    <option>Year View</option>
                </select>
            </div>

            <div class="chart-container" style="min-height: 300px; position: relative;">
                <canvas id="dashboardChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Merchant Dashboard loaded');
});
</script>

