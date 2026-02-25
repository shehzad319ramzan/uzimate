@php $filters = $filters ?? []; $categories = $categories ?? collect(); @endphp
<x-layouts.auth>
    <x-slot name="pageTitle">All Merchants</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Merchants List" :data="$data['all']">
                <x-slot name="headerCustom">
                    <form method="GET" action="{{ route('merchants.index') }}" class="row g-2 align-items-end w-100 mb-3">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label text-muted small">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search merchants" value="{{ $filters['search'] ?? '' }}">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label text-muted small">Category</label>
                            <select name="merchant_category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ ($filters['merchant_category_id'] ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary mt-3">Apply</button>
                            <a href="{{ route('merchants.index') }}" class="btn btn-outline-secondary mt-3">Reset</a>
                        </div>
                    </form>
                    @can('add_merchant')
                    <x-auth.href-link link-href="{{ route('merchants.create') }}" link-value="{{ __('Create New Merchant') }}"
                        link-class="btn btn-primary" />
                    @endcan
                </x-slot>

                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>Sr#</th>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Max Sites</th>
                            {{-- <th>Spin After (days)</th>
                            <th>Scan After (hours)</th> --}}
                            <th>Use Other Merchant Points</th>
                            <th>Status</th>
                            @canany(['view_merchant', 'edit_merchant', 'delete_merchant'])
                            <th>Action</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['all'] as $key => $merchant)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if($merchant->logo())
                                        <img src="{{ $merchant->logo() }}" alt="{{ $merchant->name }}" class="rounded-circle" width="40" height="40" style="object-fit: cover;" />
                                    @else
                                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold"
                                             style="width: 40px; height: 40px; background-color: #4A148D; font-size: 16px;">
                                            {{ strtoupper(substr($merchant->name ?? 'M', 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $merchant?->name }}</span>
                                    <div class="text-muted small mt-1">
                                        Category: @if($merchant?->category)
                                            <span class="badge bg-primary">{{ $merchant?->category?->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Not Assign</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $merchant?->max_sites ?? '-' }}</td>
                                {{-- <td>{{ $merchant?->spin_after_days ?? '-' }}</td>
                                <td>{{ $merchant?->scan_after_hours ?? '-' }}</td> --}}
                                <td>
                                    @if($merchant?->use_other_merchant_points ?? false)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($merchant?->status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                @canany(['view_merchant', 'edit_merchant', 'delete_merchant'])
                                <td class="text-center">
                                    <div class="d-inline-block dropdown">
                                        <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-display="static">
                                            <i class="fas fa-ellipsis-v bg-light rounded p-2"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @can('view_merchant')
                                            <a class="dropdown-item" href="{{ route('merchants.show', $merchant?->id) }}">
                                                <i class="fas fa-eye me-2 text-primary"></i> View Merchant
                                            </a>
                                            @endcan

                                            @can('edit_merchant')
                                            <a class="dropdown-item" href="{{ route('merchants.edit', $merchant?->id) }}">
                                                <i class="fas fa-edit me-2 text-warning"></i> Edit Merchant
                                            </a>
                                            @endcan

                                            @can('delete_merchant')
                                            <form action="{{ route('merchants.destroy', $merchant?->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this Merchant?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash-alt me-2"></i> Delete Merchant
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                                @endcanany
                            </tr>
                        @endforeach
                    </tbody>
                </x-auth.datatable>
            </x-all-list>
        </div>
    </div>
</x-layouts.auth>
