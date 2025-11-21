@php
    $isSuperMode = $isSuperMode ?? false;
    $superRoleId = $superRoleId ?? null;
@endphp
<x-layouts.auth>
    <x-slot name="pageTitle">{{ $isSuperMode ? 'Edit Super Admin' : 'Edit Site User' }}</x-slot>
    <x-auth.card card-header="{{ $isSuperMode ? 'Edit Super Admin' : 'Edit Site User' }}" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('siteusers.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>
        <x-auth.form form-action="{{ route('siteusers.update', $data->id) }}" enctype="true">
            @method('PUT')
            <div class="row">
                @php
                    $selectedSiteIds = $assignedSiteIds ?? [];
                    $selectedSiteIds = is_array($selectedSiteIds) ? $selectedSiteIds : [];
                @endphp
                @if (!$isSuperMode)
                    <div class="col-md-6 mb-3">
                        <label for="merchant_id" class="form-label">Merchant <span class="text-danger">*</span></label>
                        @if(!$isSuperAdmin && $selectedMerchantId)
                            <input type="hidden" name="merchant_id" value="{{ $selectedMerchantId }}">
                            <select class="form-select @error('merchant_id') is-invalid @enderror" id="merchant_id" disabled>
                                <option value="{{ $selectedMerchantId }}" selected>
                                    {{ $merchants->first()->name ?? 'N/A' }}
                                </option>
                            </select>
                        @else
                            <select class="form-select @error('merchant_id') is-invalid @enderror" name="merchant_id" id="merchant_id" required>
                                <option value="">Select Merchant</option>
                                @foreach ($merchants as $merchant)
                                    <option value="{{ $merchant->id }}" {{ old('merchant_id', $data->merchant_id) == $merchant->id ? 'selected' : '' }}>
                                        {{ $merchant->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('merchant_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-auth.select2
                            label="Sites"
                            name="site_ids[]"
                            id="site_ids"
                            :data="$sites"
                            :existing-id="old('site_ids', $selectedSiteIds)"
                            placeholder="Select sites"
                            selectclass="select2-sites"
                            multiple
                            required
                        />
                        <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple sites.</small>
                    </div>
                @else
                    <input type="hidden" name="merchant_id" value="{{ $data->merchant_id }}">
                    <input type="hidden" name="site_ids[]" value="{{ $data->site_id }}">
                    <div class="col-md-12 mb-3">
                        <div class="alert alert-info">
                            Super Admin accounts are not attached to a merchant or site.
                        </div>
                    </div>
                @endif

                <div class="col-md-12 mb-4">
                    <div class="d-flex justify-content-center">
                        <x-auth.upload-file image="{{ $data->user?->profile() }}" name="{{ $data->user?->first_name }}" />
                    </div>
                </div>

                <div class="col-md-6">
                    <x-auth.input-field type="text" name="first_name" id="first_name" required="true"
                        place="Enter first name" val="{{ old('first_name', $data->user?->first_name) }}" extraclasses="mb-3"
                        label="First Name" />
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="text" name="last_name" id="last_name" required="true"
                        place="Enter surname" val="{{ old('last_name', $data->user?->last_name) }}" extraclasses="mb-3"
                        label="Surname" />
                </div>

                <div class="col-md-6">
                    <x-auth.input-field type="email" name="email" id="email" required="true"
                        place="Enter email address" val="{{ old('email', $data->user?->email) }}" extraclasses="mb-3"
                        label="Email" />
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="text" name="phone" id="phone"
                        place="Enter mobile number" val="{{ old('phone', $data->user?->phone) }}" extraclasses="mb-3"
                        label="Mobile" />
                </div>

                @php
                    $assignedRole = $data->user?->roles->first();
                @endphp
                @if (!$isSuperMode)
                    <div class="col-md-6 mb-3">
                        <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select @error('role_id') is-invalid @enderror" name="role_id" id="role_id" required>
                            <option value="">Select Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $assignedRole?->id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->title ?? ucwords(str_replace('_', ' ', $role->name)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                @else
                    <input type="hidden" name="role_id" value="{{ $superRoleId }}">
                    @if (!$superRoleId)
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                Super Admin role is missing. Please create it before updating this user.
                            </div>
                        </div>
                    @endif
                @endif
                <div class="col-md-6">
                    <x-auth.input-field type="password" name="password" id="password"
                        place="Leave blank to keep current password" val="" extraclasses="mb-3"
                        label="Password" />
                    <small class="text-muted">Leave blank to keep current password.</small>
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="password" name="password_confirmation" id="password_confirmation"
                        place="Confirm password" val="" extraclasses="mb-3"
                        label="Confirm Password" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 d-flex justify-content-end">
                    <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Update" />
                </div>
            </div>
        </x-auth.form>
    </x-auth.card>
</x-layouts.auth>

@include('auth.pages.sites-user.partials.form-scripts')
