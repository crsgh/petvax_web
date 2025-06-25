@php
// Menu items array remains the same


$menuItems = [
    [
        'id' => 'dashboard',
        'label' => 'Dashboard',
        'color' => 'text-blue-500',
        'route' => '/dashboard', 
        'permission' => [1,2],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v6m8-6v6"></path></svg>'
    ],
    [
        'id' => 'clinics',
        'label' => 'Clinics',
        'color' => 'text-green-500',
        'route' => '/clinics',
        'permission' => [1,2],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>'
    ],
    [
        'id' => 'users',
        'label' => 'Users',
        'color' => 'text-purple-500',
        'hasSubmenu' => true,
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>',
        'submenu' => [
            [
                'id' => 'staffs',
                'label' => 'Staffs',
                'route' => '/staffs',
                'permission' => [1,2],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>'
            ],
            [
                'id' => 'owners',
                'label' => 'Pet Owners',
                'route' => '/owners',
                'permission' => [1,2,3],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>'
            ]
            
        ]
    ],
    [
        'id' => 'pets',
        'label' => 'Pets',
        'color' => 'text-pink-500',
        'hasSubmenu' => true,
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>',
        'submenu' => [
            [
                'id' => 'all-pets', 
                'label' => 'All Pets', 
                'route' => '/pets',
                'permission' => [1,2,3,4],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L9.5 7.5v2.5m4.5 0V10h-4m0 0L7 9m0 0v6a2 2 0 01-2 2H3m2-8h2m0 0h2v4H5v-4z"></path></svg>'
            ],
            [
                'id' => 'breeds', 
                'label' => 'Breeds', 
                'route' => '/breeds',
                'permission' => [1,2,3],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>'
            ],
            [
                'id' => 'species', 
                'label' => 'Species', 
                'route' => '/species',
                'permission' => [1,2,3],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>'
            ],
        ]
    ],
    [
        'id' => 'services',
        'label' => 'Services',
        'color' => 'text-teal-500',
        'hasSubmenu' => true,
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>',
        'submenu' => [
            [
                'id' => 'all-services',
                'label' => 'All Services',
                'route' => '/services',
                'permission' => [1,2],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>'
            ],
            [
                'id' => 'services-schedule',
                'label' => 'Services Schedule',
                'route' => '/services-schedule',
                'permission' => [1,2,3],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            ]
        ]
    ],
    [
        'id' => 'bookings',
        'label' => 'Bookings',
        'color' => 'text-orange-500',
        'route' => '/bookings',
        'permission' => [1,2,3,4],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>'
    ],
    [
        'id' => 'sales-report',
        'label' => 'Report',
        'color' => 'text-cyan-500',
        'route' => '/sales-report',
        'permission' => [1,2],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17v2a2 2 0 002 2h14a2 2 0 002-2v-2M16 11V7a4 4 0 00-8 0v4M5 17h14"></path></svg>'
    ],
    [
        'id' => 'medical-histories',
        'label' => 'Medical Histories',
        'color' => 'text-indigo-500',
        'route' => '/medical-histories',
        'permission' => [1,2,3,4],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
    ],
    [
        'id' => 'inventory',
        'label' => 'Inventory',
        'color' => 'text-amber-500',
        'hasSubmenu' => true,
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>',
        'submenu' => [
            [
                'id' => 'all-inventory',
                'label' => 'All Inventory',
                'route' => '/inventory',
                'permission' => [1,2],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>'
            ],
            [
                'id' => 'inventory-categories',
                'label' => 'Categories',
                'route' => '/categories',
                'permission' => [1,2],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>'
            ]
        ]
    ],
    [
        'id' => 'clinic-rating',
        'label' => 'Clinic Rating',
        'color' => 'text-yellow-500',
        'route' => '/clinic-ratings',
        'permission' => [1,2],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>'
    ],
    [
        'id' => 'activity-records',
        'label' => 'Activity Records',
        'color' => 'text-red-500',
        'route' => '/activity-records',
        'permission' => [1,2],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>'
    ],


];

Route::currentRouteName() === 'notifications' ? array_push($menuItems, [
        'id' => 'notifications',
        'label' => 'Notifications',
        'color' => 'text-blue-500', 
        'route' => '/notifications',
        'permission' => [1,2,3,4],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>'
    ]) : null;

$currentRoute = request()->route() ? request()->route()->getName() : '';
$activeItem = 'dashboard';
$activeSubItem = null;

foreach($menuItems as $item) {
    // Check if current path matches the main item route
    if(isset($item['route']) && trim($currentRoute, '/') === trim($item['route'], '/')) {
        $activeItem = $item['id'];
        break;
    }
    
    // Check submenu routes if they exist
    if(isset($item['submenu'])) {
        foreach($item['submenu'] as $subItem) {
            $subRoute = trim($subItem['route'], '/');
            
            // Check if current path matches the submenu route
            if(trim($currentRoute, '/') === $subRoute || str_starts_with(trim($currentRoute, '/'), $subRoute.'/')) {
                $activeItem = $item['id'];
                $activeSubItem = $subItem['id'];
                break 2;
            }
        }
    }
}
// Store active states in session
session(['activeItem' => $activeItem]);
session(['activeSubItem' => $activeSubItem]);

if ($currentRoute) {
    $foundItem = collect($menuItems)->first(function ($item) use ($currentRoute) {
        if (isset($item['route']) && trim($currentRoute, '/') === trim($item['route'], '/')) {
            return true;
        }
        if (isset($item['submenu'])) {
            return collect($item['submenu'])->contains(function ($subItem) use ($currentRoute) {
                return trim($currentRoute, '/') === trim($subItem['route'], '/');
            });
        }
        return false;
    });
    
    if ($foundItem) {
        $activeItem = $foundItem['id'];
        
        // Check if it's a submenu item
        if (isset($foundItem['submenu'])) {
            $activeSubItem = collect($foundItem['submenu'])
                ->first(function ($subItem) use ($currentRoute) {
                    return trim($currentRoute, '/') === trim($subItem['route'], '/');
                })['id'] ?? null;
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


 
  <!-- Sidebar -->
  <div id="sidebar" class="bg-white shadow-xl transition-all duration-300 ease-in-out w-72 flex flex-col border-r border-gray-200">
      <!-- Header Section -->
      <div class="p-3 border-b border-gray-100">
          <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3" id="sidebar-header">
                  <div class="w-12 h-12 rounded-xl flex items-center justify-center">
                      <img src="{{ asset('assets/img/logo.png') }}" alt="PetVax Logo" class="w-full h-full object-contain">
                  </div>
                  <div id="sidebar-brand">
                      <h1 class="text-xl font-extrabold text-gray-900">PetVax</h1>
                      <p class="text-sm font-medium text-gray-600">{{ ucfirst(\App\Models\Role::find(auth()->user()->role_id)->name ?? 'User') }}</p>
                  </div>
              </div>
              <button id="sidebar-toggle" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                  <svg id="menu-icon" class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
              </button>
          </div>
      </div>

      <!-- Main Navigation Section -->
      <nav class="flex-1 px-3 py-2 overflow-y-auto">
          <!-- Primary Menu Section -->
          <div class="mt-2">
              <h2 class="px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Main Menu</h2>
              <div class="px-3 my-2">
                  <div class="border-b border-gray-200 w-full"></div>
              </div>
              <ul class="space-y-1">
                  @foreach($menuItems as $item)
                      @if(!isset($item['section']) || $item['section'] === 'main')
                          @php
                              $isActive = $activeItem === $item['id'];
                              $hasSubmenu = $item['hasSubmenu'] ?? false;
                              $isExpanded = isset($expandedMenus[$item['id']]) && $expandedMenus[$item['id']];
                              
                              // Check permissions for main menu items
                              $hasPermission = false;
                              if ($hasSubmenu) {
                                  // For items with submenu, check if user has permission for any submenu item
                                  foreach ($item['submenu'] as $subItem) {
                                      if (in_array(auth()->user()->role_id, $subItem['permission'])) {
                                          $hasPermission = true;
                                          break;
                                      }
                                  }
                              } else {
                                  // For regular items, check direct permission
                                  $hasPermission = in_array(auth()->user()->role_id, $item['permission'] ?? []);
                              }
                          @endphp
                          
                          @if($hasPermission)
                          <li>
                              <button 
                                  @if($hasSubmenu)
                                      onclick="toggleSubmenu('{{ $item['id'] }}')"
                                  @else
                                      onclick="window.location.href='{{ $item['route'] }}'"
                                  @endif
                                  class="w-full flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{  !$hasSubmenu && $isActive ? 'bg-gradient-to-r from-blue-50 to-purple-50 border-2 border-blue-400 shadow-sm' : 'hover:bg-blue-100 hover:shadow-sm' }}"
                              >
                                  <div class="w-9 h-9 {{ $isActive ? $item['color'] : 'text-gray-600 group-hover:' . $item['color'] }} transition-colors">
                                      {!! str_replace('stroke-width="2"', 'stroke-width="2.5"', $item['svg']) !!}
                                  </div>
                                  
                                  <span class="sidebar-text ml-3 font-semibold transition-colors {{ $isActive ? 'text-gray-900' : 'text-gray-700 group-hover:text-gray-900' }}">
                                      {{ $item['label'] }}
                                  </span>
                                  
                                  @if($hasSubmenu)
                                      <svg class="sidebar-text ml-auto w-6 h-6 transition-all duration-200 submenu-chevron {{ $isActive ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500' }} {{ $isExpanded ? 'rotate-180' : '' }}" 
                                           fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" data-menu="{{ $item['id'] }}">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                      </svg>
                                  @else
                                      <svg class="sidebar-text ml-auto w-6 h-6 transition-all duration-200 {{ $isActive ? 'text-gray-500 rotate-90' : 'text-gray-400 group-hover:text-gray-500' }}" 
                                           fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                      </svg>
                                  @endif
                              </button>
                              
                              @if($hasSubmenu)
                                  <div class="sidebar-text overflow-hidden transition-all duration-300 ease-in-out {{ $isExpanded ? 'max-h-48' : 'max-h-0' }} submenu" id="submenu-{{ $item['id'] }}">
                                      <ul class="ml-6 space-y-1 mt-1">
                                          @foreach($item['submenu'] as $subItem)
                                              @if(in_array(auth()->user()->role_id, $subItem['permission']))
                                                  @php
                                                      $isSubActive = $activeSubItem === $subItem['id'];
                                                  @endphp
                                                  <li>
                                                      <a href="{{ $subItem['route'] }}"
                                                         class="w-full flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ $isSubActive ? 'bg-white border-2 border-pink-400 shadow-sm text-gray-900' : 'hover:bg-blue-100 text-gray-700' }}">
                                                          <div class="w-7 h-7 {{ $isSubActive ? 'text-pink-600' : 'text-gray-500 group-hover:text-pink-600' }} transition-colors">
                                                              {!! str_replace('stroke-width="2"', 'stroke-width="2.5"', $subItem['svg']) !!}
                                                          </div>
                                                          <span class="ml-3 text-sm font-semibold transition-colors {{ $isSubActive ? 'text-gray-900' : 'text-gray-700 group-hover:text-gray-900' }}">
                                                              {{ $subItem['label'] }}
                                                          </span>
                                                      </a>
                                                  </li>
                                              @endif
                                          @endforeach
                                      </ul>
                                  </div>
                              @endif
                          </li>
                          @endif
                      @endif
                  @endforeach
              </ul>
          </div>
      </nav>

      <!-- Footer Section -->
      <div class="p-3 border-t border-gray-100">
          <div class="flex items-center space-x-3" id="sidebar-footer">
              <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-blue-600 rounded-full flex items-center justify-center">
                  <span class="text-white text-sm font-bold">
                      {{ substr(auth()->user()->name ?? 'Admin User', 0, 1) }}
                  </span>
              </div>
              <div class="flex-1 sidebar-text">
                  <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Admin User' }}</p>
                  <p class="text-xs font-medium text-gray-600">{{ auth()->user()->email ?? 'admin@vetcare.com' }}</p>
              </div>
          </div>
      </div>
  </div>
 
               
  <!-- Main Content Area -->
  


<script>
let isCollapsed = false;
let expandedMenus = @json($expandedMenus);

document.addEventListener('DOMContentLoaded', function() {
  const activeItem = '{{ $activeItem }}';
  if (activeItem && expandedMenus[activeItem]) {
      const submenu = document.getElementById('submenu-' + activeItem);
      if (submenu) {
          submenu.classList.remove('max-h-0');
          submenu.classList.add('max-h-48');
      }
  }
});

function toggleSubmenu(itemId) {
  if (isCollapsed) return;
  
  const submenu = document.getElementById('submenu-' + itemId);
  const chevron = document.querySelector(`[data-menu="${itemId}"]`);
  
  if (!expandedMenus[itemId]) {
      expandedMenus[itemId] = true;
      submenu.classList.remove('max-h-0');
      submenu.classList.add('max-h-48');
      chevron.style.transform = 'rotate(180deg)';
  } else {
      expandedMenus[itemId] = false;
      submenu.classList.remove('max-h-48');
      submenu.classList.add('max-h-0');
      chevron.style.transform = 'rotate(0deg)';
  }
}
</script>

<style>
.submenu {
  transition: max-height 0.3s ease-in-out;
}

.submenu-chevron {
  transition: transform 0.2s ease-in-out;
}

nav::-webkit-scrollbar {
  width: 4px;
}

nav::-webkit-scrollbar-track {
  background: transparent;
}

nav::-webkit-scrollbar-thumb {
  background: #e5e7eb;
  border-radius: 2px;
}

nav::-webkit-scrollbar-thumb:hover {
  background: #d1d5db;
}
</style>