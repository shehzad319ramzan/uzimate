<x-layouts.auth>
    <x-slot name="pageTitle">Offer Detail</x-slot>
    <div class="row mt-3">
        <div class="col-md-12">
            <x-auth.card card-header="Offer Detail">
                <x-slot name="headerCustom">
                    <x-auth.href-link link-href="{{ route('offers.index') }}" link-value="{{ __('Back to List') }}"
                        link-class="btn btn-outline-primary me-2" />
                    @can('edit_offer')
                    <x-auth.href-link link-href="{{ route('offers.edit', $data->id) }}"
                        link-value="{{ __('Edit Offer') }}" link-class="btn btn-primary" />
                    @endcan
                </x-slot>

                <div class="row mb-3">
                    <div class="col-md-12 text-center">
                        @php
                            $offerImage = $data->image();
                        @endphp
                        @if(!empty($offerImage))
                            <img src="{{ $offerImage }}" alt="Offer Image" class="rounded" width="200" height="200" style="object-fit: cover;" />
                        @else
                            <div class="rounded d-inline-flex align-items-center justify-content-center text-white fw-bold mx-auto" 
                                 style="width: 200px; height: 200px; background-color: #4A148D; font-size: 72px;">
                                {{ strtoupper(substr($data->title ?? 'O', 0, 1)) }}
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
                            <th>Site</th>
                            <td>{{ $data->site->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Title</th>
                            <td>{{ $data->title ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Points Required</th>
                            <td>
                                <span class="badge bg-info">{{ $data->points_required ?? 0 }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Expires On</th>
                            <td>
                                @if($data->expires_on)
                                    {{ \Carbon\Carbon::parse($data->expires_on)->format('Y-m-d') }}
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Weekdays</th>
                            <td>
                                @if($data->weekdays && is_array($data->weekdays) && count($data->weekdays) > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($data->weekdays as $day)
                                            <span class="badge bg-primary">{{ $day }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">No weekdays selected</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>
                                @if($data->description)
                                    {{ $data->description }}
                                @else
                                    <span class="text-muted">-</span>
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

