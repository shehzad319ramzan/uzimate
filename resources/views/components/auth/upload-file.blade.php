@php
    $uniqueId = 'upload_' . uniqid();
    $hasImage = !empty($image);
    $initial = !empty($name) ? strtoupper(substr($name, 0, 1)) : 'M';
@endphp
<div class="text-center">
    @if($hasImage)
        <img id="logoImage_{{ $uniqueId }}" alt="Logo" src="{{ $image }}"
            class="rounded-circle img-responsive mt-2" width="128" height="128" style="object-fit: cover; display: block;" />
        <div id="initialDiv_{{ $uniqueId }}" class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold mt-2" 
             style="width: 128px; height: 128px; background-color: #4A148D; font-size: 48px; display: none;">
            {{ $initial }}
        </div>
    @else
        <img id="logoImage_{{ $uniqueId }}" alt="Logo" src="{{ fileUrl() }}"
            class="rounded-circle img-responsive mt-2" width="128" height="128" style="object-fit: cover; display: none;" />
        <div id="initialDiv_{{ $uniqueId }}" class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold mt-2 mx-auto" 
             style="width: 128px; height: 128px; background-color: #4A148D; font-size: 48px; display: block;">
            {{ $initial }}
        </div>
    @endif
    <div class="mt-2">
        <span class="btn btn-outline-primary" id="uploadButton_{{ $uniqueId }}"><i class="fas fa-upload"></i> Upload</span>
    </div>
    <small>For best results, use an image at least 128px by 128px in .jpg format</small>
    <!-- Hidden file input -->
    <input type="file" name="file" id="fileInput_{{ $uniqueId }}" accept="image/*" style="display: none;" />
</div>

<script>
    (function() {
        const uniqueId = '{{ $uniqueId }}';
        const uploadButton = document.getElementById('uploadButton_' + uniqueId);
        const fileInput = document.getElementById('fileInput_' + uniqueId);
        const logoImage = document.getElementById('logoImage_' + uniqueId);
        const initialDiv = document.getElementById('initialDiv_' + uniqueId);
        
        if (uploadButton && fileInput) {
            uploadButton.addEventListener('click', function() {
                fileInput.click();
            });

            fileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        logoImage.src = e.target.result;
                        logoImage.style.display = 'block';
                        if (initialDiv) {
                            initialDiv.style.display = 'none';
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    })();
</script>
