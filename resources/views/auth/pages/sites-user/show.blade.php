<x-layouts.auth>
    <x-slot name="pageTitle">Site User Detail</x-slot>
    <div class="row mt-3">
        <div class="col-md-12">
            <x-auth.card card-header="Site User Detail">
                <x-slot name="headerCustom">
                    <x-auth.href-link link-href="{{ route('siteusers.index') }}" link-value="{{ __('Back to List') }}"
                        link-class="btn btn-outline-primary me-2" />
                    <x-auth.href-link link-href="{{ route('siteusers.edit', $data->id) }}"
                        link-value="{{ __('Edit Site User') }}" link-class="btn btn-primary" />
                </x-slot>

                <div class="row mb-4">
                    <div class="col-md-12 text-center">
                        @php
                            $profileLogo = $data->user?->profile();
                        @endphp
                        @if(!empty($profileLogo))
                            <img src="{{ $profileLogo }}" alt="User Avatar" class="rounded-circle" width="128" height="128" style="object-fit: cover;" />
                        @else
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold mx-auto"
                                 style="width: 128px; height: 128px; background-color: #4A148D; font-size: 48px;">
                                {{ strtoupper(substr($data->user?->first_name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        <h5 class="mt-3 mb-0">{{ $data->user?->full_name ?? '-' }}</h5>
                        <span class="text-muted">{{ $data->user?->roles->first()?->title ?? ucwords(str_replace('_', ' ', $data->user?->roles->first()?->name ?? '')) }}</span>
                    </div>
                </div>

                <table class="table">
                    <tbody>
                        <tr>
                            <th>Merchant</th>
                            <td>{{ $data->merchant?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Site</th>
                            <td>{{ $data->site?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $data->user?->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Mobile</th>
                            <td>{{ $data->user?->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($data->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $data->created_at?->format('d M Y H:i') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $data->updated_at?->format('d M Y H:i') ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </x-auth.card>
        </div>
    </div>
</x-layouts.auth>
