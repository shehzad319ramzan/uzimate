<x-layouts.auth>
    <x-slot name="pageTitle">Site Detail</x-slot>
    <div class="row mt-3">
        <div class="col-md-12">
            <x-auth.card card-header="Site Detail">
                <x-slot name="headerCustom">
                    <x-auth.href-link link-href="{{ route('sites.index') }}" link-value="{{ __('Back to List') }}"
                        link-class="btn btn-outline-primary me-2" />
                    <x-auth.href-link link-href="{{ route('sites.edit', $data->id) }}"
                        link-value="{{ __('Edit Site') }}" link-class="btn btn-primary" />
                </x-slot>

                <div class="row mb-3">
                    <div class="col-md-12 text-center">
                        @php
                            $siteLogo = $data->displayLogo();
                        @endphp
                        @if(!empty($siteLogo))
                            <img src="{{ $siteLogo }}" alt="Site Logo" class="rounded-circle" width="128" height="128" style="object-fit: cover;" />
                        @else
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold mx-auto" 
                                 style="width: 128px; height: 128px; background-color: #4A148D; font-size: 48px;">
                                {{ strtoupper(substr($data->name ?? 'S', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </div>

                <table class="table">
                    <tbody>
                        <tr>
                            <th>Merchant</th>
                            <td>{{ $data->merchant->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $data->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $data->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Points</th>
                            <td>{{ $data->points ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Address Line 1</th>
                            <td>{{ $data->address_line_1 ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Address Line 2</th>
                            <td>{{ $data->address_line_2 ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>City</th>
                            <td>{{ $data->city ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>County</th>
                            <td>{{ $data->county ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Postcode</th>
                            <td>{{ $data->postcode ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Country</th>
                            <td>{{ $data->country ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Location</th>
                            <td>{{ $data->location ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Coordinates</th>
                            <td>{{ $data->coordinates ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Use Merchant Logo</th>
                            <td>
                                @if($data->use_merchant_logo ?? false)
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
