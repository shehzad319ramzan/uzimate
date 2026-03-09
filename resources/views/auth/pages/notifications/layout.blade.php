<x-layouts.auth page-title="{{ $title ?? 'Notifications' }}" sub-title="{{ $subTitle ?? 'Manage notification templates and send notifications' }}">
    <div class="row">
        <div class="col-md-4 col-xl-3">
            <x-auth.card card-body="p-0">
                <x-slot:card-header>
                    <i class="align-middle me-1 fas fa-fw fa-bell"></i> Notification Management
                </x-slot>

                <div class="list-group list-group-flush" role="tablist">
                    <a class="list-group-item list-group-item-action {{ (request()->route('blade') ?? $blade ?? '') == 'miss-you' ? 'active' : '' }}"
                        href="{{ route('notifications.index', 'miss-you') }}">
                        <i class="align-middle me-1 fas fa-fw fa-heart"></i> Miss You Notification
                    </a>
                    <a class="list-group-item list-group-item-action {{ (request()->route('blade') ?? $blade ?? '') == 'special-offer' ? 'active' : '' }}"
                        href="{{ route('notifications.index', 'special-offer') }}">
                        <i class="align-middle me-1 fas fa-fw fa-tag"></i> Special Offer Notification
                    </a>
                    <a class="list-group-item list-group-item-action {{ (request()->route('blade') ?? $blade ?? '') == 'birthday' ? 'active' : '' }}"
                        href="{{ route('notifications.index', 'birthday') }}">
                        <i class="align-middle me-1 fas fa-fw fa-birthday-cake"></i> Birthday Notification
                    </a>
                </div>
            </x-auth.card>
        </div>

        <div class="col-md-8 col-xl-9">
            {{ $slot }}
        </div>
    </div>
</x-layouts.auth>
