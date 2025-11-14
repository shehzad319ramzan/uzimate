<x-layouts.auth>
    <x-slot name="pageTitle">Create Merchant</x-slot>
    <x-auth.card card-header="Create Merchant" header-button="true">
        <x-auth.form form-action="{{ route('merchants.store') }}" enctype="true">
            <div class="row">
                <!-- Merchant Section -->
                <div class="col-md-12">
                    <h5 class="mb-3">Merchant</h5>
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="text" name="merchant_name" id="merchant_name" required="true"
                        place="Enter merchant name" val="{{ old('merchant_name') }}" extraclasses="mb-3"
                        label="Merchant Name" />
                </div>
                <div class="col-md-6">
                    <label class="form-label mb-2 d-block" style="font-weight: 500;">Merchant Logo</label>
                    <x-auth.upload-file image="" />
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="max_sites" id="max_sites" required="true"
                        place="Enter max sites" val="{{ old('max_sites', '1') }}" extraclasses="mb-3"
                        label="Max Sites" />
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="spin_after_days" id="spin_after_days"
                        place="Enter spin after days" val="{{ old('spin_after_days', '1') }}" extraclasses="mb-3"
                        label="Spin After (days)" />
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="scan_after_hours" id="scan_after_hours"
                        place="Enter scan after hours" val="{{ old('scan_after_hours', '6') }}" extraclasses="mb-3"
                        label="Scan After (hours)" />
                </div>
                <div class="col-md-12">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="use_other_merchant_points"
                            id="use_other_merchant_points" value="1"
                            {{ old('use_other_merchant_points') ? 'checked' : '' }}>
                        <label class="form-check-label" for="use_other_merchant_points">
                            Use other merchant's points for offers
                        </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 d-flex justify-content-end">
                    <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Create" />
                </div>
            </div>
        </x-auth.form>
    </x-auth.card>
</x-layouts.auth>
