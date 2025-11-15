<?php
namespace NinjaTestEmail\Core;

class Activator {
    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ninja_test_email_logs';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            to_email varchar(100) NOT NULL,
            subject varchar(255) NOT NULL,
            body text NOT NULL,
            status varchar(20) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $default_options = array(
            'enabled'          => true,
            'admin_capability' => 'manage_options',
        );
        
        if (!get_option('ninja_test_email_options')) {
            add_option('ninja_test_email_options', $default_options);
        }
        
        // Schedule daily cleanup cron job
        if (!wp_next_scheduled('ninja_test_email_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'ninja_test_email_daily_cleanup');
        }
        
        flush_rewrite_rules();
        do_action('ninja_test_email_activated');
    }
}
