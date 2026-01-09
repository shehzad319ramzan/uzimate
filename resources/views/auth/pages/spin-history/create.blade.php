<x-layouts.auth>
    <x-slot name="pageTitle">Create Spin History</x-slot>

    <x-auth.card card-header="Create Spin History" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('spinhistories.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>

        <x-auth.form form-action="{{ route('spinhistories.store') }}">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="merchant_id" class="form-label">Merchant</label>
                    <select class="form-select @error('merchant_id') is-invalid @enderror" id="merchant_id" name="merchant_id">
                        <option value="">All Merchants</option>
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

                <div class="col-md-6 mb-3">
                    <label for="site_id" class="form-label">Site <span class="text-danger">*</span></label>
                    <select class="form-select @error('site_id') is-invalid @enderror" id="site_id" name="site_id" required>
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

                <div class="col-md-6 mb-3">
                    <x-auth.select2
                        label="Customer *"
                        name="user_id"
                        id="user_id"
                        placeholder="Select customer"
                        :data="$customers"
                        existing-id="{{ old('user_id') }}"
                        selectclass="select2-users"
                        required
                    />
                    @error('user_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="spin_result_type" class="form-label">Result Type <span class="text-danger">*</span></label>
                    <select class="form-select @error('spin_result_type') is-invalid @enderror" id="spin_result_type" name="spin_result_type" required>
                        <option value="">Select Result Type</option>
                        @foreach($resultTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('spin_result_type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('spin_result_type')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3" id="points_earned_field" style="display: none;">
                    <x-auth.input-field type="number" name="points_earned" id="points_earned"
                        place="Enter points earned" val="{{ old('points_earned', 0) }}" label="Points Earned *" min="0" />
                    @error('points_earned')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3" id="offer_id_field" style="display: none;">
                    <label for="offer_id" class="form-label">Offer <span class="text-danger">*</span></label>
                    <select class="form-select @error('offer_id') is-invalid @enderror" id="offer_id" name="offer_id">
                        <option value="">Select Offer</option>
                        @foreach($offers as $offer)
                            <option value="{{ $offer->id }}"
                                data-site="{{ $offer->site_id }}"
                                {{ old('offer_id') == $offer->id ? 'selected' : '' }}>
                                {{ $offer->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('offer_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3" id="reward_value_field" style="display: none;">
                    <x-auth.input-field type="number" name="reward_value" id="reward_value" step="0.01"
                        place="Enter reward value" val="{{ old('reward_value') }}" label="Reward Value" min="0" />
                    @error('reward_value')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <x-auth.input-field type="number" name="spin_number" id="spin_number"
                        place="Auto-calculated if left empty" val="{{ old('spin_number') }}" label="Spin Number" min="1" />
                    <small class="text-muted">Leave empty for auto-calculation</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_eligible" id="is_eligible" value="1"
                            {{ old('is_eligible', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_eligible">
                            Is Eligible
                        </label>
                    </div>
                    @error('is_eligible')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <x-auth.text-area
                        name="notes"
                        id="notes"
                        place="Add an optional note"
                        val="{{ old('notes') }}"
                        required=""
                        extraclasses="mb-0"
                        label="Notes"
                        rows="4"
                    />
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Create Spin History" />
            </div>
        </x-auth.form>
    </x-auth.card>
</x-layouts.auth>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const merchantSelect = document.getElementById('merchant_id');
        const siteSelect = document.getElementById('site_id');
        const resultTypeSelect = document.getElementById('spin_result_type');
        const pointsField = document.getElementById('points_earned_field');
        const offerField = document.getElementById('offer_id_field');
        const rewardValueField = document.getElementById('reward_value_field');
        const pointsInput = document.getElementById('points_earned');
        const offerSelect = document.getElementById('offer_id');

        // Filter sites based on merchant
        const filterSites = () => {
            const merchantId = merchantSelect.value;
            const selectedSite = siteSelect.value;
            const options = siteSelect.querySelectorAll('option[value]');

            options.forEach(option => {
                if (option.value === '') {
                    return;
                }
                const dataMerchant = option.getAttribute('data-merchant');
                option.style.display = merchantId === '' || dataMerchant === merchantId ? 'block' : 'none';
            });

            if (selectedSite) {
                const selectedOption = siteSelect.querySelector(`option[value="${selectedSite}"]`);
                if (selectedOption && selectedOption.style.display === 'none') {
                    siteSelect.value = '';
                }
            }

            // Update offers when site changes
            updateOffers();
        };

        // Update offers based on selected site
        const updateOffers = () => {
            const siteId = siteSelect.value;
            const offerOptions = offerSelect.querySelectorAll('option[value]');

            offerOptions.forEach(option => {
                if (option.value === '') {
                    return;
                }
                const dataSite = option.getAttribute('data-site');
                option.style.display = siteId === '' || dataSite === siteId ? 'block' : 'none';
            });

            if (offerSelect.value) {
                const selectedOption = offerSelect.querySelector(`option[value="${offerSelect.value}"]`);
                if (selectedOption && selectedOption.style.display === 'none') {
                    offerSelect.value = '';
                }
            }
        };

        // Show/hide fields based on result type
        const toggleFields = () => {
            const resultType = resultTypeSelect.value;

            // Hide all fields first
            pointsField.style.display = 'none';
            offerField.style.display = 'none';
            rewardValueField.style.display = 'none';
            pointsInput.removeAttribute('required');
            offerSelect.removeAttribute('required');

            // Show relevant fields
            if (resultType === 'points') {
                pointsField.style.display = 'block';
                pointsInput.setAttribute('required', 'required');
            } else if (resultType === 'offer') {
                offerField.style.display = 'block';
                offerSelect.setAttribute('required', 'required');
                updateOffers();
            } else if (resultType === 'discount') {
                rewardValueField.style.display = 'block';
            }
        };

        merchantSelect.addEventListener('change', filterSites);
        siteSelect.addEventListener('change', updateOffers);
        resultTypeSelect.addEventListener('change', toggleFields);

        filterSites();
        toggleFields();
    });
</script>


