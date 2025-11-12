<script src="{{ asset('dashboard/js/app.js') }}"></script>

{{-- end --}}
<script>
    function showToaster(type, message, title) {

        toastr[type](message, title, {
            positionClass: 'toast-bottom-right',
            closeButton: true,
            progressBar: true,
            newestOnTop: true,
            timeOut: 5000
        });
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

    // Advanced Mode Toggle Functionality
    window.addEventListener('load', function() {
        setTimeout(function() {
            const advanceModeToggle = document.getElementById('advanceModeToggle');
            const advanceModeItems = document.querySelectorAll('.advance-mode-item');
            
            console.log('Advanced Mode Items Found:', advanceModeItems.length);
            console.log('Toggle Element:', advanceModeToggle);
            
            if (!advanceModeItems.length) {
                console.warn('No advanced mode items found');
                return;
            }
            
            // Function to toggle visibility
            function toggleAdvancedItems(show) {
                console.log('Toggling advanced items:', show, 'Items count:', advanceModeItems.length);
                advanceModeItems.forEach(function(item, index) {
                    console.log('Processing item', index, item);
                    if (show) {
                        item.classList.add('show');
                        item.style.cssText = 'display: list-item !important; visibility: visible !important; opacity: 1 !important; height: auto !important; overflow: visible !important;';
                        console.log('Item', index, 'shown. Display:', window.getComputedStyle(item).display);
                    } else {
                        item.classList.remove('show');
                        item.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; height: 0 !important; overflow: hidden !important;';
                        console.log('Item', index, 'hidden. Display:', window.getComputedStyle(item).display);
                    }
                });
            }
            
            // Load saved state from localStorage
            const savedState = localStorage.getItem('advanceMode');
            console.log('Saved state:', savedState);
            
            if (savedState === 'true') {
                if (advanceModeToggle) {
                    advanceModeToggle.checked = true;
                }
                toggleAdvancedItems(true);
            } else {
                // Ensure items are hidden by default
                toggleAdvancedItems(false);
                if (advanceModeToggle) {
                    advanceModeToggle.checked = false;
                }
            }
            
            // Handle toggle change
            if (advanceModeToggle) {
                advanceModeToggle.addEventListener('change', function() {
                    const isEnabled = this.checked;
                    console.log('Toggle changed:', isEnabled);
                    
                    // Save state to localStorage
                    localStorage.setItem('advanceMode', isEnabled);
                    
                    // Show/hide advanced mode items
                    toggleAdvancedItems(isEnabled);
                });
            } else {
                console.error('Advanced mode toggle element not found');
            }
        }, 100);
    });
</script>

@stack('auth_scripts')
@stack('scripts')
</body>

</html>
