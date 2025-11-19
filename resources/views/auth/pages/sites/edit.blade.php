<x-layouts.auth>
    <x-slot name="pageTitle">Edit Site</x-slot>
    <x-auth.card card-header="Edit Site" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('sites.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>
        <x-auth.form form-action="{{ route('sites.update', $data->id) }}" enctype="true">
            @method('PUT')
            <div class="row">
                <!-- Merchant Selection and Logo Section -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="merchant_id" class="form-label">Merchant <span class="text-danger">*</span></label>
                        @if(!$isSuperAdmin && $selectedMerchantId)
                            <input type="hidden" name="merchant_id" value="{{ $selectedMerchantId }}">
                            <select class="form-select @error('merchant_id') is-invalid @enderror" id="merchant_id" disabled>
                                <option value="{{ $selectedMerchantId }}" selected>
                                    {{ $merchants->first()->name ?? 'N/A' }}
                                </option>
                            </select>
                        @else
                            <select class="form-select @error('merchant_id') is-invalid @enderror" name="merchant_id" id="merchant_id" required>
                                <option value="">Select Merchant</option>
                                @foreach($merchants as $merchant)
                                    <option value="{{ $merchant->id }}" {{ old('merchant_id', $data->merchant_id ?? '') == $merchant->id ? 'selected' : '' }}>
                                        {{ $merchant->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('merchant_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Logo Section -->
                <div class="col-md-6 mb-3">
                    <label class="form-label mb-2 d-block" style="font-weight: 500;">Site Logo</label>
                    <div id="siteLogoUploadWrapper">
                        <x-auth.upload-file image="{{ !$data->use_merchant_logo ? $data->logo() : '' }}" name="{{ $data->name ?? '' }}" />
                    </div>
                    <div id="merchantLogoNotice" class="alert alert-info mt-3 d-none">
                        Merchant logo will be used automatically for this site. Logo upload is disabled.
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="use_merchant_logo" id="use_merchant_logo" value="1" {{ old('use_merchant_logo', $data->use_merchant_logo ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="use_merchant_logo">
                            Use merchant logo for this site
                        </label>
                    </div>
                </div>

                <!-- Information Fields - Two Column Layout -->
                <div class="col-md-12">
                    <h5 class="mb-3">Information</h5>
                </div>

                <!-- Left Column -->
                <div class="col-md-6">
                    <x-auth.input-field type="text" name="name" id="name" required="true"
                        place="Enter site name" val="{{ old('name', $data->name ?? '') }}" extraclasses="mb-3"
                        label="Name" />
                </div>
                <!-- Right Column -->
                <div class="col-md-6">
                    <x-auth.input-field type="tel" name="phone" id="phone" required="true"
                        place="Enter phone number" val="{{ old('phone', $data->phone ?? '') }}" extraclasses="mb-3"
                        label="Phone" />
                </div>

                <!-- Left Column -->
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="points" id="points" required="true"
                        place="Enter points" val="{{ old('points', $data->points ?? '') }}" extraclasses="mb-3"
                        label="Points" />
                </div>
                <!-- Right Column -->
                <div class="col-md-6">
                    <x-auth.input-field type="text" name="address_line_1" id="address_line_1" required="true"
                        place="Enter address line 1" val="{{ old('address_line_1', $data->address_line_1 ?? '') }}" extraclasses="mb-3"
                        label="Address Line 1" />
                </div>

                <!-- Left Column -->
                <div class="col-md-6">
                    <x-auth.input-field type="text" name="address_line_2" id="address_line_2"
                        place="Enter address line 2" val="{{ old('address_line_2', $data->address_line_2 ?? '') }}" extraclasses="mb-3"
                        label="Address Line 2" />
                </div>
                <!-- Right Column -->
                <div class="col-md-6">
                    <x-auth.input-field type="text" name="city" id="city" required="true"
                        place="Enter city" val="{{ old('city', $data->city ?? '') }}" extraclasses="mb-3"
                        label="City" />
                </div>

                <!-- Left Column -->
                <div class="col-md-6">
                    <x-auth.input-field type="text" name="county" id="county"
                        place="Enter county" val="{{ old('county', $data->county ?? '') }}" extraclasses="mb-3"
                        label="County" />
                </div>
                <!-- Right Column -->
                <div class="col-md-6">
                    <x-auth.input-field type="text" name="postcode" id="postcode" required="true"
                        place="Enter postcode" val="{{ old('postcode', $data->postcode ?? '') }}" extraclasses="mb-3"
                        label="Postcode" />
                </div>

                <!-- Left Column -->
                <div class="col-md-6">
                    <x-auth.input-field type="text" name="country" id="country"
                        place="Enter country" val="{{ old('country', $data->country ?? 'United Kingdom') }}" extraclasses="mb-3"
                        label="Country" />
                </div>
                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="location" class="form-label">Location</label>
                        <select class="form-select" name="location" id="location">
                            <option value="">Select Location</option>
                            <option value="1" {{ old('location', $data->location ?? '') == '1' ? 'selected' : '' }}>Location 1</option>
                            <option value="2" {{ old('location', $data->location ?? '') == '2' ? 'selected' : '' }}>Location 2</option>
                        </select>
                    </div>
                </div>

                <!-- Left Column - Coordinates -->
                <div class="col-md-6">
                    <x-auth.input-field type="text" name="coordinates" id="coordinates"
                        place="Enter coordinates" val="{{ old('coordinates', $data->coordinates ?? '') }}" extraclasses="mb-3"
                        label="Coordinates" />
                </div>
            </div>

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
    document.addEventListener('DOMContentLoaded', function() {
        const useMerchantLogoCheckbox = document.getElementById('use_merchant_logo');
        const uploadWrapper = document.getElementById('siteLogoUploadWrapper');
        const merchantNotice = document.getElementById('merchantLogoNotice');

        function toggleLogoSection() {
            if (!useMerchantLogoCheckbox) {
                return;
            }

            if (useMerchantLogoCheckbox.checked) {
                uploadWrapper?.classList.add('d-none');
                merchantNotice?.classList.remove('d-none');
            } else {
                uploadWrapper?.classList.remove('d-none');
                merchantNotice?.classList.add('d-none');
            }
        }

        toggleLogoSection();

        useMerchantLogoCheckbox?.addEventListener('change', toggleLogoSection);
    });
</script>
{{-- @endpush --}}
