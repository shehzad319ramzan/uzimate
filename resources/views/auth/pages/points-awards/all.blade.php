@php
    $filters = $filters ?? [];
    $searchValue = $filters['search'] ?? '';
    $filterMerchant = $filters['merchant_id'] ?? '';
    $filterSite = $filters['site_id'] ?? '';
    $filterDateFrom = $filters['date_from'] ?? '';
    $filterDateTo = $filters['date_to'] ?? '';
    $hasActions = auth()->user()?->can('view_point_award')
        || auth()->user()?->can('edit_point_award')
        || auth()->user()?->can('delete_point_award');
@endphp

<x-layouts.auth>
    <x-slot name="pageTitle">Point Awards</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Point Awards" :data="$data['all']">
                <x-slot name="headerCustom">
                    <form method="GET" action="{{ route('pointawards.index') }}" class="row g-2 align-items-end w-100 mb-3">
                        <div class="col-lg-3 col-md-6">
                            <label for="search" class="form-label text-muted small">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Search point awards" value="{{ $searchValue }}">
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
                            <label for="date_from" class="form-label text-muted small">Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $filterDateFrom }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="date_to" class="form-label text-muted small">Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $filterDateTo }}">
                        </div>
                        <div class="col-lg-3 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 mt-3">Apply Filters</button>
                            <a href="{{ route('pointawards.index') }}" class="btn btn-outline-secondary mt-3">Reset</a>
                        </div>
                    </form>

                    @can('add_point_award')
                        <x-auth.href-link link-href="{{ route('pointawards.create') }}" link-value="{{ __('Add Point Award') }}"
                            link-class="btn btn-primary" />
                    @endcan
                </x-slot>

                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Points</th>
                            <th>User</th>
                            <th>Site</th>
                            <th>Awarded By</th>
                            <th>Awarded At</th>
                            @if($hasActions)
                                <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['all'] as $index => $award)
                            <tr>
                                <td>{{ $index + 1 + (($data['all']->currentPage() - 1) * $data['all']->perPage()) }}</td>
                                <td>
                                    <span class="badge bg-success">{{ number_format($award->points_earned) }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $award->user->full_name ?? '-' }}</div>
                                    <small class="text-muted">{{ $award->user->email ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $award->site->name ?? '-' }}</div>
                                    <small class="text-muted">
                                        Merchant: {{ $award->merchant->name ?? $award->site->merchant->name ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    @if($award->awardedBy)
                                        <div class="fw-semibold">{{ $award->awardedBy->full_name }}</div>
                                        <small class="text-muted">{{ $award->awardedBy->email }}</small>
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>{{ optional($award->created_at)->format('d M Y, H:i') }}</td>
                                @if($hasActions)
                                    <td class="text-center">
                                        <div class="d-inline-block dropdown">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-display="static">
                                                <i class="fas fa-ellipsis-v bg-light rounded p-2"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @can('view_point_award')
                                                <a class="dropdown-item" href="{{ route('pointawards.show', $award->id) }}">
                                                    <i class="fas fa-eye me-2 text-primary"></i> View Details
                                                </a>
                                                @endcan

                                                @can('edit_point_award')
                                                <a class="dropdown-item" href="{{ route('pointawards.edit', $award->id) }}">
                                                    <i class="fas fa-edit me-2 text-warning"></i> Edit
                                                </a>
                                                @endcan

                                                @can('delete_point_award')
                                                <form action="{{ route('pointawards.destroy', $award->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this point award?');">
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
                        No point awards found.
                    </div>
                @endif
            </x-all-list>
        </div>
    </div>
</x-layouts.auth>

{{-- @push('auth_scripts') --}}
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
{{-- @endpush --}}

