<x-layouts.auth>
    <x-slot name="pageTitle">All Merchants</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Merchants List" :data="$data['all']">
                <x-slot name="headerCustom">
                    <x-auth.href-link link-href="{{ route('merchants.create') }}" link-value="{{ __('Create New Merchant') }}"
                        link-class="btn btn-primary" />
                </x-slot>

                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>Sr#</th>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Max Sites</th>
                            <th>Spin After (days)</th>
                            <th>Scan After (hours)</th>
                            <th>Use Other Merchant Points</th>
                            <th>Status</th>
                            <th>Action</th>
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
                                <td>{{ $merchant?->name }}</td>
                                <td>{{ $merchant?->max_sites ?? '-' }}</td>
                                <td>{{ $merchant?->spin_after_days ?? '-' }}</td>
                                <td>{{ $merchant?->scan_after_hours ?? '-' }}</td>
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
                                <td class="text-center">
                                    <div class="d-inline-block dropdown">
                                        <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-display="static">
                                            <i class="fas fa-ellipsis-v bg-light rounded p-2"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="{{ route('merchants.show', $merchant?->id) }}">
                                                <i class="fas fa-eye me-2 text-primary"></i> View Merchant
                                            </a>
                                            <a class="dropdown-item" href="{{ route('merchants.edit', $merchant?->id) }}">
                                                <i class="fas fa-edit me-2 text-warning"></i> Edit Merchant
                                            </a>
                                            <form action="{{ route('merchants.destroy', $merchant?->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this Merchant?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash-alt me-2"></i> Delete Merchant
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-auth.datatable>
            </x-all-list>
        </div>
    </div>
</x-layouts.auth>
