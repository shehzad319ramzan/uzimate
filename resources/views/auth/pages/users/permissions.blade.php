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

                    <div class="accordion" id="permissionsAccordion">
                        @foreach ($permissions as $category => $items)
                            @php
                                // Restricted categories that only superadmin can see
                                $restrictedCategories = ['Permissions', 'Roles', 'Website Settings'];
                                $isRestrictedCategory = in_array($category, $restrictedCategories);
                                $isSuperAdmin = auth()->user()->hasRole('super_admin');
                                $categoryId = 'category_' . Str::slug($category);
                                $hasAssignedPermissions = collect($items)->pluck('name')->intersect($assigned)->isNotEmpty();
                            @endphp
                            
                            @if (!$isRestrictedCategory || $isSuperAdmin)
                                <div class="accordion-item mb-3">
                                    <h2 class="accordion-header" id="heading_{{ $categoryId }}">
                                        <button class="accordion-button {{ $hasAssignedPermissions ? '' : 'collapsed' }}" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#collapse_{{ $categoryId }}" 
                                                aria-expanded="{{ $hasAssignedPermissions ? 'true' : 'false' }}" 
                                                aria-controls="collapse_{{ $categoryId }}">
                                            <div class="d-flex align-items-center w-100">
                                                <i class="fas fa-{{ $category === 'Users' ? 'users' : ($category === 'Merchants' ? 'store' : ($category === 'Sites' ? 'map-marker-alt' : ($category === 'Site Users' ? 'user-friends' : ($category === 'Permissions' ? 'key' : ($category === 'Roles' ? 'user-tag' : ($category === 'Website Settings' ? 'cog' : 'shield-alt')))))) }} me-3 text-primary"></i>
                                                <span class="fw-semibold">{{ $category }}</span>
                                                @if ($hasAssignedPermissions)
                                                    <span class="badge bg-success ms-auto me-3">{{ collect($items)->pluck('name')->intersect($assigned)->count() }} assigned</span>
                                                @endif
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse_{{ $categoryId }}" 
                                         class="accordion-collapse collapse {{ $hasAssignedPermissions ? 'show' : '' }}" 
                                         aria-labelledby="heading_{{ $categoryId }}" 
                                         data-bs-parent="#permissionsAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-success select-all-category" 
                                                                data-category="{{ $categoryId }}">
                                                            <i class="fas fa-check-double me-1"></i> Select All
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all-category" 
                                                                data-category="{{ $categoryId }}">
                                                            <i class="fas fa-times me-1"></i> Deselect All
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                @foreach ($items as $permission)
                                                    <div class="col-md-6 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input category-{{ $categoryId }}" type="checkbox" 
                                                                   name="permissions[]" id="perm_{{ $permission->name }}" 
                                                                   value="{{ $permission->name }}"
                                                                   {{ in_array($permission->name, $assigned) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="perm_{{ $permission->name }}">
                                                                {{ $permission->title ?? Str::title(str_replace('_', ' ', $permission->name)) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="button" class="btn btn-outline-success me-2" id="selectAllPermissions">
                                <i class="fas fa-check-double me-1"></i> Select All Permissions
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="deselectAllPermissions">
                                <i class="fas fa-times me-1"></i> Deselect All Permissions
                            </button>
                        </div>
                        <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Save Permissions" />
                    </div>
                </form>
            </x-auth.card>
        </div>
    </div>
</x-layouts.auth>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All/Deselect All for individual categories
    document.querySelectorAll('.select-all-category').forEach(button => {
        button.addEventListener('click', function() {
            const category = this.dataset.category;
            const checkboxes = document.querySelectorAll(`.category-${category}`);
            checkboxes.forEach(checkbox => checkbox.checked = true);
            updateAccordionBadge(category);
        });
    });

    document.querySelectorAll('.deselect-all-category').forEach(button => {
        button.addEventListener('click', function() {
            const category = this.dataset.category;
            const checkboxes = document.querySelectorAll(`.category-${category}`);
            checkboxes.forEach(checkbox => checkbox.checked = false);
            updateAccordionBadge(category);
        });
    });

    // Select All/Deselect All for entire form
    document.getElementById('selectAllPermissions')?.addEventListener('click', function() {
        document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
            checkbox.checked = true;
        });
        updateAllAccordionBadges();
    });

    document.getElementById('deselectAllPermissions')?.addEventListener('click', function() {
        document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        updateAllAccordionBadges();
    });

    // Update badge when individual checkboxes are changed
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const categoryClass = Array.from(this.classList).find(cls => cls.startsWith('category-'));
            if (categoryClass) {
                const category = categoryClass.replace('category-', '');
                updateAccordionBadge(category);
            }
        });
    });

    function updateAccordionBadge(categoryId) {
        const checkboxes = document.querySelectorAll(`.category-${categoryId}`);
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        const accordionButton = document.querySelector(`[data-bs-target="#collapse_${categoryId}"]`);
        
        if (accordionButton) {
            let badge = accordionButton.querySelector('.badge');
            if (checkedCount > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'badge bg-success ms-auto me-3';
                    accordionButton.querySelector('.fw-semibold').parentNode.appendChild(badge);
                }
                badge.textContent = `${checkedCount} assigned`;
                badge.className = 'badge bg-success ms-auto me-3';
            } else if (badge) {
                badge.remove();
            }
        }
    }

    function updateAllAccordionBadges() {
        document.querySelectorAll('[class*="category-"]').forEach(checkbox => {
            const categoryClass = Array.from(checkbox.classList).find(cls => cls.startsWith('category-'));
            if (categoryClass) {
                const category = categoryClass.replace('category-', '');
                updateAccordionBadge(category);
            }
        });
    }
});
</script>

