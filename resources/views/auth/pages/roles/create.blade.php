<x-layouts.auth>
    <x-slot name="pageTitle">Create New Role</x-slot>
    <div class="row mt-3">
        <div class="col-md-12">
            <x-auth.card card-header="Create New Role">
                <x-auth.form form-action="{{ route('roles.store') }}">
                    <div class="row">
                        <div class="col-md-6">
                            <x-auth.input-field type="text" name="title" id="title" place="Enter role title"
                                val="{{ old('title') }}" required="true" label="Role Title" />
                        </div>

                        <div class="col-md-6">
                            <x-auth.input-field type="color" name="color" id="color" val="{{ old('color', '#4A148D') }}"
                                required="true" label="Role Color" place="Select role color"
                                extraclasses="rounded-full w-12 h-12 p-0 border-none shadow cursor-pointer" />
                        </div>

                        <div class="col-md-12 mt-3">
                            <div class="alert alert-info px-2">
                                Permissions can be assigned after the role is created from the role details page.
                            </div>
                        </div>

                        <div class="col-md-12">
                            <x-auth.input-button btn-class="mt-3 btn-primary" btn-value="Create" btn-type="submit" />
                        </div>
                    </div>
                </x-auth.form>
            </x-auth.card>
        </div>
    </div>
</x-layouts.auth>
