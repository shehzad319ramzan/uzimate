<x-layouts.auth>
    <x-slot name="pageTitle">Edit Offer</x-slot>
    <x-auth.card card-header="Edit Offer" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('offers.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>
        <x-auth.form form-action="{{ route('offers.update', $data->id) }}" enctype="true">
            @method('PUT')
            <div class="row">
                <!-- Merchant Selection -->
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label for="merchant_id" class="form-label">Merchant</label>
                        <select class="form-select @error('merchant_id') is-invalid @enderror" name="merchant_id" id="merchant_id">
                            <option value="">Select Merchant</option>
                            @foreach($merchants as $merchant)
                                <option value="{{ $merchant->id }}" {{ old('merchant_id', $data->merchant_id ?? ($data->site->merchant_id ?? '')) == $merchant->id ? 'selected' : '' }}>
                                    {{ $merchant->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('merchant_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Site Selection -->
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label for="site_id" class="form-label">Site <span class="text-danger">*</span></label>
                        <select class="form-select @error('site_id') is-invalid @enderror" name="site_id" id="site_id" required>
                            <option value="">Select Site</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}"
                                    data-merchant="{{ $site->merchant_id }}"
                                    {{ old('site_id', $data->site_id) == $site->id ? 'selected' : '' }}>
                                    {{ $site->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('site_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="col-md-6 mb-3">
                    <label class="form-label mb-2 d-block" style="font-weight: 500;">Offer Image</label>
                    <x-auth.upload-file image="{{ $data->image() }}" name="{{ $data->title ?? '' }}" />
                    @error('file')
                        <span class="text-danger d-block mt-2">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Title and Points Required -->
                <div class="col-md-8 mb-3">
                    <x-auth.input-field type="text" name="title" id="title" required="true"
                        place="Enter title" val="{{ old('title', $data->title ?? '') }}" extraclasses="mb-3 @error('title') is-invalid @enderror"
                        label="Title" />
                </div>
                <div class="col-md-4 mb-3">
                    <x-auth.input-field type="number" name="points_required" id="points_required" required="true"
                        place="Enter points" val="{{ old('points_required', $data->points_required ?? '') }}" extraclasses="mb-3 @error('points_required') is-invalid @enderror"
                        label="Points Required" />
                </div>

                <!-- Expires On -->
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label for="expires_on" class="form-label">Expires On</label>
                        <input type="date"
                            class="form-control @error('expires_on') is-invalid @enderror"
                            name="expires_on"
                            id="expires_on"
                            value="{{ old('expires_on', $data->expires_on ? \Carbon\Carbon::parse($data->expires_on)->format('Y-m-d') : '') }}" />
                        @error('expires_on')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Weekdays -->
                <div class="col-md-12 mb-3">
                    <label class="form-label mb-2">Weekdays <small class="text-muted">(Select multiple days)</small></label>
                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                            $oldWeekdays = old('weekdays', $data->weekdays ?? []);
                            if (is_string($oldWeekdays)) {
                                $oldWeekdays = json_decode($oldWeekdays, true) ?? [];
                            }
                            if (!is_array($oldWeekdays)) {
                                $oldWeekdays = [];
                            }
                        @endphp
                        @foreach($weekdays as $day)
                            <button type="button"
                                class="btn weekday-btn {{ in_array($day, $oldWeekdays) ? 'btn-primary' : 'btn-outline-primary' }}"
                                data-day="{{ $day }}"
                                onclick="toggleWeekday(this, '{{ $day }}')">
                                {{ $day }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="weekdays" id="weekdaysInput" value="{{ json_encode($oldWeekdays) }}" />
                    <small class="text-muted d-block mt-1">Click on days to select/deselect. Multiple selections allowed.</small>
                    @error('weekdays')
                        <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            name="description"
                            id="description"
                            rows="4"
                            maxlength="255"
                            placeholder="Enter description">{{ old('description', $data->description ?? '') }}</textarea>
                        <div class="d-flex justify-content-end mt-1">
                            <small class="text-muted">
                                <span id="charCount">{{ strlen(old('description', $data->description ?? '')) }}</span>/255
                            </small>
                        </div>
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" id="status">
                            <option value="1" {{ old('status', $data->status ?? '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $data->status ?? '1') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end">
                    <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Update" />
                </div>
            </div>
        </x-auth.form>
    </x-auth.card>
</x-layouts.auth>

{{-- @push('auth_scripts') --}}
<script>
    // Character counter for description
    document.getElementById('description').addEventListener('input', function() {
        const charCount = this.value.length;
        document.getElementById('charCount').textContent = charCount;
    });

    // Weekdays toggle - Multiple selection allowed
    let selectedWeekdays = {!! json_encode($oldWeekdays) !!};
    if (!Array.isArray(selectedWeekdays)) {
        selectedWeekdays = [];
    }

    function toggleWeekday(button, day) {
        if (button.classList.contains('btn-primary')) {
            // Deselect
            button.classList.remove('btn-primary');
            button.classList.add('btn-outline-primary');
            selectedWeekdays = selectedWeekdays.filter(d => d !== day);
        } else {
            // Select
            button.classList.remove('btn-outline-primary');
            button.classList.add('btn-primary');
            if (!selectedWeekdays.includes(day)) {
                selectedWeekdays.push(day);
            }
        }
        // Update hidden input with selected weekdays
        document.getElementById('weekdaysInput').value = JSON.stringify(selectedWeekdays);
    }

    // Filter sites based on merchant selection
    document.getElementById('merchant_id').addEventListener('change', function() {
        const merchantId = this.value;
        const siteSelect = document.getElementById('site_id');
        const currentSelectedValue = siteSelect.value; // Preserve current selection
        const options = siteSelect.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                const dataMerchant = option.getAttribute('data-merchant');
                if (merchantId === '' || dataMerchant === merchantId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            }
        });
        
        // Only reset site selection if current selection doesn't match the new merchant
        // But preserve it if it matches
        if (currentSelectedValue) {
            const selectedOption = siteSelect.querySelector(`option[value="${currentSelectedValue}"]`);
            if (selectedOption) {
                const selectedMerchant = selectedOption.getAttribute('data-merchant');
                // If merchant is selected and site's merchant doesn't match, clear selection
                if (merchantId !== '' && selectedMerchant !== merchantId) {
                    siteSelect.value = '';
                } else if (selectedOption.style.display !== 'none') {
                    // Restore selection if it's still visible
                    siteSelect.value = currentSelectedValue;
                }
            }
        }
    });

    // Initialize site filtering on page load - but preserve selected site
    document.addEventListener('DOMContentLoaded', function() {
        const merchantSelect = document.getElementById('merchant_id');
        const siteSelect = document.getElementById('site_id');
        const selectedSiteId = siteSelect.value;
        
        // Only filter if merchant is selected AND we have a selected site to preserve
        if (merchantSelect.value && selectedSiteId) {
            // Filter sites based on merchant, but keep selected site visible
            const options = siteSelect.querySelectorAll('option');
            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                } else if (option.value === selectedSiteId) {
                    // Always show the currently selected site
                    option.style.display = 'block';
                } else {
                    const dataMerchant = option.getAttribute('data-merchant');
                    const merchantId = merchantSelect.value;
                    if (merchantId === '' || dataMerchant === merchantId) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                }
            });
            
            // Ensure the selected site remains selected
            siteSelect.value = selectedSiteId;
        } else if (merchantSelect.value && !selectedSiteId) {
            // If merchant is selected but no site, just filter normally
            merchantSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
{{-- @endpush --}}

