<span class="app-brand-logo demo">
    <a href="{{ route('welcome') }}">
        @php
            $logoUrl = asset('frontend/assets/logo.webp'); // Default logo
            if (isset($setting) && $setting) {
                $dbLogo = $setting->logo();
                if (!empty($dbLogo)) {
                    $logoUrl = $dbLogo;
                }
            }
        @endphp
        <img src="{{ $logoUrl }}"
            alt="{{ config('app.name') }} logo"
            class="logo"
            style="max-height: 70px; width: auto;">
    </a>
</span>
