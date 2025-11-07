@php
    $pageTitle = 'Login - Uzimate Management Portal';
@endphp

@include('layouts.guest.links')

<div class="login-page">
        <div class="container-fluid p-0">
            <div class="row g-0 min-vh-100">
                <!-- Login Form Section -->
                <div class="col-lg-6 login-form-section d-flex align-items-center">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-8 col-lg-10">
                                {{-- <div class="mb-4 text-center">
                                    <x-logo />
                                </div> --}}

                                <x-auth.form form-action="{{ route('login') }}">
                                    <x-auth.input-field type="email" name="email" id="email" required="true"
                                        place="Enter email" val="{{ old('email') }}" extraclasses="mb-3"
                                        label="Email" />

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <x-auth.input-field type="password" name="password" id="password" required="true"
                                                place="Enter password" val="" extraclasses="mb-0" />
                                            <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3" onclick="togglePassword()" style="text-decoration: none; color: var(--uzimate-text-light); border: none; background: none; z-index: 10;">
                                                <i class="fas fa-eye" id="toggleIcon"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input @error('terms_condition') is-invalid @enderror" type="checkbox" value="1"
                                                required name="terms_condition" id="terms_condition">
                                            <label class="form-check-label" for="terms_condition">
                                                By continuing, you agree to <a href="#" class="text-uzimate-purple text-decoration-none">terms of use</a> & <a href="#" class="text-uzimate-purple text-decoration-none">privacy policy</a>
                                            </label>
                                        </div>
                                        @error('terms_condition')
                                        <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <x-auth.input-button btn-class="btn btn-uzimate-purple w-100 btn-lg mb-3" btn-type="submit"
                                        btn-value="Login" />

                                    <div class="text-center mb-3">
                                        <p class="text-uzimate-light mb-0">
                                            Don't have an account? <a href="{{ route('register') }}" class="text-uzimate-purple text-decoration-none fw-semibold">Sign up</a>
                                        </p>
                                    </div>

                                    @if (Route::has('password.request'))
                                    <div class="text-center">
                                        <a href="{{ route('password.request') }}" class="text-uzimate-purple text-decoration-none">Forgot Your Password?</a>
                                    </div>
                                    @endif
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
                            <h1 class="mb-3">Welcome to Management Portal</h1>
                            <p class="tagline mb-4">An ultimate power on your side</p>
                            <p>
                                Uzimate provides an opportunity to reward your loyal customers and incentives the undecided ones. Track down other parts of your loyalty program. Create, change, amend... everything possible with a tap of finger.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@include('layouts.guest.scripts')

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

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
