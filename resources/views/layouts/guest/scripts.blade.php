{{-- Bootstrap 5 JS Bundle --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- Hover Dropdown Functionality --}}
<script>
    // Hover dropdown functionality - works on all pages
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.dropdown-hover').forEach(function(dropdown) {
            const dropdownMenu = dropdown.querySelector('.dropdown-menu');
            let hoverTimeout;
            
            function showDropdown() {
                clearTimeout(hoverTimeout);
                dropdownMenu.style.display = 'block';
                setTimeout(function() {
                    dropdownMenu.style.opacity = '1';
                    dropdownMenu.style.visibility = 'visible';
                }, 10);
            }
            
            function hideDropdown() {
                hoverTimeout = setTimeout(function() {
                    dropdownMenu.style.opacity = '0';
                    dropdownMenu.style.visibility = 'hidden';
                    setTimeout(function() {
                        dropdownMenu.style.display = 'none';
                    }, 200);
                }, 300); // Increased timeout to 300ms for better hover experience
            }
            
            dropdown.addEventListener('mouseenter', showDropdown);
            dropdownMenu.addEventListener('mouseenter', showDropdown);
            dropdown.addEventListener('mouseleave', hideDropdown);
            dropdownMenu.addEventListener('mouseleave', hideDropdown);
        });
    });
</script>

{{-- Dashboard Scripts (for admin pages only) --}}
@if(request()->is('my-account*'))
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<script src="{{ asset('dashboard/js/app.js') }}"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    function showToaster(type, message, title) {
        toastr.options.closeButton = true;
        toastr.options.showMethod = 'slideDown';
        toastr.options.closeEasing = 'swing';
        toastr.options.progressBar = true;
        toastr.options.timeOut = 2500;
        toastr.options.positionClass = "toast-bottom-right";

        toastr[type](message, title);
    }

    @if (session('success'))
        showToaster('success', '{{ session('success') }}', 'Success');
    @elseif (session('error'))
        showToaster('error', '{{ session('error') }}', 'Error');
    @elseif (session('info'))
        showToaster('info', '{{ session('info') }}', 'Info');
    @elseif (session('warning'))
        showToaster('warning', '{{ session('warning') }}', 'Warning');
    @endif
</script>
@endif
