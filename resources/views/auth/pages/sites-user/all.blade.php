<x-layouts.auth>
    <x-slot name="pageTitle">Site Users</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Site Users" :data="$data['all']">
                <x-slot name="headerCustom">
                    <x-auth.href-link link-href="{{ route('siteusers.create') }}" link-value="{{ __('Create Site User') }}"
                        link-class="btn btn-primary me-2" />
                    <x-auth.href-link link-href="{{ route('siteusers.create-super') }}" link-value="{{ __('Add Super') }}"
                        link-class="btn btn-outline-light text-primary" />
                </x-slot>

                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Merchant &amp; Site</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['all'] as $key => $siteUser)
                            @php
                                $role = $siteUser->user?->roles->first();
                            @endphp
                            <tr>
                                <td>{{ $data['all']->firstItem() + $key }}</td>
                                <td>
                                    <div class="text-dark fw-semibold">
                                        <span class="text-uppercase text-muted small me-1">Merchant:</span>
                                        {{ $siteUser->merchant?->name ?? '-' }}
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <span class="text-uppercase text-muted small me-1">Site:</span>
                                        {{ $siteUser->site?->name ?? '-' }}
                                    </small>
                                </td>
                                <td>{{ $siteUser->user?->full_name ?? '-' }}</td>
                                <td>{{ $siteUser->user?->email ?? '-' }}</td>
                                <td>
                                    @php
                                        $roleLabel = $role?->title ?? ucwords(str_replace('_', ' ', $role?->name ?? '-'));
                                        $roleColors = [
                                            'super_admin' => '#7B1FA2',
                                            'business_admin' => '#F57C00',
                                            'manager' => '#1976D2',
                                            'merchant' => '#00897B',
                                            'manager_staff' => '#EC407A',
                                        ];
                                        $roleColor = $roleColors[$role?->name ?? ''] ?? ($role?->color ?? '#4A148D');
                                    @endphp
                                    <span class="badge" style="background-color: {{ $roleColor }}1c; color: {{ $roleColor }}; border: 1px solid {{ $roleColor }};">
                                        {{ $roleLabel }}
                                    </span>
                                </td>
                                <td>
                                    @if($siteUser->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $siteUser->created_at?->format('d M Y') ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="d-inline-block dropdown">
                                        <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-display="static">
                                            <i class="fas fa-ellipsis-v bg-light rounded p-2"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="{{ route('siteusers.show', $siteUser->id) }}">
                                                <i class="fas fa-eye me-2 text-primary"></i> View
                                            </a>
                                            <a class="dropdown-item" href="{{ route('siteusers.edit', $siteUser->id) }}">
                                                <i class="fas fa-edit me-2 text-warning"></i> Edit
                                            </a>
                                            <form action="{{ route('siteusers.destroy', $siteUser->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this site user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash-alt me-2"></i> Delete
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
