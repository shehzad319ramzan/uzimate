<x-layouts.auth>
    <x-slot name="pageTitle">Create Permission</x-slot>
    <x-auth.card card-header="Create Permission" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('permissions.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>

        <x-auth.form form-action="{{ route('permissions.store') }}">
            <div class="row">
                <div class="col-md-4">
                    <x-auth.input-field type="text" name="name" id="name" required="true"
                        place="permission name" val="{{ old('name') }}" extraclasses="mb-3"
                        label="Name (unique identifier)" />
                </div>
                <div class="col-md-4">
                    <x-auth.input-field type="text" name="title" id="title" required="true"
                        place="Permission title" val="{{ old('title') }}" extraclasses="mb-3"
                        label="Title (display name)" />
                </div>
                <div class="col-md-4">
                    <x-auth.input-field type="text" name="category" id="category" required="true"
                        place="Category (e.g. Users)" val="{{ old('category') }}" extraclasses="mb-3"
                        label="Category" />
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

