<div class="row">
    {{-- Top Row Cards --}}
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
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">Customer Scans</h6>
            </div>
            <div class="dashboard-card-value">{{ $data['scans']['total'] ?? 0 }}</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">This Month</span>
                    <span class="dashboard-metric-value">{{ $data['scans']['this_month'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Second Row Cards --}}
    <div class="col-md-6 mb-4">
        <div class="dashboard-card green">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">Points Awarded</h6>
            </div>
            <div class="dashboard-card-value">{{ $data['points']['total'] ?? 0 }}</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">This Month</span>
                    <span class="dashboard-metric-value">{{ $data['points']['this_month'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">Activity Summary</h6>
            </div>
            <div class="dashboard-card-value">-</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Today's Scans</span>
                    <span class="dashboard-metric-value">0</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Today's Points</span>
                    <span class="dashboard-metric-value">0</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="col-12">
        <div class="chart-section">
            <div class="chart-tabs">
                <button class="chart-tab active">Scans</button>
                <button class="chart-tab">Points</button>
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
    console.log('Admin Dashboard loaded');
});
</script>

