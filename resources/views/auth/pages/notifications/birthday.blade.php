<x-notification-management title="Birthday Notification" sub-title="Greetings and reward points" :blade="request()->route('blade')">
    @php
        $config = $setting->config ?? [];
        $channels = $config['channels'] ?? ['email', 'push'];
        $rewardPoints = $config['reward_points'] ?? 5;
        $defaultBirthdayMessage = 'Happy Birthday, {{name}}! You\'ve earned {{points}} points.';
    @endphp

    @can('edit_notification')
    <x-auth.card card-header="Settings" header-button="true">
        <x-auth.form form-action="{{ route('notifications.update-settings', 'birthday') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Reward points</label>
                    <input type="number" name="reward_points" class="form-control" value="{{ $rewardPoints }}" min="0" max="100000" required>
                    <small class="text-muted">Points added automatically on birthday.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Channels</label>
                    <div class="d-flex gap-3">
                        @foreach(\App\Models\NotificationSetting::CHANNELS as $key => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="{{ $key }}" id="b_ch_{{ $key }}" {{ in_array($key, $channels) ? 'checked' : '' }}>
                                <label class="form-check-label" for="b_ch_{{ $key }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Message template</label>
                    <textarea name="message_template" class="form-control" rows="2" maxlength="1000">{{ $config['message_template'] ?? $defaultBirthdayMessage }}</textarea>
                    <small class="text-muted">Use @{{ name }}, @{{ points }}.</small>
                </div>
                <div class="col-12">
                    <input type="hidden" name="type" value="birthday">
                    <button type="submit" class="btn btn-primary">Save settings</button>
                </div>
            </div>
        </x-auth.form>
    </x-auth.card>
    @endcan

    @can('add_notification')
    <x-auth.card card-header="Send Birthday notification manually" header-button="true" class="mt-4">
        <form action="{{ route('notifications.send-birthday') }}" method="POST">
            @csrf
            <input type="hidden" name="channels[]" value="email">
            <input type="hidden" name="channels[]" value="push">
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="send_to_all_today" value="1" id="b_send_all_today">
                    <label class="form-check-label" for="b_send_all_today">Send to all customers with birthday today</label>
                </div>
            </div>
            <div class="mb-3" id="b_wrap_select">
                <x-auth.select2
                    label="Or select customers to send to"
                    name="user_ids[]"
                    id="birthday_users"
                    placeholder="Type to search customers..."
                    :data="[]"
                    :existing-id="old('user_ids', [])"
                    :is-ajax="true"
                    :ajax-route="route('notifications.api.customers')"
                    selectclass="select2-birthday-customers"
                    multiple
                />
                <small class="text-muted d-block mt-1">When "Send to all with birthday today" is checked, this selection is ignored.</small>
            </div>
            <button type="submit" class="btn btn-primary">Send Birthday notification</button>
        </form>
    </x-auth.card>
    @endcan

    <p class="text-muted small mt-3">Birthday notifications also run automatically via daily cron. Customers whose date of birth is today receive the message and reward points.</p>
</x-notification-management>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var sendAllToday = document.getElementById('b_send_all_today');
    var wrap = document.getElementById('b_wrap_select');
    var selectEl = document.getElementById('birthday_users');
    function toggle() {
        var disable = sendAllToday && sendAllToday.checked;
        if (wrap) wrap.style.display = disable ? 'none' : 'block';
        if (selectEl) selectEl.disabled = disable;
        if (disable && typeof $ !== 'undefined' && $('.select2-birthday-customers').data('select2')) {
            $('.select2-birthday-customers').val(null).trigger('change');
        }
    }
    if (sendAllToday) sendAllToday.addEventListener('change', toggle);
    toggle();
});
</script>
