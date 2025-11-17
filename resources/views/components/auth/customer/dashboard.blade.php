<div class="row">
    {{-- Top Row Cards --}}
    <div class="col-md-6 mb-4">
        <div class="dashboard-card green">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">My Points</h6>
            </div>
            <div class="dashboard-card-value">{{ $data['points']['total'] ?? 0 }}</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Available</span>
                    <span class="dashboard-metric-value">{{ $data['points']['available'] ?? 0 }}</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Redeemed</span>
                    <span class="dashboard-metric-value">{{ $data['points']['redeemed'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">My Scans</h6>
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
        <div class="dashboard-card purple">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">Available Offers</h6>
            </div>
            <div class="dashboard-card-value">{{ $data['offers']['available'] ?? 0 }}</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Redeemed</span>
                    <span class="dashboard-metric-value">{{ $data['offers']['redeemed'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">Rewards History</h6>
            </div>
            <div class="dashboard-card-value">-</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Total Rewards</span>
                    <span class="dashboard-metric-value">{{ ($data['offers']['redeemed'] ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="col-12">
        <div class="chart-section">
            <div class="chart-tabs">
                <button class="chart-tab active">Points History</button>
                <button class="chart-tab">Scans History</button>
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
    console.log('Customer Dashboard loaded');
});
</script>

