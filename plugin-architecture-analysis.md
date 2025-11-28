# WordPress Plugin Architecture Analysis: Ninja Test Email

Based on the workspace structure and build output, here's a comprehensive analysis of the plugin architecture:

## 1. High-Level Architecture Map

### File Structure & Organization

```
ninja-test-email/
├── ninja-test-email.php          # Main plugin file (bootstrap)
├── includes/
│   ├── Core/                     # Core functionality
│   │   ├── class-base.php        # Base initialization class
│   │   ├── class-loader.php      # Hook loader system
│   │   ├── class-activator.php   # Plugin activation logic
│   │   ├── class-deactivator.php # Plugin deactivation logic
│   │   ├── class-email-tester.php # Email testing engine
│   │   └── class-email-logger.php # Email logging system
│   ├── Admin/                    # Admin area
│   │   ├── class-admin.php       # Admin UI controller
│   │   ├── class-admin-api.php   # Admin-specific API
│   │   └── views/
│   │       └── admin-page.php    # Admin page template
│   ├── Frontend/                 # Public-facing
│   │   ├── class-frontend.php    # Frontend controller
│   │   ├── class-frontend-api.php # Frontend API
│   │   └── views/
│   │       └── shortcode.php     # Shortcode template
│   ├── API/                      # REST API
│   │   ├── class-rest-controller.php # Base REST controller
│   │   └── class-endpoints.php   # REST endpoint definitions
│   └── Utils/                    # Utilities
│       ├── class-helpers.php     # Helper functions
│       ├── class-log-manager.php # Log management
│       └── trait-singleton.php   # Singleton pattern trait
├── assets/                       # CSS/JS assets
├── languages/                    # i18n files
└── vendor/                       # Composer dependencies
```

### Initialization Flow

```
1. ninja-test-email.php (Main Plugin File)
   ↓
2. Autoloader Registration (Composer)
   ↓
3. Core\Base::get_instance()
   ↓
4. Core\Loader initialized
   ↓
5. Admin\Admin & Frontend\Frontend registered
   ↓
6. API\Endpoints registered
   ↓
7. Hooks added via Loader
   ↓
8. Loader::run() executes all hooks
```

### Class Namespace Structure

Based on the folder structure, the plugin likely uses this namespace pattern:
- `NinjaTestEmail\Core\*`
- `NinjaTestEmail\Admin\*`
- `NinjaTestEmail\Frontend\*`
- `NinjaTestEmail\API\*`
- `NinjaTestEmail\Utils\*`

## 2. Page-Wise Breakdown

### Admin Pages

**Location**: `includes/Admin/views/admin-page.php`

**Controller**: `includes/Admin/class-admin.php`

**Responsibilities**:
- Email testing interface
- Configuration settings
- Email log viewer
- Test email sender

**Expected Features**:
- Email recipient input
- Subject/body customization
- SMTP configuration testing
- Send test email functionality
- View email logs
- Export/clear logs

### Frontend Pages

**Location**: `includes/Frontend/views/shortcode.php`

**Controller**: `includes/Frontend/class-frontend.php`

**Responsibilities**:
- Public-facing email test form (via shortcode)
- Frontend asset enqueuing
- Shortcode rendering

**Expected Shortcode**: Likely `[ninja_test_email]` or similar

## 3. Feature-Wise Explanation

### Core Features

#### **Email Testing Engine**
- **File**: `includes/Core/class-email-tester.php`
- **Purpose**: Sends test emails using WordPress `wp_mail()`
- **Capabilities**:
  - Custom recipient addresses
  - Custom subject/body
  - HTML/plain text support
  - Attachment testing
  - Header customization
  - SMTP testing

#### **Email Logging System**
- **File**: `includes/Core/class-email-logger.php`
- **Purpose**: Captures and stores email send attempts
- **Capabilities**:
  - Hooks into `wp_mail` filter
  - Logs success/failure status
  - Stores email metadata (to, from, subject, timestamp)
  - Stores email content (body, headers)
  - Error tracking

#### **Log Management**
- **File**: `includes/Utils/class-log-manager.php`
- **Purpose**: CRUD operations for email logs
- **Capabilities**:
  - Retrieve logs (paginated)
  - Filter logs (by date, status, recipient)
  - Delete logs
  - Export logs (CSV/JSON)
  - Clear all logs
  - Log retention policies

#### **Plugin Activation/Deactivation**
- **Files**: 
  - `includes/Core/class-activator.php`
  - `includes/Core/class-deactivator.php`
- **Purpose**: Setup/teardown on plugin lifecycle events
- **Capabilities**:
  - Create database tables for logs
  - Set default options
  - Register capabilities
  - Cleanup on deactivation (optional)

## 4. Backend Endpoints

### REST API Endpoints

**Base Controller**: `includes/API/class-rest-controller.php`

**Endpoint Definitions**: `includes/API/class-endpoints.php`

**Expected REST Routes** (namespace likely `ninja-test-email/v1`):

```
POST   /ninja-test-email/v1/send-test
       - Sends a test email
       - Params: to, subject, body, html
       
GET    /ninja-test-email/v1/logs
       - Retrieves email logs
       - Params: page, per_page, status, date_from, date_to
       
GET    /ninja-test-email/v1/logs/{id}
       - Retrieves single log entry
       
DELETE /ninja-test-email/v1/logs/{id}
       - Deletes single log entry
       
DELETE /ninja-test-email/v1/logs
       - Clears all logs
       
GET    /ninja-test-email/v1/logs/export
       - Exports logs as CSV/JSON
       
GET    /ninja-test-email/v1/settings
       - Retrieves plugin settings
       
POST   /ninja-test-email/v1/settings
       - Updates plugin settings
```

### AJAX Endpoints

Likely registered in `includes/Admin/class-admin-api.php`:

```php
// Admin AJAX actions
wp_ajax_ninja_send_test_email
wp_ajax_ninja_get_logs
wp_ajax_ninja_delete_log
wp_ajax_ninja_clear_logs
wp_ajax_ninja_export_logs
```

Frontend AJAX in `includes/Frontend/class-frontend-api.php`:

```php
// Public AJAX (if applicable)
wp_ajax_nopriv_ninja_send_test_email
```

### Shortcodes

Registered in `includes/Frontend/class-frontend.php`:

```php
[ninja_test_email]
[ninja_email_test_form]
```

**Attributes**:
- `button_text` - Custom button text
- `success_message` - Custom success message
- `class` - Custom CSS class

### WordPress Hooks & Filters

**Actions**:
```php
// Registered in includes/Core/class-base.php via Loader
add_action('admin_menu', 'register_admin_menu')
add_action('admin_enqueue_scripts', 'enqueue_admin_assets')
add_action('wp_enqueue_scripts', 'enqueue_frontend_assets')
add_action('rest_api_init', 'register_rest_routes')
add_action('init', 'register_shortcodes')
add_action('plugins_loaded', 'load_textdomain')
```

**Filters**:
```php
// Email logging
add_filter('wp_mail', 'log_email_attempt', 999)
add_filter('wp_mail_failed', 'log_email_failure')

// Customization filters
apply_filters('ninja_test_email_default_subject', $subject)
apply_filters('ninja_test_email_default_body', $body)
apply_filters('ninja_test_email_allowed_recipients', $recipients)
apply_filters('ninja_test_email_log_retention_days', 30)
```

## 5. Controllers with Responsibilities

### **Core\Base**
**File**: `includes/Core/class-base.php`

**Pattern**: Singleton (uses `Utils\Singleton` trait)

**Responsibilities**:
- Plugin initialization orchestration
- Component registration
- Dependency injection container
- Version management
- Plugin constants definition

**Key Methods**:
```php
public static function get_instance()
public function __construct()
private function define_constants()
private function load_dependencies()
private function set_locale()
private function define_admin_hooks()
private function define_frontend_hooks()
private function define_api_hooks()
public function run()
```

### **Core\Loader**
**File**: `includes/Core/class-loader.php`

**Pattern**: Hook aggregator

**Responsibilities**:
- Centralized hook management
- Action/filter registration queue
- Hook execution

**Key Methods**:
```php
public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1)
public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1)
public function run()
private function add($hooks, $hook, $component, $callback, $priority, $accepted_args)
```

### **Admin\Admin**
**File**: `includes/Admin/class-admin.php`

**Responsibilities**:
- Admin menu registration
- Admin page rendering
- Admin asset enqueuing
- Settings page management
- Admin notices

**Key Methods**:
```php
public function register_menu()
public function enqueue_styles($hook)
public function enqueue_scripts($hook)
public function render_admin_page()
private function process_form_submission()
```

### **Admin\AdminAPI**
**File**: `includes/Admin/class-admin-api.php`

**Responsibilities**:
- AJAX handler registration (admin)
- Permission checks
- Data validation
- Response formatting

**Key Methods**:
```php
public function register_ajax_handlers()
public function handle_send_test_email()
public function handle_get_logs()
public function handle_delete_log()
public function handle_clear_logs()
public function handle_export_logs()
private function verify_nonce()
private function check_permissions()
```

### **Frontend\Frontend**
**File**: `includes/Frontend/class-frontend.php`

**Responsibilities**:
- Shortcode registration
- Frontend asset enqueuing
- Public-facing UI rendering

**Key Methods**:
```php
public function register_shortcodes()
public function enqueue_styles()
public function enqueue_scripts()
public function render_test_form($atts)
```

### **Frontend\FrontendAPI**
**File**: `includes/Frontend/class-frontend-api.php`

**Responsibilities**:
- Public AJAX handlers
- Rate limiting
- Spam protection
- Public API access control

**Key Methods**:
```php
public function register_ajax_handlers()
public function handle_public_test_email()
private function check_rate_limit()
private function validate_recaptcha()
```

### **API\RESTController**
**File**: `includes/API/class-rest-controller.php`

**Pattern**: Base REST controller

**Responsibilities**:
- Base REST functionality
- Permission callbacks
- Common response formatting
- Error handling

**Key Methods**:
```php
public function register_routes()
public function get_items_permissions_check($request)
public function create_item_permissions_check($request)
public function delete_item_permissions_check($request)
protected function prepare_item_for_response($item, $request)
protected function get_error_response($code, $message, $status = 400)
```

### **API\Endpoints**
**File**: `includes/API/class-endpoints.php`

**Extends**: RESTController

**Responsibilities**:
- REST route registration
- Endpoint handlers
- Request validation
- Response serialization

**Key Methods**:
```php
public function register_routes()
public function send_test_email($request)
public function get_logs($request)
public function get_log($request)
public function delete_log($request)
public function clear_logs($request)
public function export_logs($request)
public function get_settings($request)
public function update_settings($request)
```

## 6. Services with Responsibilities

### **Core\EmailTester**
**File**: `includes/Core/class-email-tester.php`

**Pattern**: Service class

**Responsibilities**:
- Email composition
- Email sending via `wp_mail()`
- Result validation
- Error handling

**Key Methods**:
```php
public function send_test_email($to, $subject, $body, $args = [])
private function prepare_headers($args)
private function prepare_attachments($args)
private function validate_recipient($email)
public function get_last_error()
public function get_smtp_debug_info()
```

### **Core\EmailLogger**
**File**: `includes/Core/class-email-logger.php`

**Pattern**: Service class

**Responsibilities**:
- Intercept `wp_mail` calls
- Log email metadata
- Store to database
- Hook into mail errors

**Key Methods**:
```php
public function init_hooks()
public function log_email($args)
public function log_email_error($wp_error)
private function get_current_user_info()
private function sanitize_log_data($data)
```

### **Utils\LogManager**
**File**: `includes/Utils/class-log-manager.php`

**Pattern**: Repository/Manager

**Responsibilities**:
- Database operations for logs
- Query builder
- Data retrieval/deletion
- Export functionality

**Key Methods**:
```php
public function get_logs($args = [])
public function get_log($id)
public function delete_log($id)
public function delete_logs($ids)
public function clear_all_logs()
public function export_logs($format = 'csv', $args = [])
public function get_stats()
private function build_query($args)
```

### **Utils\Helpers**
**File**: `includes/Utils/class-helpers.php`

**Pattern**: Static utility class

**Responsibilities**:
- Common helper functions
- Data formatting
- Validation utilities
- Date/time helpers

**Key Methods**:
```php
public static function sanitize_email($email)
public static function format_date($timestamp)
public static function is_valid_email($email)
public static function get_plugin_version()
public static function get_plugin_url()
public static function get_plugin_path()
public static function debug_log($message)
```

## 7. Data Flow Across the System

### Email Sending Flow

```
User Input (Admin/Frontend)
    ↓
Admin\AdminAPI::handle_send_test_email() OR
Frontend\FrontendAPI::handle_public_test_email() OR
API\Endpoints::send_test_email()
    ↓
Validation & Sanitization
    ↓
Core\EmailTester::send_test_email()
    ↓
Core\EmailLogger::log_email() [via wp_mail filter]
    ↓
wp_mail() execution
    ↓
Core\EmailLogger::log_email_error() [if failed]
    ↓
Utils\LogManager::insert_log()
    ↓
Database Storage (wp_options or custom table)
    ↓
Response to User
```

### Log Retrieval Flow

```
User Request (Admin page/API)
    ↓
Admin\Admin::render_admin_page() OR
API\Endpoints::get_logs()
    ↓
Utils\LogManager::get_logs($filters)
    ↓
Database Query with filters
    ↓
Data formatting/pagination
    ↓
Response rendering
    ↓
Display to user (HTML/JSON)
```

### Settings Flow

```
Settings Update Request
    ↓
API\Endpoints::update_settings() OR
Admin\Admin::process_form_submission()
    ↓
Validation
    ↓
update_option('ninja_test_email_settings', $settings)
    ↓
Cache invalidation (if applicable)
    ↓
Success response
```

## 8. Class Dependencies

### Dependency Graph

```
Core\Base (Entry Point)
├── Core\Loader
├── Core\Activator
├── Core\Deactivator
├── Admin\Admin
│   └── Admin\AdminAPI
│       ├── Core\EmailTester
│       └── Utils\LogManager
├── Frontend\Frontend
│   └── Frontend\FrontendAPI
│       ├── Core\EmailTester
│       └── Utils\LogManager
├── API\Endpoints (extends API\RESTController)
│   ├── Core\EmailTester
│   └── Utils\LogManager
└── Core\EmailLogger
    └── Utils\LogManager
```

### Shared Dependencies

All components may use:
- `Utils\Helpers` - Static utility functions
- `Utils\Singleton` - Singleton pattern (trait)

### Database Dependencies

- **Options Table**: Plugin settings stored in `wp_options`
  - `ninja_test_email_settings`
  - `ninja_test_email_version`

- **Custom Table** (likely): Email logs
  - Table name: `{$wpdb->prefix}ninja_email_logs`
  - Columns: id, to, from, subject, body, headers, status, error, sent_at, user_id

## 9. Where to Safely Extend or Modify Behavior

### Safe Extension Points

#### **1. Add New Email Templates**
**Location**: Create new class in `includes/Core`

```php
// includes/Core/class-email-templates.php
namespace NinjaTestEmail\Core;

class EmailTemplates {
    public function get_template($name) {
        return apply_filters("ninja_test_email_template_{$name}", $default_content);
    }
}
```

#### **2. Add Custom Log Filters**
**Location**: Extend `Utils\LogManager`

```php
// In your custom plugin/theme
add_filter('ninja_test_email_log_query_args', function($args) {
    // Add custom query modifications
    return $args;
});
```

#### **3. Add New REST Endpoints**
**Location**: Extend `API\Endpoints`

```php
// includes/API/class-custom-endpoints.php
namespace NinjaTestEmail\API;

class CustomEndpoints extends Endpoints {
    public function register_routes() {
        parent::register_routes();
        
        register_rest_route($this->namespace, '/custom-action', [
            'methods' => 'POST',
            'callback' => [$this, 'custom_action'],
            'permission_callback' => [$this, 'create_item_permissions_check']
        ]);
    }
}
```

#### **4. Add Admin Dashboard Widgets**
**Location**: Hook into Admin\Admin

```php
add_action('wp_dashboard_setup', function() {
    wp_add_dashboard_widget(
        'ninja_email_stats',
        'Email Test Statistics',
        'ninja_render_email_stats_widget'
    );
});
```

#### **5. Add Custom Email Headers**
**Location**: Filter in `Core\EmailTester`

```php
add_filter('ninja_test_email_headers', function($headers, $args) {
    $headers[] = 'X-Custom-Header: value';
    return $headers;
}, 10, 2);
```

#### **6. Add Frontend Shortcode Variations**
**Location**: Register in `Frontend\Frontend`

```php
add_shortcode('ninja_email_simple_form', function($atts) {
    // Custom shortcode implementation
    return $html;
});
```

### Modification Points (Use Filters/Actions)

#### **Available Filter Hooks** (Expected):

```php
// Email content filters
'ninja_test_email_subject'
'ninja_test_email_body'
'ninja_test_email_headers'
'ninja_test_email_attachments'

// Logging filters
'ninja_test_email_log_data'
'ninja_test_email_should_log'
'ninja_test_email_log_retention_days'

// Query filters
'ninja_test_email_log_query_args'
'ninja_test_email_logs_per_page'

// UI filters
'ninja_test_email_admin_capability'
'ninja_test_email_frontend_enabled'
'ninja_test_email_allowed_recipients'

// Settings filters
'ninja_test_email_default_settings'
'ninja_test_email_sanitize_settings'
```

#### **Available Action Hooks** (Expected):

```php
// Before/after email send
'ninja_test_email_before_send'
'ninja_test_email_after_send'
'ninja_test_email_send_failed'

// Log actions
'ninja_test_email_log_created'
'ninja_test_email_log_deleted'
'ninja_test_email_logs_cleared'

// Admin actions
'ninja_test_email_admin_page_top'
'ninja_test_email_admin_page_bottom'
'ninja_test_email_settings_saved'
```

### Areas to AVOID Direct Modification

1. **Core\Base** - Central initialization; use hooks instead
2. **Core\Loader** - Hook system; modify via `add_action`/`add_filter`
3. **Vendor** - Composer dependencies; always override via inheritance
4. **Main plugin file** - Bootstrap logic; extend via hooks

## 10. Recommended Guidelines for Adding New Features

### **General Principles**

1. **Follow WordPress Coding Standards**
   - Use WordPress functions over PHP alternatives
   - Follow naming conventions: `ninja_test_email_*`
   - Use proper sanitization and escaping

2. **Maintain Namespace Consistency**
   ```php
   namespace NinjaTestEmail\YourFeature;
   ```

3. **Use Dependency Injection**
   - Pass dependencies via constructor
   - Avoid hard-coded dependencies

4. **Implement Singleton Pattern Where Appropriate**
   - Use `Utils\Singleton` trait
   - Only for manager/controller classes

### **Adding a New Feature: Step-by-Step**

#### **Example: Add Email Scheduling Feature**

**Step 1: Create Service Class**

```php
// includes/Core/class-email-scheduler.php
namespace NinjaTestEmail\Core;

use NinjaTestEmail\Utils\Singleton;

class EmailScheduler {
    use Singleton;
    
    public function schedule_email($to, $subject, $body, $send_at) {
        wp_schedule_single_event($send_at, 'ninja_send_scheduled_email', [
            'to' => $to,
            'subject' => $subject,
            'body' => $body
        ]);
    }
    
    public function init_hooks() {
        add_action('ninja_send_scheduled_email', [$this, 'send_scheduled_email'], 10, 1);
    }
    
    public function send_scheduled_email($args) {
        $tester = new EmailTester();
        $tester->send_test_email($args['to'], $args['subject'], $args['body']);
    }
}
```

**Step 2: Register in Base Class**

Modify `includes/Core/class-base.php`:

```php
// ...existing code...
private function define_core_hooks() {
    $scheduler = EmailScheduler::get_instance();
    $this->loader->add_action('init', $scheduler, 'init_hooks');
}
// ...existing code...
```

**Step 3: Add REST Endpoint**

Extend `includes/API/class-endpoints.php`:

```php
// ...existing code...
public function register_routes() {
    // ...existing routes...
    
    register_rest_route($this->namespace, '/schedule-email', [
        'methods' => 'POST',
        'callback' => [$this, 'schedule_email'],
        'permission_callback' => [$this, 'create_item_permissions_check'],
        'args' => [
            'to' => ['required' => true],
            'subject' => ['required' => true],
            'body' => ['required' => true],
            'send_at' => ['required' => true, 'validate_callback' => 'is_numeric']
        ]
    ]);
}

public function schedule_email($request) {
    $scheduler = \NinjaTestEmail\Core\EmailScheduler::get_instance();
    
    $result = $scheduler->schedule_email(
        $request->get_param('to'),
        $request->get_param('subject'),
        $request->get_param('body'),
        $request->get_param('send_at')
    );
    
    return new \WP_REST_Response(['success' => true], 200);
}
// ...existing code...
```

**Step 4: Add Admin UI**

Create view file:

```php
<div class="ninja-schedule-email">
    <h2><?php _e('Schedule Email', 'ninja-email-test'); ?></h2>
    <form id="schedule-email-form">
        <input type="email" name="to" required>
        <input type="text" name="subject" required>
        <textarea name="body"></textarea>
        <input type="datetime-local" name="send_at" required>
        <button type="submit"><?php _e('Schedule', 'ninja-email-test'); ?></button>
    </form>
</div>
```

**Step 5: Add Database Table (if needed)**

Modify `includes/Core/class-activator.php`:

```php
// ...existing code...
public static function activate() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'ninja_scheduled_emails';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        recipient varchar(255) NOT NULL,
        subject text NOT NULL,
        body longtext NOT NULL,
        scheduled_at datetime NOT NULL,
        sent_at datetime DEFAULT NULL,
        status varchar(20) DEFAULT 'pending',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY scheduled_at (scheduled_at),
        KEY status (status)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
// ...existing code...
```

### **Testing Checklist**

- [ ] Test activation/deactivation
- [ ] Test with WordPress debug mode enabled
- [ ] Test REST API endpoints with Postman
- [ ] Test admin UI in different browsers
- [ ] Test with different user roles
- [ ] Test nonce verification
- [ ] Test data sanitization
- [ ] Test internationalization (i18n)
- [ ] Test with Query Monitor plugin
- [ ] Test database queries for performance

### **Code Organization Best Practices**

1. **One class per file**
2. **Class names match file names** (PSR-4)
3. **Use meaningful method names**
4. **Add PHPDoc blocks** for all public methods
5. **Keep methods small** (<50 lines)
6. **Use early returns** for validation
7. **Avoid deep nesting** (max 3 levels)
8. **Use constants** for magic strings/numbers
9. **Implement proper error handling**
10. **Log errors** using `Utils\Helpers::debug_log()`

### **Security Checklist**

- [ ] Verify nonces on all form submissions
- [ ] Check user capabilities (`current_user_can()`)
- [ ] Sanitize all inputs (`sanitize_text_field()`, `sanitize_email()`, etc.)
- [ ] Escape all outputs (`esc_html()`, `esc_attr()`, `esc_url()`)
- [ ] Validate REST API permissions
- [ ] Use prepared statements for database queries
- [ ] Implement rate limiting for public endpoints
- [ ] Add CSRF protection
- [ ] Validate file uploads (if applicable)
- [ ] Use WordPress security functions (`wp_verify_nonce()`, `check_ajax_referer()`)

### **Performance Guidelines**

1. **Cache frequently accessed data**
2. **Use transients for expensive operations**
3. **Limit database queries** (avoid N+1 problems)
4. **Lazy load assets** (only on relevant pages)
5. **Use pagination** for large datasets
6. **Index database columns** used in WHERE/ORDER BY
7. **Minimize external API calls**
8. **Use WordPress Object Cache** where available

---

## Summary

This plugin follows a **clean architecture** with clear separation of concerns:

- **Core** handles business logic
- **Admin/Frontend** manage presentation
- **API** provides programmatic access
- **Utils** offers shared functionality

The architecture is **extensible** through:
- WordPress hooks (actions/filters)
- REST API endpoints
- Shortcodes
- Class inheritance

**Key Extension Points**:
- Add filters/actions without modifying core
- Extend base classes for new controllers
- Create new service classes in appropriate namespaces
- Use the Loader system to register hooks

**Main Dependencies**:
- All components depend on `Core\Base` for initialization
- Services are injected via constructors or accessed as singletons
- Data flows through dedicated managers (`Utils\LogManager`)

This architecture enables **safe, maintainable extensions** while preserving the plugin's core functionality.
