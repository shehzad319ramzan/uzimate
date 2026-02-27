{{-- Styles in public/dashboard/css/voucher-preview.css --}}
<div class="voucher-app-preview bg-light rounded-3 p-3">
    <div class="small text-uppercase text-muted mb-2 fw-semibold" style="letter-spacing: 0.05em;">App preview</div>

    <div class="voucher-gift-card">
        {{-- Left: Branding + validity --}}
        <div class="voucher-gift-panel d-flex flex-column">
            <div class="voucher-gift-logo-box">
                {{ $data->merchant && $data->merchant->name ? strtoupper(substr($data->merchant->name, 0, 1)) : 'LOGO' }}
            </div>
            <div class="voucher-gift-title-cursive">Gift</div>
            <div class="voucher-gift-title-upper">VOUCHER</div>
            <div class="voucher-gift-valid">
                VALID: @if ($data->valid_until)
                    {{ $data->valid_until->format('j M') }} – {{ $data->valid_until->format('j M Y') }}
                @else
                    No expiry
                @endif
            </div>
        </div>

        {{-- Center: Image --}}
        <div class="voucher-gift-panel voucher-gift-panel-center">
            @php $img = $data->image(); @endphp
            <div class="voucher-gift-img-wrap flex-grow-1">
                @if (!empty($img))
                    <img src="{{ $img }}" alt="{{ $data->title }}" />
                @endif
            </div>
        </div>

        {{-- Right: Value + merchant --}}
        <div class="voucher-gift-panel d-flex flex-column">
            <div class="voucher-gift-value-box">
                {{ $data->points_required ?? 0 }} <span>PTS</span>
            </div>
            <div class="voucher-gift-details">
                <strong>{{ $data->title }}</strong><br>
                {{ $data->merchant->name ?? 'Merchant' }}
                @if ($data->offers->isNotEmpty())
                    <br><span class="opacity-75">{{ $data->offers->pluck('title')->take(2)->implode(' · ') }}</span>
                @endif
            </div>
        </div>
    </div>

    @if ($data->offers->isNotEmpty())
        <div class="mt-2 small text-muted">Linked offers: @foreach ($data->offers as $off) <span class="badge bg-secondary me-1">{{ $off->title }}</span> @endforeach</div>
    @endif
</div>
