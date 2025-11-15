# Phase 3 Testing Guide: Email Logging System

## Overview
Phase 3 adds comprehensive email logging functionality to the Ninja Test Email plugin. All outgoing emails from WordPress are now automatically captured and stored in the database, with automatic cleanup after 30 days.

## What's New in Phase 3

### ✨ Features Implemented
1. **Automatic Email Logging** - All emails sent via `wp_mail()` are captured
2. **Database Storage** - Email logs stored in custom database table
3. **Statistics Dashboard** - Real-time counts of logged emails
4. **Automatic Cleanup** - Daily cron job removes logs older than 30 days
5. **REST API Endpoint** - `/logs/stats` endpoint for retrieving statistics

### 🔧 Components Added
- `Utils\LogManager` - Database operations for email logs
- `Core\EmailLogger` - Service that hooks into `phpmailer_init`
- Cron job for daily cleanup
- REST API endpoint for statistics
- Updated dashboard with real email counts

---

## Installation & Setup

### If You Already Have Phase 1 & 2 Installed

**Option 1: Update Existing Installation**
1. Deactivate the plugin: Plugins → Deactivate "Ninja Test Email"
2. Delete the old plugin folder: `wp-content/plugins/ninja-test-email/`
3. Copy the new version from: `d:\jobs\wp test email\wp-test-email-rebuilt\build\ninja-test-email\`
4. Paste into: `wp-content/plugins/`
5. Reactivate the plugin

**Option 2: Fresh Installation**
1. If installed, deactivate and delete the old version
2. Install fresh from the build folder
3. Activate the plugin

### Verify Phase 3 Installation

1. **Check for New Cron Job**
   - Install "WP Crontrol" plugin (optional, for verification)
   - Go to: Tools → Cron Events
   - Look for: `ninja_test_email_daily_cleanup`
   - Schedule should be: Daily

2. **Verify Database Table**
   - Open your database (phpMyAdmin)
   - Table `wp_ninja_test_email_logs` should exist
   - Check it has columns: id, time, to_email, subject, body, status

---

## Phase 3 Testing Checklist

### ✅ Test 1: Email Logging from Test Form

**Purpose**: Verify that emails sent through the test form are logged

1. **Navigate to Dashboard**
   - Go to: Ninja Email Test → Dashboard

2. **Send a Test Email**
   - Recipient: your email address
   - Subject: `Phase 3 Test - Email Logging`
   - Message: `Testing email logging functionality`
   - Format: Plain Text
   - Click "Send Test Email"

3. **Check Database**
   - Open phpMyAdmin or database tool
   - Query: `SELECT * FROM wp_ninja_test_email_logs ORDER BY id DESC LIMIT 1`
   - **Expected Result**:
     ```
     - to_email: your email
     - subject: Phase 3 Test - Email Logging
     - body: Testing email logging functionality
     - status: Sent
     - time: current timestamp
     ```

4. **Verify Dashboard Stats Updated**
   - Refresh the Dashboard page
   - **Expected Result**:
     - "Total Emails Logged" should increase by 1
     - "Sent Today" should increase by 1
     - "Sent This Week" should increase by 1

**✓ Pass Criteria**: Email is logged in database AND dashboard stats reflect the new count

---

### ✅ Test 2: Logging WordPress Core Emails

**Purpose**: Verify that emails sent by WordPress core are also logged

1. **Trigger WordPress Password Reset Email**
   - Log out of WordPress
   - Go to login page: `wp-login.php`
   - Click "Lost your password?"
   - Enter your admin email
   - Click "Get New Password"

2. **Check Database**
   - Query: `SELECT * FROM wp_ninja_test_email_logs ORDER BY id DESC LIMIT 1`
   - **Expected Result**:
     ```
     - to_email: your admin email
     - subject: [Your Site Name] Password Reset
     - body: Contains password reset link
     - status: Sent
     ```

3. **Verify Dashboard Updated**
   - Log back in
   - Go to Dashboard
   - Stats should show +1 email

**✓ Pass Criteria**: WordPress core emails are captured in logs

---

### ✅ Test 3: Multiple Email Logging

**Purpose**: Verify accurate counting with multiple emails

1. **Send 5 Test Emails**
   - Send 5 different test emails from the dashboard form
   - Use different subjects/content for each

2. **Check Database Count**
   - Query: `SELECT COUNT(*) FROM wp_ninja_test_email_logs`
   - **Expected**: Count should match total emails sent

3. **Verify Dashboard Shows Correct Total**
   - Dashboard should display accurate count
   - "Total Emails Logged" = database count

**✓ Pass Criteria**: All 5 emails logged, stats are accurate

---

### ✅ Test 4: REST API Statistics Endpoint

**Purpose**: Test the `/logs/stats` API endpoint

1. **Get Nonce from Browser Console**
   - On Dashboard page, open DevTools (F12)
   - In Console, type: `ninjaemailtestAdmin.nonce`
   - Copy the nonce value

2. **Test with Browser or REST Client**
   
   **Method 1: Browser**
   - Navigate to: `https://your-site.com/wp-json/ninja-test-email/v1/logs/stats`
   - Must be logged in as admin
   
   **Method 2: Postman/Insomnia**
   - Method: GET
   - URL: `https://your-site.com/wp-json/ninja-test-email/v1/logs/stats`
   - Headers:
     ```
     X-WP-Nonce: [your nonce value]
     ```

3. **Expected Response**
   ```json
   {
     "success": true,
     "stats": {
       "total": 10,
       "today": 5,
       "week": 8,
       "month": 10,
       "by_status": {
         "Sent": {
           "status": "Sent",
           "count": "10"
         }
       }
     }
   }
   ```

**✓ Pass Criteria**: API returns valid statistics matching database

---

### ✅ Test 5: Automatic Email Capturing (phpmailer_init Hook)

**Purpose**: Verify the EmailLogger hooks into phpmailer_init correctly

1. **Send Email via Plugin/Theme**
   - If you have a contact form plugin (Contact Form 7, WPForms, etc.):
     - Submit a contact form
   - OR trigger a WordPress email:
     - Post a comment (if moderation emails are enabled)
     - Add a new user (triggers new user notification)

2. **Check Logs**
   - Query database for new entry
   - Should contain the email details

3. **Dashboard Stats**
   - Stats should increment automatically

**✓ Pass Criteria**: Emails from any source are captured

---

### ✅ Test 6: Log Statistics Breakdown

**Purpose**: Verify statistics calculations are accurate

1. **Check Today's Count**
   - Send an email
   - Verify "Sent Today" increments
   - Database query to verify:
     ```sql
     SELECT COUNT(*) FROM wp_ninja_test_email_logs 
     WHERE DATE(time) = CURDATE()
     ```

2. **Check Week Count**
   - "Sent This Week" should show emails from last 7 days
   - Verify with query:
     ```sql
     SELECT COUNT(*) FROM wp_ninja_test_email_logs 
     WHERE time >= DATE_SUB(NOW(), INTERVAL 7 DAY)
     ```

3. **Check Month Count**
   - "Sent This Month" would show last 30 days
   - (This stat is calculated but not displayed on current dashboard)

**✓ Pass Criteria**: Statistics match database queries

---

### ✅ Test 7: Cron Job Scheduling

**Purpose**: Verify daily cleanup cron job is scheduled

1. **Check Cron Schedule**
   
   **Method 1: Using WP Crontrol Plugin**
   - Install "WP Crontrol" plugin
   - Go to: Tools → Cron Events
   - Find: `ninja_test_email_daily_cleanup`
   - Should show:
     - Hook: `ninja_test_email_daily_cleanup`
     - Next Run: [timestamp]
     - Recurrence: Once Daily

   **Method 2: Via Code**
   - Add this temporarily to functions.php:
     ```php
     $timestamp = wp_next_scheduled('ninja_test_email_daily_cleanup');
     echo 'Next cleanup: ' . date('Y-m-d H:i:s', $timestamp);
     ```

2. **Verify Cron Callback Registered**
   - The cron should call `Base::run_daily_cleanup()`
   - Which calls `LogManager::delete_old_logs(30)`

**✓ Pass Criteria**: Cron job is scheduled and properly configured

---

### ✅ Test 8: Manual Cleanup Test

**Purpose**: Test the cleanup functionality manually

1. **Create Old Log Entry Manually**
   - Run this SQL in your database:
     ```sql
     INSERT INTO wp_ninja_test_email_logs 
     (time, to_email, subject, body, status) 
     VALUES 
     (DATE_SUB(NOW(), INTERVAL 35 DAY), 'old@test.com', 'Old Email', 'This should be deleted', 'Sent');
     ```

2. **Trigger Manual Cleanup**
   - Add this code temporarily to test:
     ```php
     // In your theme's functions.php or use Code Snippets plugin
     add_action('init', function() {
       if (isset($_GET['test_cleanup']) && current_user_can('manage_options')) {
         $deleted = \NinjaTestEmail\Utils\LogManager::delete_old_logs(30);
         echo "Deleted {$deleted} old logs";
         exit;
       }
     });
     ```
   - Visit: `https://your-site.com/?test_cleanup`

3. **Verify Old Entry Deleted**
   - Query database: `SELECT * FROM wp_ninja_test_email_logs WHERE to_email = 'old@test.com'`
   - **Expected**: No results (old entry deleted)
   - Recent logs should remain

**✓ Pass Criteria**: Logs older than 30 days are deleted, recent ones remain

---

### ✅ Test 9: Dashboard Auto-Refresh After Sending

**Purpose**: Verify stats update after sending test email

1. **Note Current Stats**
   - On Dashboard, note the current numbers

2. **Send Test Email**
   - Send via the form
   - Wait for success message

3. **Verify Stats Updated**
   - WITHOUT refreshing page, stats should update
   - Counts should increase immediately

**✓ Pass Criteria**: Stats refresh automatically after email sent

---

### ✅ Test 10: Deactivation Cleanup

**Purpose**: Verify cron job is removed on deactivation

1. **Before Deactivation**
   - Check cron exists: `wp_next_scheduled('ninja_test_email_daily_cleanup')`
   - Should return a timestamp

2. **Deactivate Plugin**
   - Go to: Plugins → Deactivate "Ninja Test Email"

3. **Check Cron Removed**
   - Using WP Crontrol or code:
     ```php
     $timestamp = wp_next_scheduled('ninja_test_email_daily_cleanup');
     // Should return false
     ```

4. **Reactivate Plugin**
   - Cron should be rescheduled automatically

**✓ Pass Criteria**: Cron is removed on deactivation, re-added on activation

---

## Common Issues & Troubleshooting

### Issue: Stats Show 0 Despite Emails in Database
**Solution**:
- Clear browser cache (Ctrl+F5)
- Check browser console for JavaScript errors
- Verify `/logs/stats` endpoint returns data
- Check nonce is valid: `ninjaemailtestAdmin.nonce`

### Issue: Emails Not Being Logged
**Solution**:
- Verify `phpmailer_init` hook is firing:
  ```php
  add_action('phpmailer_init', function() {
      error_log('phpmailer_init fired');
  });
  ```
- Check database table exists
- Verify EmailLogger is instantiated in Base class
- Check error logs for database errors

### Issue: Cron Job Not Running
**Solution**:
- Verify WordPress cron is working:
  - WP cron requires site visits to trigger
  - Consider using system cron instead
- Install "WP Crontrol" to manually run the job
- Check if cron is disabled in wp-config.php

### Issue: Old Logs Not Deleted
**Solution**:
- Manually trigger cleanup (see Test 8)
- Check cron is scheduled correctly
- Verify date calculation in `delete_old_logs()` method
- Check database permissions

### Issue: Dashboard Doesn't Update After Email
**Solution**:
- Check network tab for `/logs/stats` request
- Verify API response is successful
- Check `fetchLogStats()` is called after email sent
- Verify nonce is valid

---

## Database Queries for Testing

### View All Logs
```sql
SELECT * FROM wp_ninja_test_email_logs 
ORDER BY time DESC;
```

### Count Today's Emails
```sql
SELECT COUNT(*) FROM wp_ninja_test_email_logs 
WHERE DATE(time) = CURDATE();
```

### View Logs from Last 7 Days
```sql
SELECT * FROM wp_ninja_test_email_logs 
WHERE time >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY time DESC;
```

### Find Logs by Email Address
```sql
SELECT * FROM wp_ninja_test_email_logs 
WHERE to_email LIKE '%youremail@example.com%'
ORDER BY time DESC;
```

### Delete All Test Logs
```sql
TRUNCATE TABLE wp_ninja_test_email_logs;
```

---

## Phase 3 Success Criteria

### ✅ Must Pass All:
- [ ] Test emails are logged to database
- [ ] WordPress core emails are captured
- [ ] Dashboard statistics display correctly
- [ ] Stats update in real-time after sending
- [ ] REST API `/logs/stats` endpoint works
- [ ] Multiple emails are counted accurately
- [ ] Cron job is scheduled properly
- [ ] Cleanup function deletes old logs (30+ days)
- [ ] Emails from any source (plugins, themes, core) are logged
- [ ] No JavaScript console errors
- [ ] No PHP errors in logs

### 📊 Performance Checks:
- [ ] Dashboard loads quickly (<2 seconds)
- [ ] Sending email + logging doesn't slow down sending
- [ ] Statistics endpoint responds quickly (<500ms)
- [ ] Database queries are optimized (no N+1 queries)

---

## What's Coming in Phase 4

Phase 4 will add:
- **Email Logs Viewer** - Full table view of all logged emails
- **Search Functionality** - Search across recipient, subject, body
- **Sorting** - Sort by time, recipient, subject, status
- **Pagination** - Navigate through logs (10 per page)
- **View Email Body** - Modal popup to view full email content
- **Delete Individual Logs** - Remove specific log entries
- **Better UI** - Professional table design with Tailwind CSS

---

## Test Report Template

```
## Phase 3 Test Report

**Date**: [Date]
**Tester**: [Name]
**WordPress Version**: [version]
**PHP Version**: [version]

### Core Functionality
- [ ] Email logging works: PASS / FAIL
- [ ] Statistics accurate: PASS / FAIL  
- [ ] Dashboard updates: PASS / FAIL
- [ ] REST API works: PASS / FAIL
- [ ] Cron scheduled: PASS / FAIL

### Database
- [ ] Logs inserted correctly: PASS / FAIL
- [ ] Old logs deleted: PASS / FAIL
- [ ] Counts match stats: PASS / FAIL

### Issues Found
1. [Issue]
2. [Issue]

### Overall Phase 3 Status: PASS / FAIL
```

---

## Support & Next Steps

✅ **Phase 3 Complete When:**
- All tests pass
- Email logging is reliable
- Statistics are accurate
- No errors in console or logs

🚀 **Ready for Phase 4?**
- Once Phase 3 is verified and working
- Phase 4 will build the logs viewer interface
- Users will be able to see, search, and manage all logged emails
