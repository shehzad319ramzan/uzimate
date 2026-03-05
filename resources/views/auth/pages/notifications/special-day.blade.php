<x-notification-management title="Special Day Notification" sub-title="Send personalized messages for special occasions" :blade="request()->route('blade')">
    @can('add_notification')
    <x-auth.card card-header="Send Special Day notification" header-button="true">
        <form action="{{ route('notifications.send-special-day') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', 'Special Day') }}" maxlength="255" placeholder="e.g. Happy Holidays">
            </div>
            <div class="mb-3">
                <label class="form-label">Message <span class="text-danger">*</span></label>
                <textarea name="message" class="form-control" rows="4" required maxlength="2000" placeholder="Your custom message...">{{ old('message') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Channels</label>
                <div class="d-flex gap-3">
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="channels[]" value="email" id="sd_ch_email" checked><label class="form-check-label" for="sd_ch_email">Email</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="channels[]" value="push" id="sd_ch_push" checked><label class="form-check-label" for="sd_ch_push">Mobile Push</label></div>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="send_to_all" value="1" id="sd_send_to_all">
                    <label class="form-check-label" for="sd_send_to_all">Send to all customers</label>
                </div>
            </div>
            <div class="mb-3" id="sd_wrap_select">
                <x-auth.select2
                    label="Or select customers (search; loads in pages)"
                    name="user_ids[]"
                    id="special_day_users"
                    placeholder="Type to search customers..."
                    :data="[]"
                    :existing-id="old('user_ids', [])"
                    :is-ajax="true"
                    :ajax-route="route('notifications.api.customers')"
                    selectclass="select2-special-day-customers"
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
    var sendAll = document.getElementById('sd_send_to_all');
    var wrap = document.getElementById('sd_wrap_select');
    var selectEl = document.getElementById('special_day_users');
    function toggle() {
        var disable = sendAll && sendAll.checked;
        if (wrap) wrap.style.display = disable ? 'none' : 'block';
        if (selectEl) selectEl.disabled = disable;
        if (disable && typeof $ !== 'undefined' && $('.select2-special-day-customers').data('select2')) {
            $('.select2-special-day-customers').val(null).trigger('change');
        }
    }
    if (sendAll) sendAll.addEventListener('change', toggle);
    toggle();
});
</script>
