<x-layouts.auth>
    <x-slot name="pageTitle">Create Site User</x-slot>
    <x-auth.card card-header="Create Site User" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('siteusers.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>
        <x-auth.form form-action="{{ route('siteusers.store') }}" enctype="true">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="merchant_id" class="form-label">Merchant <span class="text-danger">*</span></label>
                    <select class="form-select @error('merchant_id') is-invalid @enderror" name="merchant_id" id="merchant_id" required>
                        <option value="">Select Merchant</option>
                        @foreach ($merchants as $merchant)
                            <option value="{{ $merchant->id }}" {{ old('merchant_id') == $merchant->id ? 'selected' : '' }}>
                                {{ $merchant->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('merchant_id')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="site_id" class="form-label">Site <span class="text-danger">*</span></label>
                    <select class="form-select @error('site_id') is-invalid @enderror" name="site_id" id="site_id" required>
                        <option value="">Select Site</option>
                        @foreach ($sites as $site)
                            <option value="{{ $site->id }}" data-merchant="{{ $site->merchant_id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                {{ $site->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('site_id')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-12 mb-4">
                    <div class="d-flex justify-content-center">
                        <x-auth.upload-file image="" name="{{ old('first_name') }}" />
                    </div>
                </div>

                <div class="col-md-6">
                    <x-auth.input-field type="text" name="first_name" id="first_name" required="true"
                        place="Enter first name" val="{{ old('first_name') }}" extraclasses="mb-3"
                        label="First Name" />
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="text" name="last_name" id="last_name" required="true"
                        place="Enter surname" val="{{ old('last_name') }}" extraclasses="mb-3"
                        label="Surname" />
                </div>

                <div class="col-md-6">
                    <x-auth.input-field type="email" name="email" id="email" required="true"
                        place="Enter email address" val="{{ old('email') }}" extraclasses="mb-3"
                        label="Email" />
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="text" name="phone" id="phone"
                        place="Enter mobile number" val="{{ old('phone') }}" extraclasses="mb-3"
                        label="Mobile" />
                </div>

                <div class="col-md-6 mb-3">
                    <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select @error('role_id') is-invalid @enderror" name="role_id" id="role_id" required>
                        <option value="">Select Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->title ?? ucwords(str_replace('_', ' ', $role->name)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="password" name="password" id="password" required="true"
                        place="********" val="" extraclasses="mb-3"
                        label="Password" />
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="password" name="password_confirmation" id="password_confirmation" required="true"
                        place="********" val="" extraclasses="mb-3"
                        label="Confirm Password" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 d-flex justify-content-end">
                    <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Create" />
                </div>
            </div>
        </x-auth.form>
    </x-auth.card>
</x-layouts.auth>

@include('auth.pages.sites-user.partials.form-scripts')
