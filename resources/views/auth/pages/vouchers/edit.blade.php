<x-layouts.auth>
    <x-slot name="pageTitle">Edit Voucher</x-slot>
    <x-auth.card card-header="Edit Voucher" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('vouchers.index') }}" link-value="{{ __('Back') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>
        <x-auth.form form-action="{{ route('vouchers.update', $data->id) }}" enctype="true">
            @method('PUT')
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Voucher Image</label>
                    <x-auth.upload-file image="{{ $data->image() }}" name="{{ $data->title ?? '' }}" />
                    @error('file')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-8 mb-3">
                    <x-auth.input-field type="text" name="title" id="title" required="true"
                        place="Voucher title" val="{{ old('title', $data->title) }}" label="Title" />
                </div>
                <div class="col-md-4 mb-3">
                    <label for="points_required" class="form-label">Points required <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('points_required') is-invalid @enderror"
                        name="points_required" id="points_required" min="0" required
                        placeholder="Auto when offers selected" value="{{ old('points_required', $data->points_required) }}" />
                    <small class="text-muted" id="points_required_hint">Auto-calculated when offers are selected (sum of each offer's points). Change offers to update.</small>
                    @error('points_required')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="merchant_id" class="form-label">Merchant <span class="text-danger">*</span></label>
                    <select class="form-select @error('merchant_id') is-invalid @enderror" name="merchant_id"
                        id="merchant_id" required>
                        <option value="">— Select Merchant —</option>
                        @foreach ($merchants as $m)
                            <option value="{{ $m->id }}"
                                {{ old('merchant_id', $data->merchant_id) == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}</option>
                        @endforeach
                    </select>
                    @error('merchant_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <x-auth.select2 label="Link to offers (optional)" name="offer_ids[]" id="offer_ids"
                        :data="$offers" :existing-id="old('offer_ids', $data->offers->pluck('id')->toArray())" placeholder="Select one or more offers"
                        selectclass="select2-offers" multiple />
                    <small class="text-muted">Only offers for the selected merchant are shown. Change merchant to load
                        their offers.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="valid_until" class="form-label">Valid until</label>
                    <input type="date" class="form-control" name="valid_until" id="valid_until"
                        value="{{ old('valid_until', $data->valid_until?->format('Y-m-d')) }}" />
                </div>
                <div class="col-md-12 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" name="status" id="status">
                        <option value="1" {{ old('status', $data->status) == '1' ? 'selected' : '' }}>Active
                        </option>
                        <option value="0" {{ old('status', $data->status) == '0' ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <x-auth.editor name="description" label="Description" :value="old('description', $data->description ?? '')" />
                </div>
                <div class="col-md-12 mb-3">
                    <x-auth.editor name="terms_and_conditions" label="Terms and conditions" id="terms_and_conditions"
                        :value="old('terms_and_conditions', $data->terms_and_conditions ?? '')" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end">
                    <a href="{{ route('vouchers.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Update Voucher" />
                </div>
            </div>
        </x-auth.form>
    </x-auth.card>




</x-layouts.auth>
    <script>
        $(document).ready(function() {
            var $merchant = $('#merchant_id');
            var $offerSelect = $('#offer_ids');
            var $pointsRequired = $('#points_required');
            var $pointsHint = $('#points_required_hint');
            var offersByMerchantUrl = @json(route('vouchers.offers-by-merchant', ['merchantId' => '__MERCHANT__']));
            var currentSelectedIds = @json(old('offer_ids', $data->offers->pluck('id')->toArray()));
            var offerPointsMap = {};

            function updatePointsFromOffers() {
                var selected = $offerSelect.val();
                if (selected && selected.length > 0) {
                    var total = 0;
                    selected.forEach(function(id) {
                        total += offerPointsMap[id] || 0;
                    });
                    $pointsRequired.val(total);
                    $pointsRequired.prop('readonly', true);
                    $pointsHint.text('Auto-calculated: sum of selected offers (' + selected.length + ' offer(s)).');
                } else {
                    $pointsRequired.prop('readonly', false);
                    $pointsHint.text('Enter points for a standalone voucher, or select offers above to auto-fill (sum of each offer\'s points).');
                }
            }

            function reinitOfferSelect2() {
                if ($offerSelect.length && $offerSelect.hasClass('select2-hidden-accessible')) {
                    $offerSelect.select2('destroy');
                }
            }

            function initOfferSelect2(selectedIds) {
                if (!$offerSelect.length) return;
                var placeholder = $offerSelect.data('placeholder') || 'Select one or more offers';
                $offerSelect.select2({
                    dropdownParent: $offerSelect.parent(),
                    placeholder: placeholder,
                    allowClear: true,
                    theme: 'classic',
                    width: '100%'
                });
                $offerSelect.off('change.points').on('change.points', updatePointsFromOffers);
                if (selectedIds && selectedIds.length) {
                    $offerSelect.val(selectedIds).trigger('change');
                }
                updatePointsFromOffers();
            }

            function loadOffersForMerchant(merchantId, keepSelection) {
                reinitOfferSelect2();
                $offerSelect.find('option').not(':first').remove();
                $offerSelect.val(null);
                offerPointsMap = {};

                if (!merchantId) {
                    $pointsRequired.prop('readonly', false);
                    initOfferSelect2();
                    return;
                }

                var url = offersByMerchantUrl.replace('__MERCHANT__', merchantId);
                $.get(url).done(function(offers) {
                    if (Array.isArray(offers)) {
                        offers.forEach(function(o) {
                            offerPointsMap[o.id] = o.points != null ? o.points : 0;
                            $offerSelect.append(new Option(o.text, o.id, false, false));
                        });
                    }
                    var ids = (keepSelection && currentSelectedIds && currentSelectedIds.length) ?
                        currentSelectedIds : [];
                    initOfferSelect2(ids);
                    currentSelectedIds = [];
                }).fail(function() {
                    initOfferSelect2();
                });
            }

            $merchant.on('change', function() {
                var merchantId = $(this).val();
                currentSelectedIds = [];
                loadOffersForMerchant(merchantId || null);
            });

            var initialMerchant = $merchant.val();
            if (initialMerchant) {
                loadOffersForMerchant(initialMerchant, true);
            }
        });
    </script>
