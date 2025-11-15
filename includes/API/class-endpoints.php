<?php
namespace NinjaTestEmail\API;

use NinjaTestEmail\Utils\Singleton;
use NinjaTestEmail\Core\Loader;
use NinjaTestEmail\Admin\Admin_API;
use NinjaTestEmail\Frontend\Frontend_API;

class Endpoints {
    use Singleton;

    protected $loader;
    protected $namespace = 'ninja-test-email/v1';

    private function __construct(Loader $loader) {
        $this->loader = $loader;
        $this->register_hooks();
    }

    private function register_hooks() {
        $this->loader->add_action('rest_api_init', $this, 'register_routes');
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
                    'callback'            => array('NinjaTestEmail\\Admin\\Admin_API', 'get_settings'),
                    'permission_callback' => function() {
                        return current_user_can('manage_options');
                    },
                ),
                array(
                    'methods'             => 'POST',
                    'callback'            => array('NinjaTestEmail\\Admin\\Admin_API', 'update_settings'),
                    'permission_callback' => function() {
                        return current_user_can('manage_options');
                    },
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/test-email',
            array(
                'methods'             => 'POST',
                'callback'            => array('NinjaTestEmail\\Admin\\Admin_API', 'send_test_email'),
                'permission_callback' => function() {
                    return current_user_can('manage_options');
                },
            )
        );

        register_rest_route(
            $this->namespace,
            '/logs/stats',
            array(
                'methods'             => 'GET',
                'callback'            => array('NinjaTestEmail\\Admin\\Admin_API', 'get_log_statistics'),
                'permission_callback' => function() {
                    return current_user_can('manage_options');
                },
            )
        );

        register_rest_route(
            $this->namespace,
            '/logs',
            array(
                'methods'             => 'GET',
                'callback'            => array('NinjaTestEmail\\Admin\\Admin_API', 'get_logs'),
                'permission_callback' => function() {
                    return current_user_can('manage_options');
                },
            )
        );

        register_rest_route(
            $this->namespace,
            '/logs/(?P<id>\d+)',
            array(
                'methods'             => 'DELETE',
                'callback'            => array('NinjaTestEmail\\Admin\\Admin_API', 'delete_log'),
                'permission_callback' => function() {
                    return current_user_can('manage_options');
                },
            )
        );

        register_rest_route(
            $this->namespace,
            '/data',
            array(
                'methods'             => 'GET',
                'callback'            => array('NinjaTestEmail\\Frontend\\Frontend_API', 'get_data'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            $this->namespace,
            '/submit',
            array(
                'methods'             => 'POST',
                'callback'            => array('NinjaTestEmail\\Frontend\\Frontend_API', 'submit_form'),
                'permission_callback' => '__return_true',
            )
        );
    }

    public function health_check() {
        return array(
            'status'    => 'ok',
            'version'   => NINJA_TEST_EMAIL_VERSION,
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
