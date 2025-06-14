{{-- resources/views/components/navbar.blade.php --}}

@props([
    'title' => 'Dashboard',
    'searchPlaceholder' => 'Search...',
    'userInitials' => Str::of(auth()->user()->name)->explode(' ')->when(
        fn($parts) => $parts->count() >= 2,
        fn($parts) => strtoupper($parts->first()[0] . $parts->last()[0]),
        fn($parts) => strtoupper(Str::substr($parts->first(), 0, 2))
    ),
    'showNotificationDot' => $notifications->count() != 0,
])

<nav class="navbar mx-4 mt-4">
    <div class="navbar-left">
        <button class="menu-toggle" id="menuToggle">
            <div class="menu-icon"></div>
        </button>
        <div class="navbar-title">{{ $title }}</div>
    </div>

    {{-- <div class="navbar-center">
        <div class="search-container">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" class="search-input" placeholder="{{ $searchPlaceholder }}">
        </div>
    </div> --}}

    <div class="navbar-right">
        <button class="nav-button" onclick="handleNotificationClick()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            @if($showNotificationDot)
                <div class="notification-dot"></div>
            @endif
        </button>
        
        {{-- <button class="nav-button" onclick="handleMessageClick()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </button>
         --}}
        {{-- <button class="nav-button" onclick="handleSettingsClick()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1 1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
        </button> --}}
        
        <div class="user-avatar" onclick="handleUserClick()">{{ $userInitials }}</div>
        <form method="GET" action="/logout" class="d-inline">
            @csrf
            <button type="submit" class="nav-button logout-button">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </button>
        </form>
    </div>
</nav>

<style>
    .navbar {
        position:relative;
       
        /* top: 16px;
        right: 2%; */
        height: 60px;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
        z-index: 1000;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
       
        
    }

    .navbar-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .menu-toggle {
        display: none;
        width: 40px;
        height: 40px;
        border: none;
        background: none;
        border-radius: 8px;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .menu-toggle:hover {
        background: rgba(0, 0, 0, 0.04);
    }

    .menu-icon {
        width: 20px;
        height: 2px;
        background: #1a1a1a;
        position: relative;
        transition: all 0.3s ease;
    }

    .menu-icon::before,
    .menu-icon::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 2px;
        background: #1a1a1a;
        transition: all 0.3s ease;
    }

    .menu-icon::before {
        top: -6px;
    }

    .menu-icon::after {
        top: 6px;
    }

    .navbar-title {
        font-size: 18px;
        font-weight: 600;
        color: #1a1a1a;
        letter-spacing: -0.2px;
    }

    .navbar-center {
        flex: 1;
        max-width: 480px;
        margin: 0 32px;
    }

    .search-container {
        position: relative;
        width: 100%;
    }

    .search-input {
        width: 100%;
        height: 36px;
        padding: 0 16px 0 40px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.6);
        font-size: 14px;
        color: #1a1a1a;
        transition: all 0.2s ease;
        outline: none;
        backdrop-filter: blur(10px);
    }

    .search-input::placeholder {
        color: #8b8b8b;
    }

    .search-input:focus {
        border-color: rgba(0, 122, 255, 0.6);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1);
        transform: scale(1.02);
    }

    .search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        opacity: 0.6;
    }

    .navbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nav-button {
        width: 36px;
        height: 36px;
        border: none;
        background: rgba(255, 255, 255, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        position: relative;
        backdrop-filter: blur(10px);
    }

    .nav-button:hover {
        background: rgba(255, 255, 255, 0.6);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .nav-button:active {
        transform: scale(0.96);
    }

    .nav-icon {
        width: 20px;
        height: 20px;
        opacity: 0.7;
    }

    .notification-dot {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 8px;
        height: 8px;
        background: #FF3B30;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.95);
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-left: 8px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }

    .user-avatar:hover {
        transform: scale(1.05) translateY(-2px);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
        border-color: rgba(255, 255, 255, 0.5);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .navbar {
            left: 16px;
            right: 16px;
            padding: 0 16px;
        }

        .menu-toggle {
            display: flex;
        }

        .navbar-center {
            display: none;
        }

        .navbar-title {
            font-size: 16px;
        }
    }

    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .navbar {
        animation: fadeIn 0.5s ease-out;
    }
</style>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.getElementById('menuToggle');
        const menuIcon = document.querySelector('.menu-icon');

        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                // Dispatch custom event for sidebar toggle
                window.dispatchEvent(new CustomEvent('toggleSidebar'));
                
                // Animate menu icon
                if (menuIcon.style.transform === 'rotate(45deg)') {
                    menuIcon.style.transform = 'rotate(0deg)';
                } else {
                    menuIcon.style.transform = 'rotate(45deg)';
                }
            });
        }

        // Enhanced search interaction
        const searchInput = document.querySelector('.search-input');
        const searchContainer = document.querySelector('.search-container');

        if (searchInput && searchContainer) {
            searchInput.addEventListener('focus', () => {
                searchContainer.style.transform = 'scale(1.02)';
            });

            searchInput.addEventListener('blur', () => {
                searchContainer.style.transform = 'scale(1)';
            });
        }

        // Navbar scroll effect with floating animation
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 20) {
                navbar.style.background = 'rgba(255, 255, 255, 0.9)';
                navbar.style.boxShadow = '0 12px 40px rgba(0, 0, 0, 0.15)';
                navbar.style.transform = 'scale(0.98)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.85)';
                navbar.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.1)';
                navbar.style.transform = 'scale(1)';
            }
        });
    });

    // Navigation handlers (customize these for your app)
    async function handleNotificationClick() {
        const notificationDot = document.querySelector('.notification-dot');
        if (notificationDot) {
            notificationDot.style.display = 'none';
        }

      
        

        // Show notification dropdown
        const dropdown = document.createElement('div');
        dropdown.className = 'notification-dropdown';
        dropdown.innerHTML = `
            <div class="notification-header" style="padding: 15px; border-bottom: 1px solid #eee;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600;">Notifications</h3>
            </div>
            <div class="notification-list" style="max-height: 400px; overflow-y: auto;">
                @foreach($notifications as $notification)
                    <div class="notification-item" style="background: #fff; padding: 20px; border-bottom: 1px solid #eee; 
                        @if($notification->is_read == 0) background-color: #f0f7ff; @endif">
                        <div class="notification-title" style="font-weight: 600; margin-bottom: 8px; font-size: 14px;">
                            @if($notification->is_read == 0)
                                <span style="display: inline-block; width: 8px; height: 8px; background: #007AFF; border-radius: 50%; margin-right: 8px;"></span>
                            @endif
                            {{ $notification->title }}
                        </div>
                        <div class="notification-text" style="color: #666; font-size: 13px;">{{ $notification->message }}</div>
                        <div class="notification-time" style="color: #999; font-size: 12px; margin-top: 8px;">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
            <div class="notification-footer" style="padding: 15px; text-align: center; border-top: 1px solid #eee;">
                <a href="/notifications" style="color: #007AFF; text-decoration: none; font-size: 14px;">View All Notifications</a>
            </div>
        `;
        document.body.appendChild(dropdown);

        // Position dropdown below notification button
        const button = event.currentTarget;
        const rect = button.getBoundingClientRect();
        dropdown.style.position = 'absolute';
        dropdown.style.top = `${rect.bottom + 12}px`;
        dropdown.style.right = `${window.innerWidth - rect.right}px`;
        dropdown.style.background = '#fff';
        dropdown.style.borderRadius = '16px';
        dropdown.style.boxShadow = '0 6px 24px rgba(0,0,0,0.12)';
        dropdown.style.minWidth = '350px';
        dropdown.style.maxWidth = '400px';
        dropdown.style.border = '1px solid rgba(0,0,0,0.1)';
        dropdown.style.zIndex = '9999';

        // Add click outside listener to close dropdown
        const closeDropdown = (e) => {
            if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                dropdown.remove();
                document.removeEventListener('click', closeDropdown);
            }
        };
        const userId = {{ auth()->user()->id }};
        const response = await fetch('/api/notifications/read/' + userId);
        setTimeout(() => document.addEventListener('click', closeDropdown), 0);
    }
    
    function handleMessageClick() {
        // Add your message logic here
        console.log('Messages clicked');
    }

    function handleSettingsClick() {
        // Add your settings logic here
        console.log('Settings clicked');
    }

    function handleUserClick() {
        // Add your user menu logic here
        console.log('User menu clicked');
    }
</script>