@props([
    'username' => 'Guest',
    'active' => 'dashboard',
])

<header class="mobile-nav-header">
    <a class="brand" href="{{ route('dashboard', ['username' => $username]) }}">
        Day<span>flow</span>
    </a>

    <button
        class="mobile-nav-toggle"
        type="button"
        aria-label="Open navigation menu"
        aria-controls="pr imary-navigation"
        aria-expanded="false"
    >
        <i class="bi bi-list" aria-hidden="true"></i>
    </button>
</header>

<aside class="sidebar" id="primary-navigation">
    <div class="sidebar-brand-row">
        <a class="brand" href="{{ route('dashboard', ['username' => $username]) }}">
            <span class="sidebar-brand-label">Day<span>flow</span></span>
            <span class="sidebar-brand-mark" aria-hidden="true">D</span>
        </a>

        <button
            class="desktop-nav-toggle"
            type="button"
            aria-label="Collapse navigation"
            aria-controls="primary-navigation"
            aria-expanded="true"
            title="Collapse navigation"
        >
            <i class="bi bi-chevron-left" aria-hidden="true"></i>
        </button>
    </div>

    <p
        class="text-uppercase mt-5 mb-3"
        style="font-size: 10px; letter-spacing: 1.5px; color: #B0ACBA;"
    >
        Menu
    </p>

    <nav class="sidebar-nav flex-column" aria-label="Main navigation">

        {{-- Dashboard --}}
        <a
            class="nav-link"
            href="{{ route('dashboard', ['username' => $username]) }}"
            aria-label="Dashboard"
            title="Dashboard"
            @class(['active' => $active === 'dashboard'])
        >
            <i class="bi bi-grid"></i>
            <span class="nav-label">Dashboard</span>
        </a>

        {{-- Task Management --}}
        <a
            class="nav-link"
            href="{{ route('management', ['username' => $username]) }}"
            aria-label="Task Management"
            title="Task Management"
            @class(['active' => $active === 'management'])
        >
            <i class="bi bi-check2-square"></i>
            <span class="nav-label">Task Management</span>
        </a>

        {{-- Calendar --}}
        <a
            class="nav-link"
            href="{{ route('calendar', ['username' => $username]) }}"
            aria-label="Calendar"
            title="Calendar"
            @class(['active' => $active === 'calendar'])
        >
            <i class="bi bi-calendar3"></i>
            <span class="nav-label">Calendar</span>
        </a>

        {{-- Profile --}}
        <a
            class="nav-link"
            href="{{ route('profile', ['username' => $username]) }}"
            aria-label="My Profile"
            title="My Profile"
            @class(['active' => $active === 'profile'])
        >
            <i class="bi bi-person"></i>
            <span class="nav-label">My Profile</span>
        </a>

    </nav>

    {{-- USER PROFILE & LOGOUT --}}
    <div class="mt-5 pt-4 border-top">
        <div class="d-flex align-items-center gap-2 mb-3">
            <div
                class="rounded-circle d-flex align-items-center justify-content-center"
                style="width: 38px; height: 38px; background: #EAE6F5; color: #7666A5;"
            >
                <i class="bi bi-person-fill"></i>
            </div>

            <div class="user-details overflow-hidden">
                <div class="fw-semibold text-truncate" style="font-size: 13px;">
                    {{ $username }}
                </div>

                <div class="text-secondary" style="font-size: 11px;">
                    Personal Account
                </div>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button
                class="nav-link w-100 border-0 bg-transparent text-start"
                type="submit"
                aria-label="Logout"
                title="Logout"
            >
                <i class="bi bi-box-arrow-left"></i>
                <span class="logout-label">Logout</span>
            </button>
        </form>
    </div>
</aside>

<script>
    (() => {
        const mobileToggle = document.querySelector('.mobile-nav-toggle');
        const desktopToggle = document.querySelector('.desktop-nav-toggle');
        const sidebar = document.getElementById('primary-navigation');

        if (!sidebar) {
            return;
        }

        function setMobileNavigationOpen(isOpen) {
            if (!mobileToggle) {
                return;
            }

            sidebar.classList.toggle('is-open', isOpen);
            mobileToggle.setAttribute('aria-expanded', String(isOpen));
            mobileToggle.setAttribute(
                'aria-label',
                isOpen ? 'Close navigation menu' : 'Open navigation menu'
            );
            mobileToggle.innerHTML = `<i class="bi ${isOpen ? 'bi-x-lg' : 'bi-list'}" aria-hidden="true"></i>`;
        }

        function setDesktopNavigationExpanded(isExpanded) {
            if (!desktopToggle) {
                return;
            }

            document.body.classList.toggle('sidebar-collapsed', !isExpanded);
            desktopToggle.setAttribute('aria-expanded', String(isExpanded));
            desktopToggle.setAttribute(
                'aria-label',
                isExpanded ? 'Collapse navigation' : 'Expand navigation'
            );
            desktopToggle.title = isExpanded
                ? 'Collapse navigation'
                : 'Expand navigation';
            desktopToggle.innerHTML = `<i class="bi bi-chevron-${isExpanded ? 'left' : 'right'}" aria-hidden="true"></i>`;
        }

        mobileToggle?.addEventListener('click', () => {
            setMobileNavigationOpen(
                mobileToggle.getAttribute('aria-expanded') !== 'true'
            );
        });

        desktopToggle?.addEventListener('click', () => {
            setDesktopNavigationExpanded(
                desktopToggle.getAttribute('aria-expanded') !== 'true'
            );
        });

        sidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => setMobileNavigationOpen(false));
        });

        document.addEventListener('click', event => {
            if (
                mobileToggle?.getAttribute('aria-expanded') === 'true'
                && !sidebar.contains(event.target)
                && !mobileToggle.contains(event.target)
            ) {
                setMobileNavigationOpen(false);
            }
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                setMobileNavigationOpen(false);
            }
        });
    })();
</script>
