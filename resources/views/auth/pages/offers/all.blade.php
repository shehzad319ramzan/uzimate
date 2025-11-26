<x-layouts.auth>
    <x-slot name="pageTitle">All Offers</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Offers List" :data="$data['all']">
                <x-slot name="headerCustom">
                    @can('add_offer')
                    <x-auth.href-link link-href="{{ route('offers.create') }}" link-value="{{ __('Create New Offer') }}"
                        link-class="btn btn-primary" />
                    @endcan
                </x-slot>

                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>Sr#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Merchant & Site</th>
                            <th>Points Required</th>
                            <th>Expires On</th>
                            <th>Weekdays</th>
                            <th>Status</th>
                            @canany(['view_offer', 'edit_offer', 'delete_offer'])
                            <th>Action</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['all'] as $key => $offer)
                            <tr>
                                <td>{{ $key + 1 + (($data['all']->currentPage() - 1) * $data['all']->perPage()) }}</td>
                                <td>
                                    @php
                                        $offerImage = $offer->image();
                                    @endphp
                                    @if(!empty($offerImage))
                                        <img src="{{ $offerImage }}" alt="{{ $offer->title }}" class="rounded" width="50" height="50" style="object-fit: cover;" />
                                    @else
                                        <div class="rounded d-inline-flex align-items-center justify-content-center text-white fw-bold" 
                                             style="width: 50px; height: 50px; background-color: #4A148D; font-size: 18px;">
                                            {{ strtoupper(substr($offer->title ?? 'O', 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark fw-semibold">{{ $offer->title ?? '-' }}</div>
                                    @if($offer->description)
                                        <small class="text-muted">{{ Str::limit($offer->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark fw-semibold">
                                        <span class="text-uppercase text-muted small me-1">Merchant:</span>
                                        {{ $offer->merchant->name ?? '-' }}
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <span class="text-uppercase text-muted small me-1">Site:</span>
                                        {{ $offer->site->name ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $offer->points_required ?? 0 }}</span>
                                </td>
                                <td>
                                    @if($offer->expires_on)
                                        {{ \Carbon\Carbon::parse($offer->expires_on)->format('Y-m-d') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($offer->weekdays && is_array($offer->weekdays) && count($offer->weekdays) > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($offer->weekdays as $day)
                                                <span class="badge bg-primary">{{ $day }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($offer->status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                @canany(['view_offer', 'edit_offer', 'delete_offer'])
                                <td class="text-center">
                                    <div class="d-inline-block dropdown">
                                        <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-display="static">
                                            <i class="fas fa-ellipsis-v bg-light rounded p-2"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @can('view_offer')
                                            <a class="dropdown-item" href="{{ route('offers.show', $offer->id) }}">
                                                <i class="fas fa-eye me-2 text-primary"></i> View Offer
                                            </a>
                                            @endcan
                                            
                                            @can('edit_offer')
                                            <a class="dropdown-item" href="{{ route('offers.edit', $offer->id) }}">
                                                <i class="fas fa-edit me-2 text-warning"></i> Edit Offer
                                            </a>
                                            @endcan
                                            
                                            @can('delete_offer')
                                            <form action="{{ route('offers.destroy', $offer->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this Offer?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash-alt me-2"></i> Delete Offer
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

