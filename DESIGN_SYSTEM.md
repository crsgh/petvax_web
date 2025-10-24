# PetVax Design System - Clean & Minimalist

## Overview
This design system provides a clean, minimalist approach to building consistent UI components across the PetVax application. All components are built with accessibility, responsiveness, and reusability in mind.

## Getting Started

### 1. Include the Design System CSS
The design system CSS is automatically included in your layout:
```html
<link href="{{ asset('css/design-system.css') }}" rel="stylesheet" />
```

### 2. Use Components in Blade Templates
All components are located in `resources/views/components/ui/` and can be used with the `<x-ui.component-name>` syntax.

## Components

### Button Component
**Location:** `resources/views/components/ui/button.blade.php`

```blade
<!-- Basic button -->
<x-ui.button>Click me</x-ui.button>

<!-- Button variants -->
<x-ui.button variant="primary">Primary</x-ui.button>
<x-ui.button variant="secondary">Secondary</x-ui.button>
<x-ui.button variant="success">Success</x-ui.button>
<x-ui.button variant="danger">Danger</x-ui.button>

<!-- Button sizes -->
<x-ui.button size="sm">Small</x-ui.button>
<x-ui.button size="default">Default</x-ui.button>
<x-ui.button size="lg">Large</x-ui.button>

<!-- Button with icon -->
<x-ui.button icon="fas fa-plus">Add New</x-ui.button>
<x-ui.button icon="fas fa-arrow-right" icon-position="right">Next</x-ui.button>

<!-- Link button -->
<x-ui.button href="/users" variant="primary">Go to Users</x-ui.button>

<!-- Disabled button -->
<x-ui.button disabled>Disabled</x-ui.button>
```

**Props:**
- `variant`: primary, secondary, success, danger (default: primary)
- `size`: sm, default, lg (default: default)
- `type`: button, submit, reset (default: button)
- `href`: URL for link buttons
- `disabled`: boolean
- `icon`: Font Awesome icon class
- `icon-position`: left, right (default: left)

### Card Component
**Location:** `resources/views/components/ui/card.blade.php`

```blade
<!-- Basic card -->
<x-ui.card>
  <p>Card content goes here</p>
</x-ui.card>

<!-- Card with title -->
<x-ui.card title="User Management">
  <p>Manage your users here</p>
</x-ui.card>

<!-- Card with title, subtitle, and header actions -->
<x-ui.card title="Users" subtitle="Manage system users">
  <x-slot name="headerActions">
    <x-ui.button variant="primary" size="sm">Add User</x-ui.button>
  </x-slot>
  
  <p>Card content</p>
</x-ui.card>

<!-- Card without padding -->
<x-ui.card title="Data Table" :padding="false">
  <table>...</table>
</x-ui.card>
```

**Props:**
- `title`: Card title
- `subtitle`: Card subtitle
- `padding`: boolean (default: true)

**Slots:**
- `headerActions`: Content for the header action area

### Input Component
**Location:** `resources/views/components/ui/input.blade.php`

```blade
<!-- Basic input -->
<x-ui.input name="username" placeholder="Enter username" />

<!-- Input with label -->
<x-ui.input 
  label="Email Address" 
  name="email" 
  type="email" 
  required 
/>

<!-- Input with error -->
<x-ui.input 
  label="Password" 
  name="password" 
  type="password" 
  error="Password is required" 
/>

<!-- Input with help text -->
<x-ui.input 
  label="Username" 
  name="username" 
  help="Username must be unique" 
/>
```

**Props:**
- `label`: Input label
- `type`: Input type (default: text)
- `name`: Input name
- `id`: Input ID (auto-generated if not provided)
- `value`: Input value
- `placeholder`: Placeholder text
- `required`: boolean
- `error`: Error message
- `help`: Help text

### Select Component
**Location:** `resources/views/components/ui/select.blade.php`

```blade
<!-- Basic select -->
<x-ui.select name="role" label="Role">
  <option value="admin">Admin</option>
  <option value="user">User</option>
</x-ui.select>

<!-- Select with options array -->
<x-ui.select 
  name="status" 
  label="Status" 
  :options="['active' => 'Active', 'inactive' => 'Inactive']"
  value="active"
/>

<!-- Select with placeholder -->
<x-ui.select 
  name="category" 
  label="Category" 
  placeholder="Choose a category"
  required
/>
```

**Props:**
- `label`: Select label
- `name`: Select name
- `id`: Select ID (auto-generated if not provided)
- `value`: Selected value
- `placeholder`: Placeholder option text
- `required`: boolean
- `error`: Error message
- `help`: Help text
- `options`: Array of options [value => label]

### Badge Component
**Location:** `resources/views/components/ui/badge.blade.php`

```blade
<!-- Basic badges -->
<x-ui.badge>Default</x-ui.badge>
<x-ui.badge variant="primary">Primary</x-ui.badge>
<x-ui.badge variant="success">Success</x-ui.badge>
<x-ui.badge variant="warning">Warning</x-ui.badge>
<x-ui.badge variant="danger">Danger</x-ui.badge>
<x-ui.badge variant="info">Info</x-ui.badge>
```

**Props:**
- `variant`: primary, success, warning, danger, info (default: primary)

### Avatar Component
**Location:** `resources/views/components/ui/avatar.blade.php`

```blade
<!-- Basic avatar -->
<x-ui.avatar src="/path/to/image.jpg" alt="User Name" />

<!-- Avatar sizes -->
<x-ui.avatar size="sm" src="/path/to/image.jpg" />
<x-ui.avatar size="md" src="/path/to/image.jpg" />
<x-ui.avatar size="lg" src="/path/to/image.jpg" />

<!-- Avatar with fallback -->
<x-ui.avatar :src="$user->avatar" :alt="$user->name" />
```

**Props:**
- `src`: Image source URL
- `alt`: Alt text (default: Avatar)
- `size`: sm, md, lg (default: md)

### Table Component
**Location:** `resources/views/components/ui/table.blade.php`

```blade
<!-- Basic table -->
<x-ui.table :headers="['Name', 'Email', 'Role', 'Actions']">
  <tr>
    <td>John Doe</td>
    <td>john@example.com</td>
    <td>Admin</td>
    <td>
      <x-ui.button size="sm">Edit</x-ui.button>
    </td>
  </tr>
</x-ui.table>

<!-- Searchable table -->
<x-ui.table 
  :headers="['Name', 'Email', 'Role']" 
  searchable 
  search-placeholder="Search users..."
>
  <!-- table rows -->
</x-ui.table>
```

**Props:**
- `headers`: Array of table headers
- `searchable`: boolean (adds search functionality)
- `search-placeholder`: Search input placeholder

### Modal Component
**Location:** `resources/views/components/ui/modal.blade.php`

```blade
<!-- Basic modal -->
<x-ui.modal id="myModal" title="Modal Title">
  <p>Modal content goes here</p>
</x-ui.modal>

<!-- Large modal -->
<x-ui.modal id="largeModal" title="Large Modal" size="lg">
  <p>Large modal content</p>
</x-ui.modal>

<!-- Modal without close button -->
<x-ui.modal id="alertModal" title="Alert" :closable="false">
  <p>This modal cannot be closed by clicking X</p>
</x-ui.modal>

<!-- Open modal with JavaScript -->
<x-ui.button onclick="openModal('myModal')">Open Modal</x-ui.button>
```

**Props:**
- `id`: Modal ID (required)
- `title`: Modal title
- `size`: sm, default, lg, xl, full (default: default)
- `closable`: boolean (default: true)

**JavaScript Functions:**
- `openModal(modalId)`: Opens a modal
- `closeModal(modalId)`: Closes a modal

## CSS Classes

### Typography
- `.text-xs`, `.text-sm`, `.text-base`, `.text-lg`, `.text-xl`, `.text-2xl`, `.text-3xl`
- `.font-medium`, `.font-semibold`, `.font-bold`
- `.text-primary`, `.text-secondary`, `.text-success`, `.text-warning`, `.text-danger`, `.text-info`
- `.text-gray-400`, `.text-gray-500`, `.text-gray-600`, `.text-gray-700`, `.text-gray-800`

### Layout
- `.container-clean`: Responsive container with max-width
- `.flex`, `.flex-col`, `.items-center`, `.justify-center`, `.justify-between`, `.justify-end`
- `.gap-2`, `.gap-3`, `.gap-4`

### Spacing
- `.p-0` to `.p-6`: Padding utilities
- `.m-0` to `.m-6`: Margin utilities
- `.mb-0` to `.mb-6`: Margin bottom utilities
- `.mt-0` to `.mt-6`: Margin top utilities

### Display
- `.hidden`, `.block`, `.inline-block`
- `.text-left`, `.text-center`, `.text-right`

## Color Palette

### Primary Colors
- Primary: `#2563eb`
- Secondary: `#64748b`
- Success: `#10b981`
- Warning: `#f59e0b`
- Danger: `#ef4444`
- Info: `#06b6d4`

### Gray Scale
- Gray 50: `#f8fafc`
- Gray 100: `#f1f5f9`
- Gray 200: `#e2e8f0`
- Gray 300: `#cbd5e1`
- Gray 400: `#94a3b8`
- Gray 500: `#64748b`
- Gray 600: `#475569`
- Gray 700: `#334155`
- Gray 800: `#1e293b`
- Gray 900: `#0f172a`

## Best Practices

### 1. Consistency
- Always use the design system components instead of custom HTML
- Stick to the defined color palette
- Use consistent spacing with the utility classes

### 2. Accessibility
- Always provide alt text for images and avatars
- Use semantic HTML elements
- Ensure proper color contrast
- Make interactive elements keyboard accessible

### 3. Responsive Design
- Test components on different screen sizes
- Use responsive utility classes when needed
- Ensure touch targets are large enough on mobile

### 4. Performance
- Components are lightweight and optimized
- CSS uses custom properties for easy theming
- Minimal JavaScript footprint

## Migration Guide

### From Old Components to New Components

**Old Button:**
```html
<button class="btn btn-primary">Click me</button>
```

**New Button:**
```blade
<x-ui.button variant="primary">Click me</x-ui.button>
```

**Old Card:**
```html
<div class="card">
  <div class="card-header">
    <h6>Title</h6>
  </div>
  <div class="card-body">
    Content
  </div>
</div>
```

**New Card:**
```blade
<x-ui.card title="Title">
  Content
</x-ui.card>
```

**Old Form Input:**
```html
<div class="mb-3">
  <label class="form-label">Name</label>
  <input type="text" class="form-control" name="name">
</div>
```

**New Form Input:**
```blade
<x-ui.input label="Name" name="name" />
```

## Customization

### CSS Custom Properties
You can customize the design system by overriding CSS custom properties:

```css
:root {
  --primary: #your-primary-color;
  --font-family: 'Your-Font', sans-serif;
  --radius: 8px;
}
```

### Component Customization
Components accept additional classes through the `class` attribute:

```blade
<x-ui.button class="my-custom-class">Button</x-ui.button>
```

## Support

For questions or issues with the design system:
1. Check this documentation first
2. Look at the component source code in `resources/views/components/ui/`
3. Test with the example implementations in `users-clean.blade.php`
