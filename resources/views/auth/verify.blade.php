@php
    $pageTitle = 'Verify Your Email - Uzimate Management Portal';
@endphp

@include('layouts.guest.links')

<div class="login-page">
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <!-- Email Verification Form Section -->
            <div class="col-lg-6 login-form-section d-flex align-items-center">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-10">
                            {{-- <div class="mb-4 text-center">
                                <x-logo />
                            </div> --}}

                            <h1 class="mb-2 fw-bold text-uzimate-dark">Verify Your Email</h1>
                            <p class="text-uzimate-light mb-4">We've sent a verification link to your email address. Please check your inbox and click the link to verify your account.</p>

                            @if (session('resent'))
                            <div class="alert alert-success mb-4" role="alert">
                                <i class="fas fa-check-circle me-2"></i>A fresh verification link has been sent to your email address.
                            </div>
                            @endif

                            <div class="mb-4">
                                <div class="alert alert-info" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Didn't receive the email?</strong>
                                    <p class="mb-0 mt-2">Check your spam folder or click the button below to resend the verification link.</p>
                                </div>
                            </div>

                            <x-auth.form form-action="{{ route('verification.resend') }}">
                                <x-auth.input-button btn-class="btn btn-uzimate-purple w-100 btn-lg mb-3" btn-type="submit"
                                    btn-value="Resend Verification Email" />
                            </x-auth.form>

                            <div class="text-center mt-4">
                                <p class="text-uzimate-light mb-0">
                                    Already verified? <a href="{{ route('login') }}" class="text-uzimate-purple text-decoration-none fw-semibold">Login here</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Brand Section -->
            <div class="col-lg-6 login-brand-section d-none d-lg-flex">
                <div class="container">
                    <div class="login-brand-content">
                        <div class="mb-4 text-center">
                            <x-logo />
                        </div>
                        <h1 class="mb-3">Verify Your Account</h1>
                        <p class="tagline mb-4">Secure your account with email verification</p>
                        <p class="mb-4">
                            Email verification helps us ensure that your account is secure and that you have access to the email address you provided. This is an important step to protect your account.
                        </p>
                        <div class="mt-4">
                            <h5 class="mb-3 text-uzimate-yellow">Why verify your email?</h5>
                            <ul class="list-unstyled" style="color: rgba(255, 255, 255, 0.9);">
                                <li class="mb-2"><i class="fas fa-shield-alt text-uzimate-yellow me-2"></i> Enhanced account security</li>
                                <li class="mb-2"><i class="fas fa-shield-alt text-uzimate-yellow me-2"></i> Password recovery access</li>
                                <li class="mb-2"><i class="fas fa-shield-alt text-uzimate-yellow me-2"></i> Important notifications</li>
                                <li class="mb-2"><i class="fas fa-shield-alt text-uzimate-yellow me-2"></i> Account protection</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.guest.scripts')

</body>
</html>
