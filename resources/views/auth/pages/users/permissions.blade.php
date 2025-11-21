<x-layouts.auth>
    <x-slot name="pageTitle">Assign Permissions - {{ $user->full_name }}</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-auth.card card-header="Assign Permissions">
                <x-slot name="headerCustom">
                    <x-auth.href-link link-href="{{ url()->previous() }}" link-value="{{ __('Back') }}"
                        link-class="btn btn-outline-primary me-2" />
                </x-slot>

                <form method="POST" action="{{ route('users.permissions.update', $user->id) }}">
                    @csrf

                    <div class="row">
                        @foreach ($permissions as $category => $items)
                            @php
                                // Restricted categories that only superadmin can see
                                $restrictedCategories = ['Permissions', 'Roles', 'Website Settings'];
                                $isRestrictedCategory = in_array($category, $restrictedCategories);
                                $isSuperAdmin = auth()->user()->hasRole('super_admin');
                            @endphp
                            
                            @if (!$isRestrictedCategory || $isSuperAdmin)
                                <div class="col-md-4 mb-4">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="fw-semibold mb-3">{{ $category }}</h6>
                                        @foreach ($items as $permission)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]"
                                                    id="perm_{{ $permission->name }}" value="{{ $permission->name }}"
                                                    {{ in_array($permission->name, $assigned) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $permission->name }}">
                                                    {{ $permission->title ?? Str::title(str_replace('_', ' ', $permission->name)) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end">
                        <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Save Permissions" />
                    </div>
                </form>
            </x-auth.card>
        </div>
    </div>
</x-layouts.auth>

