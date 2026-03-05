<x-notification-management title="Miss You Notification" sub-title="Re-engage customers who have not logged in for a certain period" :blade="request()->route('blade')">
    @php $config = $setting->config ?? []; $channels = $config['channels'] ?? ['email', 'push']; $inactiveDays = $config['inactive_days'] ?? 7; @endphp

    @can('edit_notification')
    <x-auth.card card-header="Settings" header-button="true" class="mb-4">
        <x-auth.form form-action="{{ route('notifications.update-settings', 'miss_you') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Inactive days threshold</label>
                    <select name="inactive_days" class="form-select" required>
                        @foreach($inactiveOptions ?? [7, 14, 21, 30, 60, 90] as $d)
                            <option value="{{ $d }}" {{ (int) $inactiveDays === $d ? 'selected' : '' }}>{{ $d }} days</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Customers who have not logged in for more than this many days.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Channels</label>
                    <div class="d-flex gap-3">
                        @foreach(\App\Models\NotificationSetting::CHANNELS as $key => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="{{ $key }}" id="ch_m_{{ $key }}" {{ in_array($key, $channels) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ch_m_{{ $key }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Message template</label>
                    <textarea name="message_template" class="form-control" rows="2" maxlength="1000" placeholder="We miss you, @{{ name }}!">{{ $config['message_template'] ?? '' }}</textarea>
                    <small class="text-muted">Use @{{ name }} for customer name.</small>
                </div>
                <div class="col-12">
                    <input type="hidden" name="type" value="miss_you">
                    <button type="submit" class="btn btn-primary">Save settings</button>
                </div>
            </div>
        </x-auth.form>
    </x-auth.card>
    @endcan

    @can('add_notification')
    <x-auth.card card-header="Send Miss You notification" header-button="true">
        <form action="{{ route('notifications.send-miss-you') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Inactive for (days)</label>
                <select name="inactive_days" id="send_inactive_days" class="form-select">
                    @foreach($inactiveOptions ?? [7, 14, 21, 30, 60, 90] as $d)
                        <option value="{{ $d }}" {{ (int) $inactiveDays === $d ? 'selected' : '' }}>{{ $d }} days</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="send_to_all_inactive" value="1" id="send_all_inactive" checked>
                    <label class="form-check-label" for="send_all_inactive">Send to all inactive customers (based on days above)</label>
                </div>
            </div>
            <div id="inactive_preview_wrap" class="mb-3 d-none">
                <div class="alert alert-info py-2 mb-2">
                    <strong id="inactive_preview_count">0</strong> inactive customer(s) will receive this notification.
                </div>
                <details class="small">
                    <summary>Show list (first 50)</summary>
                    <ul id="inactive_preview_list" class="list-unstyled mt-1 mb-0 ps-2 small"></ul>
                </details>
            </div>
            <div class="mb-3" id="wrap_select_inactive">
                <x-auth.select2
                    label="Or select customers (search; loads in pages, no full list)"
                    name="user_ids[]"
                    id="inactive_users_select"
                    placeholder="Type to search inactive customers..."
                    :data="[]"
                    :existing-id="old('user_ids', [])"
                    :is-ajax="true"
                    :ajax-route="route('notifications.api.inactive-customers')"
                    selectclass="select2-inactive-customers"
                    :ajax-data-keys="['days' => 'send_inactive_days']"
                    multiple
                />
                <small class="text-muted d-block mt-1">When "Send to all inactive" is checked, this selection is ignored.</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Channels</label>
                <div class="d-flex gap-3">
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="channels[]" value="email" id="send_ch_email" checked><label class="form-check-label" for="send_ch_email">Email</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="channels[]" value="push" id="send_ch_push" checked><label class="form-check-label" for="send_ch_push">Mobile Push</label></div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Send notification</button>
        </form>
    </x-auth.card>
    @endcan
</x-notification-management>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var sendAllCheckbox = document.getElementById('send_all_inactive');
    var selectEl = document.getElementById('inactive_users_select');
    var wrap = document.getElementById('wrap_select_inactive');
    var previewWrap = document.getElementById('inactive_preview_wrap');
    var previewCountEl = document.getElementById('inactive_preview_count');
    var previewListEl = document.getElementById('inactive_preview_list');
    var daysSelect = document.getElementById('send_inactive_days');
    var previewUrl = '{{ route("notifications.api.inactive-customers-preview") }}';

    function getDays() {
        return daysSelect ? parseInt(daysSelect.value, 10) || 7 : 7;
    }

    function renderPreview(data) {
        if (!previewCountEl) return;
        previewCountEl.textContent = data.count;
        if (previewListEl) {
            previewListEl.innerHTML = (data.preview || []).map(function(c) {
                return '<li>' + (c.text || '#' + c.id) + '</li>';
            }).join('');
        }
    }

    function loadPreview() {
        if (!previewWrap || !sendAllCheckbox || !sendAllCheckbox.checked) return;
        var days = getDays();
        fetch(previewUrl + '?days=' + encodeURIComponent(days))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                previewWrap.classList.remove('d-none');
                renderPreview(data);
            })
            .catch(function() {
                previewWrap.classList.add('d-none');
            });
    }

    function toggleSelect() {
        var disabled = !!sendAllCheckbox && sendAllCheckbox.checked;
        if (selectEl) selectEl.disabled = disabled;
        if (disabled && typeof $ !== 'undefined' && $('.select2-inactive-customers').data('select2')) {
            $('.select2-inactive-customers').val(null).trigger('change');
        }
        if (previewWrap) {
            if (disabled) loadPreview();
            else previewWrap.classList.add('d-none');
        }
    }

    if (sendAllCheckbox) sendAllCheckbox.addEventListener('change', toggleSelect);
    if (daysSelect) daysSelect.addEventListener('change', function() {
        if (sendAllCheckbox && sendAllCheckbox.checked) loadPreview();
    });
    toggleSelect();
});
</script>
