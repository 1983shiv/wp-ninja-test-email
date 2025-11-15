# Phase 4 Testing Guide: Email Logs Viewer

## Overview
Phase 4 implements a comprehensive email logs viewer with search, sorting, pagination, and modal view capabilities.

## Features Implemented

### 1. Email Logs Viewer Page
- New "Email Logs" submenu under Ninja Email Test menu
- React-based logs viewer with responsive table layout
- Real-time log display with automatic loading

### 2. Search Functionality
- Search across recipient email, subject, and body content
- Live search with automatic API calls
- Clears results and resets to page 1 when searching

### 3. Column Sorting
- Sortable columns: Time, To, Subject, Status
- Click column headers to sort
- Visual indicators (↑↓) showing sort direction
- Toggle between ascending (ASC) and descending (DESC)

### 4. Pagination
- Shows 10 logs per page (configurable)
- Previous/Next navigation buttons
- Page number display (e.g., "Page 1 of 5")
- Disabled state for buttons at boundaries
- Automatic reset to page 1 when searching or changing sort

### 5. Email Details Modal
- Click "View" button to see full email details
- Modal overlay with complete email information
- Displays: Time, To, Subject, Status, Full Body
- Scrollable body content for long emails
- Close button and click-outside-to-close functionality

### 6. Delete Functionality
- Delete individual log entries
- Confirmation dialog before deletion
- Success message after deletion
- Automatic refresh of logs list

---

## Testing Instructions

### Prerequisites
1. WordPress site with plugin installed and activated
2. At least a few test emails sent (use Dashboard to send test emails)
3. Admin access to WordPress dashboard

---

## Test Case 1: Access Email Logs Page

**Steps:**
1. Log in to WordPress admin
2. Navigate to **Ninja Email Test → Email Logs** in the admin menu
3. Observe the page loads

**Expected Results:**
- ✅ Email Logs page displays
- ✅ Page title reads "Email Logs"
- ✅ Subtitle reads "View all logged outgoing emails from your WordPress site"
- ✅ Search box is visible at the top
- ✅ Table displays with columns: Time, To, Subject, Status, Actions
- ✅ If no logs exist, shows "No email logs found" message with helpful text

---

## Test Case 2: View Existing Logs

**Prerequisites:** At least 5 test emails sent

**Steps:**
1. Navigate to Email Logs page
2. Observe the table content

**Expected Results:**
- ✅ Table displays all logged emails
- ✅ Each row shows:
  - Time in readable format (YYYY-MM-DD HH:MM:SS)
  - Recipient email address
  - Email subject (truncated if > 50 chars with "...")
  - Status badge (green "sent" badge)
  - View and Delete action buttons
- ✅ Logs are sorted by time (newest first) by default
- ✅ No loading spinner visible

---

## Test Case 3: Search Functionality - Recipient Email

**Steps:**
1. Navigate to Email Logs page
2. In the search box, type part of a recipient email (e.g., "admin")
3. Wait for results to update

**Expected Results:**
- ✅ Table updates to show only matching logs
- ✅ Search matches emails containing the search term in recipient field
- ✅ Non-matching logs are hidden
- ✅ Page resets to page 1
- ✅ "Page X of Y" updates accordingly

**Additional Test:**
- Type a recipient email that doesn't exist
- ✅ Shows "No email logs found" message

---

## Test Case 4: Search Functionality - Subject

**Steps:**
1. Clear previous search
2. Type part of an email subject (e.g., "Test")
3. Wait for results

**Expected Results:**
- ✅ Table shows only logs with matching subjects
- ✅ Search is case-insensitive
- ✅ Partial matches work correctly

---

## Test Case 5: Search Functionality - Body Content

**Steps:**
1. Clear search
2. Type a word you know is in an email body (e.g., "WordPress")
3. Observe results

**Expected Results:**
- ✅ Shows logs where body content contains the search term
- ✅ Search works across all three fields (to, subject, body)

---

## Test Case 6: Column Sorting - Time

**Steps:**
1. Clear any search filters
2. Click the "Time" column header
3. Observe the sort indicator
4. Click "Time" header again

**Expected Results:**
- ✅ First click: Shows ↓ arrow, logs sorted newest to oldest (DESC)
- ✅ Second click: Shows ↑ arrow, logs sorted oldest to newest (ASC)
- ✅ Third click: Toggles back to DESC
- ✅ Table updates immediately after each click

---

## Test Case 7: Column Sorting - Recipient Email

**Steps:**
1. Click the "To" column header
2. Observe sorting

**Expected Results:**
- ✅ Logs sorted alphabetically by recipient email
- ✅ Sort indicator shows next to "To" column
- ✅ Other columns show neutral ↕ indicator

---

## Test Case 8: Column Sorting - Subject

**Steps:**
1. Click "Subject" column header
2. Toggle ASC/DESC

**Expected Results:**
- ✅ Logs sorted alphabetically by subject
- ✅ Empty subjects sorted consistently
- ✅ Sort direction toggles properly

---

## Test Case 9: Column Sorting - Status

**Steps:**
1. Click "Status" column header
2. Observe sorting

**Expected Results:**
- ✅ Logs sorted by status value
- ✅ Sort indicator updates

---

## Test Case 10: Pagination - Basic Navigation

**Prerequisites:** More than 10 logs in database

**Steps:**
1. Navigate to Email Logs page
2. Observe pagination controls at bottom
3. Click "Next" button
4. Click "Previous" button

**Expected Results:**
- ✅ Shows "Page 1 of X" where X is total pages
- ✅ Previous button is disabled on page 1
- ✅ Clicking Next loads page 2
- ✅ Table updates with next 10 logs
- ✅ Previous button becomes enabled
- ✅ Clicking Previous returns to page 1
- ✅ Next button disabled on last page

---

## Test Case 11: Pagination with Search

**Steps:**
1. Enter a search term that returns more than 10 results
2. Observe pagination
3. Navigate to page 2
4. Change search term

**Expected Results:**
- ✅ Pagination shows correct total pages for filtered results
- ✅ Can navigate through filtered results
- ✅ Changing search resets to page 1
- ✅ Page count updates based on search results

---

## Test Case 12: View Email Details - Modal

**Steps:**
1. Click "View" button on any log entry
2. Observe the modal

**Expected Results:**
- ✅ Modal appears as overlay on screen
- ✅ Background is dimmed (black with 50% opacity)
- ✅ Modal displays all email details:
  - Time
  - To (recipient email)
  - Subject
  - Status (with green badge)
  - Email Body (full text, scrollable if long)
- ✅ Close button (×) visible in top-right
- ✅ "Close" button at bottom

---

## Test Case 13: Modal - Closing Mechanisms

**Steps:**
1. Open any email in modal
2. Test closing methods:
   a. Click the × button
   b. Click the "Close" button
   c. Click outside the modal (on dark background)

**Expected Results:**
- ✅ All three methods close the modal
- ✅ Modal disappears smoothly
- ✅ Returns to logs table view

---

## Test Case 14: Modal - Long Email Body

**Prerequisites:** Email with long body content (several paragraphs)

**Steps:**
1. View an email with long body text
2. Observe body display area

**Expected Results:**
- ✅ Body text displays in scrollable container
- ✅ Maximum height is enforced (max-h-96)
- ✅ Scrollbar appears when content exceeds height
- ✅ Text formatting preserved (line breaks maintained)
- ✅ Modal itself is scrollable if very tall

---

## Test Case 15: Delete Single Log

**Steps:**
1. Click "Delete" button on any log entry
2. Observe confirmation dialog
3. Click "OK" to confirm

**Expected Results:**
- ✅ Browser confirmation dialog appears: "Are you sure you want to delete this log?"
- ✅ Clicking OK sends delete request
- ✅ Success message appears: "Log deleted successfully"
- ✅ Message auto-hides after 3 seconds
- ✅ Table refreshes automatically
- ✅ Deleted log no longer appears in list
- ✅ Total count updates in pagination

---

## Test Case 16: Delete - Cancel Confirmation

**Steps:**
1. Click "Delete" on any log
2. Click "Cancel" in confirmation dialog

**Expected Results:**
- ✅ Dialog closes
- ✅ No delete action performed
- ✅ Log remains in table
- ✅ No API call made

---

## Test Case 17: Empty State After Delete

**Prerequisites:** Only 1 log in database

**Steps:**
1. Delete the last remaining log
2. Observe the page

**Expected Results:**
- ✅ Shows "No email logs found" message
- ✅ Helpful text: "Send a test email to see logs appear here"
- ✅ Search box still visible
- ✅ No table displayed

---

## Test Case 18: Combined Features - Search + Sort + Page

**Steps:**
1. Enter search term
2. Click a column header to sort
3. Navigate to page 2 (if available)
4. Change sort order
5. Clear search

**Expected Results:**
- ✅ Search, sort, and pagination work together
- ✅ Changing sort maintains search filter
- ✅ Pagination respects both search and sort
- ✅ Clearing search maintains current sort preference
- ✅ URL parameters update correctly (if implemented)

---

## Test Case 19: Responsive Table Display

**Steps:**
1. View logs page on different screen sizes
2. Resize browser window

**Expected Results:**
- ✅ Table scrolls horizontally on small screens
- ✅ All columns remain accessible
- ✅ Modal is responsive and centered
- ✅ Search box is full-width on mobile

---

## Test Case 20: Performance - Large Dataset

**Prerequisites:** 100+ logs in database

**Steps:**
1. Navigate to Email Logs page
2. Perform searches
3. Change sorting
4. Navigate pages

**Expected Results:**
- ✅ Page loads quickly (< 2 seconds)
- ✅ Search results appear promptly
- ✅ Sorting is instant
- ✅ Pagination navigation is smooth
- ✅ No console errors

---

## Test Case 21: Integration with Dashboard Stats

**Steps:**
1. Note the log statistics on Dashboard page
2. Navigate to Email Logs
3. Count visible logs
4. Delete a log
5. Return to Dashboard

**Expected Results:**
- ✅ Total count on Dashboard matches total in logs viewer
- ✅ Deleting a log updates Dashboard statistics
- ✅ Stats refresh when revisiting Dashboard

---

## Test Case 22: REST API Endpoints

**Using Browser Console or REST API Client:**

### Test GET /logs endpoint:
```javascript
fetch('/wp-json/ninja-test-email/v1/logs?search=admin&orderby=time&order=DESC&page=1&per_page=10', {
    headers: { 'X-WP-Nonce': wpApiSettings.nonce }
}).then(r => r.json()).then(console.log);
```

**Expected Response:**
```json
{
    "success": true,
    "logs": [...],
    "current_page": 1,
    "total_pages": 5,
    "total_count": 42
}
```

### Test DELETE /logs/{id} endpoint:
```javascript
fetch('/wp-json/ninja-test-email/v1/logs/123', {
    method: 'DELETE',
    headers: { 'X-WP-Nonce': wpApiSettings.nonce }
}).then(r => r.json()).then(console.log);
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Log deleted successfully"
}
```

---

## Test Case 23: Cron Cleanup Integration

**Steps:**
1. Create logs older than 30 days (modify database directly for testing)
2. Trigger cron manually: `wp cron event run ninja_test_email_daily_cleanup`
3. Refresh Email Logs page

**Expected Results:**
- ✅ Old logs (> 30 days) are deleted
- ✅ Recent logs remain
- ✅ Total count updates correctly

---

## Test Case 24: Error Handling

**Test scenarios:**

### Network Error:
1. Block network in browser DevTools
2. Navigate to Email Logs
**Expected:** Shows "Error loading logs" message

### Delete Error:
1. Try deleting non-existent log ID via API
**Expected:** Shows error message

---

## Common Issues & Troubleshooting

### Issue: Logs not appearing
**Solution:**
1. Send a test email from Dashboard
2. Check if emails are actually being sent
3. Verify Email Logger is active (check includes/Core/class-base.php)

### Issue: Search not working
**Solution:**
1. Check browser console for JavaScript errors
2. Verify REST API is accessible
3. Test API endpoint directly

### Issue: Pagination shows wrong count
**Solution:**
1. Verify database has expected number of logs
2. Check `per_page` parameter (default: 10)
3. Clear search filters

### Issue: Modal won't close
**Solution:**
1. Check for JavaScript errors
2. Try each closing method (×, Close button, click outside)
3. Refresh page if stuck

---

## Database Verification

**Check logs table directly:**
```sql
SELECT COUNT(*) FROM wp_ninja_test_email_logs;
SELECT * FROM wp_ninja_test_email_logs ORDER BY time DESC LIMIT 10;
```

**Verify table structure:**
```sql
DESCRIBE wp_ninja_test_email_logs;
```

Should show columns: `id`, `time`, `to_email`, `subject`, `body`, `status`

---

## REST API Testing Checklist

✅ GET `/wp-json/ninja-test-email/v1/logs` - Returns paginated logs  
✅ GET `/wp-json/ninja-test-email/v1/logs?search=term` - Search works  
✅ GET `/wp-json/ninja-test-email/v1/logs?orderby=time&order=ASC` - Sorting works  
✅ GET `/wp-json/ninja-test-email/v1/logs?page=2&per_page=10` - Pagination works  
✅ DELETE `/wp-json/ninja-test-email/v1/logs/{id}` - Deletes log  
✅ GET `/wp-json/ninja-test-email/v1/logs/stats` - Returns statistics  

---

## Security Testing

### Permission Checks:
1. Log out of WordPress
2. Try accessing `/wp-admin/admin.php?page=ninja-email-test-logs`
**Expected:** Redirected to login

### REST API Permission:
1. Access REST endpoints without nonce
**Expected:** 401 Unauthorized error

### SQL Injection:
1. Try search with: `'; DROP TABLE wp_ninja_test_email_logs; --`
**Expected:** Treated as literal string, no SQL execution

---

## Performance Benchmarks

| Operation | Expected Time |
|-----------|--------------|
| Initial page load | < 2 seconds |
| Search results | < 1 second |
| Sort change | Instant |
| Page navigation | < 500ms |
| Modal open | Instant |
| Delete + refresh | < 2 seconds |

---

## Sign-off Checklist

Before considering Phase 4 complete, verify:

- [ ] Email Logs menu item appears under Ninja Email Test
- [ ] Logs table displays correctly with all columns
- [ ] Search works across all fields (to, subject, body)
- [ ] All column headers are sortable
- [ ] Sort direction indicator displays correctly
- [ ] Pagination controls work (Previous/Next)
- [ ] Page count displays accurately
- [ ] View button opens modal with full details
- [ ] Modal closes via all three methods
- [ ] Delete button removes log after confirmation
- [ ] Success messages appear and auto-hide
- [ ] Empty state displays when no logs exist
- [ ] No console errors in browser DevTools
- [ ] REST API endpoints respond correctly
- [ ] Works with search + sort + pagination combined
- [ ] Responsive on mobile devices
- [ ] Integration with Dashboard statistics works

---

## Conclusion

Phase 4 successfully implements a comprehensive email logs viewer with:
- ✅ Searchable logs across multiple fields
- ✅ Sortable columns with visual indicators
- ✅ Pagination for large datasets
- ✅ Modal view for full email details
- ✅ Delete functionality with confirmation
- ✅ Clean, responsive UI using Tailwind CSS
- ✅ RESTful API architecture
- ✅ Proper error handling and user feedback

The logs viewer integrates seamlessly with the existing plugin architecture and provides administrators with powerful tools to monitor all outgoing emails from their WordPress site.
