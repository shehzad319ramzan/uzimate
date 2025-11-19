<x-layouts.auth page-title="Edit user details">

    <x-auth.card card-header="My Profile" header-button="true">
        <x-auth.form form-action="{{ route('users.update', $data?->id) }}" enctype="true">
            @method('PUT')

            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            <x-auth.input-field type="text" name="username" id="username" required="true"
                                place="Enter username" val="{{ $data?->username }}"
                                extraclasses="mb-3" label="Username" />
                        </div>

                        <div class="col-md-6">
                            <x-auth.input-field type="text" name="f_name" id="f_name" required="true"
                                place="Enter first name" val="{{ $data?->first_name }}"
                                extraclasses="mb-3" label="First Name" />
                        </div>
                        <div class="col-md-6">
                            <x-auth.input-field type="text" name="l_name" id="l_name" required="true"
                                place="Enter last name" val="{{ $data?->last_name }}"
                                extraclasses="mb-3" label="Last Name" />
                        </div>

                        <div class="col-md-12">
                            <x-auth.input-field type="email" name="email" id="email" required="true"
                                place="Enter email address" val="{{ $data?->email }}"
                                extraclasses="mb-3 disabled" label="Email" />
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" name="role" id="role" required>
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ $currentRoleId == $role->id ? 'selected' : '' }}>
                                        {{ $role->title ?? ucwords(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-12">

                            <x-auth.text-area type="text" name="about" id="about" required="true"
                                place="Enter biography" val="{{ $data?->about }}"
                                extraclasses="mb-3" label="Biography" />

                            <x-auth.input-button btn-class="btn btn-primary" btn-type="submit"
                                btn-value="Update User" />
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <x-auth.upload-file image="{{ $data?->profile() }}" />
                </div>
            </div>
        </x-auth.form>
    </x-auth.card>
</x-layouts.auth>
