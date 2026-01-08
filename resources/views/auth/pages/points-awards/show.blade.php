<x-layouts.auth>
    <x-slot name="pageTitle">Point Award Details</x-slot>

    <x-auth.card card-header="Point Award Details" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('pointawards.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>

        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th class="text-muted" style="width: 220px;">Points Earned</th>
                        <td>
                            <span class="badge bg-success fs-6">{{ number_format($data->points_earned) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">User</th>
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
                        <th class="text-muted">Awarded By</th>
                        <td>
                            @if($data->awardedBy)
                                <div class="fw-semibold">{{ $data->awardedBy->full_name }}</div>
                                <small class="text-muted">{{ $data->awardedBy->email }}</small>
                            @else
                                <span class="text-muted">System</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Awarded At</th>
                        <td>{{ optional($data->created_at)->format('d M Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Notes</th>
                        <td>{{ $data->notes ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-auth.card>
</x-layouts.auth>

