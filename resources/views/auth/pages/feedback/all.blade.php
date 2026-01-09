@php
    $filters = $filters ?? [];
    $searchValue = $filters['search'] ?? '';
    $filterStatus = $filters['status'] ?? '';
    $hasActions = auth()->user()?->can('view_feedback')
        || auth()->user()?->can('edit_feedback')
        || auth()->user()?->can('delete_feedback');
@endphp

<x-layouts.auth>
    <x-slot name="pageTitle">Feedback</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Feedback" :data="$data['all']">
                <x-slot name="headerCustom">
                    <form method="GET" action="{{ route('feedbacks.index') }}" class="row g-2 align-items-end w-100 mb-3">
                        <div class="col-lg-4 col-md-6">
                            <label for="search" class="form-label text-muted small">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Search feedback" value="{{ $searchValue }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="status_filter" class="form-label text-muted small">Status</label>
                            <select name="status" id="status_filter" class="form-select">
                                <option value="">All Status</option>
                                <option value="1" {{ $filterStatus === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $filterStatus === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 mt-3">Apply Filters</button>
                            <a href="{{ route('feedbacks.index') }}" class="btn btn-outline-secondary mt-3">Reset</a>
                        </div>
                    </form>

                    @can('add_feedback')
                        <x-auth.href-link link-href="{{ route('feedbacks.create') }}" link-value="{{ __('Add Feedback') }}"
                            link-class="btn btn-primary" />
                    @endcan
                </x-slot>

                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Created At</th>
                            @if($hasActions)
                                <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['all'] as $index => $item)
                            <tr>
                                <td>{{ $index + 1 + (($data['all']->currentPage() - 1) * $data['all']->perPage()) }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $item->name }}</div>
                                </td>
                                <td>
                                    @if($item->status == '1')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ optional($item->created_at)->format('d M Y, H:i') }}</td>
                                @if($hasActions)
                                    <td class="text-center">
                                        <div class="d-inline-block dropdown">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-display="static">
                                                <i class="fas fa-ellipsis-v bg-light rounded p-2"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @can('view_feedback')
                                                <a class="dropdown-item" href="{{ route('feedbacks.show', $item->id) }}">
                                                    <i class="fas fa-eye me-2 text-primary"></i> View Details
                                                </a>
                                                @endcan

                                                @can('edit_feedback')
                                                <a class="dropdown-item" href="{{ route('feedbacks.edit', $item->id) }}">
                                                    <i class="fas fa-edit me-2 text-warning"></i> Edit
                                                </a>
                                                @endcan

                                                @can('delete_feedback')
                                                <form action="{{ route('feedbacks.destroy', $item->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash-alt me-2"></i> Delete
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </x-auth.datatable>

                @if($data['all']->isEmpty())
                    <div class="alert alert-light text-center mb-0 px-3">
                        No feedback found.
                    </div>
                @endif
            </x-all-list>
        </div>
    </div>
</x-layouts.auth>

