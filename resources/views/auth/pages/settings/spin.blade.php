<x-settings title="Spin Wheel" sub-title="Configure the spin wheel: daily limit, outcome chances, and point/discount ranges">
    <x-auth.card card-header="Spin Wheel Configuration" header-button="true">
        <x-auth.form form-action="{{ route('settings.spin_update') }}">
            @method('PUT')

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="alert alert-info mb-3 p-2 small">
                <strong>Outcome chances</strong> must sum to 100. <strong>Points/Discount ranges</strong> define min–max when the wheel lands on those outcomes.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="spins_per_day" id="spins_per_day" place="e.g. 1"
                        val="{{ $data->spin_spins_per_day ?? config('spin.spins_per_day', 1) }}" required="true" label="Spins per day (per user)" />
                </div>
                {{-- Default site: hidden for now (not used in code yet). Uncomment to show. --}}
                <div class="col-md-6 d-none">
                    <label for="default_site_id" class="form-label text-muted">Default site</label>
                    <select name="default_site_id" id="default_site_id" class="form-select">
                        <option value="">— Use first available —</option>
                        @foreach($sites ?? [] as $site)
                            <option value="{{ $site->id }}" {{ ($data->spin_default_site_id ?? '') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h6 class="mt-4 mb-2">Wheel outcome chances (percent, sum = 100)</h6>
            <div class="row">
                <div class="col-md-3">
                    <x-auth.input-field type="number" name="outcome_nothing" id="outcome_nothing" place="e.g. 50"
                        val="{{ $data->spin_outcome_nothing ?? config('spin.outcomes.nothing', 50) }}" required="true" label="Nothing" />
                </div>
                <div class="col-md-3">
                    <x-auth.input-field type="number" name="outcome_points" id="outcome_points" place="e.g. 30"
                        val="{{ $data->spin_outcome_points ?? config('spin.outcomes.points', 30) }}" required="true" label="Points" />
                </div>
                <div class="col-md-3">
                    <x-auth.input-field type="number" name="outcome_offer" id="outcome_offer" place="e.g. 15"
                        val="{{ $data->spin_outcome_offer ?? config('spin.outcomes.offer', 15) }}" required="true" label="Offer" />
                </div>
                <div class="col-md-3">
                    <x-auth.input-field type="number" name="outcome_discount" id="outcome_discount" place="e.g. 5"
                        val="{{ $data->spin_outcome_discount ?? config('spin.outcomes.discount', 5) }}" required="true" label="Discount" />
                </div>
            </div>

            <h6 class="mt-4 mb-2">Points range (when result is &quot;Points&quot;)</h6>
            <div class="row">
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="points_min" id="points_min" place="e.g. 25"
                        val="{{ $data->spin_points_min ?? config('spin.points_range.0', 25) }}" required="true" label="Min points" />
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="points_max" id="points_max" place="e.g. 100"
                        val="{{ $data->spin_points_max ?? config('spin.points_range.1', 100) }}" required="true" label="Max points" />
                </div>
            </div>

            <h6 class="mt-4 mb-2">Discount range (when result is &quot;Discount&quot;, percent)</h6>
            <div class="row">
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="discount_min" id="discount_min" place="e.g. 5"
                        val="{{ $data->spin_discount_min ?? config('spin.discount_range.0', 5) }}" required="true" label="Min discount %" />
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="discount_max" id="discount_max" place="e.g. 20"
                        val="{{ $data->spin_discount_max ?? config('spin.discount_range.1', 20) }}" required="true" label="Max discount %" />
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <x-auth.input-button btn-class="btn-primary" btn-type="submit"
                        btn-value="{{ __('Update Spin Wheel') }}" />
                </div>
            </div>

        </x-auth.form>
    </x-auth.card>
</x-settings>
