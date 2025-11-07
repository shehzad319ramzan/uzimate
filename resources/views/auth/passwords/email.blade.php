@php
    $pageTitle = 'Forgot Password - Uzimate Management Portal';
@endphp

@include('layouts.guest.links')

<div class="login-page">
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <!-- Forgot Password Form Section -->
            <div class="col-lg-6 login-form-section d-flex align-items-center">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-10">
                            <div class="mb-4 text-center">
                                <x-logo />
                            </div>

                            <h1 class="mb-2 fw-bold text-uzimate-dark">Forgot Your Password?</h1>
                            <p class="text-uzimate-light mb-4">No worries! Enter your email address and we'll send you a link to reset your password.</p>

                            @if (session('status'))
                            <div class="alert alert-success mb-4" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                            </div>
                            @endif

                            <x-auth.form form-action="{{ route('password.email') }}">
                                <x-auth.input-field type="email" name="email" id="email" required="true"
                                    place="Enter your email address" val="{{ old('email') }}" extraclasses="mb-3"
                                    label="Email address" />

                                <x-auth.input-button btn-class="btn btn-uzimate-purple w-100 btn-lg mb-3" btn-type="submit"
                                    btn-value="Send Password Reset Link" />

                                <div class="text-center">
                                    <p class="text-uzimate-light mb-0">
                                        Remember your password? <a href="{{ route('login') }}" class="text-uzimate-purple text-decoration-none fw-semibold">Login here</a>
                                    </p>
                                </div>
                            </x-auth.form>
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
                        <h1 class="mb-3">Reset Your Password</h1>
                        <p class="tagline mb-4">Secure and easy password recovery</p>
                        <p class="mb-4">
                            We'll send you a secure link to reset your password. Simply click the link in your email and follow the instructions to create a new password.
                        </p>
                        <div class="mt-4">
                            <h5 class="mb-3 text-uzimate-yellow">Security Tips:</h5>
                            <ul class="list-unstyled" style="color: rgba(255, 255, 255, 0.9);">
                                <li class="mb-2"><i class="fas fa-shield-alt text-uzimate-yellow me-2"></i> Use a strong, unique password</li>
                                <li class="mb-2"><i class="fas fa-shield-alt text-uzimate-yellow me-2"></i> Don't share your password</li>
                                <li class="mb-2"><i class="fas fa-shield-alt text-uzimate-yellow me-2"></i> Change password regularly</li>
                                <li class="mb-2"><i class="fas fa-shield-alt text-uzimate-yellow me-2"></i> Check your email spam folder</li>
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
