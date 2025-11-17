<x-layouts.auth page-title="Dashboard">
    @if($userRole === 'super_admin')
        <x-auth.admin.dashboard :data="$data" />
    @elseif($userRole === 'merchant')
        <x-auth.merchant.dashboard :data="$data" />
    @elseif($userRole === 'admin')
        <x-auth.admin-staff.dashboard :data="$data" />
    @elseif($userRole === 'customer')
        <x-auth.customer.dashboard :data="$data" />
    @else
        <x-auth.admin.dashboard :data="$data" />
    @endif
</x-layouts.auth>
