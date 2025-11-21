@push('auth_styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@php
    $selectedValues = collect(
        is_array($existingId) || $existingId instanceof \Illuminate\Support\Collection
            ? $existingId
            : array_filter([$existingId])
    )
        ->flatten()
        ->filter(fn ($value) => $value !== null && $value !== '')
        ->map(fn ($value) => (string) $value)
        ->unique()
        ->values()
        ->all();

    $errorName = preg_replace('/\[\]$/', '', $name);
@endphp

<div class='required field'>
    @if ($label != null)
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endif

    <select class="{{$selectclass}} form-select @error($name)
    is-invalid
    @enderror @error($errorName)
    is-invalid
    @enderror" name="{{ $name }}" data-placeholder="{{ $placeholder }}" id="{{ $id }}" {{ $attributes }}>
        <option></option>

        @if ($loopData == null)
        @else
        @foreach ($loopData as $item)
        @php
            $value = (string) ($item->id ?? $item['id'] ?? '');
            $label = $item->name ?? $item['name'] ?? $value;
            $dataMerchant = $item->merchant_id ?? $item['merchant_id'] ?? '';
        @endphp
        <option value="{{ $value }}" {{ $dataMerchant !== '' ? 'data-merchant="'.$dataMerchant.'"' : '' }}
            {{ in_array($value, $selectedValues, true) ? 'selected' : '' }}>
            {{ $label }}
        </option>
        @endforeach
        @endif
    </select>

    @error($name)
    <span for="{{ $id }}" class="text-danger">{{ $message }}</span>
    @enderror
    @if ($errorName !== $name)
        @error($errorName)
        <span for="{{ $id }}" class="text-danger">{{ $message }}</span>
        @enderror
        @error($errorName . '.*')
        <span for="{{ $id }}" class="text-danger">{{ $message }}</span>
        @enderror
    @endif
</div>

@push('auth_scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
            const isAjax = @json($isAjax)

            var select2 = $('.{{$selectclass}}');

            if (select2.length) {
                select2.each(function() {
                    var $this = $(this);
                    var select2Options = {
                        dropdownParent: $this.parent(),
                        placeholder: $this.data('placeholder'),
                        allowClear: true,
                        theme: "classic",
                        delay: 250,
                        tags: '{{$tags}}',
                    };

                    if (isAjax) {
                        select2Options.ajax = {
                            url: "{{ $ajaxRoute }}",
                            dataType: 'json',
                            data: function(params) {
                                return {
                                    search: params.term,
                                    page: params.page || 1
                                };
                            },
                            processResults: function(data, params) {
                                params.page = params.page || 1;

                                return {
                                    results: data.results,
                                    pagination: {
                                        more: data.more
                                    }
                                };
                            }
                        };
                    }

                    $this.wrap('<div class="position-relative"></div>').select2(select2Options);
                });
            }
        });
</script>
@endpush
