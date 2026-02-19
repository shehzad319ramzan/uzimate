<x-settings title="Spin Wheel" sub-title="Configure the spin wheel: daily limit, outcome chances, and points range (points only, no offer or discount)">
    <x-auth.card card-header="Spin Wheel Configuration" header-button="true">
        <x-auth.form form-action="{{ route('settings.spin_update') }}">
            @method('PUT')

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="alert alert-info mb-3 p-3">
                <strong class="d-block mb-2">How the Spin Wheel works</strong>
                <ul class="mb-0 ps-3 small">
                    <li><strong>Spins per day</strong> — Each user can spin the wheel only this many times per day (e.g. 1 = once per day). The limit is per user, not per site.</li>
                    <li><strong>Outcome chances (Nothing &amp; Points only)</strong> — The wheel awards only <strong>points</strong> or <strong>nothing</strong>. These two values are the chance in <em>percent</em> for each result and must add up to <strong>100</strong> (e.g. 50 + 50 = 100).</li>
                    <li><strong>Points range</strong> — When the wheel lands on &quot;Points&quot;, the user wins a random number of points between Min and Max (e.g. 10–90).</li>
                    <li><strong>Nothing</strong> — No reward; the user can try again next spin or next day.</li>
                    <li class="text-muted mt-1">For spin points to be added to the user&#39;s balance, ensure a <strong>Reward Rule</strong> for action &quot;Spin completed&quot; is active (Reward Rules section).</li>
                </ul>
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

            <h6 class="mt-4 mb-2">Wheel outcome chances (percent, sum = 100) — points</h6>
            <div class="row">
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="outcome_nothing" id="outcome_nothing" place="e.g. 50"
                        val="{{ $data->spin_outcome_nothing ?? config('spin.outcomes.nothing', 50) }}" required="true" label="Nothing" />
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="outcome_points" id="outcome_points" place="e.g. 50"
                        val="{{ $data->spin_outcome_points ?? config('spin.outcomes.points', 50) }}" required="true" label="Points" />
                </div>
                <input type="hidden" name="outcome_offer" value="0">
                <input type="hidden" name="outcome_discount" value="0">
            </div>

            <h6 class="mt-4 mb-2">Points range (when result is &quot;Points&quot;)</h6>
            <div class="row">
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="points_min" id="points_min" place="e.g. 10"
                        val="{{ $data->spin_points_min ?? config('spin.points_range.0', 25) }}" required="true" label="Min points" />
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="points_max" id="points_max" place="e.g. 90"
                        val="{{ $data->spin_points_max ?? config('spin.points_range.1', 100) }}" required="true" label="Max points" />
                </div>
            </div>
            <input type="hidden" name="discount_min" value="0">
            <input type="hidden" name="discount_max" value="0">

            <div class="row mt-3">
                <div class="col-md-12">
                    <x-auth.input-button btn-class="btn-primary" btn-type="submit"
                        btn-value="{{ __('Update Spin Wheel') }}" />
                </div>
            </div>

        </x-auth.form>
    </x-auth.card>
</x-settings>
