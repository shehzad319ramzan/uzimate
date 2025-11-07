@include('layouts.guest.links')

<body>
    @if(!request()->is('account/signin') && !request()->is('account/signup'))
        @include('layouts.guest.navigation')
    @endif

    <main>
        {{ $slot }}
    </main>

    @if(!request()->is('account/signin') && !request()->is('account/signup'))
        @include('layouts.guest.footer')
    @endif
</body>

@include('layouts.guest.scripts')

@stack('scripts')

</html>
