# Phase 1 & 2 Testing Guide: Ninja Test Email Plugin

## Overview
This document provides comprehensive testing instructions for Phase 1 (Setup and Core Structure) and Phase 2 (Email Testing Functionality) of the Ninja Test Email plugin.

## Prerequisites
- WordPress installation (5.0 or higher)
- PHP 7.4 or higher
- Administrator access to WordPress
- Access to email inbox for the test recipient

## Phase 1: Setup and Core Structure

### Installation Steps

1. **Locate the Plugin Files**
   - Navigate to: `d:\jobs\wp test email\wp-test-email-rebuilt\build\ninja-test-email\`
   - This folder contains the production-ready plugin

2. **Install the Plugin**
   
   **Option A: Manual Installation**
   - Copy the entire `ninja-test-email` folder from the build directory
   - Paste it into your WordPress plugins directory: `wp-content/plugins/`
   - The final path should be: `wp-content/plugins/ninja-test-email/`

   **Option B: ZIP Installation**
   - Compress the `ninja-test-email` folder into a ZIP file
   - In WordPress admin, go to: Plugins → Add New → Upload Plugin
   - Choose the ZIP file and click "Install Now"

3. **Activate the Plugin**
   - Go to: Plugins → Installed Plugins
   - Find "Ninja Test Email" in the list
   - Click "Activate"

### Phase 1 Testing Checklist

#### ✅ Database Table Creation
1. **Verify Table Creation**
   - Access your WordPress database (via phpMyAdmin or similar)
   - Look for a table named: `wp_ninja_test_email_logs` (prefix may vary)
   - Confirm the table has these columns:
     - `id` (mediumint, primary key, auto-increment)
     - `time` (datetime)
     - `to_email` (varchar 100)
     - `subject` (varchar 255)
     - `body` (text)
     - `status` (varchar 20)

#### ✅ Plugin Options
1. **Check Plugin Options**
   - In WordPress admin, run this in the browser console or via a plugin like "WP Console":
     ```php
     get_option('ninja_test_email_options');
     ```
   - Should return an array with:
     ```php
     array(
         'enabled' => true,
         'admin_capability' => 'manage_options'
     )
     ```

#### ✅ Admin Menu Registration
1. **Verify Menu Structure**
   - In WordPress admin sidebar, look for "Ninja Email Test" menu item
   - Icon should be visible (dashicons-admin-generic)
   - Click on the menu item to expand it
   - Verify two submenu items exist:
     - "Dashboard"
     - "Settings"

2. **Test Dashboard Page**
   - Click on "Ninja Email Test" → "Dashboard"
   - URL should be: `wp-admin/admin.php?page=ninja-email-test`
   - Page should load without errors
   - Verify the page displays properly styled content

3. **Test Settings Page**
   - Click on "Ninja Email Test" → "Settings"
   - URL should be: `wp-admin/admin.php?page=ninja-email-test-settings`
   - Page should load without errors

#### ✅ Asset Loading
1. **Check JavaScript Loading**
   - On the Dashboard or Settings page, open browser DevTools (F12)
   - Go to Network tab, filter by JS
   - Verify `admin.js` is loaded from: `wp-content/plugins/ninja-test-email/assets/dist/js/`
   - Check Console tab for any JavaScript errors

2. **Check CSS Loading**
   - In Network tab, filter by CSS
   - Verify `admin.css` is loaded from: `wp-content/plugins/ninja-test-email/assets/dist/css/`
   - Page should have proper Tailwind CSS styling

3. **Check React Initialization**
   - In Console tab, type: `ninjaemailtestAdmin`
   - Should display an object with properties:
     - `ajaxUrl`
     - `restUrl`
     - `nonce`
     - `currentPage`
     - `userEmail`

#### ✅ REST API Endpoints
1. **Test Health Check Endpoint**
   - Open a new browser tab
   - Navigate to: `https://your-site.com/wp-json/ninja-test-email/v1/health`
   - Should return JSON with:
     ```json
     {
       "status": "ok",
       "version": "1.0.0",
       "timestamp": "2025-11-05 12:00:00",
       "endpoints": {
         "health": "...",
         "settings": "...",
         "data": "...",
         "submit": "..."
       }
     }
     ```

2. **Test Settings Endpoint**
   - Use a REST client (like Postman) or browser extension
   - Make GET request to: `https://your-site.com/wp-json/ninja-test-email/v1/admin/settings`
   - Must be logged in as admin
   - Should return settings object

---

## Phase 2: Email Testing Functionality

### Test Email Form Testing

#### ✅ Form Display
1. **Navigate to Dashboard**
   - Go to: Ninja Email Test → Dashboard
   - Verify the "Send Test Email" form is displayed at the top
   - Form should contain:
     - **Recipient Email** field (required, marked with red asterisk)
     - **Subject** field (optional)
     - **Message** textarea (optional)
     - **Email Format** radio buttons (Plain Text / HTML)
     - **Send Test Email** button

2. **Check Default Values**
   - The recipient email field should be pre-filled with your WordPress user email
   - Format should default to "Plain Text"
   - Subject and message should be empty

#### ✅ Form Validation
1. **Test Required Field**
   - Clear the recipient email field
   - Click "Send Test Email"
   - Browser should show validation error (HTML5 required attribute)

2. **Test Email Format Validation**
   - Enter invalid email: `notanemail`
   - Click "Send Test Email"
   - Browser should show "Please enter a valid email address" error

#### ✅ Send Plain Text Email
1. **Test with Default Message**
   - Enter a valid recipient email (your email for testing)
   - Leave Subject and Message fields empty
   - Keep Format as "Plain Text"
   - Click "Send Test Email"
   
2. **Expected Behavior**
   - Button should change to "Sending..."
   - After 1-2 seconds, success message appears in green box
   - Message should say: "Test email sent successfully to [email]"
   - Form fields (Subject and Message) should be cleared
   - Recipient email should remain

3. **Check Email Inbox**
   - Open the recipient's email inbox
   - Look for new email with subject: "Test Email from [Your Site Name]"
   - Email should contain:
     - Site name and URL
     - Timestamp
     - Success confirmation message
     - Footer with plugin name

4. **Test with Custom Subject and Message**
   - Recipient: your email
   - Subject: `My Custom Test Subject`
   - Message: `This is my custom test message content.`
   - Format: Plain Text
   - Click "Send Test Email"
   
5. **Check Email**
   - Should receive email with subject: "My Custom Test Subject"
   - Body should contain: "This is my custom test message content."

#### ✅ Send HTML Email
1. **Test HTML Format**
   - Recipient: your email
   - Subject: `HTML Test Email`
   - Message: Leave empty for default HTML
   - Format: Select **HTML**
   - Click "Send Test Email"

2. **Expected Behavior**
   - Success message should say: "HTML test email sent successfully to [email]"
   
3. **Check Email Inbox**
   - Email should have HTML formatting:
     - Styled header with site name
     - Colored elements
     - Formatted layout with borders and backgrounds
     - Professional appearance

4. **Test HTML with Custom Message**
   - Recipient: your email
   - Subject: `Custom HTML Test`
   - Message: `<strong>Bold text</strong> and <em>italic text</em>`
   - Format: HTML
   - Click "Send Test Email"

5. **Check Email**
   - Should render HTML tags (bold and italic text)

#### ✅ Error Handling
1. **Test Invalid Email**
   - Recipient: `invalid@fakemaildomainthatdoesnotexist12345.com`
   - Click "Send Test Email"
   - May succeed (WordPress attempts to send) but email won't be delivered
   - Check for appropriate messaging

2. **Test Without Email Configuration**
   - If your WordPress is not configured to send emails:
   - Should display error message: "Failed to send test email. Please check your email configuration."

#### ✅ REST API Testing
1. **Test via REST API Directly**
   - Use Postman or similar tool
   - Make POST request to: `https://your-site.com/wp-json/ninja-test-email/v1/test-email`
   - Headers:
     ```
     Content-Type: application/json
     X-WP-Nonce: [get from ninjaemailtestAdmin.nonce in browser console]
     ```
   - Body (JSON):
     ```json
     {
       "to": "your-email@example.com",
       "subject": "API Test",
       "message": "Testing via REST API",
       "format": "text"
     }
     ```
   - Should return:
     ```json
     {
       "success": true,
       "message": "Test email sent successfully to your-email@example.com"
     }
     ```

#### ✅ Multiple Email Tests
1. **Send Multiple Emails**
   - Send 5 different test emails with different content
   - All should succeed
   - Check that each arrives in inbox

2. **Test Different Recipients**
   - Send to different email addresses
   - Verify all receive their emails

#### ✅ UI/UX Testing
1. **Responsive Design**
   - Resize browser window to mobile size
   - Form should remain usable and properly formatted
   - All fields should be accessible

2. **Styling Verification**
   - Form inputs should have:
     - Blue focus ring when clicked
     - Proper spacing and padding
     - Clear labels
   - Button should:
     - Change color on hover (darker blue)
     - Show disabled state when sending
     - Have smooth transitions

3. **Loading States**
   - When sending email, button is disabled
   - Text changes to "Sending..."
   - Cannot submit form multiple times rapidly

4. **Success Messages**
   - Appear in green box above form
   - Automatically disappear after 5 seconds
   - Clear and informative

#### ✅ Dashboard Overview Section
1. **Verify Statistics Display**
   - Below the test email form
   - Should show three stat boxes:
     - **Emails Sent**: 0 (will be functional in Phase 3)
     - **Logs Stored**: 0 (will be functional in Phase 3)
     - **Plugin Status**: Active/Inactive
   - Boxes should have colored backgrounds (blue, green, purple)

---

## Settings Page Testing

### ✅ Settings Form
1. **Navigate to Settings**
   - Go to: Ninja Email Test → Settings
   
2. **Verify Form Elements**
   - Should display "Admin Capability" input field
   - Default value: `manage_options`
   - "Save Settings" button present

3. **Test Settings Update**
   - Change Admin Capability to: `edit_posts`
   - Click "Save Settings"
   - Should show success message
   - Page should not reload

4. **Verify Settings Persist**
   - Refresh the page
   - Admin Capability should still show: `edit_posts`
   - Change back to: `manage_options`
   - Save again

### ✅ Plugin Options Inspector
1. **Locate Inspector Section**
   - On Settings page, scroll down
   - Should see "Plugin Options Inspector" section

2. **Toggle Enable Plugin Checkbox**
   - Checkbox should show current state (checked = enabled)
   - Click the checkbox to toggle
   - Should auto-save immediately
   - Success message: "Settings updated!"

3. **View Raw Options**
   - JSON preview should display current options
   - Should match checkbox state
   - Shows: `ninja_test_email_options` option key

---

## Common Issues & Troubleshooting

### Issue: Plugin Menu Not Appearing
- **Solution**: Ensure you're logged in as an administrator
- Check if plugin is activated
- Clear WordPress cache if using caching plugin

### Issue: Assets Not Loading (No Styling)
- **Solution**: Check browser console for 404 errors
- Verify `assets/dist/` folder exists in plugin directory
- Rebuild assets: `npm run build` in development directory
- Clear browser cache (Ctrl+F5)

### Issue: REST API Returns 401 Unauthorized
- **Solution**: Ensure you're logged in
- Check that nonce is being passed correctly
- Verify REST API is enabled in WordPress

### Issue: Emails Not Sending
- **Solution**: 
  - Test WordPress email functionality with another plugin (e.g., WP Mail SMTP)
  - Check PHP mail configuration
  - Verify email isn't in spam folder
  - Check server mail logs

### Issue: JavaScript Errors in Console
- **Solution**: 
  - Check that React is loaded (`wp-element` dependency)
  - Verify `ninjaemailtestAdmin` object exists
  - Ensure no JavaScript conflicts with other plugins
  - Try deactivating other plugins temporarily

### Issue: Form Submission Hangs
- **Solution**: 
  - Check browser Network tab for failed requests
  - Verify REST endpoint URL is correct
  - Check for CORS issues if on subdomain
  - Increase PHP max_execution_time if needed

---

## Success Criteria Summary

### Phase 1 Complete When:
- ✅ Plugin activates without errors
- ✅ Database table created successfully
- ✅ Admin menu appears with 2 submenu items
- ✅ Both pages (Dashboard, Settings) load properly
- ✅ JavaScript and CSS assets load correctly
- ✅ REST API health check returns success
- ✅ No console errors or warnings

### Phase 2 Complete When:
- ✅ Test email form displays and functions
- ✅ Can send plain text emails successfully
- ✅ Can send HTML emails successfully
- ✅ Emails received in inbox with correct content
- ✅ Form validation works correctly
- ✅ Success/error messages display appropriately
- ✅ REST API endpoint sends emails via API
- ✅ UI is responsive and well-styled
- ✅ Settings page allows configuration changes
- ✅ All functionality works without errors

---

## Next Steps (Phase 3 Preview)
Once Phase 1 & 2 testing is complete and successful:
- Phase 3 will add email logging to database
- All sent emails will be captured and stored
- Dashboard statistics will show real counts
- Email logs viewer will be implemented

---

## Test Report Template

Use this template to document your testing:

```
## Test Report: Ninja Test Email - Phase 1 & 2

**Date**: [Date]
**Tester**: [Your Name]
**WordPress Version**: [e.g., 6.4]
**PHP Version**: [e.g., 8.0]
**Browser**: [e.g., Chrome 119]

### Phase 1 Results
- [ ] Database table created: YES / NO
- [ ] Admin menu appears: YES / NO
- [ ] Dashboard loads: YES / NO
- [ ] Settings loads: YES / NO
- [ ] Assets load correctly: YES / NO
- [ ] REST API works: YES / NO
- [ ] Console errors: NONE / [List errors]

### Phase 2 Results
- [ ] Test form displays: YES / NO
- [ ] Plain text email sent: YES / NO
- [ ] Plain text email received: YES / NO
- [ ] HTML email sent: YES / NO
- [ ] HTML email received: YES / NO
- [ ] Form validation works: YES / NO
- [ ] Settings save correctly: YES / NO
- [ ] UI is responsive: YES / NO

### Issues Found
1. [Issue description]
2. [Issue description]

### Screenshots
- [Attach relevant screenshots]

### Overall Status
- [ ] Phase 1: PASS / FAIL
- [ ] Phase 2: PASS / FAIL
```

---

## Support
For issues or questions during testing:
1. Check browser console for errors
2. Review this testing guide's troubleshooting section
3. Verify WordPress and PHP versions meet requirements
4. Test with default WordPress theme (Twenty Twenty-Four) to rule out theme conflicts
