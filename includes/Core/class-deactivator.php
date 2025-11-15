<?php
namespace NinjaTestEmail\Core;

class Deactivator {
    public static function deactivate() {
        // Clear scheduled cron job
        $timestamp = wp_next_scheduled('ninja_test_email_daily_cleanup');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'ninja_test_email_daily_cleanup');
        }
        
        flush_rewrite_rules();
        do_action('ninja_test_email_deactivated');
    }
}
