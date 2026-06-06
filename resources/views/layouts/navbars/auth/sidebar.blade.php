@php
// Menu items array remains the same


$menuItems = [
    [
        'id' => 'clinics',
        'label' => 'All Clinics',
        'color' => 'text-blue-500',
        'route' => '/owner', 
        'permission' => [5],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M3 21H21"/><path d="M5 21V7L12 3L19 7V21"/><path d="M9 21V17H15V21"/><path d="M9 9H11"/><path d="M13 9H15"/><path d="M9 13H11"/><path d="M13 13H15"/></svg>',
        
    ],
    [
        'id' => 'dashboard',
        'label' => 'Dashboard',
        'color' => 'text-blue-500',
        'route' => '/dashboard', 
        'permission' => [1,2],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M10 3H3V13H10V3Z"/><path d="M21 3H14V8H21V3Z"/><path d="M21 12H14V21H21V12Z"/><path d="M10 17H3V21H10V17Z"/></svg>'
    ],
    [
        'id' => 'clinics',
        'label' => 'Clinics',
        'color' => 'text-green-500',
        'route' => '/clinics',
        'permission' => [1,2],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M3 21H21"/><path d="M5 21V7L12 3L19 7V21"/><path d="M9 21V17H15V21"/><path d="M9 9H11"/><path d="M13 9H15"/><path d="M9 13H11"/><path d="M13 13H15"/></svg>'
    ],
    [
        'id' => 'users',
        'label' => 'Users',
        'color' => 'text-purple-500',
        'hasSubmenu' => true,
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M17 21V19C17 16.7909 15.2091 15 13 15H5C2.79086 15 1 16.7909 1 19V21"/><path d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z"/><path d="M23 21V19C22.7353 17.4697 21.922 16.1272 20.74 15.17"/><path d="M16 3.13C17.188 4.07978 18.0026 5.4187 18.2678 6.90901C18.533 8.39933 18.2302 9.93183 17.41 11.21"/></svg>',
        'submenu' => [
            [
                'id' => 'staffs',
                'label' => 'Staffs',
                'route' => '/staffs',
                'permission' => [1,2],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M17 20H22V18C22 15.7909 20.2091 14 18 14"/><path d="M14 20H2V18C2 15.7909 3.79086 14 6 14H10C12.2091 14 14 15.7909 14 18V20Z"/><path d="M12 7C12 9.20914 10.2091 11 8 11C5.79086 11 4 9.20914 4 7C4 4.79086 5.79086 3 8 3C10.2091 3 12 4.79086 12 7Z"/><path d="M18 11C20.2091 11 22 9.20914 22 7C22 4.79086 20.2091 3 18 3"/></svg>'
            ],
            [
                'id' => 'owners',
                'label' => 'Pet Owners',
                'route' => '/owners',
                'permission' => [1,2,3],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21"/><path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z"/></svg>'
            ]
            
        ]
    ],
    [
        'id' => 'pets',
        'label' => 'Pets',
        'color' => 'text-pink-500',
        'hasSubmenu' => true,
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M12 22C7.02944 22 3 18.4183 3 14C3 13.053 3.18719 12.1441 3.53066 11.3009C3.73678 10.7952 3.83984 10.5421 3.85136 10.386C3.86289 10.2299 3.79135 9.93697 3.64826 9.35094C3.48638 8.68825 3.4236 7.96055 3.42029 7.24286C3.41215 5.48234 3.40808 4.60208 4.21032 4.17938C5.01255 3.75667 5.82239 4.30935 7.44206 5.41552C7.57926 5.50928 7.71208 5.60047 7.83857 5.68783C8.41215 6.08349 8.69893 6.28142 8.88778 6.32417C9.07663 6.36692 9.51313 6.29736 10.3861 6.15824C10.8215 6.08882 11.3595 6.03778 12 6.03778C12.6405 6.03778 13.1785 6.08882 13.6139 6.15824C14.4869 6.29736 14.9234 6.36692 15.1122 6.32417C15.3011 6.28142 15.5878 6.08349 16.1614 5.68783C16.2879 5.60047 16.4207 5.50928 16.5579 5.41552C18.1776 4.30935 18.9874 3.75667 19.7897 4.17938C20.5919 4.60208 20.5878 5.48234 20.5797 7.24286C20.5764 7.96055 20.5136 8.68825 20.3517 9.35094C20.2087 9.93697 20.1371 10.2299 20.1486 10.386C20.1602 10.5421 20.2632 10.7952 20.4693 11.3009C20.8128 12.1441 21 13.053 21 14C21 18.4183 16.9706 22 12 22Z"/><path d="M9 14.5C9 15.3284 8.32843 16 7.5 16C6.67157 16 6 15.3284 6 14.5C6 13.6716 6.67157 13 7.5 13C8.32843 13 9 13.6716 9 14.5Z"/><path d="M18 14.5C18 15.3284 17.3284 16 16.5 16C15.6716 16 15 15.3284 15 14.5C15 13.6716 15.6716 13 16.5 13C17.3284 13 18 13.6716 18 14.5Z"/></svg>',
        'submenu' => [
            [
                'id' => 'all-pets', 
                'label' => 'All Pets', 
                'route' => '/pets',
                'permission' => [1,2,3,4,5],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M12 22C7.02944 22 3 18.4183 3 14C3 13.053 3.18719 12.1441 3.53066 11.3009C3.73678 10.7952 3.83984 10.5421 3.85136 10.386C3.86289 10.2299 3.79135 9.93697 3.64826 9.35094C3.48638 8.68825 3.4236 7.96055 3.42029 7.24286C3.41215 5.48234 3.40808 4.60208 4.21032 4.17938C5.01255 3.75667 5.82239 4.30935 7.44206 5.41552C7.57926 5.50928 7.71208 5.60047 7.83857 5.68783C8.41215 6.08349 8.69893 6.28142 8.88778 6.32417C9.07663 6.36692 9.51313 6.29736 10.3861 6.15824C10.8215 6.08882 11.3595 6.03778 12 6.03778C12.6405 6.03778 13.1785 6.08882 13.6139 6.15824C14.4869 6.29736 14.9234 6.36692 15.1122 6.32417C15.3011 6.28142 15.5878 6.08349 16.1614 5.68783C16.2879 5.60047 16.4207 5.50928 16.5579 5.41552C18.1776 4.30935 18.9874 3.75667 19.7897 4.17938C20.5919 4.60208 20.5878 5.48234 20.5797 7.24286C20.5764 7.96055 20.5136 8.68825 20.3517 9.35094C20.2087 9.93697 20.1371 10.2299 20.1486 10.386C20.1602 10.5421 20.2632 10.7952 20.4693 11.3009C20.8128 12.1441 21 13.053 21 14C21 18.4183 16.9706 22 12 22Z"/><path d="M9 14.5C9 15.3284 8.32843 16 7.5 16C6.67157 16 6 15.3284 6 14.5C6 13.6716 6.67157 13 7.5 13C8.32843 13 9 13.6716 9 14.5Z"/><path d="M18 14.5C18 15.3284 17.3284 16 16.5 16C15.6716 16 15 15.3284 15 14.5C15 13.6716 15.6716 13 16.5 13C17.3284 13 18 13.6716 18 14.5Z"/></svg>'
            ],
            [
                'id' => 'breeds', 
                'label' => 'Breeds', 
                'route' => '/breeds',
                'permission' => [1],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M10 4C10 4 8 2 6 3C4 4 4 6 4 6L2 10L4 12L7 9L13 12L18 9L20 10L22 8L19 5L16 8L13 7L11 4H10Z"/><path d="M16 14C16 14 14 16 12 16C10 16 8 14 8 14"/><path d="M7 13C7 13 5 15 5 17C5 19 7 21 9 21"/><path d="M17 13C17 13 19 15 19 17C19 19 17 21 15 21"/></svg>'
            ],
            [
                'id' => 'species', 
                'label' => 'Species', 
                'route' => '/species',
                'permission' => [1],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M12 3L3 8L12 13L21 8L12 3Z"/><path d="M3 13L12 18L21 13"/><path d="M3 18L12 23L21 18"/></svg>'
            ],
        ]
    ],
    [
        'id' => 'services',
        'label' => 'Services',
        'color' => 'text-teal-500',
        'hasSubmenu' => true,
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M4 8C4 6.89543 4.89543 6 6 6C7.10457 6 8 6.89543 8 8V13C8 16.3137 10.6863 19 14 19C17.3137 19 20 16.3137 20 13V11"/><path d="M20 11C21.1046 11 22 10.1046 22 9C22 7.89543 21.1046 7 20 7C18.8954 7 18 7.89543 18 9C18 10.1046 18.8954 11 20 11Z"/></svg>',
        'submenu' => [
            [
                'id' => 'all-services',
                'label' => 'All Services',
                'route' => '/services',
                'permission' => [1,2,3],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M4 8C4 6.89543 4.89543 6 6 6C7.10457 6 8 6.89543 8 8V13C8 16.3137 10.6863 19 14 19C17.3137 19 20 16.3137 20 13V11"/><path d="M20 11C21.1046 11 22 10.1046 22 9C22 7.89543 21.1046 7 20 7C18.8954 7 18 7.89543 18 9C18 10.1046 18.8954 11 20 11Z"/></svg>'
            ],
            [
                'id' => 'services-schedule',
                'label' => 'Services Schedule',
                'route' => '/services-schedule',
                'permission' => [1,2,3],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M12 6V12L16 14"/><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"/></svg>'
            ]
        ]
    ],
    [
        'id' => 'bookings',
        'label' => 'Bookings',
        'color' => 'text-orange-500',
        'route' => '/bookings',
        'permission' => [1,2,3,4,5],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M3 9H21"/><path d="M7 3V5"/><path d="M17 3V5"/><path d="M5 5H19C20.1046 5 21 5.89543 21 7V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V7C3 5.89543 3.89543 5 5 5Z"/><path d="M8 13H10"/><path d="M14 13H16"/><path d="M8 17H10"/><path d="M14 17H16"/></svg>'
    ],
    [
        'id' => 'sales-report',
        'label' => 'Report',
        'color' => 'text-cyan-500',
        'route' => '/sales-report',
        'permission' => [1,2],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M21 21H3V3"/><path d="M7 17V13"/><path d="M11 17V9"/><path d="M15 17V5"/><path d="M19 17V11"/></svg>'
    ],
    [
        'id' => 'medical-histories',
        'label' => 'Medical Histories',
        'color' => 'text-indigo-500',
        'route' => '/medical-histories',
        'permission' => [1,2,3,4],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z"/><path d="M7 12H17"/><path d="M12 7V17"/></svg>'
    ],
    [
        'id' => 'inventory',
        'label' => 'Inventory',
        'color' => 'text-amber-500',
        'hasSubmenu' => true,
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M20 7L12 12L4 7"/><path d="M12 21V12"/><path d="M3 7V17C3 17.5304 3.21071 18.0391 3.58579 18.4142C3.96086 18.7893 4.46957 19 5 19H19C19.5304 19 20.0391 18.7893 20.4142 18.4142C20.7893 18.0391 21 17.5304 21 17V7"/><path d="M16 5H8V9H16V5Z"/></svg>',
        'submenu' => [
            [
                'id' => 'all-inventory',
                'label' => 'All Inventory',
                'route' => '/inventory',
                'permission' => [1,2,3],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M20 7L12 12L4 7"/><path d="M12 21V12"/><path d="M3 7V17C3 17.5304 3.21071 18.0391 3.58579 18.4142C3.96086 18.7893 4.46957 19 5 19H19C19.5304 19 20.0391 18.7893 20.4142 18.4142C20.7893 18.0391 21 17.5304 21 17V7"/><path d="M16 5H8V9H16V5Z"/></svg>'
            ],
            [
                'id' => 'inventory-categories',
                'label' => 'Categories',
                'route' => '/categories',
                'permission' => [1,2,3],
                'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M4 4H10V10H4V4Z"/><path d="M14 4H20V10H14V4Z"/><path d="M4 14H10V20H4V14Z"/><path d="M14 14H20V20H14V14Z"/></svg>'
            ]
        ]
    ],
    [
        'id' => 'rule-base',
        'label' => 'Rulebase',
        'color' => 'text-rose-500',
        'route' => '/rule-base',
        'permission' => [1],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M8 6H21"/><path d="M8 12H21"/><path d="M8 18H21"/><path d="M3 6H3.01"/><path d="M3 12H3.01"/><path d="M3 18H3.01"/></svg>'
    ],
    [
        'id' => 'clinic-rating',
        'label' => 'Clinic Rating',
        'color' => 'text-yellow-500',
        'route' => '/clinic-ratings',
        'permission' => [1,2],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>'
    ],
    [
        'id' => 'activity-records',
        'label' => 'Activity Records',
        'color' => 'text-red-500',
        'route' => '/activity-records',
        'permission' => [1,2],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M22 12H18L15 21L9 3L6 12H2"/></svg>'
    ],


];

Route::currentRouteName() === 'notifications' ? array_push($menuItems, [
        'id' => 'notifications',
        'label' => 'Notifications',
        'color' => 'text-blue-500', 
        'route' => '/notifications',
        'permission' => [1,2,3,4],
        'svg' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M18 8C18 6.4087 17.3679 4.88258 16.2426 3.75736C15.1174 2.63214 13.5913 2 12 2C10.4087 2 8.88258 2.63214 7.75736 3.75736C6.63214 4.88258 6 6.4087 6 8C6 15 3 17 3 17H21C21 17 18 15 18 8Z"/><path d="M13.73 21C13.5542 21.3031 13.3019 21.5547 12.9982 21.7295C12.6945 21.9044 12.3504 21.9965 12 21.9965C11.6496 21.9965 11.3055 21.9044 11.0018 21.7295C10.6981 21.5547 10.4458 21.3031 10.27 21"/></svg>'
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
                  <x-ui.icon name="menu" class="w-7 h-7" />
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
                                      <x-ui.icon name="chevron-down" class="sidebar-text ml-auto w-6 h-6 transition-all duration-200 submenu-chevron {{ $isActive ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500' }} {{ $isExpanded ? 'rotate-180' : '' }}" data-menu="{{ $item['id'] }}" />
                                  @else
                                      <x-ui.icon name="chevron-right" class="sidebar-text ml-auto w-6 h-6 transition-all duration-200 {{ $isActive ? 'text-gray-500 rotate-90' : 'text-gray-400 group-hover:text-gray-500' }}" />
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