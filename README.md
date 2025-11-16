# Ninja Test Email

**Contributors:** 1983shiv  
**Tags:** email, test-email, smtp, email-logging, email-testing  
**Requires at least:** 6.0  
**Tested up to:** 6.8  
**Requires PHP:** 7.4  
**Stable tag:** 1.0.0  
**License:** GPL-2.0-or-later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

Modern WP Email Test Plugin

## ✅ Ready to Use

This plugin includes **pre-compiled assets** and is ready to use immediately after installation. No build step required!

## Quick Start

1. Upload the plugin ZIP to WordPress
2. Activate the plugin
3. Access **Ninja Email Test** from the WordPress admin menu
4. Done! 🎉

## Features

- 🎨 Modern UI with React 18 and Tailwind CSS
- 🔌 REST API support
- 📱 Responsive design
- 🎯 Admin dashboard
- 🎨 Frontend shortcode
- 🔧 Modular architecture
- ✨ Pre-compiled assets included

## Installation

### Standard Installation (No Build Required)

1. Upload the plugin ZIP file via WordPress admin
2. Activate the plugin
3. Access via **Ninja Email Test** menu

### Manual Installation

1. Extract the plugin ZIP file
2. Upload the `ninja-email-test` folder to `wp-content/plugins/`
3. Activate the plugin in WordPress admin
4. Access via **Ninja Email Test** menu

## For Developers

Want to customize the interface? The source files are included!

### Setup Development Environment

```bash
cd wp-content/plugins/ninja-email-test

# Install dependencies (first time only)
npm install
composer install
```

### Development Workflow

```bash
# Development mode with live reload
npm run dev

# Production build (overwrites pre-compiled assets)
npm run build

# Create distribution package
npm run build && grunt package
```

### File Structure for Customization

- `assets/src/admin/` - Admin React components
- `assets/src/frontend/` - Frontend React components
- `assets/tailwind.config.js` - Tailwind configuration
- `includes/` - PHP backend classes

After making changes, run `npm run build` to compile your custom assets.

## REST API Endpoints

- `GET /wp-json/ninja-email-test/v1/health` - Health check
- `GET /wp-json/ninja-email-test/v1/admin/settings` - Get plugin settings (requires admin)
- `POST /wp-json/ninja-email-test/v1/admin/settings` - Update plugin settings (requires admin)
- `GET /wp-json/ninja-email-test/v1/data` - Get public data
- `POST /wp-json/ninja-email-test/v1/submit` - Submit form data

## Shortcode Usage

Display the frontend form anywhere:

```
[ninja-email-test]
```

With attributes:

```
[ninja-email-test id="123" type="custom"]
```

## File Structure

```
ninja-email-test/
├── assets/
│   ├── src/                # Source files (for customization)
│   │   ├── admin/          # React admin app
│   │   └── frontend/       # React frontend app
│   ├── dist/               # Pre-compiled assets (ready to use)
│   │   ├── css/
│   │   └── js/
│   └── tailwind.config.js
├── includes/
│   ├── Core/               # Core functionality
│   ├── Admin/              # Admin interface
│   ├── Frontend/           # Frontend interface
│   ├── API/                # REST API
│   └── Utils/              # Utilities & helpers
├── languages/              # Translation files
├── composer.json
├── package.json
└── webpack.config.js
```

## Requirements

- WordPress 6.0+
- PHP 7.4+

### For Development Only
- Node.js 14+
- npm or yarn

## License

GPL v2 or later

## Author

Shiv Srivastava - ninjatech.app@gmail.com
