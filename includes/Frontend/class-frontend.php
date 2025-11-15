<?php
namespace NinjaTestEmail\Frontend;

use NinjaTestEmail\Utils\Singleton;
use NinjaTestEmail\Core\Loader;

class Frontend {
    use Singleton;

    protected $loader;

    private function __construct(Loader $loader) {
        $this->loader = $loader;
        $this->register_hooks();
    }

    private function register_hooks() {
        $this->loader->add_action('wp_enqueue_scripts', $this, 'enqueue_assets');
        $this->loader->add_action('init', $this, 'register_shortcode');
    }

    public function register_shortcode() {
        add_shortcode('ninja-test-email', array($this, 'render_shortcode'));
    }

    public function enqueue_assets() {
        wp_enqueue_script('wp-element');

        wp_enqueue_style(
            'ninja-email-test-frontend',
            NINJA_TEST_EMAIL_URL . 'assets/dist/css/frontend.css',
            array(),
            NINJA_TEST_EMAIL_VERSION
        );

        wp_enqueue_script(
            'ninja-email-test-frontend',
            NINJA_TEST_EMAIL_URL . 'assets/dist/js/frontend.js',
            array('wp-element'),
            NINJA_TEST_EMAIL_VERSION,
            true
        );

        wp_localize_script(
            'ninja-email-test-frontend',
            'ninjaemailtestFrontend',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'restUrl' => rest_url('ninja-test-email/v1'),
                'nonce'   => wp_create_nonce('wp_rest'),
                'version' => NINJA_TEST_EMAIL_VERSION,
            )
        );
    }

    public function render_shortcode($atts) {
        $atts = shortcode_atts(
            array(
                'id'   => '',
                'type' => 'default',
            ),
            $atts,
            'ninja-email-test'
        );

        ob_start();
        require NINJA_TEST_EMAIL_PATH . 'includes/Frontend/views/shortcode.php';
        return ob_get_clean();
    }
}
