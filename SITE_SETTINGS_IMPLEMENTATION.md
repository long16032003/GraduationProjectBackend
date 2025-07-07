# Site Settings Implementation Guide

## Overview
This document describes the complete implementation of the dynamic site settings feature for the graduation project.

## Features Implemented

### Backend (Laravel)
1. **SiteSetting Model** (`app/Models/SiteSetting.php`)
   - Key-value pair storage
   - Helper methods: `get()`, `set()`, `getAll()`
   - Database table: `site_settings`

2. **SiteSettingController** (`app/Http/Controllers/Settings/SiteSettingController.php`)
   - Full CRUD operations
   - API endpoints with proper permissions
   - Supports both individual and batch updates

3. **API Routes** (`routes/web.php`)
   - `GET /site-settings` - Get all settings
   - `POST /site-settings` - Create new setting
   - `PUT /site-settings` - Update all settings
   - `PUT /site-settings/{key}` - Update single setting
   - `DELETE /site-settings/{key}` - Delete setting

4. **Database Migration** (`database/migrations/2025_06_25_191050_create_site_settings_table.php`)
   - Creates `site_settings` table with id, key, value, timestamps

5. **Database Seeder** (`database/seeders/SiteSettingSeeder.php`)
   - Populates default settings on database setup

### Frontend (React/TypeScript)
1. **Site Settings Hook** (`src/hooks/useSiteSettings.ts`)
   - Fetches settings from API
   - Applies settings to document (CSS variables, favicon, title)
   - Helper functions for accessing settings

2. **Site Settings Provider** (`src/providers/SiteSettingsProvider.tsx`)
   - React context to share settings across the app
   - Provides `useSiteSettingsContext` hook

3. **Admin Panel** (`src/pages/admin/siteSettings.tsx`)
   - Comprehensive settings management interface
   - Tabs for different setting categories:
     - General Information
     - Appearance (Colors)
     - Images (Logo, Favicon, Banners)
     - Typography (Fonts, Sizes)
   - Live preview functionality

4. **Dynamic CSS Variables** (`src/styles/siteSettings.css`)
   - CSS custom properties for colors, fonts, sizes
   - Utility classes for applying site settings
   - Responsive design support

## Settings Schema

### General Settings
- `site_name`: Restaurant name
- `site_tagline`: Restaurant tagline/slogan
- `contact_email`: Contact email address
- `contact_phone`: Contact phone number
- `address`: Restaurant address
- `opening_hours`: Operating hours

### Social Media
- `facebook_url`: Facebook page URL
- `zalo_url`: Zalo contact URL

### Appearance
- `primary_color`: Primary brand color
- `secondary_color`: Secondary brand color
- `accent_color`: Accent color for highlights

### Typography
- `heading_font`: Font family for headings
- `body_font`: Font family for body text
- `font_size`: Base font size (small/medium/large)

### Images (JSON format)
- `logo`: Logo image data
- `favicon`: Favicon image data
- `banner_images`: Banner images array

## How Settings Are Applied

### 1. CSS Variables
Settings are automatically applied as CSS custom properties:
```css
:root {
  --primary-color: #e53935;
  --secondary-color: #4caf50;
  --accent-color: #ff9800;
  --heading-font: 'Montserrat', sans-serif;
  --body-font: 'Roboto', sans-serif;
  --base-font-size: 16px;
}
```

### 2. Component Integration
Components use the `useSiteSettingsContext` hook to access settings:
```typescript
const { getSetting, getJsonSetting } = useSiteSettingsContext();
const siteName = getSetting('site_name', 'Default Name');
const logoData = getJsonSetting('logo', []);
```

### 3. Dynamic Updates
Settings are applied automatically when:
- Page loads
- Settings are changed in admin panel
- Context provider re-renders

## Components Updated

### Homepage (`src/pages/home/home.tsx`)
- Dynamic site name and tagline
- Dynamic contact information
- Dynamic banner images
- Dynamic color scheme

### Main Header (`src/components/ui/main-header.tsx`)
- Dynamic logo display
- Dynamic site name
- Dynamic button colors

### Admin Settings Page (`src/pages/admin/siteSettings.tsx`)
- Complete settings management interface
- Real-time preview
- Form validation
- API integration

## CSS Classes Available

### Color Classes
- `.site-primary-color` - Primary text color
- `.site-primary-bg` - Primary background color
- `.site-secondary-color` - Secondary text color
- `.site-secondary-bg` - Secondary background color
- `.site-accent-color` - Accent text color
- `.site-accent-bg` - Accent background color

### Button Classes
- `.site-btn-primary` - Primary button styling
- `.site-btn-secondary` - Secondary button styling
- `.site-btn-accent` - Accent button styling

### Typography Classes
- `.site-heading-font` - Heading font family
- `.site-body-font` - Body font family

## Usage Instructions

### For Administrators
1. Login to admin panel
2. Navigate to "Thiết lập trang web"
3. Update settings in appropriate tabs
4. Click "Lưu thiết lập" to save changes
5. Changes will be reflected immediately on the website

### For Developers
1. Use `useSiteSettingsContext()` hook to access settings
2. Use `getSetting(key, fallback)` for string values
3. Use `getJsonSetting(key, fallback)` for JSON values
4. Apply site setting CSS classes for consistent styling

## Database Setup

Run the following commands to set up the database:
```bash
php artisan migrate
php artisan db:seed --class=SiteSettingSeeder
```

## API Permissions

The site settings API requires the following permissions:
- `site-setting:browse` - View all settings
- `site-setting:read` - Read specific setting
- `site-setting:create` - Create new setting
- `site-setting:update` - Update existing setting
- `site-setting:delete` - Delete setting

## Testing

To test the implementation:
1. Access admin panel at `/admin/site-settings`
2. Change various settings (colors, text, images)
3. Navigate to homepage to see changes
4. Verify that settings persist after page reload

## Files Modified/Created

### Backend Files
- `app/Models/SiteSetting.php` (existing)
- `app/Http/Controllers/Settings/SiteSettingController.php` (existing)
- `database/migrations/2025_06_25_191050_create_site_settings_table.php` (existing)
- `database/seeders/SiteSettingSeeder.php` (updated)
- `routes/web.php` (updated with API routes)

### Frontend Files
- `src/hooks/useSiteSettings.ts` (created)
- `src/providers/SiteSettingsProvider.tsx` (created)
- `src/styles/siteSettings.css` (created)
- `src/App.tsx` (updated with provider)
- `src/pages/home/home.tsx` (updated with dynamic content)
- `src/components/ui/main-header.tsx` (updated with dynamic content)
- `src/pages/admin/siteSettings.tsx` (existing, enhanced)

## Conclusion

The site settings system is now fully functional and allows administrators to dynamically change website appearance and content through the admin panel. All changes are immediately reflected on the public website without requiring code modifications or deployments. 
