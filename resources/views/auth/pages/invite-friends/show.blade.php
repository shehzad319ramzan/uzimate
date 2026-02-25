<x-layouts.auth>
    <x-slot name="pageTitle">Referral Details</x-slot>

    <x-auth.card card-header="Referral Details" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('invitefriends.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>

        <div class="table-responsive mb-4">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th class="text-muted" style="width: 200px;">Referrer (who invited)</th>
                        <td>
                            <div class="fw-semibold">{{ $data->referrer->first_name ?? '' }}
                                {{ $data->referrer->last_name ?? '' }}</div>
                            <small class="text-muted">{{ $data->referrer->email ?? '-' }}</small>
                            @if($data->referrer->referral_code ?? null)
                                <br><span class="badge bg-primary">Code: {{ $data->referrer->referral_code }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Referred user (who joined)</th>
                        <td>
                            <div class="fw-semibold">{{ $data->referredUser->first_name ?? '' }}
                                {{ $data->referredUser->last_name ?? '' }}</div>
                            <small class="text-muted">{{ $data->referredUser->email ?? '-' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Points awarded</th>
                        <td><span class="badge bg-success">{{ number_format($data->points_awarded ?? 0) }} pts</span></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Joined at</th>
                        <td>{{ $data->created_at ? $data->created_at->format('d M Y, H:i:s') : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-auth.card>
</x-layouts.auth>
