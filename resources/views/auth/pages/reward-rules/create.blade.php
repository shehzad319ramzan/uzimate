<x-layouts.auth>
    <x-slot name="pageTitle">Add Reward Rule</x-slot>

    <x-auth.card card-header="Add Reward Rule" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('reward-rules.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>

        <x-auth.form form-action="{{ route('reward-rules.store') }}">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="merchant_id" class="form-label">Merchant</label>
                    <select class="form-select @error('merchant_id') is-invalid @enderror" id="merchant_id" name="merchant_id">
                        <option value="">Global (all merchants)</option>
                        @foreach($merchants as $merchant)
                            <option value="{{ $merchant->id }}" {{ old('merchant_id') == $merchant->id ? 'selected' : '' }}>
                                {{ $merchant->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Leave empty for a rule that applies to all merchants.</small>
                    @error('merchant_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="action_type" class="form-label">Action Type <span class="text-danger">*</span></label>
                    <select class="form-select @error('action_type') is-invalid @enderror" id="action_type" name="action_type" required>
                        <option value="">Select action type</option>
                        @foreach($actionTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('action_type') == $key ? 'selected' : '' }}>{{ $label }} ({{ $key }})</option>
                        @endforeach
                    </select>
                    @error('action_type')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <x-auth.input-field type="text" name="label" id="label" required="true"
                        place="e.g. QR Code Scanned" val="{{ old('label') }}" label="Display Label" />
                    @error('label')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <x-auth.input-field type="number" name="points" id="points"
                        place="e.g. 80 (optional)" val="{{ old('points') }}" label="Points (optional)" min="0" />
                    <small class="text-muted">Points to award when this action happens (e.g. first login). Leave empty if not applicable.</small>
                    @error('points')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="trigger_condition" class="form-label">Trigger <span class="text-danger">*</span></label>
                    <select class="form-select @error('trigger_condition') is-invalid @enderror" id="trigger_condition" name="trigger_condition" required>
                        @foreach($triggerConditions as $key => $label)
                            <option value="{{ $key }}" {{ old('trigger_condition', 'every_time') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">First time only = award points only the first time (e.g. first login).</small>
                    @error('trigger_condition')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="is_active" class="form-label">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                    @error('is_active')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Create Reward Rule" />
            </div>
        </x-auth.form>
    </x-auth.card>
</x-layouts.auth>
