<nav id="sidebar" class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-user">
            <div class="sidebar-user-info">
                @php
                    $user = auth()->user();
                    $profileImage = $user->profile();
                    $hasProfileImage = !empty($profileImage) && trim($profileImage) !== '';
                @endphp
                @if($hasProfileImage)
                    <div class="sidebar-logo-container">
                        <img src="{{ $profileImage }}" alt="{{ $user->first_name ?? 'User' }}" 
                             class="sidebar-profile-logo" />
                    </div>
                @endif
                <div class="user-details">
                    <div class="user-role">{{ $user->roles->first()->title ?? 'Super Admin' }}</div>
                    <div class="user-email">{{ $user->email }}</div>
                </div>
            </div>
        </div>

        <ul class="sidebar-nav mt-3">

            {{-- SUPER ADMIN DASHBOARD --}}
            <li class="sidebar-header">Main</li>
            <li class="sidebar-item {{ Str::startsWith(request()->route()->getName(), 'auth') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('auth') }}">
                    <i class="align-middle me-2 fas fa-fw fa-home"></i>
                    <span class="align-middle">{{ __('language.dashboard') }}</span>
                </a>
            </li>

            {{-- CORE MANAGEMENT --}}
            <li class="sidebar-header">Core Management</li>

            {{-- STAFF MANAGEMENT --}}
            @can('all_user')
            <li class="sidebar-item">
                <a data-bs-target="#usersDropdown" data-bs-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle me-2 fas fa-file-invoice-dollar"></i>
                    <span class="align-middle">{{ __('language.staff_management') }}</span>
                </a>
                <ul id="usersDropdown"
                    class="sidebar-dropdown list-unstyled collapse {{ Str::startsWith(request()->route()->getName(), 'users.') ? 'show' : '' }}"
                    data-bs-parent="#sidebar">

                    @can('all_user')
                    @foreach ($roles as $roleKey => $role)
                    <li class="sidebar-item {{ request()->user == $roleKey ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('users.index', $roleKey) }}">
                            <i class="fas fa-angle-double-right me-2"></i> {{ $role }}
                        </a>
                    </li>
                    @endforeach
                    @endcan
                </ul>
            </li>
            @endcan

            {{-- Merchants --}}
            <li class="sidebar-item {{ Str::startsWith(request()->route()->getName(), 'merchants.') || request()->route()->getName() == 'merchants.index' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ Route::has('merchants.index') ? route('merchants.index') : '#' }}">
                    <i class="align-middle me-2 fas fa-store"></i>
                    <span class="align-middle">Merchants</span>
                </a>
            </li>

            {{-- Sites (Advanced Mode Only) --}}
            <li class="sidebar-item advance-mode-item {{ Str::startsWith(request()->route()->getName(), 'sites.') || request()->route()->getName() == 'sites.index' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ Route::has('sites.index') ? route('sites.index') : '#' }}">
                    <i class="align-middle me-2 fas fa-th-large"></i>
                    <span class="align-middle">Sites</span>
                </a>
            </li>

            {{-- Site Users (Advanced Mode Only) --}}
            <li class="sidebar-item advance-mode-item {{ Str::startsWith(request()->route()->getName(), 'site-users.') || request()->route()->getName() == 'site-users.index' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ Route::has('site-users.index') ? route('site-users.index') : '#' }}">
                    <i class="align-middle me-2 fas fa-user-plus"></i>
                    <span class="align-middle">Site Users</span>
                </a>
            </li>

            {{-- Offers (Advanced Mode Only) --}}
            <li class="sidebar-item advance-mode-item {{ Str::startsWith(request()->route()->getName(), 'offers.') || request()->route()->getName() == 'offers.index' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ Route::has('offers.index') ? route('offers.index') : '#' }}">
                    <i class="align-middle me-2 fas fa-tag"></i>
                    <span class="align-middle">Offers</span>
                </a>
            </li>

            {{-- Customer Scans (Advanced Mode Only) --}}
            <li class="sidebar-item advance-mode-item {{ Str::startsWith(request()->route()->getName(), 'customer-scans.') || request()->route()->getName() == 'customer-scans.index' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ Route::has('customer-scans.index') ? route('customer-scans.index') : '#' }}">
                    <i class="align-middle me-2 fas fa-bullseye"></i>
                    <span class="align-middle">Customer Scans</span>
                </a>
            </li>

            {{-- Offer Scans (Advanced Mode Only) --}}
            <li class="sidebar-item advance-mode-item {{ Str::startsWith(request()->route()->getName(), 'offer-scans.') || request()->route()->getName() == 'offer-scans.index' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ Route::has('offer-scans.index') ? route('offer-scans.index') : '#' }}">
                    <i class="align-middle me-2 fas fa-qrcode"></i>
                    <span class="align-middle">Offer Scans</span>
                </a>
            </li>

            {{-- Point Awards (Advanced Mode Only) --}}
            <li class="sidebar-item advance-mode-item {{ Str::startsWith(request()->route()->getName(), 'point-awards.') || request()->route()->getName() == 'point-awards.index' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ Route::has('point-awards.index') ? route('point-awards.index') : '#' }}">
                    <i class="align-middle me-2 fas fa-award"></i>
                    <span class="align-middle">Point Awards</span>
                </a>
            </li>

            {{-- Spin History (Advanced Mode Only) --}}
            <li class="sidebar-item advance-mode-item {{ Str::startsWith(request()->route()->getName(), 'spin-history.') || request()->route()->getName() == 'spin-history.index' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ Route::has('spin-history.index') ? route('spin-history.index') : '#' }}">
                    <i class="align-middle me-2 fas fa-redo"></i>
                    <span class="align-middle">Spin History</span>
                </a>
            </li>

            {{-- Customer Logs --}}
            <li class="sidebar-item {{ Str::startsWith(request()->route()->getName(), 'customer-logs.') || request()->route()->getName() == 'customer-logs.index' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ Route::has('customer-logs.index') ? route('customer-logs.index') : '#' }}">
                    <i class="align-middle me-2 fas fa-clipboard-list"></i>
                    <span class="align-middle">Customer Logs</span>
                </a>
            </li>

            {{-- Inbox --}}
            <li class="sidebar-item {{ Str::startsWith(request()->route()->getName(), 'inbox.') || request()->route()->getName() == 'inbox.index' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ Route::has('inbox.index') ? route('inbox.index') : '#' }}">
                    <i class="align-middle me-2 fas fa-inbox"></i>
                    <span class="align-middle">Inbox</span>
                </a>
            </li>

            {{-- Feedbacks --}}
            <li class="sidebar-item {{ Str::startsWith(request()->route()->getName(), 'feedbacks.') || request()->route()->getName() == 'feedbacks.index' ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ Route::has('feedbacks.index') ? route('feedbacks.index') : '#' }}">
                    <i class="align-middle me-2 fas fa-comment-dots"></i>
                    <span class="align-middle">Feedbacks</span>
                </a>
            </li>

            {{-- SYSTEM SETTINGS (Dropdown) --}}
            @canany(['all_role', 'site_setting'])
            <li class="sidebar-header">System Settings</li>
            <li class="sidebar-item">
                <a data-bs-target="#settingsCollapse" data-bs-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle me-2 fas fa-cogs"></i>
                    <span class="align-middle">{{ __('language.configuration') }}</span>
                </a>
                <ul id="settingsCollapse"
                    class="sidebar-dropdown list-unstyled collapse {{ Str::startsWith(request()->route()->getName(), 'roles.') || Str::startsWith(request()->route()->getName(), 'settings.') || Str::startsWith(request()->route()->getName(), 'tags.') || Str::startsWith(request()->route()->getName(), 'systemstatuses.') ? 'show' : '' }}"
                    data-bs-parent="#sidebar">
                    @can('all_role')
                    <li
                        class="sidebar-item {{ Str::startsWith(request()->route()->getName(), 'roles.') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('roles.index') }}">
                            <i class="fas fa-angle-double-right me-2"></i> {{ __('language.role_permission') }}
                        </a>
                    </li>
                    @endcan

                    @can('site_setting')
                    <li
                        class="sidebar-item {{ Str::startsWith(request()->route()->getName(), 'settings.') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('settings.index', 'basic-info') }}">
                            <i class="fas fa-angle-double-right me-2"></i> {{ __('language.site_configuration') }}
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany
            {{--  --}}
            {{-- <li class="sidebar-item {{ request()->is('messages*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('messages.seen') }}">
                    <i class="align-middle me-2 fas fa-comments"></i>
                    <span class="align-middle">Communication</span>
                </a>
            </li> --}}
            {{-- USER ACCOUNT --}}
            <li class="sidebar-header">Account</li>
            <li
                class="sidebar-item {{ in_array(request()->route()->getName(), ['change_password', 'myprofile', 'safety_privacy']) ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('change_password') }}">
                    <i class="align-middle me-2 fas fa-user-cog"></i>
                    <span class="align-middle">{{ __('language.settings') }}</span>
                </a>
            </li>



        </ul>

        {{-- Footer Logo --}}
        <div class="sidebar-footer">
            <div class="uzimate-footer-logo">
                <span class="uzimate-logo-icon-small">U</span>
                <span class="uzimate-text">Uzimate</span>
            </div>
        </div>
    </div>
</nav>
