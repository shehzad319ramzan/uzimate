<style>
.ck-editor__editable {
    min-height: 250px;
}
</style>
@props(['value' => null, 'name' => 'article', 'label' => 'Description', 'id' => null])
@php $fieldId = $id ?? $name; @endphp

<div class="mt-3">
    <label for="{{ $fieldId }}" class="form-label">{{ $label }}</label>
    <textarea class="form-control" id="{{ $fieldId }}" name="{{ $name }}" rows="50" placeholder="Your post description">{{ $value ?? '' }}</textarea>

    @if ($errors->has($name))
        <span for="{{ $fieldId }}" class="text-danger">{{ $errors->first($name) }}</span>
    @endif

</div>

<script src="https://cdn.ckeditor.com/ckeditor5/36.0.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#{{ $fieldId }}'), {
            ckfinder: {
                uploadUrl: "{{ Route::has('uploadPostImage') ? route('uploadPostImage') . '?_token=' . csrf_token() : '' }}"
            }
        })
        .catch(error => {
            console.error(error);
        });
</script>
