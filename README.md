# Ninja Test Email

**Contributors:** shivknp
**Tags:** email, test-email, smtp, email-logging, email-testing  
**Requires at least:** 6.0  
**Tested up to:** 6.8  
**Requires PHP:** 7.4  
**Stable tag:** 1.0.0  
**License:** GPL-2.0-or-later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html
**Donate link:** https://github.com/1983shiv/ninja-test-email

Test your WordPress email configuration with detailed logging, statistics, and a modern React-powered interface.

## Description

Ninja Test Email is a comprehensive email testing solution for WordPress that helps you verify your site's email configuration is working correctly. Whether you're troubleshooting SMTP issues, testing email deliverability, or monitoring outgoing emails, this plugin provides all the tools you need.

### Why Ninja Test Email?

WordPress relies heavily on email functionality for user notifications, password resets, contact forms, and more. If emails aren't sending properly, your site's core functionality breaks down. Ninja Test Email gives you instant visibility into your email system with detailed logs, success/failure tracking, and comprehensive statistics.

### Who Is This For?

* WordPress administrators troubleshooting email issues
* Developers testing email functionality during development
* Site owners monitoring email deliverability
* Agencies managing multiple WordPress installations
* Anyone who needs to verify SMTP configuration

### How It Works

Ninja Test Email integrates seamlessly into WordPress, logging all outgoing emails automatically while providing a dedicated interface for sending test emails. The plugin uses WordPress's native `wp_mail()` function, ensuring compatibility with any SMTP plugin you're already using (WP Mail SMTP, Easy WP SMTP, Post SMTP, etc.).

### Technical Features

* **Modern React Interface**: Built with React 18 and Tailwind CSS for a responsive, fast admin experience
* **REST API**: Full REST API support for programmatic access to email testing and logs
* **Custom Database Table**: Efficient email log storage with optimized queries
* **Automated Cleanup**: Scheduled daily cron job to remove old logs based on retention settings
* **Translation Ready**: Full internationalization support with 41 translatable strings
* **Shortcode Support**: Frontend email testing via `[ninja-test-email]` shortcode
* **Search & Filtering**: Powerful search across recipient, subject, and email body
* **Statistics Dashboard**: Visual graphs showing success rates, daily volume, and trends

## Features

### Email Testing
* Send plain text test emails
* Send HTML test emails with custom formatting
* Validate email addresses before sending
* Customizable subject and message content
* Default templates for quick testing

### Email Logging
* Automatic logging of all WordPress emails
* Capture recipient, subject, and message body
* Track success/failure status
* Record timestamps for each email
* Store email content for debugging

### Dashboard & Statistics
* Visual success rate graphs
* Total emails sent counter
* Today's email count
* Weekly email statistics
* Monthly email volume
* Status breakdown (sent vs. failed)

### Log Management
* Search across all email fields
* Filter by date, recipient, or status
* Sort by time, recipient, subject, or status
* Paginated results for large datasets
* Individual log deletion
* Bulk log cleanup

### Settings & Configuration
* Configurable log retention period
* Automatic cleanup scheduling
* Daily, weekly, or monthly retention options
* REST API health check endpoint
* Cron status monitoring

### Developer Features
* REST API endpoints for all functions
* Frontend shortcode `[ninja-test-email]`
* Custom action hooks
* Namespaced code (`NinjaTestEmail`)
* Composer autoloading support
* WordPress Coding Standards compliant

### Performance & Security
* Optimized database queries with caching
* Prepared SQL statements for security
* Capability checks (`manage_options` required)
* Nonce verification on all actions
* Input sanitization and output escaping
* ABSPATH security checks

## Installation

### Standard Installation (Recommended)

1. Log in to your WordPress dashboard
2. Navigate to **Plugins > Add New**
3. Search for "Ninja Test Email"
4. Click **Install Now**
5. Click **Activate**
6. Access via **Ninja Email Test** menu in WordPress admin

### Manual Installation

1. Download the plugin ZIP file
2. Upload to `/wp-content/plugins/` directory
3. Extract the ZIP file
4. Activate the plugin through the **Plugins** menu in WordPress
5. Access via **Ninja Email Test** menu

### Using the Shortcode

Add the email testing interface to any page or post:

```
[ninja-test-email]
```

Optional attributes:
* `title="Custom Title"` - Change the form heading
* `button_text="Send"` - Customize the submit button text

### REST API Endpoints

The plugin exposes REST API endpoints under `/wp-json/ninja-test-email/v1/`:

* `GET /health` - API health check
* `GET /admin/settings` - Retrieve settings
* `POST /admin/settings` - Update settings
* `POST /test-email` - Send test email
* `GET /logs` - Retrieve email logs
* `GET /logs/stats` - Get log statistics
* `DELETE /logs/{id}` - Delete specific log

All admin endpoints require the `manage_options` capability.

## Frequently Asked Questions

**Does this plugin send emails or just test them?**

The plugin does both. It automatically logs all emails sent by WordPress (from any plugin or theme) AND provides a dedicated interface for sending test emails to verify your configuration.

**Will this work with my SMTP plugin?**

Yes! Ninja Test Email uses WordPress's native `wp_mail()` function, so it's fully compatible with WP Mail SMTP, Easy WP SMTP, Post SMTP, Mailgun, SendGrid, and any other SMTP plugin.

**How long are email logs stored?**

By default, logs are kept for 30 days. You can configure this in Settings, choosing retention periods from 7 days to unlimited (never delete). The plugin runs a daily cleanup cron job automatically.

**Does this slow down my site?**

No. The plugin uses optimized database queries, caches results where appropriate, and stores logs in a custom table (not post meta). Email logging happens asynchronously and won't delay email sending.

**Can I use this on a production site?**

Absolutely! The plugin is designed for both development and production use. The logging feature helps you monitor email deliverability on live sites, making it easier to catch and fix issues before they affect users.

**What if my emails aren't sending?**

If test emails fail, check:
1. Verify PHP mail() is enabled on your server
2. Install an SMTP plugin (WP Mail SMTP recommended)
3. Check your email logs for error messages
4. Ensure your hosting allows outbound email
5. Test with different recipient addresses

## Screenshots

1. **Dashboard** - Main dashboard showing email statistics with success rate graphs
2. **Send Test Email** - Test email interface with HTML/plain text options
3. **Email Logs** - Comprehensive log viewer with search and filtering
4. **Settings** - Configuration page for log retention and cleanup
5. **Statistics View** - Detailed statistics with daily, weekly, and monthly breakdown
6. **Log Details** - Individual email log with full content and delivery status

## Development

### Requirements
* PHP 7.4 or higher
* WordPress 6.0 or higher
* Node.js 16+ (for building assets)
* Composer (for autoloading)

### Building from Source

```bash
# Install dependencies
npm install
composer install

# Build production assets
npm run build

# Development mode with watch
npm run dev
```

### File Structure

```
ninja-test-email/
├── assets/               # Source files (React, CSS)
│   ├── src/
│   │   ├── admin/       # Admin interface
│   │   └── frontend/    # Frontend shortcode
│   └── tailwind.config.js
├── includes/
│   ├── Admin/           # Admin functionality
│   ├── API/             # REST API endpoints
│   ├── Core/            # Core plugin functionality
│   ├── Frontend/        # Frontend features
│   └── Utils/           # Utility classes
├── languages/           # Translation files
├── vendor/              # Composer dependencies
├── ninja-test-email.php # Main plugin file
└── readme.txt           # WordPress.org readme

```

### Architecture

* **Namespace**: `NinjaTestEmail`
* **Autoloading**: Composer PSR-4
* **Database**: Custom table `wp_ninja_test_email_logs`
* **REST API**: `/wp-json/ninja-test-email/v1/`
* **Shortcode**: `[ninja-test-email]`
* **Cron**: `ninja_test_email_daily_cleanup`

## Changelog

### 1.0.0 - Initial Release

* Email testing functionality (plain text and HTML)
* Automatic email logging for all WordPress emails
* Modern React-powered admin interface
* REST API endpoints for programmatic access
* Dashboard with statistics and success rate graphs
* Email log search and filtering
* Configurable log retention and automatic cleanup
* Daily cron job for old log removal
* Settings page for configuration
* Frontend shortcode `[ninja-test-email]`
* Full internationalization support (41 translatable strings)
* Custom database table for efficient log storage
* Validation and error handling
* WordPress Coding Standards compliance
* Security hardening (prepared statements, capability checks, nonce verification)
* Compatible with all SMTP plugins
* Responsive design with Tailwind CSS

## Support

* **GitHub**: [https://github.com/1983shiv/ninja-test-email](https://github.com/1983shiv/ninja-test-email)
* **WordPress.org**: [Plugin Directory](https://wordpress.org/plugins/ninja-test-email/)

## License

This plugin is licensed under the GPL v2 or later.

```
Ninja Test Email - WordPress Email Testing Plugin
Copyright (C) 2025 Shiv Srivastava

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

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

## Upgrade Notice

### 1.0.0
Initial release. Install to start testing and monitoring your WordPress email functionality with detailed logs and statistics.
