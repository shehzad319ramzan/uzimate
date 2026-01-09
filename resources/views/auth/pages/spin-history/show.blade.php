<x-layouts.auth>
    <x-slot name="pageTitle">Spin History Details</x-slot>

    <x-auth.card card-header="Spin History Details" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('spinhistories.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>

        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th class="text-muted" style="width: 220px;">Spin Number</th>
                        <td>
                            <span class="badge bg-info fs-6">#{{ $data->spin_number }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Date/Time</th>
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
                        <th class="text-muted">Site</th>
                        <td>
                            <div class="fw-semibold">{{ $data->site->name ?? '-' }}</div>
                            <small class="text-muted">Merchant: {{ $data->merchant->name ?? $data->site->merchant->name ?? '-' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Result Type</th>
                        <td>
                            @php
                                $badgeClass = match($data->spin_result_type) {
                                    'points' => 'bg-success',
                                    'offer' => 'bg-primary',
                                    'discount' => 'bg-warning',
                                    default => 'bg-secondary',
                                };
                                $resultLabel = ucfirst($data->spin_result_type);
                            @endphp
                            <span class="badge {{ $badgeClass }} fs-6">{{ $resultLabel }}</span>
                        </td>
                    </tr>
                    @if($data->spin_result_type === 'points')
                    <tr>
                        <th class="text-muted">Points Earned</th>
                        <td>
                            <span class="badge bg-success fs-6">{{ number_format($data->points_earned) }}</span>
                        </td>
                    </tr>
                    @endif
                    @if($data->spin_result_type === 'offer' && $data->offer)
                    <tr>
                        <th class="text-muted">Won Offer</th>
                        <td>
                            <div class="fw-semibold">{{ $data->offer->title }}</div>
                            @if($data->offer->points_required)
                                <small class="text-muted">Points Required: {{ number_format($data->offer->points_required) }}</small>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @if($data->spin_result_type === 'discount' && $data->reward_value)
                    <tr>
                        <th class="text-muted">Discount Value</th>
                        <td>
                            <span class="badge bg-warning fs-6">{{ number_format($data->reward_value, 2) }}%</span>
                        </td>
                    </tr>
                    @endif
                    @if($data->reward_value && $data->spin_result_type !== 'discount')
                    <tr>
                        <th class="text-muted">Reward Value</th>
                        <td>{{ number_format($data->reward_value, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th class="text-muted">Eligibility Status</th>
                        <td>
                            @if($data->is_eligible)
                                <span class="badge bg-success">Eligible</span>
                            @else
                                <span class="badge bg-danger">Not Eligible</span>
                            @endif
                        </td>
                    </tr>
                    @if($data->last_spin_date)
                    <tr>
                        <th class="text-muted">Last Spin Date</th>
                        <td>{{ \Carbon\Carbon::parse($data->last_spin_date)->format('d M Y') }}</td>
                    </tr>
                    @endif
                    @if($data->ip_address)
                    <tr>
                        <th class="text-muted">IP Address</th>
                        <td>{{ $data->ip_address }}</td>
                    </tr>
                    @endif
                    @if($data->device_info)
                    <tr>
                        <th class="text-muted">Device Info</th>
                        <td>
                            <pre class="mb-0">{{ json_encode($data->device_info, JSON_PRETTY_PRINT) }}</pre>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <th class="text-muted">Notes</th>
                        <td>{{ $data->notes ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-auth.card>
</x-layouts.auth>


