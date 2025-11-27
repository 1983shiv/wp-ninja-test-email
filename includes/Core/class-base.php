<?php
namespace Ninja_KNP\Core;

use Ninja_KNP\Utils\Ninja_KNP_Singleton;
use Ninja_KNP\Admin\Ninja_KNP_Admin;
use Ninja_KNP\Frontend\Ninja_KNP_Frontend;
use Ninja_KNP\API\Ninja_KNP_Endpoints;
use Ninja_KNP\Utils\Ninja_KNP_Log_Manager;

if (!class_exists('Ninja_KNP\Core\Ninja_KNP_Base')) {
    class Ninja_KNP_Base {
        use Ninja_KNP_Singleton;

    protected $loader;
    protected $version;

    private function __construct() {
        $this->version = NINJA_KNP_VERSION;
        $this->loader = new Ninja_KNP_Loader();
        
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
        $admin = Ninja_KNP_Admin::instance($this->loader);
    }

    private function define_frontend_hooks() {
        if (is_admin()) {
            return;
        }
        $frontend = Ninja_KNP_Frontend::instance($this->loader);
    }

    private function define_api_hooks() {
        $endpoints = Ninja_KNP_Endpoints::instance($this->loader);
    }

    private function define_logger_hooks() {
        $logger = Ninja_KNP_Email_Logger::instance($this->loader);
    }

    private function define_cron_hooks() {
        $this->loader->add_action('ninja_knp_daily_cleanup', $this, 'run_daily_cleanup');
    }

    public function run_daily_cleanup() {
        Ninja_KNP_Log_Manager::delete_old_logs(30);
    }

    public function run() {
        $this->loader->run();
    }

    public function get_version() {
        return $this->version;
    }
}
}

