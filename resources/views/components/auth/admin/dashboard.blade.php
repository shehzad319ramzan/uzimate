<div class="row">
    {{-- Top Row Cards --}}
    <div class="col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">Merchants</h6>
            </div>
            <div class="dashboard-card-value">9</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">This Month</span>
                    <span class="dashboard-metric-value">0</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">This Year</span>
                    <span class="dashboard-metric-value">0</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Sites</span>
                    <span class="dashboard-metric-value">12</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">Site Users</h6>
            </div>
            <div class="dashboard-card-value">22</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Merchants</span>
                    <span class="dashboard-metric-value">9</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Admins</span>
                    <span class="dashboard-metric-value">10</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Super Admins</span>
                    <span class="dashboard-metric-value">3</span>
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
            <div class="dashboard-card-value">10</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Active</span>
                    <span class="dashboard-metric-value">0</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Expired</span>
                    <span class="dashboard-metric-value">10</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="dashboard-card green">
            <div class="dashboard-card-header">
                <h6 class="dashboard-card-title">Customers</h6>
            </div>
            <div class="dashboard-card-value">20</div>
            <div class="dashboard-card-metrics">
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Active</span>
                    <span class="dashboard-metric-value">20</span>
                </div>
                <div class="dashboard-metric">
                    <span class="dashboard-metric-label">Inactive</span>
                    <span class="dashboard-metric-value">0</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="col-12">
        <div class="chart-section">
            <div class="chart-tabs">
                <button class="chart-tab active">Customers</button>
                <button class="chart-tab">Merchants</button>
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
// Chart will be initialized here if needed
document.addEventListener('DOMContentLoaded', function() {
    // Chart initialization code can go here
    console.log('Dashboard loaded');
});
</script>

