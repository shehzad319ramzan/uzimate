{{-- Frontend Styles --}}
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Brand Colors CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/css/brand-colors.css') }}">

<!-- Custom CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/css/custom.css') }}">

{{-- Dashboard Styles (for admin pages only) --}}
@if(request()->is('my-account*'))
<link href="{{ asset('dashboard/css/modern.css') }}" rel="stylesheet">
<link href="{{ asset('dashboard/css/custom.css') }}" rel="stylesheet">
@endif
