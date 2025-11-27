<?php
namespace Ninja_KNP\Core;

if (!class_exists('Ninja_KNP\Core\Ninja_KNP_Deactivator')) {
    class Ninja_KNP_Deactivator {
        public static function deactivate() {
        // Clear scheduled cron job
        $timestamp = wp_next_scheduled('ninja_knp_daily_cleanup');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'ninja_knp_daily_cleanup');
        }
        
        flush_rewrite_rules();
        do_action('ninja_knp_deactivated');
    }
}
}

