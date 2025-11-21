{{-- @push('auth_scripts') --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const merchantSelect = document.getElementById('merchant_id');
        const siteSelect = document.getElementById('site_ids');

        if (!merchantSelect || !siteSelect || merchantSelect.tagName !== 'SELECT' || siteSelect.tagName !== 'SELECT') {
            return;
        }

        // Keep a snapshot of all sites before filtering (so Select2 doesn't strip options)
        const originalSites = Array.from(siteSelect.options)
            .filter(option => option.value)
            .map(option => ({
                value: option.value,
                label: option.text,
                merchant: (option.dataset.merchant || '').replace(/^"|"$/g, ''), // Remove surrounding quotes
            }));
        

        const $siteSelect = window.jQuery ? window.jQuery(siteSelect) : null;

        const renderSites = () => {
            const merchantId = merchantSelect.value;
            
            // Get current selected values properly
            let currentValues = [];
            if ($siteSelect && $siteSelect.data('select2')) {
                currentValues = $siteSelect.val() || [];
            } else {
                currentValues = Array.from(siteSelect.selectedOptions).map(option => option.value);
            }

            const filtered = originalSites.filter(site => !merchantId || site.merchant === merchantId);

            if ($siteSelect && $siteSelect.data('select2')) {
                // Clear and rebuild options for Select2
                $siteSelect.empty();
                
                // Add empty option for Select2 placeholder
                $siteSelect.append(new Option('', '', false, false));
                
                filtered.forEach(site => {
                    const option = new Option(site.label, site.value, false, currentValues.includes(site.value));
                    option.dataset.merchant = site.merchant;
                    $siteSelect.append(option);
                });

                // Set the selected values and trigger change
                const validValues = currentValues.filter(value => filtered.some(site => site.value === value));
                $siteSelect.val(validValues).trigger('change');
            } else {
                // Fallback for non-Select2
                siteSelect.innerHTML = '<option></option>';
                filtered.forEach(site => {
                    const option = new Option(site.label, site.value, false, currentValues.includes(site.value));
                    option.dataset.merchant = site.merchant;
                    siteSelect.appendChild(option);
                });
            }
        };

        renderSites();
        merchantSelect.addEventListener('change', renderSites);
    });
</script>
{{-- @endpush --}}

