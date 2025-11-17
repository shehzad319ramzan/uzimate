<x-layouts.auth>
    <x-slot name="pageTitle">All Sites</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Sites List" :data="$data['all']">
                <x-slot name="headerCustom">
                    <x-auth.href-link link-href="{{ route('sites.create') }}" link-value="{{ __('Create New Site') }}"
                        link-class="btn btn-primary" />
                </x-slot>

                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>Sr#</th>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Merchant</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['all'] as $key => $site)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if($site->logo())
                                        <img src="{{ $site->logo() }}" alt="{{ $site->name }}" class="rounded-circle" width="40" height="40" style="object-fit: cover;" />
                                    @else
                                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold" 
                                             style="width: 40px; height: 40px; background-color: #4A148D; font-size: 16px;">
                                            {{ strtoupper(substr($site->name ?? 'S', 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $site?->name }}</td>
                                <td>{{ $site->merchant->name ?? '-' }}</td>
                                <td>{{ $site?->phone ?? '-' }}</td>
                                <td>{{ $site?->city ?? '-' }}</td>
                                <td>
                                    @if($site?->status == 1)
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
                                            <a class="dropdown-item" href="{{ route('sites.show', $site?->id) }}">
                                                <i class="fas fa-eye me-2 text-primary"></i> View Site
                                            </a>
                                            <a class="dropdown-item" href="{{ route('sites.edit', $site?->id) }}">
                                                <i class="fas fa-edit me-2 text-warning"></i> Edit Site
                                            </a>
                                            <form action="{{ route('sites.destroy', $site?->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this Site?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash-alt me-2"></i> Delete Site
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
