@php
    $filters = $filters ?? [];
    $searchValue = $filters['search'] ?? '';
    $filterStatus = $filters['status'] ?? '';
@endphp
<x-layouts.auth>
    <x-slot name="pageTitle">Merchant Categories</x-slot>
    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Merchant Categories" :data="$data['all']">
                <x-slot name="headerCustom">
                    <form method="GET" action="{{ route('merchantcategories.index') }}"
                        class="row g-2 align-items-end w-100 mb-3">
                        <div class="col-lg-4 col-md-6"><label class="form-label text-muted small">Search</label>
                            <input type="text" name="search" class="form-control" value="{{ $searchValue }}">
                        </div>
                        <div class="col-lg-2 col-md-6"><label class="form-label text-muted small">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="1" {{ $filterStatus === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $filterStatus === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-12 d-flex gap-2"><button type="submit"
                                class="btn btn-primary mt-3">Apply</button>
                            <a href="{{ route('merchantcategories.index') }}"
                                class="btn btn-outline-secondary mt-3">Reset</a>
                        </div>
                    </form>
                    @can('view_merchant')
                        <x-auth.href-link link-href="{{ route('merchantcategories.create') }}" link-value="Add Category"
                            link-class="btn btn-primary" />
                    @endcan
                </x-slot>
                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Merchants</th>
                            <th>Created</th>
                            @can('view_merchant')
                                <th class="text-center">Action</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['all'] as $index => $item)
                            <tr>
                                <td>{{ $index + 1 + ($data['all']->currentPage() - 1) * $data['all']->perPage() }}
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    @if ((string) $item->status === '1')
                                    <span class="badge bg-success">Active</span>@else<span
                                            class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $item->merchants_count ?? 0 }}</td>
                                <td>{{ optional($item->created_at)->format('d M Y') }}</td>
                                @can('view_merchant')
                                    <td class="text-center">
                                        <div class="d-inline-block dropdown">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown"><i
                                                    class="fas fa-ellipsis-v bg-light rounded p-2"></i></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item"
                                                    href="{{ route('merchantcategories.show', $item->id) }}">View</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('merchantcategories.edit', $item->id) }}">Edit</a>
                                                <form action="{{ route('merchantcategories.destroy', $item->id) }}"
                                                    method="POST" onsubmit="return confirm('Delete?');">@csrf
                                                    @method('DELETE')<button type="submit"
                                                        class="dropdown-item text-danger">Delete</button></form>
                                            </div>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </x-auth.datatable>
                @if ($data['all']->isEmpty())
                    <div class="alert alert-light text-center mb-0 px-3">No categories found.</div>
                @endif
            </x-all-list>
        </div>
    </div>
</x-layouts.auth>
