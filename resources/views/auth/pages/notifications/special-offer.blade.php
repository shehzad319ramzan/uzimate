<x-notification-management title="Special Offer Notification" sub-title="Notify customers of new offers" :blade="request()->route('blade')">
    @php
        $config = $setting->config ?? [];
        $defaultOfferMessage = 'New offer: {{offer_title}}! Get great rewards.';
    @endphp
    @can('edit_notification')
    <x-auth.card card-header="Default message template" header-button="true" class="mb-4">
        <x-auth.form form-action="{{ route('notifications.update-settings', 'special_offer') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Message template (used when creating offer with &quot;Send notification&quot;)</label>
                <textarea name="message_template" class="form-control" rows="2" maxlength="1000">{{ $config['message_template'] ?? $defaultOfferMessage }}</textarea>
                <small class="text-muted">Use @{{ offer_title }}, @{{ name }}.</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Channels</label>
                <div class="d-flex gap-3">
                    @foreach(\App\Models\NotificationSetting::CHANNELS as $key => $label)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="channels[]" value="{{ $key }}" {{ in_array($key, $config['channels'] ?? ['email','push']) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <input type="hidden" name="type" value="special_offer">
            <button type="submit" class="btn btn-primary">Save</button>
        </x-auth.form>
    </x-auth.card>
    @endcan

    @can('add_notification')
    <x-auth.card card-header="Send offer notification manually" header-button="true">
        <form action="{{ route('notifications.send-special-offer') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Offer <span class="text-danger">*</span></label>
                <select name="offer_id" class="form-select" required>
                    <option value="">Select offer</option>
                    @foreach($offers ?? [] as $o)
                        <option value="{{ $o->id }}">{{ $o->title }} @if($o->site)({{ $o->site->name }})@endif</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Message <span class="text-danger">*</span></label>
                <textarea name="message" class="form-control" rows="3" required maxlength="2000" placeholder="e.g. Get 20% off!">{{ old('message') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Channels</label>
                <div class="d-flex gap-3">
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="channels[]" value="email" checked><label class="form-check-label">Email</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="channels[]" value="push" checked><label class="form-check-label">Mobile Push</label></div>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="send_to_all" value="1" id="offer_send_to_all">
                    <label class="form-check-label" for="offer_send_to_all">Send to all customers</label>
                </div>
            </div>
            <div class="mb-3" id="offer_wrap_select">
                <x-auth.select2
                    label="Or select customers (search; loads in pages)"
                    name="user_ids[]"
                    id="offer_users"
                    placeholder="Type to search customers..."
                    :data="[]"
                    :existing-id="old('user_ids', [])"
                    :is-ajax="true"
                    :ajax-route="route('notifications.api.customers')"
                    selectclass="select2-offer-customers"
                    multiple
                />
                <small class="text-muted d-block mt-1">When "Send to all customers" is checked, this selection is ignored.</small>
                @error('user_ids')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Send notification</button>
        </form>
    </x-auth.card>
    @endcan
</x-notification-management>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var sendAll = document.getElementById('offer_send_to_all');
    var wrap = document.getElementById('offer_wrap_select');
    var selectEl = document.getElementById('offer_users');
    function toggle() {
        var disable = sendAll && sendAll.checked;
        if (wrap) wrap.style.display = disable ? 'none' : 'block';
        if (selectEl) selectEl.disabled = disable;
        if (disable && typeof $ !== 'undefined' && $('.select2-offer-customers').data('select2')) {
            $('.select2-offer-customers').val(null).trigger('change');
        }
    }
    if (sendAll) sendAll.addEventListener('change', toggle);
    toggle();
});
</script>
