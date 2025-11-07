@php
    $pageTitle = 'Reset Password - Uzimate Management Portal';
@endphp

@include('layouts.guest.links')

<div class="login-page">
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <!-- Reset Password Form Section -->
            <div class="col-lg-6 login-form-section d-flex align-items-center">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-10">
                            {{-- <div class="mb-4 text-center">
                                <x-logo />
                            </div> --}}

                            <h1 class="mb-2 fw-bold text-uzimate-dark">Reset Your Password</h1>
                            <p class="text-uzimate-light mb-4">Enter your new password below to complete the reset process.</p>

                            <x-auth.form form-action="{{ route('password.update') }}">
                                <input type="hidden" name="token" value="{{ $token }}">
                                <input type="hidden" name="email" value="{{ $email }}">

                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control mb-0 @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="Enter new password" required
                                            value="{{ old('password') }}" autocomplete="off" style="padding-right: 45px;">
                                        <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3"
                                            onclick="togglePassword('password', 'toggleIcon1')"
                                            style="text-decoration: none; color: var(--uzimate-text-light); border: none; background: none; z-index: 10; height: 100%; display: flex; align-items: center; padding: 0;">
                                            <i class="fas fa-eye" id="toggleIcon1"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                    <span for="password" class="text-danger">{{ $errors->first('password') }}</span>
                                    @enderror
                                    <small class="text-muted">Password must be at least 8 characters</small>
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control mb-0 @error('password_confirmation') is-invalid @enderror"
                                            id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" required
                                            value="{{ old('password_confirmation') }}" autocomplete="off" style="padding-right: 45px;">
                                        <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3"
                                            onclick="togglePassword('password_confirmation', 'toggleIcon2')"
                                            style="text-decoration: none; color: var(--uzimate-text-light); border: none; background: none; z-index: 10; height: 100%; display: flex; align-items: center; padding: 0;">
                                            <i class="fas fa-eye" id="toggleIcon2"></i>
                                        </button>
                                    </div>
                                    @error('password_confirmation')
                                    <span for="password_confirmation" class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                                    @enderror
                                </div>

                                <x-auth.input-button btn-class="btn btn-uzimate-purple w-100 btn-lg mb-3" btn-type="submit"
                                    btn-value="Reset Password" />

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
                        <h1 class="mb-3">Create New Password</h1>
                        <p class="tagline mb-4">Secure your account with a strong password</p>
                        <p class="mb-4">
                            Choose a strong password that you haven't used before. Make sure it's at least 8 characters long and includes a mix of letters, numbers, and special characters.
                        </p>
                        <div class="mt-4">
                            <h5 class="mb-3 text-uzimate-yellow">Password Requirements:</h5>
                            <ul class="list-unstyled" style="color: rgba(255, 255, 255, 0.9);">
                                <li class="mb-2"><i class="fas fa-check-circle text-uzimate-yellow me-2"></i> At least 8 characters long</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-uzimate-yellow me-2"></i> Mix of letters and numbers</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-uzimate-yellow me-2"></i> Include special characters</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-uzimate-yellow me-2"></i> Don't use common words</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.guest.scripts')

<script>
    // Toggle password visibility
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>

</body>
</html>
