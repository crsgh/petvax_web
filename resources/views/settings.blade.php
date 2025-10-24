@extends('layouts.user_type.auth')

@section('content')
<div class="settings-page">
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Application Settings</h1>
      <p class="page-subtitle">Customize the appearance, branding, and system configuration</p>
    </div>
  </div>

  <div class="settings-content">
    <div class="settings-tabs">
      <button class="tab-button active" onclick="showTab('appearance')" data-tab="appearance">
        <i class="fas fa-palette"></i>
        Appearance
      </button>
      <button class="tab-button" onclick="showTab('branding')" data-tab="branding">
        <i class="fas fa-building"></i>
        Branding
      </button>
      <button class="tab-button" onclick="showTab('system')" data-tab="system">
        <i class="fas fa-cog"></i>
        System
      </button>
    </div>

    <div class="settings-panels">
      <!-- Appearance Settings -->
      <div id="appearance-panel" class="settings-panel active">
        <form id="appearanceForm" method="POST" action="/settings/appearance">
          @csrf
          
          <div class="settings-section">
            <h3 class="section-title">Colors</h3>
            <div class="settings-grid">
              <div class="setting-item">
                <label class="setting-label">Primary Color</label>
                <div class="color-input-group">
                  <input type="color" class="color-input" name="site_primary_color" id="primaryColorPage" value="#3b82f6">
                  <input type="text" class="color-text" value="#3b82f6" readonly>
                </div>
                <p class="setting-description">Main brand color for buttons, links, and accents</p>
              </div>

              <div class="setting-item">
                <label class="setting-label">Font Color</label>
                <div class="color-input-group">
                  <input type="color" class="color-input" name="site_font_color" id="fontColorPage" value="#111827">
                  <input type="text" class="color-text" value="#111827" readonly>
                </div>
                <p class="setting-description">Primary text color for content</p>
              </div>

              <div class="setting-item">
                <label class="setting-label">Button Color</label>
                <div class="color-input-group">
                  <input type="color" class="color-input" name="site_button_color" id="buttonColorPage" value="#6b7280">
                  <input type="text" class="color-text" value="#6b7280" readonly>
                </div>
                <p class="setting-description">Background color for secondary buttons</p>
              </div>

              <div class="setting-item">
                <label class="setting-label">Button Text Color</label>
                <div class="color-input-group">
                  <input type="color" class="color-input" name="site_button_text_color" id="buttonTextColorPage" value="#ffffff">
                  <input type="text" class="color-text" value="#ffffff" readonly>
                </div>
                <p class="setting-description">Text color for secondary buttons</p>
              </div>

              <div class="setting-item">
                <label class="setting-label">Success Color</label>
                <div class="color-input-group">
                  <input type="color" class="color-input" name="site_success_color" id="successColor" value="#10b981">
                  <input type="text" class="color-text" value="#10b981" readonly>
                </div>
                <p class="setting-description">Color for success messages and positive actions</p>
              </div>

              <div class="setting-item">
                <label class="setting-label">Warning Color</label>
                <div class="color-input-group">
                  <input type="color" class="color-input" name="site_warning_color" id="warningColor" value="#f59e0b">
                  <input type="text" class="color-text" value="#f59e0b" readonly>
                </div>
                <p class="setting-description">Color for warnings and caution messages</p>
              </div>

              <div class="setting-item">
                <label class="setting-label">Danger Color</label>
                <div class="color-input-group">
                  <input type="color" class="color-input" name="site_danger_color" id="dangerColor" value="#ef4444">
                  <input type="text" class="color-text" value="#ef4444" readonly>
                </div>
                <p class="setting-description">Color for errors and destructive actions</p>
              </div>
            </div>
          </div>

          <div class="settings-section">
            <h3 class="section-title">Typography</h3>
            <div class="settings-grid">
              <div class="setting-item">
                <label class="setting-label">Font Family</label>
                <select class="setting-select" name="site_font_family" id="fontFamilyPage">
                  <option value="Poppins">Poppins (Default)</option>
                  <option value="Inter">Inter</option>
                  <option value="Roboto">Roboto</option>
                  <option value="Open Sans">Open Sans</option>
                  <option value="Lato">Lato</option>
                  <option value="Montserrat">Montserrat</option>
                </select>
                <p class="setting-description">Primary font family for the application</p>
              </div>

              <div class="setting-item">
                <label class="setting-label">Font Size</label>
                <div class="range-input-group">
                  <input type="range" class="range-input" name="site_font_size" id="fontSizePage" min="12" max="18" value="14">
                  <span class="range-value">14px</span>
                </div>
                <p class="setting-description">Base font size for text content</p>
              </div>
            </div>
          </div>

          <div class="settings-actions">
            <x-ui.button type="button" variant="secondary" onclick="resetAppearanceSettings()">
              Reset to Defaults
            </x-ui.button>
            <x-ui.button type="submit" variant="primary">
              Save Appearance Settings
            </x-ui.button>
          </div>
        </form>
      </div>

      <!-- Branding Settings -->
      <div id="branding-panel" class="settings-panel">
        <form id="brandingForm" method="POST" action="/settings/branding" enctype="multipart/form-data">
          @csrf
          
          <div class="settings-section">
            <h3 class="section-title">System Identity</h3>
            <div class="settings-grid">
              <div class="setting-item">
                <label class="setting-label">System Name</label>
                <input type="text" class="setting-input" name="site_name" id="siteName" value="PetVax Clinic Management" placeholder="Enter system name">
                <p class="setting-description">Name displayed in the application header and title</p>
              </div>

              <div class="setting-item">
                <label class="setting-label">System Description</label>
                <textarea class="setting-textarea" name="site_description" id="siteDescription" rows="3" placeholder="Brief description of your system">Comprehensive veterinary clinic management system for pet care and vaccination tracking.</textarea>
                <p class="setting-description">Brief description shown in various parts of the application</p>
              </div>
            </div>
          </div>

          <div class="settings-section">
            <h3 class="section-title">Logo & Branding</h3>
            <div class="settings-grid">
              <div class="setting-item">
                <label class="setting-label">System Logo</label>
                <div class="logo-upload-container">
                  <div class="current-logo">
                    <img id="currentLogo" src="{{ asset('assets/img/logo-ct.png') }}" alt="Current Logo" class="logo-preview">
                  </div>
                  <div class="upload-controls">
                    <input type="file" name="site_logo" id="logoUpload" accept="image/*" class="hidden" onchange="previewLogo(this)">
                    <x-ui.button type="button" variant="secondary" onclick="document.getElementById('logoUpload').click()">
                      <i class="fas fa-upload"></i>
                      Upload New Logo
                    </x-ui.button>
                    <x-ui.button type="button" variant="secondary" onclick="resetLogo()">
                      <i class="fas fa-undo"></i>
                      Reset to Default
                    </x-ui.button>
                  </div>
                </div>
                <p class="setting-description">Upload a logo image (PNG, JPG, SVG recommended). Optimal size: 200x60px</p>
              </div>

              <div class="setting-item">
                <label class="setting-label">Favicon</label>
                <div class="favicon-upload-container">
                  <div class="current-favicon">
                    <img id="currentFavicon" src="{{ asset('assets/img/favicon.png') }}" alt="Current Favicon" class="favicon-preview">
                  </div>
                  <div class="upload-controls">
                    <input type="file" name="site_favicon" id="faviconUpload" accept="image/*" class="hidden" onchange="previewFavicon(this)">
                    <x-ui.button type="button" variant="secondary" onclick="document.getElementById('faviconUpload').click()">
                      <i class="fas fa-upload"></i>
                      Upload Favicon
                    </x-ui.button>
                  </div>
                </div>
                <p class="setting-description">Upload a favicon (ICO, PNG recommended). Size: 32x32px or 16x16px</p>
              </div>
            </div>
          </div>

          <div class="settings-actions">
            <x-ui.button type="button" variant="secondary" onclick="resetBrandingSettings()">
              Reset to Defaults
            </x-ui.button>
            <x-ui.button type="submit" variant="primary">
              Save Branding Settings
            </x-ui.button>
          </div>
        </form>
      </div>

      <!-- System Settings -->
      <div id="system-panel" class="settings-panel">
        <form id="systemForm" method="POST" action="/settings/system">
          @csrf
          
          <div class="settings-section">
            <h3 class="section-title">Application Configuration</h3>
            <div class="settings-grid">
              <div class="setting-item">
                <label class="setting-label">Application Environment</label>
                <select class="setting-select" name="app_env" id="appEnv">
                  <option value="production">Production</option>
                  <option value="staging">Staging</option>
                  <option value="development">Development</option>
                </select>
                <p class="setting-description">Current environment mode</p>
              </div>

              <div class="setting-item">
                <label class="setting-label">Debug Mode</label>
                <div class="toggle-container">
                  <input type="checkbox" id="debugMode" name="app_debug" class="toggle-input">
                  <label for="debugMode" class="toggle-label">
                    <span class="toggle-slider"></span>
                  </label>
                </div>
                <p class="setting-description">Enable debug mode for development (disable in production)</p>
              </div>

              <div class="setting-item">
                <label class="setting-label">Cache Enabled</label>
                <div class="toggle-container">
                  <input type="checkbox" id="cacheEnabled" name="cache_enabled" class="toggle-input" checked>
                  <label for="cacheEnabled" class="toggle-label">
                    <span class="toggle-slider"></span>
                  </label>
                </div>
                <p class="setting-description">Enable caching for better performance</p>
              </div>

              <div class="setting-item">
                <label class="setting-label">Session Timeout (minutes)</label>
                <input type="number" class="setting-input" name="session_timeout" id="sessionTimeout" value="120" min="15" max="480">
                <p class="setting-description">How long users stay logged in without activity</p>
              </div>
            </div>
          </div>

          <div class="settings-section">
            <h3 class="section-title">Security</h3>
            <div class="settings-grid">
              <div class="setting-item">
                <label class="setting-label">Two-Factor Authentication</label>
                <div class="toggle-container">
                  <input type="checkbox" id="twoFactorAuth" name="two_factor_enabled" class="toggle-input">
                  <label for="twoFactorAuth" class="toggle-label">
                    <span class="toggle-slider"></span>
                  </label>
                </div>
                <p class="setting-description">Require 2FA for all user accounts</p>
              </div>

              <div class="setting-item">
                <label class="setting-label">Password Minimum Length</label>
                <input type="number" class="setting-input" name="password_min_length" id="passwordMinLength" value="8" min="6" max="32">
                <p class="setting-description">Minimum required password length</p>
              </div>
            </div>
          </div>

          <div class="settings-actions">
            <x-ui.button type="button" variant="secondary" onclick="resetSystemSettings()">
              Reset to Defaults
            </x-ui.button>
            <x-ui.button type="submit" variant="primary">
              Save System Settings
            </x-ui.button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
.settings-page {
  padding: 1.5rem;
  background: #fff;
  min-height: 100vh;
  font-family: var(--site-font, 'Poppins'), sans-serif;
}

.page-header {
  margin-bottom: 2rem;
}

.page-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: var(--font-color, #111827);
  margin: 0;
  font-family: var(--site-font, 'Poppins'), sans-serif;
}

.page-subtitle {
  color: var(--button-color, #6b7280);
  font-size: 0.875rem;
  margin: 0.5rem 0 0 0;
}

.settings-content {
  background: var(--sidebar-bg-color, #ffffff);
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.settings-tabs {
  display: flex;
  border-bottom: 1px solid #e5e7eb;
  background: #f8fafc;
}

.tab-button {
  padding: 1rem 1.5rem;
  border: none;
  background: none;
  color: var(--button-color, #6b7280);
  font-family: var(--site-font, 'Poppins'), sans-serif;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  border-bottom: 3px solid transparent;
}

.tab-button:hover,
.tab-button.active {
  color: var(--primary-color, #3b82f6);
  background: var(--sidebar-bg-color, #ffffff);
  border-bottom-color: var(--primary-color, #3b82f6);
}

.settings-panels {
  position: relative;
}

.settings-panel {
  display: none;
  padding: 2rem;
}

.settings-panel.active {
  display: block;
}

.settings-section {
  margin-bottom: 3rem;
}

.section-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--font-color, #111827);
  margin: 0 0 1.5rem 0;
  font-family: var(--site-font, 'Poppins'), sans-serif;
}

.settings-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.setting-item {
  display: flex;
  flex-direction: column;
}

.setting-label {
  font-weight: 500;
  color: var(--font-color, #111827);
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  font-family: var(--site-font, 'Poppins'), sans-serif;
}

.setting-description {
  font-size: 0.8125rem;
  color: var(--button-color, #6b7280);
  margin: 0.5rem 0 0 0;
  line-height: 1.4;
}

.color-input-group {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.color-input {
  width: 50px;
  height: 40px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  cursor: pointer;
  background: none;
}

.color-text {
  flex: 1;
  padding: 0.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  background: #f8fafc;
  color: var(--font-color, #111827);
  font-family: monospace;
  font-size: 0.8125rem;
}

.setting-select,
.setting-input,
.setting-textarea {
  padding: 0.75rem;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  background: var(--sidebar-bg-color, #ffffff);
  color: var(--font-color, #111827);
  font-family: var(--site-font, 'Poppins'), sans-serif;
  font-size: 0.875rem;
}

.setting-textarea {
  resize: vertical;
  min-height: 80px;
}

.range-input-group {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.range-input {
  flex: 1;
  accent-color: var(--primary-color, #3b82f6);
}

.range-value {
  font-weight: 500;
  color: var(--font-color, #111827);
  min-width: 40px;
  font-size: 0.875rem;
}

/* Logo Upload Styles */
.logo-upload-container,
.favicon-upload-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.current-logo,
.current-favicon {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  border: 2px dashed #e5e7eb;
  border-radius: 8px;
  background: #f8fafc;
}

.logo-preview {
  max-width: 200px;
  max-height: 60px;
  object-fit: contain;
}

.favicon-preview {
  width: 32px;
  height: 32px;
  object-fit: contain;
}

.upload-controls {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

/* Toggle Switch Styles */
.toggle-container {
  display: flex;
  align-items: center;
}

.toggle-input {
  display: none;
}

.toggle-label {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
  background: #e5e7eb;
  border-radius: 12px;
  cursor: pointer;
  transition: background 0.2s ease;
}

.toggle-slider {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 20px;
  height: 20px;
  background: white;
  border-radius: 50%;
  transition: transform 0.2s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.toggle-input:checked + .toggle-label {
  background: var(--primary-color, #3b82f6);
}

.toggle-input:checked + .toggle-label .toggle-slider {
  transform: translateX(26px);
}

.settings-actions {
  margin-top: 3rem;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  flex-wrap: wrap;
}

.hidden {
  display: none !important;
}

@media (max-width: 768px) {
  .settings-grid {
    grid-template-columns: 1fr;
  }
  
  .settings-tabs {
    flex-direction: column;
  }
  
  .settings-actions {
    justify-content: stretch;
  }
  
  .settings-actions button {
    flex: 1;
  }
  
  .upload-controls {
    flex-direction: column;
  }
}
</style>

<script>
// Tab switching functionality
function showTab(tabName) {
  // Hide all panels
  document.querySelectorAll('.settings-panel').forEach(panel => {
    panel.classList.remove('active');
  });
  
  // Remove active class from all tabs
  document.querySelectorAll('.tab-button').forEach(tab => {
    tab.classList.remove('active');
  });
  
  // Show selected panel
  document.getElementById(tabName + '-panel').classList.add('active');
  
  // Add active class to clicked tab
  document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
}

// Color input handlers for settings page
document.addEventListener('DOMContentLoaded', function() {
  // Load current settings from localStorage
  loadSettingsPageValues();
  
  // Color input handlers
  document.querySelectorAll('.color-input').forEach(input => {
    input.addEventListener('change', function() {
      const textInput = this.parentElement.querySelector('.color-text');
      textInput.value = this.value;
      
      // Apply color immediately for preview
      applySettingsPagePreview(this.name || this.id, this.value);
    });
  });

  // Range input handler
  document.getElementById('fontSizePage')?.addEventListener('input', function() {
    const valueSpan = this.parentElement.querySelector('.range-value');
    valueSpan.textContent = this.value + 'px';
    
    // Apply font size immediately for preview
    document.documentElement.style.setProperty('--font-size', this.value + 'px');
  });

  // Font family handler
  document.getElementById('fontFamilyPage')?.addEventListener('change', function() {
    document.documentElement.style.setProperty('--site-font', `'${this.value}', sans-serif`);
  });
});

function loadSettingsPageValues() {
  // Load from localStorage
  const primaryColor = localStorage.getItem('siteColor') || '#3b82f6';
  const fontColor = localStorage.getItem('fontColor') || '#111827';
  const buttonColor = localStorage.getItem('buttonColor') || '#6b7280';
  const buttonTextColor = localStorage.getItem('buttonTextColor') || '#ffffff';
  const siteFont = localStorage.getItem('siteFont') || 'Poppins';
  
  // Set values
  if (document.getElementById('primaryColorPage')) {
    document.getElementById('primaryColorPage').value = primaryColor;
    document.querySelector('#primaryColorPage').parentElement.querySelector('.color-text').value = primaryColor;
  }
  
  if (document.getElementById('fontColorPage')) {
    document.getElementById('fontColorPage').value = fontColor;
    document.querySelector('#fontColorPage').parentElement.querySelector('.color-text').value = fontColor;
  }
  
  if (document.getElementById('buttonColorPage')) {
    document.getElementById('buttonColorPage').value = buttonColor;
    document.querySelector('#buttonColorPage').parentElement.querySelector('.color-text').value = buttonColor;
  }
  
  if (document.getElementById('buttonTextColorPage')) {
    document.getElementById('buttonTextColorPage').value = buttonTextColor;
    document.querySelector('#buttonTextColorPage').parentElement.querySelector('.color-text').value = buttonTextColor;
  }
  
  if (document.getElementById('fontFamilyPage')) {
    document.getElementById('fontFamilyPage').value = siteFont;
  }
}

function applySettingsPagePreview(settingName, color) {
  const cssVarMap = {
    'site_primary_color': '--primary-color',
    'primaryColorPage': '--primary-color',
    'site_font_color': '--font-color',
    'fontColorPage': '--font-color',
    'site_button_color': '--button-color',
    'buttonColorPage': '--button-color',
    'site_button_text_color': '--button-text-color',
    'buttonTextColorPage': '--button-text-color'
  };
  
  const cssVar = cssVarMap[settingName];
  if (cssVar) {
    document.documentElement.style.setProperty(cssVar, color);
    
    // Also update localStorage for persistence
    const storageMap = {
      '--primary-color': 'siteColor',
      '--font-color': 'fontColor',
      '--button-color': 'buttonColor',
      '--button-text-color': 'buttonTextColor'
    };
    
    const storageKey = storageMap[cssVar];
    if (storageKey) {
      localStorage.setItem(storageKey, color);
    }
  }
}

// Logo preview functions
function previewLogo(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('currentLogo').src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function previewFavicon(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('currentFavicon').src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function resetLogo() {
  document.getElementById('currentLogo').src = '{{ asset('assets/img/logo-ct.png') }}';
  document.getElementById('logoUpload').value = '';
}

// Reset functions
function resetAppearanceSettings() {
  if (confirm('Reset all appearance settings to defaults?')) {
    // Reset colors
    document.getElementById('primaryColorPage').value = '#3b82f6';
    document.getElementById('fontColorPage').value = '#111827';
    document.getElementById('buttonColorPage').value = '#6b7280';
    document.getElementById('buttonTextColorPage').value = '#ffffff';
    document.getElementById('fontFamilyPage').value = 'Poppins';
    document.getElementById('fontSizePage').value = '14';
    
    // Update text inputs
    document.querySelectorAll('.color-input').forEach(input => {
      const textInput = input.parentElement.querySelector('.color-text');
      textInput.value = input.value;
    });
    
    // Apply changes
    document.documentElement.style.setProperty('--primary-color', '#3b82f6');
    document.documentElement.style.setProperty('--font-color', '#111827');
    document.documentElement.style.setProperty('--button-color', '#6b7280');
    document.documentElement.style.setProperty('--button-text-color', '#ffffff');
    document.documentElement.style.setProperty('--site-font', "'Poppins', sans-serif");
    document.documentElement.style.setProperty('--font-size', '14px');
    
    // Update localStorage
    localStorage.setItem('siteColor', '#3b82f6');
    localStorage.setItem('fontColor', '#111827');
    localStorage.setItem('buttonColor', '#6b7280');
    localStorage.setItem('buttonTextColor', '#ffffff');
    localStorage.setItem('siteFont', 'Poppins');
    
    document.querySelector('.range-value').textContent = '14px';
  }
}

function resetBrandingSettings() {
  if (confirm('Reset all branding settings to defaults?')) {
    document.getElementById('siteName').value = 'PetVax Clinic Management';
    document.getElementById('siteDescription').value = 'Comprehensive veterinary clinic management system for pet care and vaccination tracking.';
    resetLogo();
  }
}

function resetSystemSettings() {
  if (confirm('Reset all system settings to defaults?')) {
    document.getElementById('appEnv').value = 'production';
    document.getElementById('debugMode').checked = false;
    document.getElementById('cacheEnabled').checked = true;
    document.getElementById('sessionTimeout').value = '120';
    document.getElementById('twoFactorAuth').checked = false;
    document.getElementById('passwordMinLength').value = '8';
  }
}
</script>
@endsection
