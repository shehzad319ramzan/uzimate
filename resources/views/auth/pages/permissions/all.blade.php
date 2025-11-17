<x-layouts.auth>
    <x-slot name="pageTitle">Permissions</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Permissions" :data="$data['all']">
                <x-slot name="headerCustom">
                    <x-auth.href-link link-href="{{ route('permissions.create') }}" link-value="{{ __('Create Permission') }}"
                        link-class="btn btn-primary" />
                </x-slot>

                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['all'] as $key => $permission)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->title }}</td>
                                <td>{{ $permission->category }}</td>
                                <td class="text-center">
                                    <div class="d-inline-block dropdown">
                                        <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-display="static">
                                            <i class="fas fa-ellipsis-v bg-light rounded p-2"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="{{ route('permissions.edit', $permission->id) }}">
                                                <i class="fas fa-edit me-2 text-warning"></i> Edit
                                            </a>
                                            <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this permission?');">
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

