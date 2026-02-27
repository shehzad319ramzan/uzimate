<x-layouts.auth>
    <x-slot name="pageTitle">Create Voucher</x-slot>
    <x-auth.card card-header="Create Voucher" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('vouchers.index') }}" link-value="{{ __('Back') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>
        <x-auth.form form-action="{{ route('vouchers.store') }}" enctype="true">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Voucher Image</label>
                    <x-auth.upload-file image="" name="" />
                    @error('file')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="merchant_id" class="form-label">Merchant <span class="text-danger">*</span></label>
                    <select class="form-select @error('merchant_id') is-invalid @enderror" name="merchant_id" id="merchant_id" required>
                        <option value="">— Select Merchant —</option>
                        @foreach ($merchants as $m)
                            <option value="{{ $m->id }}" {{ old('merchant_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                    @error('merchant_id')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <x-auth.select2
                        label="Link to offers (optional)"
                        name="offer_ids[]"
                        id="offer_ids"
                        :data="$offers"
                        :existing-id="old('offer_ids', [])"
                        placeholder="Select a merchant first to see their offers"
                        selectclass="select2-offers"
                        multiple
                    />
                    <small class="text-muted">Select a merchant first; only that merchant's offers will appear here. You can leave blank for a standalone voucher.</small>
                </div>
                <div class="col-md-8 mb-3">
                    <x-auth.input-field type="text" name="title" id="title" required="true" place="Voucher title"
                        val="{{ old('title') }}" label="Title" />
                </div>
                <div class="col-md-4 mb-3">
                    <x-auth.input-field type="number" name="points_required" id="points_required" required="true"
                        place="e.g. 200" val="{{ old('points_required', '200') }}" label="Points required" />
                </div>
                <div class="col-md-6 mb-3">
                    <label for="valid_until" class="form-label">Valid until</label>
                    <input type="date" class="form-control" name="valid_until" id="valid_until" value="{{ old('valid_until') }}" />
                </div>
                <div class="col-md-12 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" name="status" id="status">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <x-auth.editor name="description" label="Description" :value="old('description')" />
                </div>
                <div class="col-md-12 mb-3">
                    <x-auth.editor name="terms_and_conditions" label="Terms and conditions" id="terms_and_conditions" :value="old('terms_and_conditions')" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end">
                    <a href="{{ route('vouchers.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Create Voucher" />
                </div>
            </div>
        </x-auth.form>
    </x-auth.card>
</x-layouts.auth>

<script>
$(document).ready(function() {
    var $merchant = $('#merchant_id');
    var $offerSelect = $('#offer_ids');
    var offersByMerchantUrl = @json(route('vouchers.offers-by-merchant', ['merchantId' => '__MERCHANT__']));

    function reinitOfferSelect2() {
        if ($offerSelect.length && $offerSelect.hasClass('select2-hidden-accessible')) {
            $offerSelect.select2('destroy');
        }
    }

    function initOfferSelect2(noResultsText) {
        if (!$offerSelect.length) return;
        var placeholder = $offerSelect.data('placeholder') || 'Select one or more offers';
        var opts = {
            dropdownParent: $offerSelect.parent(),
            placeholder: placeholder,
            allowClear: true,
            theme: 'classic',
            width: '100%'
        };
        if (noResultsText) {
            opts.language = { noResults: function() { return noResultsText; } };
        }
        $offerSelect.select2(opts);
    }

    function loadOffersForMerchant(merchantId) {
        reinitOfferSelect2();
        $offerSelect.find('option').not(':first').remove();
        $offerSelect.val(null).trigger('change');

        if (!merchantId) {
            $offerSelect.prop('disabled', true);
            initOfferSelect2('Select a merchant first to see their offers');
            return;
        }

        $offerSelect.prop('disabled', false);
        var url = offersByMerchantUrl.replace('__MERCHANT__', merchantId);
        $.get(url).done(function(offers) {
            if (Array.isArray(offers) && offers.length > 0) {
                offers.forEach(function(o) {
                    $offerSelect.append(new Option(o.text, o.id, false, false));
                });
                initOfferSelect2();
            } else {
                var $noOffers = new Option('No offers for this merchant', '', false, false);
                $noOffers.disabled = true;
                $offerSelect.append($noOffers);
                initOfferSelect2('No offers for this merchant');
            }
        }).fail(function(xhr) {
            var $err = new Option('Unable to load offers', '', false, false);
            $err.disabled = true;
            $offerSelect.append($err);
            initOfferSelect2('Unable to load offers. Please try again.');
            console.error('Offers load failed:', xhr.status, xhr.responseText);
        });
    }

    $merchant.on('change', function() {
        var merchantId = $(this).val();
        loadOffersForMerchant(merchantId || null);
    });

    var initialMerchant = $merchant.val();
    if (initialMerchant) {
        loadOffersForMerchant(initialMerchant);
    } else {
        $offerSelect.prop('disabled', true);
        initOfferSelect2('Select a merchant first to see their offers');
    }
});
</script>
