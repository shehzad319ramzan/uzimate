<x-layouts.auth page-title="Dashboard">
    @if($userRole === 'super_admin')
        <x-auth.admin.dashboard :stats="$data" :filters="$filters" :filter-options="$filterOptions" />
    @elseif($userRole === 'merchant')
        <x-auth.merchant.dashboard :stats="$data" :filters="$filters" :filter-options="$filterOptions" />
    @elseif($userRole === 'admin')
        <x-auth.admin-staff.dashboard :stats="$data" :filters="$filters" :filter-options="$filterOptions" />
    @elseif($userRole === 'customer')
        <x-auth.customer.dashboard :stats="$data" :filters="$filters" :filter-options="$filterOptions" />
    @else
        <x-auth.admin.dashboard :stats="$data" :filters="$filters" :filter-options="$filterOptions" />
    @endif
</x-layouts.auth>
