<?php
namespace Ninja_KNP\Frontend;

use Ninja_KNP\Utils\Ninja_KNP_Singleton;
use Ninja_KNP\Core\Ninja_KNP_Loader;


if (!class_exists('Ninja_KNP\Frontend\Ninja_KNP_Frontend')) {
class Ninja_KNP_Frontend {
    use Ninja_KNP_Singleton;

    protected $loader;

    private function __construct(Ninja_KNP_Loader $loader) {
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
            NINJA_KNP_URL . 'assets/dist/css/frontend.css',
            array(),
            NINJA_KNP_VERSION
        );

        wp_enqueue_script(
            'ninja-email-test-frontend',
            NINJA_KNP_URL . 'assets/dist/js/frontend.js',
            array('wp-element'),
            NINJA_KNP_VERSION,
            true
        );

        wp_localize_script(
            'ninja-email-test-frontend',
            'ninjaemailtestFrontend',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'restUrl' => rest_url('ninja-knp/v1'),
                'nonce'   => wp_create_nonce('wp_rest'),
                'version' => NINJA_KNP_VERSION,
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
        require NINJA_KNP_PATH . 'includes/Frontend/views/shortcode.php';
        return ob_get_clean();
    }
}
}
