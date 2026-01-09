<x-layouts.auth>
    <x-slot name="pageTitle">Customer Log Details</x-slot>

    <x-auth.card card-header="Customer Log Details" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('customerlogs.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>

        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th class="text-muted" style="width: 220px;">Date/Time</th>
                        <td>{{ optional($data->created_at)->format('d M Y, H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Customer</th>
                        <td>
                            <div class="fw-semibold">{{ $data->user->full_name ?? '-' }}</div>
                            <small class="text-muted">{{ $data->user->email ?? 'N/A' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Action Type</th>
                        <td>
                            @php
                                $badgeClass = match($data->action_type) {
                                    'point_earned' => 'bg-success',
                                    'point_redeemed' => 'bg-danger',
                                    'point_expired' => 'bg-warning',
                                    'spin_completed' => 'bg-info',
                                    'offer_redeemed' => 'bg-primary',
                                    'qr_code_scanned' => 'bg-secondary',
                                    default => 'bg-dark',
                                };
                                $actionLabel = ucfirst(str_replace('_', ' ', $data->action_type));
                            @endphp
                            <span class="badge {{ $badgeClass }} fs-6">{{ $actionLabel }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Category</th>
                        <td>
                            <span class="badge bg-light text-dark fs-6">{{ ucfirst($data->action_category) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Description</th>
                        <td>{{ $data->description }}</td>
                    </tr>
                    @if($data->site)
                    <tr>
                        <th class="text-muted">Site</th>
                        <td>
                            <div class="fw-semibold">{{ $data->site->name }}</div>
                            <small class="text-muted">Merchant: {{ $data->merchant->name ?? $data->site->merchant->name ?? '-' }}</small>
                        </td>
                    </tr>
                    @endif
                    @if($data->points_affected !== null)
                    <tr>
                        <th class="text-muted">Points Affected</th>
                        <td>
                            @if($data->points_affected > 0)
                                <span class="badge bg-success fs-6">+{{ number_format($data->points_affected) }}</span>
                            @elseif($data->points_affected < 0)
                                <span class="badge bg-danger fs-6">{{ number_format($data->points_affected) }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @if($data->points_balance_before !== null || $data->points_balance_after !== null)
                    <tr>
                        <th class="text-muted">Points Balance</th>
                        <td>
                            @if($data->points_balance_before !== null && $data->points_balance_after !== null)
                                <span class="text-muted">{{ number_format($data->points_balance_before) }}</span>
                                <i class="fas fa-arrow-right mx-2"></i>
                                <span class="fw-semibold">{{ number_format($data->points_balance_after) }}</span>
                            @elseif($data->points_balance_before !== null)
                                Before: <span class="text-muted">{{ number_format($data->points_balance_before) }}</span>
                            @elseif($data->points_balance_after !== null)
                                After: <span class="fw-semibold">{{ number_format($data->points_balance_after) }}</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @if($data->performedBy)
                    <tr>
                        <th class="text-muted">Performed By</th>
                        <td>
                            <div class="fw-semibold">{{ $data->performedBy->full_name }}</div>
                            <small class="text-muted">{{ $data->performedBy->email }}</small>
                        </td>
                    </tr>
                    @endif
                    @if($data->related_model_type && $data->related_model_id)
                    <tr>
                        <th class="text-muted">Related Record</th>
                        <td>
                            <span class="badge bg-info">{{ class_basename($data->related_model_type) }}</span>
                            <span class="text-muted ms-2">ID: {{ $data->related_model_id }}</span>
                        </td>
                    </tr>
                    @endif
                    @if($data->ip_address)
                    <tr>
                        <th class="text-muted">IP Address</th>
                        <td>{{ $data->ip_address }}</td>
                    </tr>
                    @endif
                    @if($data->user_agent)
                    <tr>
                        <th class="text-muted">User Agent</th>
                        <td><small>{{ $data->user_agent }}</small></td>
                    </tr>
                    @endif
                    @if($data->metadata)
                    <tr>
                        <th class="text-muted">Metadata</th>
                        <td>
                            <pre class="mb-0 bg-light p-2 rounded">{{ json_encode($data->metadata, JSON_PRETTY_PRINT) }}</pre>
                        </td>
                    </tr>
                    @endif
                    @if($data->location_data)
                    <tr>
                        <th class="text-muted">Location Data</th>
                        <td>
                            <pre class="mb-0 bg-light p-2 rounded">{{ json_encode($data->location_data, JSON_PRETTY_PRINT) }}</pre>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </x-auth.card>
</x-layouts.auth>

