<?php
namespace NinjaTestEmail\Admin;

use NinjaTestEmail\Utils\Singleton;
use NinjaTestEmail\Core\Loader;

class Admin {
    use Singleton;

    protected $loader;

    private function __construct(Loader $loader) {
        $this->loader = $loader;
        $this->register_hooks();
    }

    private function register_hooks() {
        $this->loader->add_action('admin_menu', $this, 'add_admin_menu');
        $this->loader->add_action('admin_enqueue_scripts', $this, 'enqueue_assets');
        $this->loader->add_action('admin_init', $this, 'verify_cron_scheduled');
        $this->loader->add_action('admin_notices', $this, 'display_admin_notices');
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Ninja Email Test', 'ninja-test-email'),
            __('Ninja Email Test', 'ninja-test-email'),
            'manage_options',
            'ninja-email-test',
            array($this, 'render_admin_page'),
            'dashicons-admin-generic',
            30
        );

        add_submenu_page(
            'ninja-email-test',
            __('Dashboard', 'ninja-test-email'),
            __('Dashboard', 'ninja-test-email'),
            'manage_options',
            'ninja-email-test',
            array($this, 'render_admin_page')
        );

        add_submenu_page(
            'ninja-email-test',
            __('Settings', 'ninja-test-email'),
            __('Settings', 'ninja-test-email'),
            'manage_options',
            'ninja-email-test-settings',
            array($this, 'render_admin_page')
        );

        add_submenu_page(
            'ninja-email-test',
            __('Email Logs', 'ninja-test-email'),
            __('Email Logs', 'ninja-test-email'),
            'manage_options',
            'ninja-email-test-logs',
            array($this, 'render_admin_page')
        );
    }

    public function verify_cron_scheduled() {
        // Auto-fix: Schedule cron if not scheduled
        if (!wp_next_scheduled('ninja_test_email_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'ninja_test_email_daily_cleanup');
        }
    }

    public function display_admin_notices() {
        // Only show on plugin pages
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET parameter for admin notice display only, no action taken
        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        if (empty($page) || strpos($page, 'ninja-email-test') === false) {
            return;
        }

        // Check if cron is scheduled
        $cron_scheduled = wp_next_scheduled('ninja_test_email_daily_cleanup');
        
        if ($cron_scheduled) {
            $next_run = gmdate('Y-m-d H:i:s', $cron_scheduled);
            echo '<div class="notice notice-info is-dismissible">';
            /* translators: %s: next scheduled run time */
            echo '<p>' . sprintf(esc_html__('Cron Status: Daily cleanup is scheduled. Next run: %s', 'ninja-test-email'), '<strong>' . esc_html($next_run) . '</strong>') . '</p>';
            echo '</div>';
        } else {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p>' . esc_html__('Warning: Daily cleanup cron is not scheduled. It should auto-schedule on next page load.', 'ninja-test-email') . '</p>';
            echo '</div>';
        }
    }

    public function render_admin_page() {
        require_once NINJA_TEST_EMAIL_PATH . 'includes/Admin/views/admin-page.php';
    }

    public function enqueue_assets($hook) {
        if (strpos($hook, 'ninja-email-test') === false) {
            return;
        }

        wp_enqueue_script('wp-element');

        wp_enqueue_style(
            'ninja-email-test-admin',
            NINJA_TEST_EMAIL_URL . 'assets/dist/css/admin.css',
            array(),
            NINJA_TEST_EMAIL_VERSION
        );

        wp_enqueue_script(
            'ninja-email-test-admin',
            NINJA_TEST_EMAIL_URL . 'assets/dist/js/admin.js',
            array('wp-element'),
            NINJA_TEST_EMAIL_VERSION,
            true
        );

        wp_localize_script(
            'ninja-email-test-admin',
            'ninjaemailtestAdmin',
            array(
                'ajaxUrl'     => admin_url('admin-ajax.php'),
                'restUrl'     => rest_url('ninja-test-email/v1'),
                'nonce'       => wp_create_nonce('wp_rest'),
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET parameter for display only
                'currentPage' => isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '',
                'userEmail'   => wp_get_current_user()->user_email,
            )
        );
    }
}
