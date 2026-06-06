@php
$menuItems = [
    [
        'id' => 'clinics',
        'label' => 'All Clinics',
        'route' => '/owner', 
        'permission' => [5],
        'icon_name' => 'building',
    ],
    [
        'id' => 'dashboard',
        'label' => 'Dashboard',
        'route' => '/dashboard', 
        'permission' => [1,2],
        'icon_name' => 'chart'
    ],
    [
        'id' => 'clinics',
        'label' => 'Clinics',
        'route' => '/clinics',
        'permission' => [1,2],
        'icon_name' => 'building'
    ],
    [
        'id' => 'users',
        'label' => 'Users',
        'hasSubmenu' => true,
        'icon_name' => 'users',
        'submenu' => [
            [
                'id' => 'staffs',
                'label' => 'Staff Members',
                'route' => '/staffs',
                'permission' => [1,2],
                'icon_name' => 'staff'
            ],
            [
                'id' => 'owners',
                'label' => 'Pet Owners',
                'route' => '/owners',
                'permission' => [1,2,3],
                'icon_name' => 'users'
            ]
        ]
    ],
    [
        'id' => 'pets',
        'label' => 'Pet Management',
        'hasSubmenu' => true,
        'icon_name' => 'pets',
        'submenu' => [
            [
                'id' => 'all-pets', 
                'label' => 'All Pets', 
                'route' => '/pets',
                'permission' => [1,2,3,4,5],
                'icon_name' => 'pets'
            ],
            [
                'id' => 'breeds', 
                'label' => 'Breeds', 
                'route' => '/breeds',
                'permission' => [1],
                'icon_name' => 'breeds'
            ],
            [
                'id' => 'species', 
                'label' => 'Species', 
                'route' => '/species',
                'permission' => [1],
                'icon_name' => 'species'
            ],
        ]
    ],
    [
        'id' => 'services',
        'label' => 'Services',
        'hasSubmenu' => true,
        'icon_name' => 'stethoscope',
        'submenu' => [
            [
                'id' => 'all-services',
                'label' => 'All Services',
                'route' => '/services',
                'permission' => [1,2,3],
                'icon_name' => 'list'
            ],
            [
                'id' => 'services-schedule',
                'label' => 'Schedule',
                'route' => '/services-schedule',
                'permission' => [1,2,3],
                'icon_name' => 'calendar'
            ]
        ]
    ],
    [
        'id' => 'bookings',
        'label' => 'Appointments',
        'route' => '/bookings',
        'permission' => [1,2,3,4,5],
        'icon_name' => 'calendar'
    ],
    [
        'id' => 'medical-histories',
        'label' => 'Medical Records',
        'route' => '/medical-histories',
        'permission' => [1,2,3,4],
        'icon_name' => 'medical'
    ],
    [
        'id' => 'inventory',
        'label' => 'Inventory',
        'hasSubmenu' => true,
        'icon_name' => 'package',
        'submenu' => [
            [
                'id' => 'all-inventory',
                'label' => 'All Items',
                'route' => '/inventory',
                'permission' => [1,2,3],
                'icon_name' => 'package'
            ],
            [
                'id' => 'inventory-categories',
                'label' => 'Categories',
                'route' => '/categories',
                'permission' => [1,2,3],
                'icon_name' => 'category'
            ]
        ]
    ],
    [
        'id' => 'sales-report',
        'label' => 'Reports',
        'route' => '/sales-report',
        'permission' => [1,2],
        'icon_name' => 'chart'
    ],
    [
        'id' => 'rule-base',
        'label' => 'Rule Base',
        'route' => '/rule-base',
        'permission' => [1],
        'icon_name' => 'settings'
    ],
    [
        'id' => 'clinic-rating',
        'label' => 'Ratings',
        'route' => '/clinic-ratings',
        'permission' => [1,2],
        'icon_name' => 'star'
    ],
    [
        'id' => 'activity-records',
        'label' => 'Activity Log',
        'route' => '/activity-records',
        'permission' => [1,2],
        'icon_name' => 'clock'
    ],
];

Route::currentRouteName() === 'notifications' ? array_push($menuItems, [
    'id' => 'notifications',
    'label' => 'Notifications',
    'route' => '/notifications',
    'permission' => [1,2,3,4],
    'icon_name' => 'notification'
]) : null;

$currentRoute = request()->route() ? request()->route()->getName() : '';
$activeItem = 'dashboard';
$activeSubItem = null;

foreach($menuItems as $item) {
    if(isset($item['route']) && trim($currentRoute, '/') === trim($item['route'], '/')) {
        $activeItem = $item['id'];
        break;
    }
    
    if(isset($item['submenu'])) {
        foreach($item['submenu'] as $subItem) {
            $subRoute = trim($subItem['route'], '/');
            if(trim($currentRoute, '/') === $subRoute || str_starts_with(trim($currentRoute, '/'), $subRoute.'/')) {
                $activeItem = $item['id'];
                $activeSubItem = $subItem['id'];
                break 2;
            }
        }
    }
}

$expandedMenus = [];
if($activeItem) {
    foreach($menuItems as $item) {
        if(isset($item['hasSubmenu']) && $item['hasSubmenu'] && $item['id'] === $activeItem) {
            $expandedMenus[$item['id']] = true;
        }
    }
}
@endphp

<aside class="sidebar-clean">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="brand-logo">
                <img src="{{ asset('assets/img/logo.png') }}" alt="PetVax Logo">
            </div>
            <div class="brand-text">
                <h1>PetVax</h1>
                <span>{{ ucfirst(\App\Models\Role::find(auth()->user()->role_id)->name ?? 'User') }}</span>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <div class="nav-section">
            <h3 class="nav-title">Main Menu</h3>
            <ul class="nav-list">
                @foreach($menuItems as $item)
                    @php
                        $isActive = $activeItem === $item['id'];
                        $hasSubmenu = $item['hasSubmenu'] ?? false;
                        $isExpanded = isset($expandedMenus[$item['id']]) && $expandedMenus[$item['id']];
                        
                        $hasPermission = false;
                        if ($hasSubmenu) {
                            foreach ($item['submenu'] as $subItem) {
                                if (in_array(auth()->user()->role_id, $subItem['permission'])) {
                                    $hasPermission = true;
                                    break;
                                }
                            }
                        } else {
                            $hasPermission = in_array(auth()->user()->role_id, $item['permission'] ?? []);
                        }
                    @endphp
                    
                    @if($hasPermission)
                    <li class="nav-item">
                        <a href="{{ $hasSubmenu ? '#' : $item['route'] }}" 
                           class="nav-link {{ $isActive ? 'active' : '' }}"
                           @if($hasSubmenu) onclick="toggleSubmenu('{{ $item['id'] }}'); return false;" @endif>
                            <x-ui.icon name="{{ $item['icon_name'] }}" class="nav-icon" />
                            <span class="nav-text">{{ $item['label'] }}</span>
                            @if($hasSubmenu)
                                <x-ui.icon name="chevron-down" class="nav-arrow {{ $isExpanded ? 'expanded' : '' }}" />
                            @endif
                        </a>
                        
                        @if($hasSubmenu)
                            <ul class="nav-submenu {{ $isExpanded ? 'expanded' : '' }}" id="submenu-{{ $item['id'] }}">
                                @foreach($item['submenu'] as $subItem)
                                    @if(in_array(auth()->user()->role_id, $subItem['permission']))
                                        @php $isSubActive = $activeSubItem === $subItem['id']; @endphp
                                        <li class="nav-subitem">
                                            <a href="{{ $subItem['route'] }}" class="nav-sublink {{ $isSubActive ? 'active' : '' }}">
                                                <x-ui.icon name="{{ $subItem['icon_name'] }}" class="nav-subicon" />
                                                <span class="nav-subtext">{{ $subItem['label'] }}</span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </nav>

    <!-- User Profile -->
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">
                <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('assets/img/team-2.jpg') }}" 
                     alt="{{ auth()->user()->name }}"
                     onerror="this.src='{{ asset('assets/img/team-2.jpg') }}'">
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name ?? 'Admin User' }}</div>
                <div class="user-email">{{ auth()->user()->email ?? 'admin@petvax.com' }}</div>
            </div>
            <div class="user-actions">
                <a href="/logout" class="logout-btn" title="Logout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </a>
            </div>
        </div>
    </div>
</aside>

<style>
.sidebar-clean {
    width: 280px;
    height: 100vh;
    background: var(--white);
    border-right: 1px solid var(--gray-200);
    display: flex;
    flex-direction: column;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    transition: all 0.3s ease;
    font-family: var(--site-font, var(--font-family));
}

.sidebar-clean.collapsed {
    width: 70px;
}

/* Header */
.sidebar-header {
    padding: var(--space-6);
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: var(--space-3);
}

.brand-logo {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-lg);
    overflow: hidden;
    flex-shrink: 0;
}

.brand-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.brand-text h1 {
    font-size: var(--font-size-xl);
    font-weight: 700;
    color: var(--font-color, var(--gray-900));
    margin: 0;
    line-height: 1.2;
}

.brand-text span {
    font-size: var(--font-size-xs);
    color: var(--gray-500);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.sidebar-toggle {
    width: 36px;
    height: 36px;
    border: none;
    background: var(--gray-100);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-600);
    cursor: pointer;
    transition: var(--transition);
}

.sidebar-toggle:hover {
    background: var(--gray-200);
    color: var(--gray-800);
}

/* Navigation */
.sidebar-nav {
    flex: 1;
    padding: var(--space-4) 0;
    overflow-y: auto;
}

.nav-section {
    padding: 0 var(--space-4);
}

.nav-title {
    font-size: var(--font-size-xs);
    font-weight: 600;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0 0 var(--space-4) var(--space-4);
}

.nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin-bottom: var(--space-1);
}

.nav-link {
    display: flex;
    align-items: center;
    padding: var(--space-3) var(--space-4);
    color: var(--font-color, var(--gray-700));
    text-decoration: none;
    border-radius: var(--radius-lg);
    transition: var(--transition);
    font-weight: 500;
    position: relative;
}

.nav-link:hover {
    background: var(--gray-50);
    color: var(--primary-color) !important;
    text-decoration: none;
}

.nav-link.active {
    background: var(--primary-bg-light);
    color: var(--primary-color) !important;
    font-weight: 600;
}

.nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 20px;
    background: var(--primary-color) !important;
    border-radius: 0 2px 2px 0;
}

.nav-icon {
    width: 20px;
    font-size: var(--font-size-base);
    margin-right: var(--space-3);
    flex-shrink: 0;
}

.nav-text {
    flex: 1;
    font-size: var(--font-size-sm);
}

.nav-arrow {
    font-size: var(--font-size-xs);
    transition: transform 0.2s ease;
    margin-left: var(--space-2);
}

.nav-arrow.expanded {
    transform: rotate(180deg);
}

/* Submenu */
.nav-submenu {
    list-style: none;
    padding: 0;
    margin: 0;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.nav-submenu.expanded {
    max-height: 300px;
    padding-top: var(--space-2);
}

.nav-subitem {
    margin-bottom: var(--space-1);
}

.nav-sublink {
    display: flex;
    align-items: center;
    padding: var(--space-2) var(--space-4) var(--space-2) var(--space-12);
    color: var(--font-color, var(--gray-600));
    text-decoration: none;
    border-radius: var(--radius);
    transition: var(--transition);
    font-size: var(--font-size-sm);
    position: relative;
}

.nav-sublink:hover {
    background: var(--gray-50);
    color: var(--primary-color) !important;
    text-decoration: none;
}

.nav-sublink.active {
    background: var(--primary-bg-light);
    color: var(--primary-color) !important;
    font-weight: 500;
}

.nav-sublink.active::before {
    content: '';
    position: absolute;
    left: var(--space-8);
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 6px;
    background: var(--primary-color) !important;
    border-radius: 50%;
}

.nav-subicon {
    width: 16px;
    font-size: var(--font-size-sm);
    margin-right: var(--space-3);
    flex-shrink: 0;
}

.nav-subtext {
    font-size: var(--font-size-sm);
}

/* Footer */
.sidebar-footer {
    padding: var(--space-4);
    border-top: 1px solid var(--gray-200);
}

.user-profile {
    display: flex;
    align-items: center;
    gap: var(--space-3);
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-info {
    flex: 1;
    min-width: 0;
}

.user-name {
    font-size: var(--font-size-sm);
    font-weight: 600;
    color: var(--font-color, var(--gray-900));
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-email {
    font-size: var(--font-size-xs);
    color: var(--gray-500);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-actions {
    flex-shrink: 0;
}

.logout-btn {
    width: 32px;
    height: 32px;
    border-radius: var(--radius);
    background: var(--gray-100);
    color: var(--gray-600);
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: var(--transition);
}

.logout-btn:hover {
    background: var(--danger-light);
    color: var(--danger);
    text-decoration: none;
}

/* Collapsed state */
.sidebar-clean.collapsed .brand-text,
.sidebar-clean.collapsed .nav-text,
.sidebar-clean.collapsed .nav-title,
.sidebar-clean.collapsed .nav-arrow,
.sidebar-clean.collapsed .user-info {
    display: none;
}

.sidebar-clean.collapsed .nav-link {
    justify-content: center;
    padding: var(--space-3);
}

.sidebar-clean.collapsed .nav-icon {
    margin-right: 0;
}

.sidebar-clean.collapsed .nav-submenu {
    display: none;
}

/* Scrollbar */
.sidebar-nav::-webkit-scrollbar {
    width: 4px;
}

.sidebar-nav::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar-nav::-webkit-scrollbar-thumb {
    background: var(--gray-300);
    border-radius: 2px;
}

.sidebar-nav::-webkit-scrollbar-thumb:hover {
    background: var(--gray-400);
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar-clean {
        transform: translateX(-100%);
    }
    
    .sidebar-clean.mobile-open {
        transform: translateX(0);
    }
}
</style>

<script>
let isCollapsed = false;
let expandedMenus = @json($expandedMenus);

function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar-clean');
    const mainContent = document.querySelector('.main-content-with-sidebar');
    isCollapsed = !isCollapsed;
    
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
        if (mainContent) mainContent.classList.add('sidebar-collapsed');
        // Close all submenus when collapsed
        Object.keys(expandedMenus).forEach(key => {
            expandedMenus[key] = false;
            const submenu = document.getElementById('submenu-' + key);
            const arrow = document.querySelector(`[onclick*="${key}"] .nav-arrow`);
            if (submenu) submenu.classList.remove('expanded');
            if (arrow) arrow.classList.remove('expanded');
        });
    } else {
        sidebar.classList.remove('collapsed');
        if (mainContent) mainContent.classList.remove('sidebar-collapsed');
        // Restore active submenu
        const activeItem = '{{ $activeItem }}';
        if (activeItem && expandedMenus[activeItem]) {
            const submenu = document.getElementById('submenu-' + activeItem);
            const arrow = document.querySelector(`[onclick*="${activeItem}"] .nav-arrow`);
            if (submenu) submenu.classList.add('expanded');
            if (arrow) arrow.classList.add('expanded');
        }
    }
}

function toggleSubmenu(itemId) {
    if (isCollapsed) return;
    
    const submenu = document.getElementById('submenu-' + itemId);
    const arrow = document.querySelector(`[onclick*="${itemId}"] .nav-arrow`);
    
    if (!expandedMenus[itemId]) {
        expandedMenus[itemId] = true;
        submenu.classList.add('expanded');
        arrow.classList.add('expanded');
    } else {
        expandedMenus[itemId] = false;
        submenu.classList.remove('expanded');
        arrow.classList.remove('expanded');
    }
}

// Initialize expanded menus on page load
document.addEventListener('DOMContentLoaded', function() {
    const activeItem = '{{ $activeItem }}';
    if (activeItem && expandedMenus[activeItem]) {
        const submenu = document.getElementById('submenu-' + activeItem);
        const arrow = document.querySelector(`[onclick*="${activeItem}"] .nav-arrow`);
        if (submenu) submenu.classList.add('expanded');
        if (arrow) arrow.classList.add('expanded');
    }
});

// Mobile menu toggle
function toggleMobileSidebar() {
    const sidebar = document.querySelector('.sidebar-clean');
    sidebar.classList.toggle('mobile-open');
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(e) {
    const sidebar = document.querySelector('.sidebar-clean');
    const toggle = document.querySelector('.mobile-menu-toggle');
    
    if (window.innerWidth <= 768 && 
        !sidebar.contains(e.target) && 
        !toggle?.contains(e.target)) {
        sidebar.classList.remove('mobile-open');
    }
});
</script>
