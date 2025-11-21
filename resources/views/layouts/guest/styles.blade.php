{{-- Frontend Styles --}}
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Brand Colors CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/css/brand-colors.css') }}">

<!-- Custom CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/css/custom.css') }}">

<!-- Mobile Navigation Styles -->
<style>
/* Mobile Navigation Styles - Only for mobile screens */
@media (max-width: 991.98px) {
    /* Completely disable desktop dropdown hover effects on mobile */
    .dropdown-hover .dropdown-menu {
        display: none !important;
    }
    
    .dropdown-hover:hover .dropdown-menu {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }
    
    /* Disable all hover effects on mobile */
    .navbar-uzimate .dropdown-hover:hover > .dropdown-menu,
    .navbar-uzimate .dropdown-hover .dropdown-menu:hover {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }
    
    /* Force mobile dropdowns to only work with Bootstrap's data-bs-toggle */
    .navbar-uzimate .dropdown-menu {
        display: none !important;
    }
    
    .navbar-uzimate .dropdown.show .dropdown-menu {
        display: block !important;
    }
    
    /* Mobile navbar styling - keep the purple background */
    .navbar-uzimate {
        background-color: var(--uzimate-purple) !important;
        padding: 0.75rem 1rem !important;
    }
    
    /* Mobile navbar collapse - style it like your screenshot */
    .navbar-uzimate .navbar-collapse {
        background-color: var(--uzimate-purple) !important;
        margin-top: 1rem;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
    /* Mobile navigation items */
    .navbar-uzimate .navbar-nav {
        width: 100%;
    }
    
    .navbar-uzimate .navbar-nav .nav-item {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .navbar-uzimate .navbar-nav .nav-link {
        color: white !important;
        padding: 0.75rem 1rem !important;
        border-radius: 6px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        font-weight: 500;
        font-size: 1rem;
    }
    
    .navbar-uzimate .navbar-nav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: var(--uzimate-yellow) !important;
    }
    
    .navbar-uzimate .navbar-nav .nav-link.active {
        background-color: var(--uzimate-yellow) !important;
        color: var(--uzimate-purple) !important;
    }
    
    /* Mobile dropdown styling */
    .navbar-uzimate .navbar-nav .dropdown {
        width: 100%;
    }
    
    .navbar-uzimate .navbar-nav .dropdown-toggle {
        width: 100%;
        text-align: left;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }
    
    /* Remove hover effects completely on mobile */
    .navbar-uzimate .navbar-nav .dropdown-toggle:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: var(--uzimate-yellow) !important;
    }
    
    /* Custom dropdown arrow for mobile */
    .navbar-uzimate .navbar-nav .dropdown-toggle::after {
        border: none;
        content: '\f107';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        margin-left: auto;
        transition: transform 0.3s ease;
        font-size: 0.8rem;
    }
    
    .navbar-uzimate .navbar-nav .dropdown.show .dropdown-toggle::after {
        transform: rotate(180deg);
    }
    
    /* Ensure dropdown only opens on click, not hover */
    .navbar-uzimate .navbar-nav .dropdown-hover {
        pointer-events: auto;
    }
    
    .navbar-uzimate .navbar-nav .dropdown-hover:hover .dropdown-menu {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }
    
    .navbar-uzimate .navbar-nav .dropdown-menu {
        background-color: rgba(0, 0, 0, 0.2) !important;
        border: none !important;
        border-radius: 6px !important;
        margin-top: 0.5rem !important;
        padding: 0.5rem 0 !important;
        box-shadow: none !important;
        width: 100% !important;
        position: static !important;
        transform: none !important;
    }
    
    .navbar-uzimate .navbar-nav .dropdown-item {
        color: rgba(255, 255, 255, 0.9) !important;
        padding: 0.5rem 1.5rem !important;
        font-size: 0.9rem !important;
        transition: all 0.3s ease;
        border-radius: 4px;
        margin: 0 0.5rem;
    }
    
    .navbar-uzimate .navbar-nav .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: var(--uzimate-yellow) !important;
    }
    
    /* Mobile header right side elements */
    .navbar-uzimate .d-flex.align-items-center {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        flex-direction: column;
        gap: 1rem !important;
        width: 100%;
    }
    
    .navbar-uzimate .d-flex.align-items-center .dropdown {
        width: 100%;
    }
    
    .navbar-uzimate .d-flex.align-items-center .dropdown .nav-link {
        justify-content: center;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 6px;
    }
    
    .navbar-uzimate .d-flex.align-items-center .nav-link {
        width: 100%;
        text-align: center;
        justify-content: center;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 6px;
    }
    
    .navbar-uzimate .btn-uzimate-yellow {
        width: 100% !important;
        padding: 0.75rem 1rem !important;
        font-size: 1rem !important;
        font-weight: 600 !important;
        border-radius: 6px !important;
    }
    
    /* Mobile navbar toggler styling */
    .navbar-uzimate .navbar-toggler {
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        padding: 0.5rem !important;
        border-radius: 4px;
    }
    
    .navbar-uzimate .navbar-toggler:focus {
        box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25) !important;
    }
    
    .navbar-uzimate .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        width: 1.5em !important;
        height: 1.5em !important;
    }
    
    /* Mobile brand styling */
    .navbar-uzimate .navbar-brand .logo-icon {
        width: 45px !important;
        height: 45px !important;
        font-size: 1.3rem !important;
    }
    
    .navbar-uzimate .navbar-brand .brand-name {
        font-size: 1.3rem !important;
    }
    
    .navbar-uzimate .navbar-brand .brand-tagline {
        font-size: 0.7rem !important;
    }
}

/* Ensure mobile menu works properly with Bootstrap collapse */
@media (max-width: 991.98px) {
    .navbar-uzimate .navbar-collapse.collapsing,
    .navbar-uzimate .navbar-collapse.show {
        display: block !important;
    }
    
    .navbar-uzimate .navbar-collapse.collapse:not(.show) {
        display: none !important;
    }
}
</style>

<!-- Mobile Navigation JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Only apply mobile dropdown behavior on mobile screens
    function isMobile() {
        return window.innerWidth <= 991.98;
    }
    
    // Disable hover effects on mobile
    function handleMobileDropdowns() {
        if (isMobile()) {
            // Remove hover class functionality on mobile
            const dropdownHovers = document.querySelectorAll('.navbar-uzimate .dropdown-hover');
            dropdownHovers.forEach(function(dropdown) {
                // Remove hover event listeners
                dropdown.style.pointerEvents = 'auto';
                
                // Ensure dropdown menu is hidden by default
                const dropdownMenu = dropdown.querySelector('.dropdown-menu');
                if (dropdownMenu) {
                    dropdownMenu.style.display = 'none';
                    dropdownMenu.style.opacity = '0';
                    dropdownMenu.style.visibility = 'hidden';
                }
                
                // Handle click events properly
                const dropdownToggle = dropdown.querySelector('.dropdown-toggle');
                if (dropdownToggle) {
                    dropdownToggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Close other dropdowns
                        dropdownHovers.forEach(function(otherDropdown) {
                            if (otherDropdown !== dropdown) {
                                otherDropdown.classList.remove('show');
                                const otherMenu = otherDropdown.querySelector('.dropdown-menu');
                                if (otherMenu) {
                                    otherMenu.style.display = 'none';
                                }
                            }
                        });
                        
                        // Toggle current dropdown
                        dropdown.classList.toggle('show');
                        if (dropdown.classList.contains('show')) {
                            dropdownMenu.style.display = 'block';
                        } else {
                            dropdownMenu.style.display = 'none';
                        }
                    });
                }
            });
        }
    }
    
    // Initialize mobile dropdown behavior
    handleMobileDropdowns();
    
    // Re-initialize on window resize
    window.addEventListener('resize', function() {
        handleMobileDropdowns();
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (isMobile()) {
            const navbarCollapse = document.querySelector('.navbar-collapse');
            if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                const dropdowns = document.querySelectorAll('.navbar-uzimate .dropdown-hover');
                dropdowns.forEach(function(dropdown) {
                    if (!dropdown.contains(e.target)) {
                        dropdown.classList.remove('show');
                        const dropdownMenu = dropdown.querySelector('.dropdown-menu');
                        if (dropdownMenu) {
                            dropdownMenu.style.display = 'none';
                        }
                    }
                });
            }
        }
    });
});
</script>

{{-- Dashboard Styles (for admin pages only) --}}
@if(request()->is('my-account*'))
<link href="{{ asset('dashboard/css/modern.css') }}" rel="stylesheet">
<link href="{{ asset('dashboard/css/custom.css') }}" rel="stylesheet">
@endif
