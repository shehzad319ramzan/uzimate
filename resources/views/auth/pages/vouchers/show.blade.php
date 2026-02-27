<x-layouts.auth>
    <x-slot name="pageTitle">Voucher Details</x-slot>
    <x-auth.card card-header="Voucher Details" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('vouchers.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
            @can('edit_voucher')
                <a href="{{ route('vouchers.edit', $data->id) }}" class="btn btn-outline-secondary me-2">Edit</a>
            @endcan
        </x-slot>

        <div class="row mb-4">
            <div class="col-md-2">
                @php $img = $data->image(); @endphp
                @if (!empty($img))
                    <img src="{{ $img }}" alt="{{ $data->title }}" class="rounded img-fluid" style="max-height: 120px; object-fit: cover;" />
                @else
                    <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted" style="height: 120px;">
                        <i class="fas fa-ticket-alt fa-3x"></i>
                    </div>
                @endif
            </div>
            <div class="col-md-10">
                <h5 class="fw-bold">{{ $data->title }}</h5>
                <p class="text-muted mb-1">Merchant: {{ $data->merchant->name ?? '-' }}</p>
                @if ($data->offers->isNotEmpty())
                    <p class="text-muted mb-1 small">Linked offers: @foreach ($data->offers as $off) <span class="badge bg-secondary me-1">{{ $off->title }}</span> @endforeach</p>
                @endif
                <p class="mb-1"><span class="badge bg-info">{{ $data->points_required ?? 0 }} points</span>
                    @if ($data->valid_until)
                        · Valid until: {{ $data->valid_until->format('d M Y') }}
                    @endif
                </p>
                @if ($data->status == '1')
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-danger">Inactive</span>
                @endif
            </div>
        </div>

        @if ($data->description)
            <h6 class="fw-bold mb-2">Description</h6>
            <div class="border rounded p-3 bg-light mb-4 voucher-description">
                {!! $data->description !!}
            </div>
        @endif

        @if ($data->terms_and_conditions)
            <h6 class="fw-bold mb-2">Terms and conditions</h6>
            <div class="border rounded p-3 bg-light voucher-terms">
                {!! $data->terms_and_conditions !!}
            </div>
        @endif
    </x-auth.card>
</x-layouts.auth>
