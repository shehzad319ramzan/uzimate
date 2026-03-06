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
    {{-- Send by segment table (like Birthday): 7, 14, 21, 30, 60 days, All, Custom --}}
    <x-auth.card card-header="Send by segment" header-button="true" class="mb-4">
        <p class="text-muted small mb-3">Choose an inactive segment and send Miss You notifications immediately. View the user list or send in one click.</p>
        <div class="mb-3">
            <label class="form-label small fw-medium">Send via</label>
            <div class="d-flex gap-4 flex-wrap">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="m_channel_email" id="m_channel_email" value="email" checked>
                    <label class="form-check-label" for="m_channel_email">Email</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="m_channel_push" id="m_channel_push" value="push" checked>
                    <label class="form-check-label" for="m_channel_push">Mobile Push</label>
                </div>
            </div>
            <small class="text-muted">Select at least one channel for each send from the table below.</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="m_segment_table">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="rounded-start">Segment</th>
                        <th scope="col">Total users</th>
                        <th scope="col" class="text-end rounded-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach([7, 14, 21, 30, 60] as $d)
                    <tr data-days="{{ $d }}">
                        <td><span class="fw-medium">Inactive {{ $d }} days</span></td>
                        <td><span class="m_segment_count badge bg-secondary">—</span></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary m_btn_view" data-days="{{ $d }}" data-label="Inactive {{ $d }} days">View users</button>
                            <button type="button" class="btn btn-sm btn-success m_btn_send" data-days="{{ $d }}">Send</button>
                        </td>
                    </tr>
                    @endforeach
                    <tr data-days="all" class="table-light">
                        <td><span class="fw-semibold">All customers</span></td>
                        <td><span class="m_segment_count badge bg-dark">—</span></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary m_btn_view" data-days="all" data-label="All customers">View users</button>
                            <button type="button" class="btn btn-sm btn-success m_btn_send" data-days="all">Send</button>
                        </td>
                    </tr>
                    <tr id="m_custom_row" class="border-top">
                        <td>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-medium">Custom days inactive</span>
                                <input type="number" id="m_custom_days" class="form-control form-control-sm" style="width: 5rem;" min="1" max="365" placeholder="e.g. 90">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="m_custom_apply">Apply</button>
                            </div>
                        </td>
                        <td><span class="m_segment_count badge bg-info" id="m_custom_count">—</span></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary m_btn_view d-none" id="m_custom_btn_view" data-days="" data-label="">View users</button>
                            <button type="button" class="btn btn-sm btn-success m_btn_send d-none" id="m_custom_btn_send" data-days="">Send</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-2 d-flex justify-content-end">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="m_refresh_counts">Refresh counts</button>
        </div>
    </x-auth.card>

    {{-- Modal: list users for selected segment --}}
    <div class="modal fade" id="m_users_modal" tabindex="-1" aria-labelledby="m_users_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="m_users_modal_label">Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div id="m_modal_loading" class="text-center py-4 d-none">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 mb-0 small text-muted">Loading users…</p>
                    </div>
                    <div id="m_modal_content" class="d-none">
                        <p class="text-muted small mb-2"><strong id="m_modal_count">0</strong> user(s) in this segment.</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead><tr><th>#</th><th>Name / Email</th></tr></thead>
                                <tbody id="m_modal_list"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="m_modal_send_btn" data-days="">Send to these users</button>
                </div>
            </div>
        </div>
    </div>

    @endcan
</x-notification-management>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var countsUrl = '{{ route("notifications.api.inactive-counts") }}';
    var previewUrl = '{{ route("notifications.api.inactive-customers-preview") }}';
    var sendUrl = '{{ route("notifications.send-miss-you") }}';
    var csrf = '{{ csrf_token() }}';

    var segmentTable = document.getElementById('m_segment_table');
    var refreshCountsBtn = document.getElementById('m_refresh_counts');
    var modal = document.getElementById('m_users_modal');
    var modalLabel = document.getElementById('m_users_modal_label');
    var modalLoading = document.getElementById('m_modal_loading');
    var modalContent = document.getElementById('m_modal_content');
    var modalCountEl = document.getElementById('m_modal_count');
    var modalListEl = document.getElementById('m_modal_list');
    var modalSendBtn = document.getElementById('m_modal_send_btn');
    var customDaysInput = document.getElementById('m_custom_days');
    var customApplyBtn = document.getElementById('m_custom_apply');
    var customCountEl = document.getElementById('m_custom_count');
    var customBtnView = document.getElementById('m_custom_btn_view');
    var customBtnSend = document.getElementById('m_custom_btn_send');

    function getSelectedChannels() {
        var ch = [];
        if (document.getElementById('m_channel_email') && document.getElementById('m_channel_email').checked) ch.push('email');
        if (document.getElementById('m_channel_push') && document.getElementById('m_channel_push').checked) ch.push('push');
        return ch;
    }

    function showMessage(message, isSuccess) {
        if (typeof showToaster === 'function') showToaster(isSuccess ? 'success' : 'error', message, isSuccess ? 'Success' : 'Error');
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

    function loadSegmentCounts() {
        if (!segmentTable) return;
        fetch(countsUrl)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                segmentTable.querySelectorAll('tbody tr').forEach(function(row) {
                    if (row.id === 'm_custom_row') return;
                    var days = row.getAttribute('data-days');
                    var countEl = row.querySelector('.m_segment_count');
                    if (!countEl) return;
                    var n = data[days] !== undefined ? data[days] : '—';
                    countEl.textContent = n;
                    countEl.className = 'm_segment_count badge ' + (days === 'all' ? 'bg-dark' : 'bg-secondary');
                });
            })
            .catch(function() {});
    }

    function sendForDays(daysValue, sendBtn, done) {
        var channels = getSelectedChannels();
        if (channels.length === 0) {
            showMessage('Please select at least one channel (Email or Mobile Push).', false);
            return;
        }
        var form = new FormData();
        form.append('_token', csrf);
        form.append('inactive_days', daysValue);
        form.append('send_to_all_inactive', '1');
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
            var viewBtn = e.target.closest('.m_btn_view');
            var sendBtn = e.target.closest('.m_btn_send');
            if (viewBtn) {
                var days = viewBtn.getAttribute('data-days');
                var label = viewBtn.getAttribute('data-label') || days;
                modalLabel.textContent = 'Users — ' + label;
                modalSendBtn.setAttribute('data-days', days);
                modalLoading.classList.remove('d-none');
                modalContent.classList.add('d-none');
                var bsModal = typeof bootstrap !== 'undefined' && bootstrap.Modal ? bootstrap.Modal.getOrCreateInstance(modal) : null;
                if (bsModal) bsModal.show();
                fetch(previewUrl + '?days=' + encodeURIComponent(days))
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        modalLoading.classList.add('d-none');
                        modalContent.classList.remove('d-none');
                        if (modalCountEl) modalCountEl.textContent = data.count;
                        if (modalListEl) {
                            var list = (data.preview || []).map(function(c, i) {
                                return '<tr><td>' + (i + 1) + '</td><td>' + (c.text || c.email || '') + '</td></tr>';
                            }).join('');
                            modalListEl.innerHTML = list || '<tr><td colspan="2" class="text-muted">No users</td></tr>';
                        }
                    })
                    .catch(function() {
                        modalLoading.classList.add('d-none');
                        modalContent.classList.remove('d-none');
                        if (modalListEl) modalListEl.innerHTML = '<tr><td colspan="2" class="text-danger">Failed to load</td></tr>';
                    });
            }
            if (sendBtn) {
                var days = sendBtn.getAttribute('data-days');
                sendForDays(days, sendBtn, function() {});
            }
        });
    }
    if (refreshCountsBtn) refreshCountsBtn.addEventListener('click', loadSegmentCounts);
    if (modalSendBtn) {
        modalSendBtn.addEventListener('click', function() {
            var days = modalSendBtn.getAttribute('data-days');
            if (!days) return;
            sendForDays(days, modalSendBtn, function() {
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
            fetch(previewUrl + '?days=' + days)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    customCountEl.textContent = data.count;
                    customBtnView.setAttribute('data-days', String(days));
                    customBtnView.setAttribute('data-label', 'Inactive ' + days + ' days');
                    customBtnSend.setAttribute('data-days', String(days));
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
