<x-layouts.auth>
    <x-slot name="pageTitle">Create Offer</x-slot>
    <x-auth.card card-header="Create Offer" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('offers.index') }}" link-value="{{ __('Back') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>
        <x-auth.form form-action="{{ route('offers.store') }}" enctype="true">
            <div class="row">
                <!-- Merchant Selection -->
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label for="merchant_id" class="form-label">Merchant</label>
                        <select class="form-select @error('merchant_id') is-invalid @enderror" name="merchant_id" id="merchant_id">
                            <option value="">Select Merchant</option>
                            @foreach($merchants as $merchant)
                                <option value="{{ $merchant->id }}" {{ old('merchant_id') == $merchant->id ? 'selected' : '' }}>
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
                                    {{ old('site_id') == $site->id ? 'selected' : '' }}>
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
                    <x-auth.upload-file image="" name="{{ old('title') }}" />
                    @error('file')
                        <span class="text-danger d-block mt-2">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Title and Points Required -->
                <div class="col-md-8 mb-3">
                    <x-auth.input-field type="text" name="title" id="title" required="true"
                        place="Enter title" val="{{ old('title') }}" extraclasses="mb-3 @error('title') is-invalid @enderror"
                        label="Title" />
                </div>
                <div class="col-md-4 mb-3">
                    <x-auth.input-field type="number" name="points_required" id="points_required" required="true"
                        place="Enter points" val="{{ old('points_required') }}" extraclasses="mb-3 @error('points_required') is-invalid @enderror"
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
                            value="{{ old('expires_on') }}" />
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
                            $oldWeekdays = old('weekdays', []);
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
                            placeholder="Enter description">{{ old('description') }}</textarea>
                        <div class="d-flex justify-content-end mt-1">
                            <small class="text-muted">
                                <span id="charCount">0</span>/255
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
                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-md-12 d-flex justify-content-between">
                    <a href="{{ route('offers.index') }}" class="btn btn-secondary">Delete</a>
                    <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Create" />
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

    // Initialize character count
    document.addEventListener('DOMContentLoaded', function() {
        const desc = document.getElementById('description');
        document.getElementById('charCount').textContent = desc.value.length;
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

        // Reset site selection if current selection doesn't match
        const selectedOption = siteSelect.options[siteSelect.selectedIndex];
        if (selectedOption.value !== '' && merchantId !== '' && selectedOption.getAttribute('data-merchant') !== merchantId) {
            siteSelect.value = '';
        }
    });
</script>
{{-- @endpush --}}

