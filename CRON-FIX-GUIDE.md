# Cron Job Fix - Quick Guide

## Issue
The `ninja_test_email_daily_cleanup` cron job is not showing in WP Crontrol after plugin activation.

## Why This Happens
The cron job is only scheduled during plugin **activation**. If the plugin was already active when you updated the files, the activation hook didn't run again.

## Solution - 3 Options

### Option 1: Deactivate and Reactivate (Recommended)
1. Go to: **Plugins** → **Installed Plugins**
2. Find "Ninja Test Email"
3. Click **Deactivate**
4. Click **Activate**
5. The cron job will be scheduled automatically

### Option 2: Update Plugin Files and Auto-Fix
I've added auto-scheduling code that runs on every admin page load:

1. **Update the plugin**:
   - Deactivate the plugin
   - Delete old folder: `wp-content/plugins/ninja-test-email/`
   - Copy new version from: `d:\jobs\wp test email\wp-test-email-rebuilt\build\ninja-test-email\`
   - Paste to: `wp-content/plugins/ninja-test-email/`
   - Activate the plugin

2. **Visit any plugin page**:
   - Go to: **Ninja Email Test** → **Dashboard** or **Settings**
   - The code will automatically schedule the cron if it's missing
   - You'll see a notice showing the cron status

### Option 3: Manual Schedule via Code Snippet
1. Install "Code Snippets" plugin (or add to functions.php)
2. Add this code:

```php
add_action('admin_init', function() {
    if (!wp_next_scheduled('ninja_test_email_daily_cleanup')) {
        wp_schedule_event(time(), 'daily', 'ninja_test_email_daily_cleanup');
        echo '<div class="notice notice-success"><p>Cron job scheduled!</p></div>';
    }
});
```

3. Visit any admin page
4. Remove the code snippet after cron is scheduled

## Verify It's Working

### Check in WP Crontrol
1. Go to: **Tools** → **Cron Events**
2. Search for: `ninja_test_email_daily_cleanup`
3. You should see:
   - **Hook**: ninja_test_email_daily_cleanup
   - **Next Run**: [future timestamp]
   - **Recurrence**: Once Daily

### Check via Admin Notice
After updating (Option 2):
1. Visit: **Ninja Email Test** → **Dashboard**
2. Look for blue notice box at top
3. Should say: **"Cron Status: Daily cleanup is scheduled. Next run: YYYY-MM-DD HH:MM:SS"**

### Verify the Callback is Registered
The cron job is registered to call:
- **Hook**: `ninja_test_email_daily_cleanup`
- **Callback**: `Base::run_daily_cleanup()`
- **Action**: Deletes logs older than 30 days via `LogManager::delete_old_logs(30)`

## Test the Cleanup Function

### Manual Test
1. Use WP Crontrol to run the cron manually:
   - Go to: **Tools** → **Cron Events**
   - Find: `ninja_test_email_daily_cleanup`
   - Click: **Run Now**

2. Check error logs:
   - Should log: "Ninja Test Email: Deleted X old log entries older than 30 days"

### Verify It Deletes Old Logs
1. Insert a test old log:
```sql
INSERT INTO wp_ninja_test_email_logs 
(time, to_email, subject, body, status) 
VALUES 
(DATE_SUB(NOW(), INTERVAL 35 DAY), 'old@test.com', 'Old Test', 'Should be deleted', 'Sent');
```

2. Run the cron manually (via WP Crontrol)

3. Check if deleted:
```sql
SELECT * FROM wp_ninja_test_email_logs WHERE to_email = 'old@test.com';
```
Should return no results.

## Recommended: Use Option 2

**Step-by-step:**

1. **Deactivate** the current plugin
2. **Delete** the old folder from `wp-content/plugins/ninja-test-email/`
3. **Copy** the new build from: `d:\jobs\wp test email\wp-test-email-rebuilt\build\ninja-test-email\`
4. **Paste** to `wp-content/plugins/ninja-test-email/`
5. **Activate** the plugin
6. **Visit** Dashboard page: Ninja Email Test → Dashboard
7. **Look for** the blue notice showing cron status
8. **Verify** in WP Crontrol: Tools → Cron Events

## What Changed in the Update

I added auto-fix code to `includes/Admin/class-admin.php`:

```php
public function verify_cron_scheduled() {
    // Auto-fix: Schedule cron if not scheduled
    if (!wp_next_scheduled('ninja_test_email_daily_cleanup')) {
        wp_schedule_event(time(), 'daily', 'ninja_test_email_daily_cleanup');
    }
}

public function display_admin_notices() {
    // Shows cron status on plugin pages
    $cron_scheduled = wp_next_scheduled('ninja_test_email_daily_cleanup');
    
    if ($cron_scheduled) {
        // Shows next run time
    } else {
        // Shows warning if not scheduled
    }
}
```

This ensures the cron is always scheduled, even if activation hook was missed.

## Expected Result

After following any option above, you should see in **WP Crontrol**:

```
Hook: ninja_test_email_daily_cleanup
Arguments: (none)
Next Run: 2025-11-16 03:00:00 (example)
Recurrence: Once Daily
```

And on the **Dashboard page**, you'll see:

```
ℹ️ Cron Status: Daily cleanup is scheduled. Next run: 2025-11-16 03:00:00
```

## Need Help?

If the cron still doesn't appear after trying all options:
1. Check WordPress error logs
2. Verify WordPress cron isn't disabled in wp-config.php
3. Try scheduling manually via WP Crontrol: Add Cron Event
   - Hook Name: `ninja_test_email_daily_cleanup`
   - Next Run: Tomorrow
   - Recurrence: Once Daily
