<x-layouts.auth>
    <x-slot name="pageTitle">Edit Point Award</x-slot>

    <x-auth.card card-header="Edit Point Award" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('pointawards.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>

        <x-auth.form form-action="{{ route('pointawards.update', $data->id) }}">
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="merchant_id" class="form-label">Merchant</label>
                    <select class="form-select @error('merchant_id') is-invalid @enderror" id="merchant_id" name="merchant_id">
                        <option value="">All Merchants</option>
                        @php
                            $selectedMerchant = old('merchant_id', $data->merchant_id ?? $data->site->merchant_id ?? '');
                        @endphp
                        @foreach($merchants as $merchant)
                            <option value="{{ $merchant->id }}" {{ (string)$selectedMerchant === (string)$merchant->id ? 'selected' : '' }}>
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
                        @php
                            $selectedSite = old('site_id', $data->site_id);
                        @endphp
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}"
                                data-merchant="{{ $site->merchant_id }}"
                                {{ (string)$selectedSite === (string)$site->id ? 'selected' : '' }}>
                                {{ $site->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('site_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <x-auth.input-field type="number" name="points_earned" id="points_earned" required="true"
                        place="Enter points" val="{{ old('points_earned', $data->points_earned) }}" label="Points Earned *" min="1" />
                </div>

                <div class="col-md-6 mb-3">
                    <x-auth.select2
                        label="User *"
                        name="user_id"
                        id="user_id"
                        placeholder="Select user"
                        :data="$customers"
                        existing-id="{{ old('user_id', $data->user_id) }}"
                        selectclass="select2-users"
                        required
                    />
                    @error('user_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <x-auth.text-area
                        name="notes"
                        id="notes"
                        place="Add an optional note"
                        val="{{ old('notes', $data->notes) }}"
                        required=""
                        extraclasses="mb-0"
                        label="Notes"
                        rows="4"
                    />
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Update Point Award" />
            </div>
        </x-auth.form>
    </x-auth.card>
</x-layouts.auth>

{{-- @push('auth_scripts') --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const merchantSelect = document.getElementById('merchant_id');
        const siteSelect = document.getElementById('site_id');

        if (!merchantSelect || !siteSelect) {
            return;
        }

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
        };

        merchantSelect.addEventListener('change', filterSites);
        filterSites();
    });
</script>
{{-- @endpush --}}

