{{-- @push('auth_scripts') --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const merchantSelect = document.getElementById('merchant_id');
        const siteSelect = document.getElementById('site_id');

        if (!merchantSelect || !siteSelect || merchantSelect.tagName !== 'SELECT' || siteSelect.tagName !== 'SELECT') {
            return;
        }

        const originalSites = Array.from(siteSelect.options)
            .filter(option => option.value)
            .map(option => ({
                value: option.value,
                label: option.text,
                merchant: option.dataset.merchant || '',
            }));

        const renderSites = () => {
            const merchantId = merchantSelect.value;
            const currentValue = siteSelect.value;

            siteSelect.innerHTML = '';
            siteSelect.appendChild(new Option('Select Site', ''));

            const filtered = originalSites.filter(site => !merchantId || site.merchant === merchantId);

            filtered.forEach(site => {
                const option = new Option(site.label, site.value);
                option.dataset.merchant = site.merchant;
                siteSelect.appendChild(option);
            });

            const stillValid = filtered.some(site => site.value === currentValue);
            if (stillValid) {
                siteSelect.value = currentValue;
            }
        };

        renderSites();
        merchantSelect.addEventListener('change', renderSites);
    });
</script>
{{-- @endpush --}}

