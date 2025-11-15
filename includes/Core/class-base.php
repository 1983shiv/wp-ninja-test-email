<?php
namespace NinjaTestEmail\Core;

use NinjaTestEmail\Utils\Singleton;
use NinjaTestEmail\Admin\Admin;
use NinjaTestEmail\Frontend\Frontend;
use NinjaTestEmail\API\Endpoints;
use NinjaTestEmail\Utils\LogManager;

class Base {
    use Singleton;

    protected $loader;
    protected $version;

    private function __construct() {
        $this->version = NINJA_TEST_EMAIL_VERSION;
        $this->loader = new Loader();
        
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_frontend_hooks();
        $this->define_api_hooks();
        $this->define_logger_hooks();
        $this->define_cron_hooks();
    }

    private function load_dependencies() {
        // Dependencies autoloaded via Composer
    }

    private function define_admin_hooks() {
        if (!is_admin()) {
            return;
        }
        $admin = Admin::instance($this->loader);
    }

    private function define_frontend_hooks() {
        if (is_admin()) {
            return;
        }
        $frontend = Frontend::instance($this->loader);
    }

    private function define_api_hooks() {
        $endpoints = Endpoints::instance($this->loader);
    }

    private function define_logger_hooks() {
        $logger = EmailLogger::instance($this->loader);
    }

    private function define_cron_hooks() {
        $this->loader->add_action('ninja_test_email_daily_cleanup', $this, 'run_daily_cleanup');
    }

    public function run_daily_cleanup() {
        LogManager::delete_old_logs(30);
    }

    public function run() {
        $this->loader->run();
    }

    public function get_version() {
        return $this->version;
    }
}
