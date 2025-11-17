<x-layouts.auth>
    <x-slot name="pageTitle">Edit Permission</x-slot>
    <x-auth.card card-header="Edit Permission" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('permissions.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>

        <x-auth.form form-action="{{ route('permissions.update', $data->id) }}">
            @method('PUT')
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label mb-1">Name (unique identifier)</label>
                    <div class="form-control-plaintext border rounded px-3 py-2 bg-light">
                        {{ $data->name }}
                    </div>
                    <input type="hidden" name="name" value="{{ $data->name }}">
                </div>
                <div class="col-md-4">
                    <x-auth.input-field type="text" name="title" id="title" required="true"
                        place="Permission title" val="{{ old('title', $data->title) }}" extraclasses="mb-3"
                        label="Title (display name)" />
                </div>
                <div class="col-md-4">
                    <x-auth.input-field type="text" name="category" id="category" required="true"
                        place="Category (e.g. Users)" val="{{ old('category', $data->category) }}" extraclasses="mb-3"
                        label="Category" />
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

