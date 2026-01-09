@php
    $filters = $filters ?? [];
    $searchValue = $filters['search'] ?? '';
    $filterMerchant = $filters['merchant_id'] ?? '';
    $filterSite = $filters['site_id'] ?? '';
    $filterActionType = $filters['action_type'] ?? '';
    $filterActionCategory = $filters['action_category'] ?? '';
    $filterDateFrom = $filters['date_from'] ?? '';
    $filterDateTo = $filters['date_to'] ?? '';
    $hasActions = auth()->user()?->can('view_customer_log');
@endphp

<x-layouts.auth>
    <x-slot name="pageTitle">Customer Logs</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Customer Logs" :data="$data['all']">
                <x-slot name="headerCustom">
                    <form method="GET" action="{{ route('customerlogs.index') }}" class="row g-2 align-items-end w-100 mb-3">
                        <div class="col-lg-2 col-md-6">
                            <label for="search" class="form-label text-muted small">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Search by customer or description" value="{{ $searchValue }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="merchant_filter" class="form-label text-muted small">Merchants</label>
                            <select name="merchant_id" id="merchant_filter" class="form-select">
                                <option value="">All Merchants</option>
                                @foreach($merchants as $merchant)
                                    <option value="{{ $merchant->id }}" {{ (string)$filterMerchant === (string)$merchant->id ? 'selected' : '' }}>
                                        {{ $merchant->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="site_filter" class="form-label text-muted small">Sites</label>
                            <select name="site_id" id="site_filter" class="form-select">
                                <option value="">All Sites</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}"
                                        data-merchant="{{ $site->merchant_id }}"
                                        {{ (string)$filterSite === (string)$site->id ? 'selected' : '' }}>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="action_type" class="form-label text-muted small">Action Type</label>
                            <select name="action_type" id="action_type" class="form-select">
                                <option value="">All Types</option>
                                @foreach($actionTypes as $key => $label)
                                    <option value="{{ $key }}" {{ $filterActionType === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="action_category" class="form-label text-muted small">Category</label>
                            <select name="action_category" id="action_category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($actionCategories as $key => $label)
                                    <option value="{{ $key }}" {{ $filterActionCategory === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="date_from" class="form-label text-muted small">Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $filterDateFrom }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="date_to" class="form-label text-muted small">Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $filterDateTo }}">
                        </div>
                        <div class="col-lg-12 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 mt-3">Apply Filters</button>
                            <a href="{{ route('customerlogs.index') }}" class="btn btn-outline-secondary mt-3">Reset</a>
                        </div>
                    </form>

                    <div class="alert alert-info mb-3 p-2">
                        <h6 class="alert-heading mb-2"><i class="fas fa-info-circle me-2"></i>Customer Logs Information</h6>
                        <ul class="mb-0 small">
                            <li><i class="fas fa-check-circle text-success me-2"></i>Logs are <strong>automatically created</strong> - no manual entry needed</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i><strong>Point Awards</strong> → Creates log when admin awards points</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i><strong>Spin Completed</strong> → Creates log when customer spins</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i><strong>Customer Login</strong> → Creates log when customer logs in</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i><strong>Customer Logout</strong> → Creates log when customer logs out</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>All logs created <strong>instantly</strong> when action happens</li>
                        </ul>
                    </div>
                </x-slot>

                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date/Time</th>
                            <th>Customer</th>
                            <th>Action Type</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Points</th>
                            <th>Site</th>
                            @if($hasActions)
                                <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['all'] as $index => $log)
                            <tr>
                                <td>{{ $index + 1 + (($data['all']->currentPage() - 1) * $data['all']->perPage()) }}</td>
                                <td>{{ optional($log->created_at)->format('d M Y, H:i') }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $log->user->full_name ?? '-' }}</div>
                                    <small class="text-muted">{{ $log->user->email ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($log->action_type) {
                                            'point_earned' => 'bg-success',
                                            'point_redeemed' => 'bg-danger',
                                            'point_expired' => 'bg-warning',
                                            'spin_completed' => 'bg-info',
                                            'offer_redeemed' => 'bg-primary',
                                            'qr_code_scanned' => 'bg-secondary',
                                            default => 'bg-dark',
                                        };
                                        $actionLabel = $actionTypes[$log->action_type] ?? ucfirst(str_replace('_', ' ', $log->action_type));
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $actionLabel }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ ucfirst($log->action_category) }}</span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $log->description }}">
                                        {{ $log->description }}
                                    </div>
                                </td>
                                <td>
                                    @if($log->points_affected !== null)
                                        @if($log->points_affected > 0)
                                            <span class="badge bg-success">+{{ number_format($log->points_affected) }}</span>
                                        @elseif($log->points_affected < 0)
                                            <span class="badge bg-danger">{{ number_format($log->points_affected) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->site)
                                        <div class="fw-semibold">{{ $log->site->name }}</div>
                                        <small class="text-muted">
                                            Merchant: {{ $log->merchant->name ?? $log->site->merchant->name ?? '-' }}
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                @if($hasActions)
                                    <td class="text-center">
                                        <div class="d-inline-block dropdown">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-display="static">
                                                <i class="fas fa-ellipsis-v bg-light rounded p-2"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @can('view_customer_log')
                                                <a class="dropdown-item" href="{{ route('customerlogs.show', $log->id) }}">
                                                    <i class="fas fa-eye me-2 text-primary"></i> View Details
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </x-auth.datatable>

                @if($data['all']->isEmpty())
                    <div class="alert alert-light text-center mb-0 px-3">
                        No customer logs found.
                    </div>
                @endif
            </x-all-list>
        </div>
    </div>
</x-layouts.auth>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const merchantFilter = document.getElementById('merchant_filter');
        const siteFilter = document.getElementById('site_filter');

        if (!merchantFilter || !siteFilter) {
            return;
        }

        const filterSites = () => {
            const merchantId = merchantFilter.value;
            const selectedSite = siteFilter.value;
            const options = siteFilter.querySelectorAll('option[value]');

            options.forEach(option => {
                if (option.value === '') {
                    return;
                }
                const dataMerchant = option.getAttribute('data-merchant');
                option.style.display = merchantId === '' || dataMerchant === merchantId ? 'block' : 'none';
            });

            if (selectedSite) {
                const selectedOption = siteFilter.querySelector(`option[value="${selectedSite}"]`);
                if (selectedOption && selectedOption.style.display === 'none') {
                    siteFilter.value = '';
                }
            }
        };

        merchantFilter.addEventListener('change', filterSites);
        filterSites();
    });
</script>

