@php
    $filters = $filters ?? [];
    $searchValue = $filters['search'] ?? '';
    $filterMerchant = $filters['merchant_id'] ?? '';
    $filterStatus = $filters['status'] ?? '';
@endphp

@push('auth_styles')
<link href="{{ asset('dashboard/css/voucher-preview.css') }}" rel="stylesheet">
@endpush

<x-layouts.auth>
    <x-slot name="pageTitle">Vouchers</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Vouchers" :data="$data['all']">
                <x-slot name="headerCustom">
                    <form method="GET" action="{{ route('vouchers.index') }}" class="row g-2 align-items-end w-100 mb-3">
                        <div class="col-lg-3 col-md-6">
                            <label for="search" class="form-label text-muted small">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Title or merchant name" value="{{ $searchValue }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="merchant_filter" class="form-label text-muted small">Merchant</label>
                            <select name="merchant_id" id="merchant_filter" class="form-select">
                                <option value="">All Merchants</option>
                                @foreach ($merchants as $merchant)
                                    <option value="{{ $merchant->id }}"
                                        {{ (string) $filterMerchant === (string) $merchant->id ? 'selected' : '' }}>
                                        {{ $merchant->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="status_filter" class="form-label text-muted small">Status</label>
                            <select name="status" id="status_filter" class="form-select">
                                <option value="">All</option>
                                <option value="1" {{ $filterStatus === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $filterStatus === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-12 col-md-12 d-flex gap-2 align-items-center">
                            <button type="submit" class="btn btn-primary mt-3">Apply Filters</button>
                            <a href="{{ route('vouchers.index') }}" class="btn btn-outline-secondary mt-3">Reset</a>
                            @can('add_voucher')
                                <x-auth.href-link link-href="{{ route('vouchers.create') }}"
                                    link-value="{{ __('Create Voucher') }}" link-class="btn btn-success mt-3" />
                            @endcan
                        </div>
                    </form>
                </x-slot>

                <x-auth.datatable id="vouchersTable">
                    <thead>
                        <tr>
                            <th>Sr#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Merchant</th>
                            <th>Points</th>
                            <th>Valid until</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['all'] as $key => $voucher)
                            <tr>
                                <td>{{ $key + 1 + ($data['all']->currentPage() - 1) * $data['all']->perPage() }}</td>
                                <td>
                                    @php $img = $voucher->image(); @endphp
                                    @if (!empty($img))
                                        <img src="{{ $img }}" alt="{{ $voucher->title }}" class="rounded"
                                            width="50" height="50" style="object-fit: cover;" />
                                    @else
                                        <div class="rounded bg-light d-inline-flex align-items-center justify-content-center text-muted"
                                            style="width: 50px; height: 50px;"><i class="fas fa-ticket-alt"></i></div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $voucher->title }}</div>
                                    @if ($voucher->description)
                                        <small class="text-muted">{{ Str::limit(strip_tags($voucher->description), 40) }}</small>
                                    @endif
                                </td>
                                <td>{{ $voucher->merchant->name ?? '-' }}</td>
                                <td><span class="badge bg-info">{{ $voucher->points_required ?? 0 }} pts</span></td>
                                <td>{{ $voucher->valid_until ? $voucher->valid_until->format('d M Y') : '-' }}</td>
                                <td>
                                    @if ($voucher->status == '1')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @can('view_voucher')
                                        <button type="button" class="btn btn-sm btn-outline-primary voucher-preview-btn"
                                            data-preview-url="{{ route('vouchers.preview', $voucher->id) }}"
                                            data-edit-url="{{ route('vouchers.edit', $voucher->id) }}">View</button>
                                    @endcan
                                    @can('edit_voucher')
                                        <a href="{{ route('vouchers.edit', $voucher->id) }}"
                                            class="btn btn-sm btn-outline-secondary">Edit</a>
                                    @endcan
                                    @can('delete_voucher')
                                        <form action="{{ route('vouchers.destroy', $voucher->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete this voucher?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-muted py-4">No vouchers yet.</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-auth.datatable>
            </x-all-list>
        </div>
    </div>

    <div class="modal fade" id="voucherPreviewModal" tabindex="-1" aria-labelledby="voucherPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                <div class="modal-header border-0 pb-0 bg-light">

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2" id="voucherPreviewContent">
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Loading…
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light pt-0">
                    @can('edit_voucher')
                        <a href="#" id="voucherPreviewEditLink" class="btn btn-outline-secondary btn-sm me-2" style="display: none;">Edit</a>
                    @endcan
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            var modal = document.getElementById('voucherPreviewModal');
            var content = document.getElementById('voucherPreviewContent');
            var editLink = document.getElementById('voucherPreviewEditLink');
            if (!modal || !content) return;

            document.querySelectorAll('.voucher-preview-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var url = this.getAttribute('data-preview-url');
                    var editUrl = this.getAttribute('data-edit-url');
                    if (!url) return;
                    content.innerHTML = '<div class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading…</div>';
                    if (editLink) { editLink.style.display = 'none'; editLink.href = editUrl || '#'; }

                    var bsModal = new bootstrap.Modal(modal);
                    bsModal.show();

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
                        .then(function (r) { return r.text(); })
                        .then(function (html) {
                            content.innerHTML = html;
                            if (editLink) editLink.style.display = editUrl ? 'inline-block' : 'none';
                        })
                        .catch(function () {
                            content.innerHTML = '<div class="text-center py-4 text-danger"><i class="fas fa-exclamation-circle me-2"></i>Could not load preview.</div>';
                        });
                });
            });
        })();
    </script>
    @endpush
</x-layouts.auth>
