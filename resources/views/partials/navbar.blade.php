<nav class="navbar">
    <div class="nav-left">
        <a href="{{ route('dashboard') }}"><img src="/logo/olga_neu.svg" alt="Frank Group"></a>
        <div class="company-switcher" id="companySwitcher">
            <button class="switcher-btn" id="switcherBtn">
                <i class="fas fa-building"></i>
                <span id="navCompanyName">{{ $companyName ?? 'Firmenansicht' }}</span>
                <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
            </button>
            <div class="switcher-content">
                <div style="padding: 10px 20px; font-size: 0.75rem; color: #1DA1F2; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.03);">Branding Europe GmbH</div>
                <a href="{{ route('company.switch', 1) }}" class="switcher-item {{ ($companyId ?? 1) == 1 && !request()->routeIs('offers.index') && !request()->routeIs('orders.index') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="{{ route('company.switch', 1) }}?redirect=offers" class="switcher-item {{ ($companyId ?? 1) == 1 && request()->routeIs('offers.index') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i> Angebotsübersicht
                </a>
                <a href="{{ route('company.switch', 1) }}?redirect=orders" class="switcher-item {{ ($companyId ?? 1) == 1 && request()->routeIs('orders.index') ? 'active' : '' }}">
                    <i class="fas fa-truck-loading"></i> Auftragsübersicht
                </a>
                <div style="height: 1px; background: var(--glass-border); margin: 5px 0;"></div>
                <div style="padding: 10px 20px; font-size: 0.75rem; color: #0088CC; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.03);">Europe Pen GmbH</div>
                <a href="{{ route('company.switch', 2) }}" class="switcher-item {{ ($companyId ?? 1) == 2 && !request()->routeIs('offers.index') && !request()->routeIs('orders.index') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="{{ route('company.switch', 2) }}?redirect=offers" class="switcher-item {{ ($companyId ?? 1) == 2 && request()->routeIs('offers.index') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i> Angebotsübersicht
                </a>
                <a href="{{ route('company.switch', 2) }}?redirect=orders" class="switcher-item {{ ($companyId ?? 1) == 2 && request()->routeIs('orders.index') ? 'active' : '' }}">
                    <i class="fas fa-truck-loading"></i> Auftragsübersicht
                </a>
            </div>
        </div>

        <!-- Global Search -->
        <form action="{{ route('global.search') }}" method="GET" style="display: flex; align-items: center; margin-left: 20px;">
            <div style="position: relative; display: flex; align-items: center;">
                <i class="fas fa-search" style="position: absolute; left: 12px; color: var(--text-muted); font-size: 0.85rem;"></i>
                <input type="text" name="query" placeholder="Nummer suchen..." 
                    style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: #fff; padding: 7px 15px 7px 35px; border-radius: 20px; font-size: 0.85rem; width: 180px; transition: all 0.3s; outline: none;"
                    onfocus="this.style.width='250px'; this.style.borderColor='var(--primary-accent)'; this.style.background='rgba(255,255,255,0.18)'"
                    onblur="this.style.width='180px'; this.style.borderColor='var(--glass-border)'; this.style.background='var(--glass-bg)'">
            </div>
        </form>
    </div>

    <div style="display: flex; align-items: center; gap: 10px;">
        <div class="user-dropdown" id="userDropdown">
            <button class="user-btn" id="userBtn">
                <i class="fas fa-user-circle" style="color: var(--primary-accent); font-size: 1.1rem;"></i>
                <span id="navUserName">{{ $user->name_komplett ?? 'User' }}</span>
                @if(isset($openTodoCount) && $openTodoCount > 0)
                    <span class="todo-badge" id="navTodoBadge">{{ $openTodoCount }}</span>
                @endif
                <i class="fas fa-chevron-down" style="font-size: 0.65rem; color: var(--text-muted);"></i>
            </button>
            <div class="user-dropdown-menu">
                <div class="user-dropdown-header" style="padding: 14px 18px; background: rgba(255,255,255,0.04); border-bottom: 1px solid var(--glass-border);">
                    <div class="user-name" style="font-weight: 600; font-size: 0.9rem; color: #fff;">{{ $user->name_komplett ?? 'User' }}</div>
                    <div class="user-role" style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">{{ $companyName ?? '' }}</div>
                </div>
                <a href="{{ route('my.dashboard') }}" class="user-dropdown-item {{ request()->routeIs('my.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i> Mein Dashboard
                </a>
                <a href="{{ route('calendar') }}" class="user-dropdown-item {{ request()->routeIs('calendar') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i> Mein Kalender
                </a>
                <a href="{{ route('products.index') }}" class="user-dropdown-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i> Produkte
                </a>
                <a href="{{ route('manufacturers.index') }}" class="user-dropdown-item {{ request()->routeIs('manufacturers.*') ? 'active' : '' }}">
                    <i class="fas fa-industry"></i> Hersteller
                </a>
                <a href="{{ route('portals.index') }}" class="user-dropdown-item {{ request()->routeIs('portals.*') ? 'active' : '' }}">
                    <i class="fas fa-globe"></i> Portale
                </a>
                <a href="{{ route('companies.index') }}" class="user-dropdown-item {{ request()->routeIs('companies.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i> Firmen verwalten
                </a>
                <a href="{{ route('settings.email.index') }}" class="user-dropdown-item {{ request()->routeIs('settings.email.*') ? 'active' : '' }}">
                    <i class="fas fa-envelope-open-text"></i> E-Mail Einstellungen
                </a>
                <div class="user-dropdown-divider" style="height: 1px; background: var(--glass-border); margin: 4px 0;"></div>
                <a href="#" class="user-dropdown-item logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Abmelden
                </a>
            </div>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</nav>

@push('styles')
<style>
    .navbar {
        position: sticky; top: 0; z-index: 1000;
        background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(15px);
        padding: 12px 40px; border-bottom: 1px solid var(--glass-border);
        display: flex; justify-content: space-between; align-items: center;
        box-shadow: 0 4px 20px rgba(0,0,0,0.4);
    }
    .nav-left { display: flex; align-items: center; gap: 30px; }
    .navbar img { height: 38px; }

    .company-switcher { position: relative; display: inline-block; }
    .switcher-btn {
        background: var(--glass-bg); border: 1px solid var(--glass-border);
        padding: 8px 16px; border-radius: 10px; color: var(--text-main);
        cursor: pointer; font-size: 0.9rem; display: flex; align-items: center; gap: 10px;
        transition: all 0.3s;
    }
    .switcher-btn:hover { background: rgba(255,255,255,0.15); border-color: var(--primary-accent); }
    .switcher-content {
        display: none; position: absolute; top: 100%; left: 0;
        background: #1e293b; min-width: 220px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        border-radius: 10px; margin-top: 8px; overflow: hidden;
        border: 1px solid var(--glass-border);
    }
    .company-switcher.active .switcher-content { display: block; }
    .switcher-item {
        padding: 12px 20px; color: var(--text-muted); text-decoration: none;
        display: flex; align-items: center; gap: 10px; transition: background 0.3s, color 0.3s;
        font-size: 0.85rem;
    }
    .switcher-item:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }
    .switcher-item.active { border-left: 3px solid var(--primary-accent); color: var(--text-main); background: rgba(255,255,255,0.05); }

    .user-dropdown { position: relative; }
    .user-btn {
        background: none; border: none; color: var(--text-main); cursor: pointer;
        display: flex; align-items: center; gap: 8px; font-size: 0.95rem; font-family: 'Inter', sans-serif;
        padding: 6px 10px; border-radius: 8px; transition: background 0.2s;
    }
    .user-btn:hover { background: rgba(255,255,255,0.08); }
    .todo-badge {
        background: #ef4444; color: #fff; font-size: 0.65rem; font-weight: 700;
        padding: 1px 5px; border-radius: 10px; border: 2px solid #0f172a;
        margin-left: -5px; margin-top: -12px;
    }
    .user-dropdown-menu {
        display: none; position: absolute; top: 110%; right: 0;
        background: #1e293b; min-width: 220px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        border-radius: 12px; overflow: hidden; border: 1px solid var(--glass-border); z-index: 2000;
    }
    .user-dropdown.active .user-dropdown-menu { display: block; }
    .user-dropdown-item {
        padding: 11px 18px; color: var(--text-muted); text-decoration: none;
        display: flex; align-items: center; gap: 10px; font-size: 0.85rem; transition: background 0.2s, color 0.2s;
    }
    .user-dropdown-item:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }
    .user-dropdown-item.active { color: var(--primary-accent); background: rgba(255,255,255,0.03); font-weight: 600; }
    .user-dropdown-item.logout { color: #f87171; }
    .user-dropdown-item.logout:hover { background: rgba(248, 113, 113, 0.1); }
</style>
@endpush

@push('scripts')
<script>
    (function() {
        const userBtn = document.getElementById('userBtn');
        const userDropdown = document.getElementById('userDropdown');
        const switcherBtn = document.getElementById('switcherBtn');
        const companySwitcher = document.getElementById('companySwitcher');
        
        if(userBtn) {
            userBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
                if(companySwitcher) companySwitcher.classList.remove('active');
            });
        }

        if(switcherBtn) {
            switcherBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                companySwitcher.classList.toggle('active');
                if(userDropdown) userDropdown.classList.remove('active');
            });
        }

        document.addEventListener('click', () => {
            if(userDropdown) userDropdown.classList.remove('active');
            if(companySwitcher) companySwitcher.classList.remove('active');
        });
    })();
</script>
@endpush
