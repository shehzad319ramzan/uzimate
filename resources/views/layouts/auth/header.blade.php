<nav class="navbar navbar-expand navbar-theme">
    <div class="d-flex align-items-center">
        <a class="sidebar-toggle d-flex me-3">
            <i class="hamburger align-self-center"></i>
        </a>
        <span class="header-title-nav">{{ $pageTitle ?? 'Dashboard' }}</span>
    </div>

    <div class="navbar-collapse collapse">
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item">
                <div class="advance-mode-toggle">
                    <span>Advance Mode:</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="advanceModeToggle">
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <span class="super-admin-badge">{{ auth()->user()->roles->first()->title ?? 'Super Admin' }}</span>
            </li>
            <li class="nav-item">
                <a href="{{ route('logout') }}" class="logout-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </li>
        </ul>
    </div>
</nav>
