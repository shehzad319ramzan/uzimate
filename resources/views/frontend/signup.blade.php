@php
    $pageTitle = 'Sign Up - Uzimate Management Portal';
@endphp

@include('layouts.guest.links')

<div class="login-page">
        <div class="container-fluid p-0">
            <div class="row g-0 min-vh-100">
                <!-- Signup Form Section -->
                <div class="col-lg-6 login-form-section d-flex align-items-center">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-8 col-lg-10">
                                {{-- <div class="mb-4 text-center">
                                    <x-logo />
                                </div> --}}

                                <h1 class="mb-2 fw-bold text-uzimate-dark">Create your account</h1>
                                <p class="text-uzimate-light mb-4">Start your journey to better customer loyalty today</p>

                                <x-auth.form form-action="{{ route('register') }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <x-auth.input-field type="text" name="first_name" id="first_name" required="true"
                                                place="Enter your first name" val="{{ old('first_name') }}" extraclasses="mb-0"
                                                label="First Name" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <x-auth.input-field type="text" name="last_name" id="last_name" required="true"
                                                place="Enter your last name" val="{{ old('last_name') }}" extraclasses="mb-0"
                                                label="Last Name" />
                                        </div>
                                    </div>

                                    <x-auth.input-field type="email" name="email" id="email" required="true"
                                        place="Enter your email" val="{{ old('email') }}" extraclasses="mb-3"
                                        label="Email address" />

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control mb-0 @error('password') is-invalid @enderror" 
                                                id="password" name="password" placeholder="Create a password" required 
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
                                                id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required 
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

                                    <div class="mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input @error('terms_condition') is-invalid @enderror" type="checkbox" value="1"
                                                required name="terms_condition" id="terms">
                                            <label class="form-check-label" for="terms">
                                                By continuing, you agree to <a href="#" class="text-uzimate-purple text-decoration-none">terms of use</a> & <a href="#" class="text-uzimate-purple text-decoration-none">privacy policy</a>
                                            </label>
                                        </div>
                                        @error('terms_condition')
                                        <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <x-auth.input-button btn-class="btn btn-uzimate-purple w-100 btn-lg mb-3" btn-type="submit"
                                        btn-value="Create Account" />

                                    <div class="text-center">
                                        <p class="text-uzimate-light mb-0">
                                            Already have an account? <a href="{{ route('login') }}" class="text-uzimate-purple text-decoration-none fw-semibold">Login here</a>
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
                            <h1 class="mb-3">Join Uzimate Today</h1>
                            <p class="tagline mb-4">Start rewarding loyalty, amplify growth</p>
                            <p class="mb-4">
                                Create your free account and start building lasting customer relationships. Uzimate makes it easy to reward your loyal customers and incentivize new ones with our powerful loyalty platform.
                            </p>
                            <div class="mt-4">
                                <h5 class="mb-3 text-uzimate-yellow">What you'll get:</h5>
                                <ul class="list-unstyled" style="color: rgba(255, 255, 255, 0.9);">
                                    <li class="mb-2"><i class="fas fa-check-circle text-uzimate-yellow me-2"></i> Free 7-day trial</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-uzimate-yellow me-2"></i> No credit card required</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-uzimate-yellow me-2"></i> Easy setup in minutes</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-uzimate-yellow me-2"></i> Full customer support</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-uzimate-yellow me-2"></i> Access to all features</li>
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

        // Password validation
        function validatePassword() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const confirmPasswordField = document.getElementById('password_confirmation');

            if (password !== confirmPassword) {
                confirmPasswordField.setCustomValidity('Passwords do not match');
                confirmPasswordField.classList.add('is-invalid');
                return false;
            } else {
                confirmPasswordField.setCustomValidity('');
                confirmPasswordField.classList.remove('is-invalid');
                return true;
            }
        }

        // Real-time password matching validation
        document.getElementById('password_confirmation').addEventListener('input', function() {
            validatePassword();
        });

        document.getElementById('password').addEventListener('input', function() {
            const confirmPassword = document.getElementById('password_confirmation').value;
            if (confirmPassword) {
                validatePassword();
            }
        });
    </script>
</body>
</html>
