<x-layouts.auth>
    <x-slot name="pageTitle">Merchant Detail</x-slot>
    <div class="row mt-3">
        <div class="col-md-12">
            <x-auth.card card-header="Merchant Detail">
                <x-slot name="headerCustom">
                    <x-auth.href-link link-href="{{ route('merchants.edit', $data->id) }}"
                        link-value="{{ __('Edit Merchant') }}" link-class="btn btn-primary" />
                </x-slot>

                <div class="row mb-3">
                    <div class="col-md-12 text-center">
                        @if($data->logo())
                            <img src="{{ $data->logo() }}" alt="Merchant Logo" class="rounded-circle" width="128" height="128" style="object-fit: cover;" />
                        @else
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold mx-auto" 
                                 style="width: 128px; height: 128px; background-color: #4A148D; font-size: 48px;">
                                {{ strtoupper(substr($data->name ?? 'M', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </div>
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Merchant Name</th>
                            <td>{{ $data->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Max Sites</th>
                            <td>{{ $data->max_sites ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Spin After (days)</th>
                            <td>{{ $data->spin_after_days ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Scan After (hours)</th>
                            <td>{{ $data->scan_after_hours ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Use Other Merchant Points</th>
                            <td>
                                @if($data->use_other_merchant_points ?? false)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($data->status == 1)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $data->created_at ? $data->created_at->format('Y-m-d H:i:s') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $data->updated_at ? $data->updated_at->format('Y-m-d H:i:s') : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </x-auth.card>
        </div>
    </div>
</x-layouts.auth>
