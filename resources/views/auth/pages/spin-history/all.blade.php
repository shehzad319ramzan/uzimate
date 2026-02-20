@php
    $filters = $filters ?? [];
    $searchValue = $filters['search'] ?? '';
    $filterMerchant = $filters['merchant_id'] ?? '';
    $filterSite = $filters['site_id'] ?? '';
    $filterResultType = $filters['spin_result_type'] ?? '';
    $filterDateFrom = $filters['date_from'] ?? '';
    $filterDateTo = $filters['date_to'] ?? '';
    $hasActions = auth()->user()?->can('view_spin_history')
        || auth()->user()?->can('edit_spin_history')
        || auth()->user()?->can('delete_spin_history');
@endphp

<x-layouts.auth>
    <x-slot name="pageTitle">Spin History</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Spin History" :data="$data['all']">
                <x-slot name="headerCustom">
                    <form method="GET" action="{{ route('spinhistories.index') }}" class="row g-2 align-items-end w-100 mb-3">
                        <div class="col-lg-2 col-md-6">
                            <label for="search" class="form-label text-muted small">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Search by customer" value="{{ $searchValue }}">
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
                            <label for="spin_result_type" class="form-label text-muted small">Result Type</label>
                            <select name="spin_result_type" id="spin_result_type" class="form-select">
                                <option value="">All Types</option>
                                @foreach($resultTypes as $key => $label)
                                    <option value="{{ $key }}" {{ $filterResultType === $key ? 'selected' : '' }}>
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
                            <a href="{{ route('spinhistories.index') }}" class="btn btn-outline-secondary mt-3">Reset</a>
                        </div>
                    </form>

                    {{-- @can('add_spin_history')
                        <x-auth.href-link link-href="{{ route('spinhistories.create') }}" link-value="{{ __('Add Spin History') }}"
                            link-class="btn btn-primary" />
                    @endcan --}}
                </x-slot>

                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date/Time</th>
                            <th>Customer</th>
                            <th>Site</th>
                            <th>Spin #</th>
                            <th>Result Type</th>
                            <th>Reward</th>
                            <th>Status</th>
                            @if($hasActions)
                                <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['all'] as $index => $spin)
                            <tr>
                                <td>{{ $index + 1 + (($data['all']->currentPage() - 1) * $data['all']->perPage()) }}</td>
                                <td>{{ optional($spin->created_at)->format('d M Y, H:i') }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $spin->user->full_name ?? '-' }}</div>
                                    <small class="text-muted">{{ $spin->user->email ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $spin->site->name ?? '-' }}</div>
                                    <small class="text-muted">
                                        Merchant: {{ $spin->merchant->name ?? $spin->site->merchant->name ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-info">#{{ $spin->spin_number }}</span>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($spin->spin_result_type) {
                                            'points' => 'bg-success',
                                            'offer' => 'bg-primary',
                                            'discount' => 'bg-warning',
                                            default => 'bg-secondary',
                                        };
                                        $resultLabel = $resultTypes[$spin->spin_result_type] ?? ucfirst($spin->spin_result_type);
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $resultLabel }}</span>
                                </td>
                                <td>
                                    @if($spin->spin_result_type === 'points')
                                        <span class="badge bg-success">{{ number_format($spin->points_earned) }} Points</span>
                                    @elseif($spin->spin_result_type === 'offer')
                                        <div class="fw-semibold">{{ $spin->offer->title ?? 'N/A' }}</div>
                                    @elseif($spin->spin_result_type === 'discount' && $spin->reward_value)
                                        <span class="badge bg-warning">{{ number_format($spin->reward_value, 2) }}%</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($spin->is_eligible)
                                        <span class="badge bg-success">Eligible</span>
                                    @else
                                        <span class="badge bg-danger">Not Eligible</span>
                                    @endif
                                </td>
                                @if($hasActions)
                                    <td class="text-center">
                                        <div class="d-inline-block dropdown">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-display="static">
                                                <i class="fas fa-ellipsis-v bg-light rounded p-2"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @can('view_spin_history')
                                                <a class="dropdown-item" href="{{ route('spinhistories.show', $spin->id) }}">
                                                    <i class="fas fa-eye me-2 text-primary"></i> View Details
                                                </a>
                                                @endcan

                                                @can('edit_spin_history')
                                                <a class="dropdown-item" href="{{ route('spinhistories.edit', $spin->id) }}">
                                                    <i class="fas fa-edit me-2 text-warning"></i> Edit
                                                </a>
                                                @endcan

                                                @can('delete_spin_history')
                                                <form action="{{ route('spinhistories.destroy', $spin->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this spin history?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash-alt me-2"></i> Delete
                                                    </button>
                                                </form>
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
                        No spin history found.
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


