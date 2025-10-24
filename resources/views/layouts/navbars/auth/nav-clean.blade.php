@props([
    'title' => ucfirst(str_replace(['-', '_'], ' ', Request::segment(1) ?: 'Dashboard')),
    'showSearch' => false,
    'searchPlaceholder' => 'Search...',
])

@php
$notifications = \App\Models\Notification::where('user_id', auth()->id())->latest()->limit(5)->get();
$unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', 0)->count();
@endphp

<header class="topbar-clean">
    <div class="topbar-container">
        <!-- Left Section -->
        <div class="topbar-left">
            <button class="mobile-menu-toggle" onclick="toggleMobileSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="page-title">
                <h1>{{ $title }}</h1>
                <div class="breadcrumb">
                    <span class="breadcrumb-item">{{ ucfirst(\App\Models\Role::find(auth()->user()->role_id)->name ?? 'User') }}</span>
                    <i class="fas fa-chevron-right breadcrumb-separator"></i>
                    <span class="breadcrumb-item current text-primary">{{ $title }}</span>
                </div>
            </div>
        </div>

        <!-- Center Section (Search) -->
        @if($showSearch)
        <div class="topbar-center">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="{{ $searchPlaceholder }}" />
                <div class="search-suggestions hidden">
                    <!-- Search suggestions will be populated here -->
                </div>
            </div>
        </div>
        @endif

        <!-- Right Section -->
        <div class="topbar-right">
            <!-- Notifications -->
            <div class="topbar-item notification-wrapper">
                <button class="topbar-btn" onclick="toggleNotifications()" id="notificationBtn">
                    <i class="fas fa-bell"></i>
                    @if($unreadCount > 0)
                        <span class="notification-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>
                
                <!-- Notifications Dropdown -->
                <div class="notifications-dropdown hidden" id="notificationsDropdown">
                    <div class="dropdown-header">
                        <h4>Notifications</h4>
                        @if($unreadCount > 0)
                            <button class="mark-all-read" onclick="markAllAsRead()">Mark all as read</button>
                        @endif
                    </div>
                    <div class="notifications-list">
                        @forelse($notifications as $notification)
                            <div class="notification-item {{ $notification->is_read ? '' : 'unread' }}">
                                <div class="notification-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">{{ $notification->title }}</div>
                                    <div class="notification-message">{{ $notification->message }}</div>
                                    <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="no-notifications">
                                <i class="fas fa-bell-slash"></i>
                                <p>No notifications yet</p>
                            </div>
                        @endforelse
                    </div>
                    @if($notifications->count() > 0)
                        <div class="dropdown-footer">
                            <a href="/notifications" class="view-all-btn">View All Notifications</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Settings (Admin Only) -->
            @if(auth()->user()->role_id == 1)
            <div class="topbar-item">
                <button class="topbar-btn" onclick="openModal('settingsModal')" title="Settings">
                    <i class="fas fa-cog"></i>
                </button>
            </div>
            @endif

            <!-- User Profile -->
            <div class="topbar-item user-profile-wrapper">
                <button class="user-profile-btn" onclick="toggleUserMenu()">
                    <div class="user-avatar">
                        <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('assets/img/team-2.jpg') }}" 
                             alt="{{ auth()->user()->name }}"
                             onerror="this.src='{{ asset('assets/img/team-2.jpg') }}'">
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">{{ ucfirst(\App\Models\Role::find(auth()->user()->role_id)->name ?? 'User') }}</div>
                    </div>
                    <i class="fas fa-chevron-down user-dropdown-arrow"></i>
                </button>

                <!-- User Dropdown -->
                <div class="user-dropdown hidden" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="user-avatar-large">
                            <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('assets/img/team-2.jpg') }}" 
                                 alt="{{ auth()->user()->name }}"
                                 onerror="this.src='{{ asset('assets/img/team-2.jpg') }}'">
                        </div>
                        <div class="user-details">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-email">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="dropdown-menu">
                        <a href="/profile" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>Profile</span>
                        </a>
                        <a href="#" onclick="openChangePasswordModal()" class="dropdown-item">
                            <i class="fas fa-key"></i>
                            <span>Change Password</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="GET" action="/logout" class="logout-form">
                            @csrf
                            <button type="submit" class="dropdown-item logout-item">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Change Password Modal -->
<div class="modal-overlay hidden" id="changePasswordModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Change Password</h3>
            <button class="modal-close" onclick="closeChangePasswordModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="changePasswordForm" class="modal-body" method="POST" action="/settings/password">
            @csrf
            <div class="form-group-clean">
                <label class="form-label-clean">Current Password</label>
                <input type="password" name="old_password" class="form-input-clean" required>
            </div>
            <div class="form-group-clean">
                <label class="form-label-clean">New Password</label>
                <input type="password" name="new_password" class="form-input-clean" required>
            </div>
            <div class="form-group-clean">
                <label class="form-label-clean">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-input-clean" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-clean btn-secondary-clean"  onclick="closeChangePasswordModal()">Cancel</button>
                <button type="submit" class="btn-clean btn-primary-clean" >Change Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Settings Modal (Admin Only) -->
@if(auth()->user()->role_id == 1)
<div class="modal-overlay hidden" id="settingsModal">
    <div class="modal-content settings-modal">
        <div class="modal-header">
            <h3>Site Settings</h3>
            <button class="modal-close" onclick="closeModal('settingsModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="settingsForm" class="modal-body">
            @csrf
            <div class="settings-form">
                <div class="form-group-clean">
                    <label class="form-label-clean">Primary Color</label>
                    <div class="color-picker-wrapper">
                        <input 
                            type="color" 
                            id="siteColor" 
                            class="color-picker"
                            value="#3b82f6"
                            onchange="updateSiteColor(this.value)"
                        />
                        <input 
                            type="text" 
                            id="siteColorHex" 
                            class="form-input-clean color-input"
                            value="#3b82f6"
                            placeholder="#3b82f6"
                            onchange="updateSiteColorFromHex(this.value)"
                        />
                    </div>
                    <small class="form-hint">Primary color for buttons and accents</small>
                </div>

                <div class="form-group-clean">
                    <label class="form-label-clean">Font Color</label>
                    <div class="color-picker-wrapper">
                        <input 
                            type="color" 
                            id="fontColor" 
                            class="color-picker"
                            value="#111827"
                            onchange="updateFontColor(this.value)"
                        />
                        <input 
                            type="text" 
                            id="fontColorHex" 
                            class="form-input-clean color-input"
                            value="#111827"
                            placeholder="#111827"
                            onchange="updateFontColorFromHex(this.value)"
                        />
                    </div>
                    <small class="form-hint">Main text color</small>
                </div>

                <div class="form-group-clean">
                    <label class="form-label-clean">Button Color</label>
                    <div class="color-picker-wrapper">
                        <input 
                            type="color" 
                            id="buttonColor" 
                            class="color-picker"
                            value="#6b7280"
                            onchange="updateButtonColor(this.value)"
                        />
                        <input 
                            type="text" 
                            id="buttonColorHex" 
                            class="form-input-clean color-input"
                            value="#6b7280"
                            placeholder="#6b7280"
                            onchange="updateButtonColorFromHex(this.value)"
                        />
                    </div>
                    <small class="form-hint">Background color for secondary buttons</small>
                </div>

                <div class="form-group-clean">
                    <label class="form-label-clean">Button Text Color</label>
                    <div class="color-picker-wrapper">
                        <input 
                            type="color" 
                            id="buttonTextColor" 
                            class="color-picker"
                            value="#ffffff"
                            onchange="updateButtonTextColor(this.value)"
                        />
                        <input 
                            type="text" 
                            id="buttonTextColorHex" 
                            class="form-input-clean color-input"
                            value="#ffffff"
                            placeholder="#ffffff"
                            onchange="updateButtonTextColorFromHex(this.value)"
                        />
                    </div>
                    <small class="form-hint">Text color for secondary buttons</small>
                </div>

                
                <div class="form-group-clean">
                    <label class="form-label-clean">Font Family</label>
                    <select id="siteFont" class="form-input-clean" onchange="updateSiteFont(this.value)">
                        <option value="Poppins">Poppins (Default)</option>
                        <option value="Inter">Inter</option>
                        <option value="Roboto">Roboto</option>
                        <option value="Open Sans">Open Sans</option>
                        <option value="Lato">Lato</option>
                        <option value="Montserrat">Montserrat</option>
                    </select>
                    <small class="form-hint">Font family for the entire application</small>
                </div>
                
                <div class="preview-section">
                    <h4>Preview</h4>
                    <div class="preview-content">
                        <div class="preview-buttons" style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                            <button type="button" class="preview-button-primary" style="background: var(--primary-color, #3b82f6); font-family: var(--site-font, 'Poppins'); color: white; padding: 0.5rem 1rem; border: none; border-radius: 6px;">
                                Primary Button
                            </button>
                            <button type="button" class="preview-button-secondary" style="background: var(--button-color, #6b7280); font-family: var(--site-font, 'Poppins'); color: var(--button-text-color, #ffffff); padding: 0.5rem 1rem; border: none; border-radius: 6px;">
                                Secondary Button
                            </button>
                        </div>
                        <p style="font-family: var(--site-font, 'Poppins'); color: var(--font-color, #111827);">
                            This is how your text will look with the selected font and color.
                        </p>
                        <div class="preview-sidebar" style="background: var(--sidebar-bg-color, #ffffff); border: 1px solid #e5e7eb; padding: 1rem; border-radius: 8px; margin-top: 0.5rem;">
                            <div class="preview-sidebar-header" style="background: var(--sidebar-header-color, #f8fafc); padding: 0.5rem; margin: -1rem -1rem 0.5rem -1rem; border-radius: 8px 8px 0 0;">
                                <span style="font-family: var(--site-font, 'Poppins'); color: var(--font-color, #111827); font-weight: 600;">Sidebar Preview</span>
                            </div>
                            <p style="font-family: var(--site-font, 'Poppins'); color: var(--font-color, #111827); margin: 0; font-size: 0.875rem;">Sample sidebar content</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-clean btn-secondary-clean" onclick="window.location.href='/settings'">
                    More Settings
                </button>
                <button type="button" class="btn-clean btn-secondary-clean" onclick="resetSettings()">
                    Reset to Default
                </button>
                <button type="submit" class="btn-clean btn-primary-clean">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<style>
.topbar-clean {
    background: var(--white);
    border-bottom: 1px solid var(--gray-200);
    padding: var(--space-3) var(--space-6);
    position: sticky;
    top: 0;
    z-index: 100;
    font-family: var(--font-family);
}

.topbar-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 100%;
}

/* Left Section */
.topbar-left {
    display: flex;
    align-items: center;
    gap: var(--space-4);
    flex: 1;
}

.mobile-menu-toggle {
    display: none;
    width: 40px;
    height: 40px;
    border: none;
    background: var(--gray-100);
    border-radius: var(--radius);
    color: var(--gray-600);
    cursor: pointer;
    transition: var(--transition);
}

.mobile-menu-toggle:hover {
    background: var(--gray-200);
    color: var(--gray-800);
}

.page-title h1 {
    font-size: var(--font-size-2xl);
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
    line-height: 1.2;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    margin-top: var(--space-1);
}

.breadcrumb-item {
    font-size: var(--font-size-sm);
    color: var(--gray-500);
    font-weight: 500;
}

.breadcrumb-item.current {
    color: var(--primary);
}

.breadcrumb-separator {
    font-size: var(--font-size-xs);
    color: var(--gray-400);
}

/* Center Section (Search) */
.topbar-center {
    flex: 1;
    max-width: 400px;
    margin: 0 var(--space-6);
}

.search-box {
    position: relative;
}

.search-input {
    width: 100%;
    padding: var(--space-3) var(--space-3) var(--space-3) var(--space-10);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-lg);
    font-size: var(--font-size-sm);
    background: var(--gray-50);
    transition: var(--transition);
}

.search-input:focus {
    outline: none;
    border-color: var(--primary);
    background: var(--white);
    box-shadow: 0 0 0 3px var(--primary-light);
}

.search-icon {
    position: absolute;
    left: var(--space-3);
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
    font-size: var(--font-size-sm);
}

/* Right Section */
.topbar-right {
    display: flex;
    align-items: center;
    gap: var(--space-3);
}

.topbar-item {
    position: relative;
}

.topbar-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: var(--gray-100);
    border-radius: var(--radius);
    color: var(--gray-600);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    position: relative;
}

.topbar-btn:hover {
    background: var(--gray-200);
    color: var(--gray-800);
}

.notification-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: var(--danger);
    color: var(--white);
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* User Profile */
.user-profile-btn {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    padding: var(--space-2);
    border: none;
    background: var(--gray-50);
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: var(--transition);
}

.user-profile-btn:hover {
    background: var(--gray-100);
}

.user-avatar {
    width: 36px;
    height: 36px;
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
    text-align: left;
}

.user-name {
    font-size: var(--font-size-sm);
    font-weight: 600;
    color: var(--gray-900);
    line-height: 1.2;
}

.user-role {
    font-size: var(--font-size-xs);
    color: var(--gray-500);
    font-weight: 500;
}

.user-dropdown-arrow {
    font-size: var(--font-size-xs);
    color: var(--gray-400);
    transition: var(--transition);
}

.user-profile-btn:hover .user-dropdown-arrow {
    color: var(--gray-600);
}

/* Dropdowns */
.notifications-dropdown,
.user-dropdown {
    position: absolute;
    top: calc(100% + var(--space-2));
    right: 0;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    z-index: 1000;
    min-width: 320px;
    max-height: 400px;
    overflow: hidden;
}

.user-dropdown {
    min-width: 240px;
}

.dropdown-header {
    padding: var(--space-4);
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dropdown-header h4 {
    font-size: var(--font-size-base);
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.mark-all-read {
    font-size: var(--font-size-xs);
    color: var(--primary);
    background: none;
    border: none;
    cursor: pointer;
    font-weight: 500;
}

.notifications-list {
    max-height: 300px;
    overflow-y: auto;
}

.notification-item {
    display: flex;
    gap: var(--space-3);
    padding: var(--space-4);
    border-bottom: 1px solid var(--gray-100);
    transition: var(--transition);
}

.notification-item:hover {
    background: var(--gray-50);
}

.notification-item.unread {
    background: var(--primary-light);
}

.notification-icon {
    width: 32px;
    height: 32px;
    background: var(--primary-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    flex-shrink: 0;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-size: var(--font-size-sm);
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: var(--space-1);
}

.notification-message {
    font-size: var(--font-size-sm);
    color: var(--gray-600);
    margin-bottom: var(--space-1);
}

.notification-time {
    font-size: var(--font-size-xs);
    color: var(--gray-500);
}

.no-notifications {
    text-align: center;
    padding: var(--space-8);
    color: var(--gray-500);
}

.no-notifications i {
    font-size: var(--font-size-2xl);
    margin-bottom: var(--space-2);
    display: block;
}

.dropdown-footer {
    padding: var(--space-3);
    border-top: 1px solid var(--gray-200);
    text-align: center;
}

.view-all-btn {
    color: var(--primary);
    text-decoration: none;
    font-size: var(--font-size-sm);
    font-weight: 500;
}

.view-all-btn:hover {
    text-decoration: underline;
}

/* User Dropdown Specific */
.user-avatar-large {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: var(--space-3);
}

.user-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-details {
    flex: 1;
}

.user-details .user-name {
    font-size: var(--font-size-base);
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: var(--space-1);
}

.user-details .user-email {
    font-size: var(--font-size-sm);
    color: var(--gray-500);
}

.dropdown-menu {
    padding: var(--space-2) 0;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    padding: var(--space-3) var(--space-4);
    color: var(--gray-700);
    text-decoration: none;
    transition: var(--transition);
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    font-size: var(--font-size-sm);
}

.dropdown-item:hover {
    background: var(--gray-50);
    color: var(--gray-900);
    text-decoration: none;
}

.dropdown-item i {
    width: 16px;
    color: var(--gray-500);
}

.dropdown-divider {
    height: 1px;
    background: var(--gray-200);
    margin: var(--space-2) 0;
}

.logout-form {
    margin: 0;
}

.logout-item {
    color: var(--danger);
}

.logout-item:hover {
    background: var(--danger-light);
    color: var(--danger);
}

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    backdrop-filter: blur(4px);
}

.modal-content {
    background: var(--white);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    width: 90%;
    max-width: 400px;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.settings-modal {
    max-width: 500px;
}

.settings-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.color-picker-wrapper {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.color-picker {
    width: 60px;
    height: 40px;
    border: 2px solid var(--gray-300);
    border-radius: var(--radius);
    cursor: pointer;
    background: none;
}

.color-input {
    flex: 1;
}

.form-hint {
    color: var(--gray-500);
    font-size: var(--font-size-xs);
    margin-top: 0.25rem;
}

.preview-section {
    padding: 1rem;
    background: var(--gray-50);
    border-radius: var(--radius);
    border: 1px solid var(--gray-200);
}

.preview-section h4 {
    margin: 0 0 1rem 0;
    font-size: var(--font-size-base);
    font-weight: 600;
    color: var(--gray-900);
}

.preview-content {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.preview-button {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: var(--radius);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.preview-button:hover {
    opacity: 0.9;
}

/* CSS Variables for Dynamic Theming */
:root {
    --primary-color: #3b82f6;
    --site-font: 'Poppins', sans-serif;
    --font-color: #111827;
    --button-color: #6b7280;
    --button-text-color: #ffffff;
    --sidebar-bg-color: #ffffff;
    --sidebar-header-color: #f8fafc;
    --primary-bg-light: rgba(59, 130, 246, 0.1);
}

/* Apply dynamic theming */
.btn-primary-clean,
.topbar-btn:hover {
    background: var(--primary-color) !important;
}

body, 
.topbar-clean,
.form-label-clean,
.page-title h1 {
    font-family: var(--site-font) !important;
}

/* Apply font color globally */
body,
.page-title,
.page-subtitle,
.breeds-page,
.species-page,
.rule-base-page,
.breeds-table,
.species-table,
.questions-table,
.breed-name,
.species-name,
.question-text,
.clinic-name,
.actions-group,
h1, h2, h3, h4, h5, h6,
p, span, div,
.form-label,
.setting-label,
.sidebar-title,
.modal-title,
.topbar-clean .page-title h1,
.topbar-clean .breadcrumb-item {
    color: var(--font-color) !important;
}

/* Ensure table text uses font color */
.breeds-table th,
.breeds-table td,
.species-table th,
.species-table td,
.questions-table th,
.questions-table td {
    color: var(--font-color) !important;
}

/* Page headers */
.header-content .page-title,
.header-content .page-subtitle {
    color: var(--font-color) !important;
}

/* Additional elements that need font color */
.notification-item,
.notification-title,
.notification-message,
.user-name,
.user-email,
.dropdown-item,
.nav-text,
.nav-subtext,
.brand-text h1,
.species-info,
.question-id,
.response-text,
.response-desc,
.response-link,
.search-input::placeholder,
.form-input::placeholder {
    color: var(--font-color) !important;
}

/* Input text color */
.form-input,
.search-input,
.color-text,
.setting-input,
.setting-select,
input[type="text"],
input[type="email"],
input[type="tel"],
input[type="time"],
textarea,
select {
    color: var(--font-color) !important;
}

/* Control elements that use font color for text */
.topbar-btn,
.sidebar-toggle,
.modal-close,
.sidebar-close,
.logout-btn,
.dropdown-item,
.nav-arrow,
.user-dropdown-arrow {
    color: var(--font-color) !important;
}

/* Tab buttons use button color as background when active */
.tab-button:hover,
.tab-button.active {
    background-color: var(--button-color) !important;
    color: var(--button-text-color) !important;
}

/* Secondary buttons and links that are not UI components */
.btn-secondary,
.btn-outline,
.cancel-btn,
.reset-btn,
.filter-btn,
.search-btn,
.close-btn {
    background-color: var(--button-color) !important;
    color: var(--button-text-color) !important;
    border-color: var(--button-color) !important;
}

/* UI Button Component styles - secondary buttons use button color as background */
.btn-secondary-clean {
    background-color: var(--button-color) !important;
    color: var(--button-text-color) !important;
    border-color: var(--button-color) !important;
}

.btn-secondary-clean:hover {
    background-color: var(--button-color) !important;
    opacity: 0.9;
    color: var(--button-text-color) !important;
}

/* Action buttons in tables and modals - only secondary buttons */
.actions-group .btn-secondary-clean,
.modal-actions .btn-secondary-clean,
.sidebar-footer .btn-secondary-clean {
    background-color: var(--button-color) !important;
    color: var(--button-text-color) !important;
    border-color: var(--button-color) !important;
}

/* Keep semantic colors for success/danger buttons */
.btn-success-clean {
    color: white !important;
}

.btn-danger-clean {
    color: white !important;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-6);
    border-bottom: 1px solid var(--gray-200);
    flex-shrink: 0;
}

.modal-header h3 {
    font-size: var(--font-size-lg);
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.modal-close {
    width: 32px;
    height: 32px;
    border: none;
    background: var(--gray-100);
    border-radius: var(--radius);
    color: var(--gray-600);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
}

.modal-close:hover {
    background: var(--gray-200);
    color: var(--gray-800);
}

.modal-body {
    flex: 1;
    overflow-y: auto;
    padding: var(--space-6);
}

.modal-footer {
    display: flex;
    gap: var(--space-3);
    justify-content: flex-end;
    padding: var(--space-6);
    border-top: 1px solid var(--gray-200);
    flex-shrink: 0;
}

/* Utilities */
.hidden {
    display: none !important;
}

/* Responsive */
@media (max-width: 768px) {
    .mobile-menu-toggle {
        display: flex;
    }
    
    .topbar-center {
        display: none;
    }
    
    .user-info {
        display: none;
    }
    
    .user-dropdown-arrow {
        display: none;
    }
    
    .page-title h1 {
        font-size: var(--font-size-xl);
    }
    
    .breadcrumb {
        display: none;
    }
    
    .notifications-dropdown,
    .user-dropdown {
        right: var(--space-4);
        left: var(--space-4);
        min-width: auto;
    }
}
</style>

<script>
// Global state
let activeDropdown = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (activeDropdown && !activeDropdown.contains(e.target) && !e.target.closest('.topbar-btn, .user-profile-btn')) {
            closeAllDropdowns();
        }
    });
    
    // Handle escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllDropdowns();
            closeChangePasswordModal();
            closeModal('settingsModal');
        }
    });
    
    // Load saved settings
    loadSavedSettings();
    
    // Settings form submission
    const settingsForm = document.getElementById('settingsForm');
    if (settingsForm) {
        settingsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const color = document.getElementById('siteColor').value;
            const font = document.getElementById('siteFont').value;
            
            // Save settings
            updateSiteColor(color);
            updateSiteFont(font);
            
            // Close modal
            closeModal('settingsModal');
            
            // Show success message
            alert('Settings saved successfully!');
        });
    }
});

// Dropdown functions
function toggleNotifications() {
    const dropdown = document.getElementById('notificationsDropdown');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userDropdown && !userDropdown.classList.contains('hidden')) {
        userDropdown.classList.add('hidden');
    }
    
    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        activeDropdown = dropdown;
        markNotificationsAsRead();
    } else {
        dropdown.classList.add('hidden');
        activeDropdown = null;
    }
}

function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    const notificationsDropdown = document.getElementById('notificationsDropdown');
    
    if (notificationsDropdown && !notificationsDropdown.classList.contains('hidden')) {
        notificationsDropdown.classList.add('hidden');
    }
    
    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        activeDropdown = dropdown;
    } else {
        dropdown.classList.add('hidden');
        activeDropdown = null;
    }
}

function closeAllDropdowns() {
    const dropdowns = document.querySelectorAll('.notifications-dropdown, .user-dropdown');
    dropdowns.forEach(dropdown => {
        dropdown.classList.add('hidden');
    });
    activeDropdown = null;
}

function toggleSettings() {
    openModal('settingsModal');
}

// Settings Functions
function updateSiteColor(color) {
    document.documentElement.style.setProperty('--primary-color', color);
    
    // Convert hex to rgba for light background
    const rgb = hexToRgb(color);
    if (rgb) {
        const lightBg = `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.1)`;
        document.documentElement.style.setProperty('--primary-bg-light', lightBg);
    }
    
    const hexInput = document.getElementById('siteColorHex');
    if (hexInput) hexInput.value = color;
    
    // Update preview buttons
    const previewButtonPrimary = document.querySelector('.preview-button-primary');
    if (previewButtonPrimary) {
        previewButtonPrimary.style.background = color;
    }
    
    // Save to localStorage
    localStorage.setItem('siteColor', color);
}

// Helper function to convert hex to RGB
function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function updateSiteColorFromHex(color) {
    if (color.match(/^#[0-9A-F]{6}$/i)) {
        const colorInput = document.getElementById('siteColor');
        if (colorInput) colorInput.value = color;
        updateSiteColor(color);
    }
}

function updateSiteFont(font) {
    document.documentElement.style.setProperty('--site-font', `'${font}', sans-serif`);
    
    // Update preview text
    const previewElements = document.querySelectorAll('.preview-content *');
    previewElements.forEach(el => {
        el.style.fontFamily = `'${font}', sans-serif`;
    });
    
    // Save to localStorage
    localStorage.setItem('siteFont', font);
}

// Font Color Functions
function updateFontColor(color) {
    document.documentElement.style.setProperty('--font-color', color);
    const hexInput = document.getElementById('fontColorHex');
    if (hexInput) hexInput.value = color;
    localStorage.setItem('fontColor', color);
}

function updateFontColorFromHex(color) {
    if (color.match(/^#[0-9A-F]{6}$/i)) {
        const colorInput = document.getElementById('fontColor');
        if (colorInput) colorInput.value = color;
        updateFontColor(color);
    }
}

// Button Color Functions
function updateButtonColor(color) {
    document.documentElement.style.setProperty('--button-color', color);
    const hexInput = document.getElementById('buttonColorHex');
    if (hexInput) hexInput.value = color;
    
    // Update preview button
    const previewButtonSecondary = document.querySelector('.preview-button-secondary');
    if (previewButtonSecondary) {
        previewButtonSecondary.style.background = color;
    }
    
    localStorage.setItem('buttonColor', color);
}

function updateButtonColorFromHex(color) {
    if (color.match(/^#[0-9A-F]{6}$/i)) {
        const colorInput = document.getElementById('buttonColor');
        if (colorInput) colorInput.value = color;
        updateButtonColor(color);
    }
}

// Button Text Color Functions
function updateButtonTextColor(color) {
    document.documentElement.style.setProperty('--button-text-color', color);
    const hexInput = document.getElementById('buttonTextColorHex');
    if (hexInput) hexInput.value = color;
    
    // Update preview button
    const previewButtonSecondary = document.querySelector('.preview-button-secondary');
    if (previewButtonSecondary) {
        previewButtonSecondary.style.color = color;
    }
    
    localStorage.setItem('buttonTextColor', color);
}

function updateButtonTextColorFromHex(color) {
    if (color.match(/^#[0-9A-F]{6}$/i)) {
        const colorInput = document.getElementById('buttonTextColor');
        if (colorInput) colorInput.value = color;
        updateButtonTextColor(color);
    }
}


function resetSettings() {
    updateSiteColor('#3b82f6');
    updateSiteFont('Poppins');
    updateFontColor('#111827');
    updateButtonColor('#6b7280');
    updateButtonTextColor('#ffffff');
    
    // Reset CSS variables to defaults
    document.documentElement.style.setProperty('--primary-bg-light', 'rgba(59, 130, 246, 0.1)');
    
    // Update form inputs
    const inputs = [
        { id: 'siteColor', value: '#3b82f6' },
        { id: 'siteColorHex', value: '#3b82f6' },
        { id: 'siteFont', value: 'Poppins' },
        { id: 'fontColor', value: '#111827' },
        { id: 'fontColorHex', value: '#111827' },
        { id: 'buttonColor', value: '#6b7280' },
        { id: 'buttonColorHex', value: '#6b7280' },
        { id: 'buttonTextColor', value: '#ffffff' },
        { id: 'buttonTextColorHex', value: '#ffffff' }
    ];
    
    inputs.forEach(input => {
        const element = document.getElementById(input.id);
        if (element) element.value = input.value;
    });
}

// Load saved settings on page load
function loadSavedSettings() {
    const settings = [
        { key: 'siteColor', updateFunc: updateSiteColor, inputIds: ['siteColor', 'siteColorHex'], default: '#3b82f6' },
        { key: 'siteFont', updateFunc: updateSiteFont, inputIds: ['siteFont'], default: 'Poppins' },
        { key: 'fontColor', updateFunc: updateFontColor, inputIds: ['fontColor', 'fontColorHex'], default: '#111827' },
        { key: 'buttonColor', updateFunc: updateButtonColor, inputIds: ['buttonColor', 'buttonColorHex'], default: '#6b7280' },
        { key: 'buttonTextColor', updateFunc: updateButtonTextColor, inputIds: ['buttonTextColor', 'buttonTextColorHex'], default: '#ffffff' }
    ];
    
    settings.forEach(setting => {
        const savedValue = localStorage.getItem(setting.key);
        if (savedValue) {
            setting.updateFunc(savedValue);
            setting.inputIds.forEach(inputId => {
                const element = document.getElementById(inputId);
                if (element) element.value = savedValue;
            });
        }
    });
}

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        closeAllDropdowns();
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Modal functions
function openChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.remove('hidden');
    closeAllDropdowns();
}

function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.add('hidden');
}

// Notification functions
async function markNotificationsAsRead() {
    try {
        const userId = {{ auth()->user()->id }};
        await fetch(`/api/notifications/read/${userId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        // Update UI
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            badge.style.display = 'none';
        }
        
        const unreadItems = document.querySelectorAll('.notification-item.unread');
        unreadItems.forEach(item => {
            item.classList.remove('unread');
        });
    } catch (error) {
        console.error('Error marking notifications as read:', error);
    }
}

async function markAllAsRead() {
    await markNotificationsAsRead();
    location.reload(); // Refresh to update the UI
}

// Mobile sidebar toggle
function toggleMobileSidebar() {
    const sidebar = document.querySelector('.sidebar-clean');
    if (sidebar) {
        sidebar.classList.toggle('mobile-open');
    }
}

// Form handling
document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const newPassword = formData.get('new_password');
    const confirmPassword = formData.get('confirm_password');
    
    if (newPassword !== confirmPassword) {
        alert('New passwords do not match');
        return;
    }
    
    try {
        const response = await fetch('/settings/password', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            alert('Password changed successfully');
            closeChangePasswordModal();
            this.reset();
        } else {
            const data = await response.json();
            alert(data.message || 'Error changing password');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error changing password');
    }
});
</script>
