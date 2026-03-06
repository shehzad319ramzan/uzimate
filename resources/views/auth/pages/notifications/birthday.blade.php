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
    {{-- Segment table: send by filter with View users modal and quick Send --}}
    <x-auth.card card-header="Send by segment" header-button="true" class="mt-4">
        <p class="text-muted small mb-3">Choose a segment and send birthday notifications immediately. View the user list or send in one click.</p>
        <div class="mb-3">
            <label class="form-label small fw-medium">Send via</label>
            <div class="d-flex gap-4 flex-wrap">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="b_channel_email" id="b_channel_email" value="email" checked>
                    <label class="form-check-label" for="b_channel_email">Email</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="b_channel_push" id="b_channel_push" value="push" checked>
                    <label class="form-check-label" for="b_channel_push">Mobile Push</label>
                </div>
            </div>
            <small class="text-muted">Select at least one channel for each send from the table below.</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="b_segment_table">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="rounded-start">Segment</th>
                        <th scope="col">Total users</th>
                        <th scope="col" class="text-end rounded-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr data-filter="today">
                        <td><span class="fw-medium">Today's birthdays</span></td>
                        <td><span class="b_segment_count badge bg-secondary">—</span></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary b_btn_view" data-filter="today" data-label="Today's birthdays">View users</button>
                            <button type="button" class="btn btn-sm btn-success b_btn_send" data-filter="today">Send</button>
                        </td>
                    </tr>
                    @for ($d = 1; $d <= 7; $d++)
                    <tr data-filter="{{ $d }}">
                        <td><span class="fw-medium">{{ $d }} day{{ $d > 1 ? 's' : '' }} before</span></td>
                        <td><span class="b_segment_count badge bg-secondary">—</span></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary b_btn_view" data-filter="{{ $d }}" data-label="{{ $d }} day{{ $d > 1 ? 's' : '' }} before">View users</button>
                            <button type="button" class="btn btn-sm btn-success b_btn_send" data-filter="{{ $d }}">Send</button>
                        </td>
                    </tr>
                    @endfor
                    <tr data-filter="all" class="table-light">
                        <td><span class="fw-semibold">All customers</span></td>
                        <td><span class="b_segment_count badge bg-dark">—</span></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary b_btn_view" data-filter="all" data-label="All customers">View users</button>
                            <button type="button" class="btn btn-sm btn-success b_btn_send" data-filter="all">Send</button>
                        </td>
                    </tr>
                    <tr id="b_custom_row" class="border-top">
                        <td>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-medium">Custom days before</span>
                                <input type="number" id="b_custom_days" class="form-control form-control-sm" style="width: 5rem;" min="1" max="365" placeholder="e.g. 10">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="b_custom_apply">Apply</button>
                            </div>
                        </td>
                        <td><span class="b_segment_count badge bg-info" id="b_custom_count">—</span></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary b_btn_view d-none" id="b_custom_btn_view" data-filter="" data-label="">View users</button>
                            <button type="button" class="btn btn-sm btn-success b_btn_send d-none" id="b_custom_btn_send" data-filter="">Send</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-2 d-flex justify-content-end">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="b_refresh_counts">Refresh counts</button>
        </div>
    </x-auth.card>

    {{-- Modal: list users for selected segment --}}
    <div class="modal fade" id="b_users_modal" tabindex="-1" aria-labelledby="b_users_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="b_users_modal_label">Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div id="b_modal_loading" class="text-center py-4 d-none">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 mb-0 small text-muted">Loading users…</p>
                    </div>
                    <div id="b_modal_content" class="d-none">
                        <p class="text-muted small mb-2"><strong id="b_modal_count">0</strong> user(s) in this segment.</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead><tr><th>#</th><th>Name / Email</th><th>Date of birth</th></tr></thead>
                                <tbody id="b_modal_list"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="b_modal_send_btn" data-filter="">Send to these users</button>
                </div>
            </div>
        </div>
    </div>

    @endcan

    <p class="text-muted small mt-3">Birthday notifications also run automatically via daily cron. Customers whose date of birth is today receive the message and reward points.</p>
</x-notification-management>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var countsUrl = '{{ route("notifications.api.birthday-counts") }}';
    var previewUrl = '{{ route("notifications.api.birthday-preview") }}';
    var sendUrl = '{{ route("notifications.send-birthday") }}';
    var csrf = '{{ csrf_token() }}';

    var segmentTable = document.getElementById('b_segment_table');
    var refreshCountsBtn = document.getElementById('b_refresh_counts');
    var modal = document.getElementById('b_users_modal');
    var modalLabel = document.getElementById('b_users_modal_label');
    var modalLoading = document.getElementById('b_modal_loading');
    var modalContent = document.getElementById('b_modal_content');
    var modalCountEl = document.getElementById('b_modal_count');
    var modalListEl = document.getElementById('b_modal_list');
    var modalSendBtn = document.getElementById('b_modal_send_btn');
    var customDaysInput = document.getElementById('b_custom_days');
    var customApplyBtn = document.getElementById('b_custom_apply');
    var customCountEl = document.getElementById('b_custom_count');
    var customBtnView = document.getElementById('b_custom_btn_view');
    var customBtnSend = document.getElementById('b_custom_btn_send');

    function loadSegmentCounts() {
        fetch(countsUrl)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!segmentTable) return;
                var rows = segmentTable.querySelectorAll('tbody tr');
                rows.forEach(function(row) {
                    var filter = row.getAttribute('data-filter');
                    if (filter === 'custom' || row.id === 'b_custom_row') return;
                    var countEl = row.querySelector('.b_segment_count');
                    if (!countEl) return;
                    var n = data[filter] !== undefined ? data[filter] : '—';
                    countEl.textContent = n;
                    countEl.className = 'b_segment_count badge ' + (filter === 'all' ? 'bg-dark' : 'bg-secondary');
                });
            })
            .catch(function() {});
    }

    function showMessage(message, isSuccess) {
        if (typeof showToaster === 'function') {
            showToaster(isSuccess ? 'success' : 'error', message, isSuccess ? 'Success' : 'Error');
        }
    }

    function setSendButtonLoading(btn, loading) {
        if (!btn) return;
        if (loading) {
            btn.dataset.originalText = btn.textContent;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Sending...';
        } else {
            btn.disabled = false;
            btn.textContent = btn.dataset.originalText || 'Send';
        }
    }

    function getSelectedChannels() {
        var ch = [];
        var emailEl = document.getElementById('b_channel_email');
        var pushEl = document.getElementById('b_channel_push');
        if (emailEl && emailEl.checked) ch.push('email');
        if (pushEl && pushEl.checked) ch.push('push');
        return ch;
    }

    function sendForFilter(filter, sendBtn, done) {
        var channels = getSelectedChannels();
        if (channels.length === 0) {
            showMessage('Please select at least one channel (Email or Mobile Push).', false);
            return;
        }
        var form = new FormData();
        form.append('_token', csrf);
        form.append('birthday_filter', filter);
        channels.forEach(function(c) { form.append('channels[]', c); });
        setSendButtonLoading(sendBtn, true);
        fetch(sendUrl, { method: 'POST', body: form, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) {
                    showMessage(res.message || 'Sent.', true);
                    loadSegmentCounts();
                } else {
                    showMessage(res.message || 'Failed.', false);
                }
            })
            .catch(function() { showMessage('Request failed.', false); })
            .finally(function() {
                setSendButtonLoading(sendBtn, false);
                if (typeof done === 'function') done();
            });
    }

    if (segmentTable) {
        loadSegmentCounts();
        segmentTable.addEventListener('click', function(e) {
            var viewBtn = e.target.closest('.b_btn_view');
            var sendBtn = e.target.closest('.b_btn_send');
            if (viewBtn) {
                var filter = viewBtn.getAttribute('data-filter');
                var label = viewBtn.getAttribute('data-label') || filter;
                modalLabel.textContent = 'Users — ' + label;
                modalSendBtn.setAttribute('data-filter', filter);
                modalLoading.classList.remove('d-none');
                modalContent.classList.add('d-none');
                var bsModal = typeof bootstrap !== 'undefined' && bootstrap.Modal ? bootstrap.Modal.getOrCreateInstance(modal) : null;
                if (bsModal) bsModal.show();
                fetch(previewUrl + '?filter=' + encodeURIComponent(filter))
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        modalLoading.classList.add('d-none');
                        modalContent.classList.remove('d-none');
                        if (modalCountEl) modalCountEl.textContent = data.count;
                        if (modalListEl) {
                            var list = (data.preview || []).map(function(c, i) {
                                return '<tr><td>' + (i + 1) + '</td><td>' + (c.text || c.email || '') + '</td><td>' + (c.date_of_birth || '—') + '</td></tr>';
                            }).join('');
                            modalListEl.innerHTML = list || '<tr><td colspan="3" class="text-muted">No users</td></tr>';
                        }
                    })
                    .catch(function() {
                        modalLoading.classList.add('d-none');
                        modalContent.classList.remove('d-none');
                        if (modalListEl) modalListEl.innerHTML = '<tr><td colspan="3" class="text-danger">Failed to load</td></tr>';
                    });
            }
            if (sendBtn) {
                var filter = sendBtn.getAttribute('data-filter');
                sendForFilter(filter, sendBtn, function() {});
            }
        });
    }
    if (refreshCountsBtn) refreshCountsBtn.addEventListener('click', loadSegmentCounts);
    if (modalSendBtn) {
        modalSendBtn.addEventListener('click', function() {
            var filter = modalSendBtn.getAttribute('data-filter');
            if (!filter) return;
            sendForFilter(filter, modalSendBtn, function() {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal && modal) {
                    var m = bootstrap.Modal.getInstance(modal);
                    if (m) m.hide();
                }
            });
        });
    }

    if (customApplyBtn && customDaysInput && customCountEl && customBtnView && customBtnSend) {
        customApplyBtn.addEventListener('click', function() {
            var days = parseInt(customDaysInput.value, 10);
            if (isNaN(days) || days < 1 || days > 365) {
                customCountEl.textContent = '—';
                customBtnView.classList.add('d-none');
                customBtnSend.classList.add('d-none');
                return;
            }
            customApplyBtn.disabled = true;
            fetch(previewUrl + '?filter=' + days)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    customCountEl.textContent = data.count;
                    customBtnView.setAttribute('data-filter', String(days));
                    customBtnView.setAttribute('data-label', days + ' day' + (days > 1 ? 's' : '') + ' before');
                    customBtnSend.setAttribute('data-filter', String(days));
                    customBtnView.classList.remove('d-none');
                    customBtnSend.classList.remove('d-none');
                })
                .catch(function() {
                    customCountEl.textContent = '—';
                    customBtnView.classList.add('d-none');
                    customBtnSend.classList.add('d-none');
                })
                .finally(function() { customApplyBtn.disabled = false; });
        });
    }
});
</script>
