@php
    $filters = $filters ?? [];
    $searchValue = $filters['search'] ?? '';
    $filterMerchant = $filters['merchant_id'] ?? '';
    $filterSite = $filters['site_id'] ?? '';
    $filterDateFrom = $filters['date_from'] ?? '';
    $filterDateTo = $filters['date_to'] ?? '';
@endphp

<x-layouts.auth>
    <x-slot name="pageTitle">Scans</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Scans" :data="$data['all']">
                <x-slot name="headerCustom">
                    <form method="GET" action="{{ route('customer-scans.index') }}" class="row g-2 align-items-end w-100 mb-3">
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
                            <label for="date_from" class="form-label text-muted small">Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $filterDateFrom }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="date_to" class="form-label text-muted small">Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $filterDateTo }}">
                        </div>
                        <div class="col-lg-12 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 mt-3">Apply Filters</button>
                            <a href="{{ route('customer-scans.index') }}" class="btn btn-outline-secondary mt-3">Reset</a>
                        </div>
                    </form>

                    <div class="alert alert-info mb-3 p-2">
                        <h6 class="alert-heading mb-2"><i class="fas fa-qrcode me-2"></i>Scans</h6>
                        <p class="mb-0 small">Shows all QR code scans. Each row is a customer scanning an offer to earn points.</p>
                    </div>
                </x-slot>

                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date/Time</th>
                            <th>Customer</th>
                            <th>Offer</th>
                            <th>Merchant & Site</th>
                            <th>Points</th>
                            <th>Action</th>
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
                                    @php $offer = $log->offer ?? ($log->relatedModel instanceof \App\Models\Offer ? $log->relatedModel : null); @endphp
                                    @if($offer)
                                        <a href="{{ route('offers.show', $offer->id) }}" class="text-dark fw-semibold">{{ $offer->title ?? '-' }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->site)
                                        <div class="fw-semibold">{{ $log->merchant->name ?? $log->site->merchant->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $log->site->name ?? '-' }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->points_affected !== null && $log->points_affected > 0)
                                        <span class="badge bg-success">+{{ number_format($log->points_affected) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @can('view_customer_log')
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('customerlogs.show', $log->id) }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-auth.datatable>

                @if($data['all']->isEmpty())
                    <div class="alert alert-light text-center mb-0 px-3">
                        No customer scans found.
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

        if (!merchantFilter || !siteFilter) return;

        const filterSites = () => {
            const merchantId = merchantFilter.value;
            const options = siteFilter.querySelectorAll('option[value]');
            options.forEach(option => {
                if (option.value === '') return;
                const dataMerchant = option.getAttribute('data-merchant');
                option.style.display = merchantId === '' || dataMerchant === merchantId ? 'block' : 'none';
            });
            const selectedSite = siteFilter.value;
            if (selectedSite) {
                const selectedOption = siteFilter.querySelector('option[value="' + selectedSite + '"]');
                if (selectedOption && selectedOption.style.display === 'none') siteFilter.value = '';
            }
        };

        merchantFilter.addEventListener('change', filterSites);
        filterSites();
    });
</script>
