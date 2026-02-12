<x-layouts.auth>
    <x-slot name="pageTitle">Edit Merchant Category</x-slot>

    <x-auth.card card-header="Edit Merchant Category" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('merchantcategories.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>

        <x-auth.form form-action="{{ route('merchantcategories.update', $data->id) }}">
            @method('PUT')
            <div class="row">
                <div class="col-md-12 mb-3">
                    <x-auth.input-field type="text" name="name" id="name"
                        place="Enter category name" val="{{ old('name', $data->name) }}" label="Name *" required />
                    @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                        <option value="1" {{ (string)old('status', $data->status) === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (string)old('status', $data->status) === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Update Category" />
            </div>
        </x-auth.form>
    </x-auth.card>
</x-layouts.auth>
