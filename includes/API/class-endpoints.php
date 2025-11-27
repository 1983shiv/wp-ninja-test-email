<?php
namespace Ninja_KNP\API;

use Ninja_KNP\Utils\Ninja_KNP_Singleton;
use Ninja_KNP\Core\Ninja_KNP_Loader;
use Ninja_KNP\Admin\Ninja_KNP_Admin_API;
use Ninja_KNP\Frontend\Ninja_KNP_Frontend_API;


if (!class_exists('Ninja_KNP\API\Ninja_KNP_Endpoints')) {
class Ninja_KNP_Endpoints {
    use Ninja_KNP_Singleton;

    protected $loader;
    protected $namespace = 'ninja-knp/v1';

    private function __construct(Ninja_KNP_Loader $loader) {
        $this->loader = $loader;
        $this->register_hooks();
    }

    private function register_hooks() {
        $this->loader->add_action('rest_api_init', $this, 'register_routes');
    }

    /**
     * Check if user has admin permissions
     *
     * @return bool
     */
    public function check_admin_permission() {
        return current_user_can('manage_options');
    }

    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/health',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'health_check'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            $this->namespace,
            '/admin/settings',
            array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array('Ninja_KNP\\Admin\\Ninja_KNP_Admin_API', 'get_settings'),
                    'permission_callback' => array($this, 'check_admin_permission'),
                ),
                array(
                    'methods'             => 'POST',
                    'callback'            => array('Ninja_KNP\\Admin\\Ninja_KNP_Admin_API', 'update_settings'),
                    'permission_callback' => array($this, 'check_admin_permission'),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/test-email',
            array(
                'methods'             => 'POST',
                'callback'            => array('Ninja_KNP\\Admin\\Ninja_KNP_Admin_API', 'send_test_email'),
                'permission_callback' => array($this, 'check_admin_permission'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/logs/stats',
            array(
                'methods'             => 'GET',
                'callback'            => array('Ninja_KNP\\Admin\\Ninja_KNP_Admin_API', 'get_log_statistics'),
                'permission_callback' => array($this, 'check_admin_permission'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/logs',
            array(
                'methods'             => 'GET',
                'callback'            => array('Ninja_KNP\\Admin\\Ninja_KNP_Admin_API', 'get_logs'),
                'permission_callback' => array($this, 'check_admin_permission'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/logs/(?P<id>\\d+)',
            array(
                'methods'             => 'DELETE',
                'callback'            => array('Ninja_KNP\\Admin\\Ninja_KNP_Admin_API', 'delete_log'),
                'permission_callback' => array($this, 'check_admin_permission'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/data',
            array(
                'methods'             => 'GET',
                'callback'            => array('Ninja_KNP\\Frontend\\Ninja_KNP_Frontend_API', 'get_data'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            $this->namespace,
            '/submit',
            array(
                'methods'             => 'POST',
                'callback'            => array('Ninja_KNP\\Frontend\\Ninja_KNP_Frontend_API', 'submit_form'),
                'permission_callback' => '__return_true',
            )
        );
    }

    public function health_check() {
        return array(
            'status'    => 'ok',
            'version'   => NINJA_KNP_VERSION,
            'timestamp' => current_time('mysql'),
            'endpoints' => array(
                'health'   => rest_url($this->namespace . '/health'),
                'settings' => rest_url($this->namespace . '/admin/settings'),
                'data'     => rest_url($this->namespace . '/data'),
                'submit'   => rest_url($this->namespace . '/submit'),
            ),
        );
    }
}



}
