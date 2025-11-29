<?php
/**
 * Plugin Name: Ninja Test Email
 * Plugin URI: https://github.com/1983shiv/ninja-test-email
 * Description: Modern WP Test Email Plugin
 * Version: 1.1.1
 * Author: Shiv Srivastava
 * Author URI: https://github.com/1983shiv
 * License: GPL v2 or later
 * Text Domain: ninja-test-email
 * Domain Path: /languages
 * Requires PHP: 7.4
 *
 * @package Ninja_KNP
 */

namespace Ninja_KNP;

if (!defined('WPINC')) {
    die;
}

define('NINJA_KNP_VERSION', '1.1.1');
define('NINJA_KNP_PATH', plugin_dir_path(__FILE__));
define('NINJA_KNP_URL', plugin_dir_url(__FILE__));
define('NINJA_KNP_FILE', __FILE__);
define('NINJA_KNP_BASENAME', plugin_basename(__FILE__));
define('NINJA_KNP_SLUG', 'ninja-test-email');

// Load Composer autoloader if available
if (file_exists(NINJA_KNP_PATH . 'vendor/autoload.php')) {
    require_once NINJA_KNP_PATH . 'vendor/autoload.php';
}

// Load core files manually
require_once NINJA_KNP_PATH . 'includes/Utils/trait-singleton.php';
require_once NINJA_KNP_PATH . 'includes/Utils/class-helpers.php';
require_once NINJA_KNP_PATH . 'includes/Utils/class-log-manager.php';
require_once NINJA_KNP_PATH . 'includes/Core/class-loader.php';
require_once NINJA_KNP_PATH . 'includes/Core/class-base.php';
require_once NINJA_KNP_PATH . 'includes/Core/class-activator.php';
require_once NINJA_KNP_PATH . 'includes/Core/class-deactivator.php';
require_once NINJA_KNP_PATH . 'includes/Core/class-email-tester.php';
require_once NINJA_KNP_PATH . 'includes/Core/class-email-logger.php';
require_once NINJA_KNP_PATH . 'includes/Admin/class-admin.php';
require_once NINJA_KNP_PATH . 'includes/Admin/class-admin-api.php';
require_once NINJA_KNP_PATH . 'includes/Frontend/class-frontend.php';
require_once NINJA_KNP_PATH . 'includes/Frontend/class-frontend-api.php';
require_once NINJA_KNP_PATH . 'includes/API/class-rest-controller.php';
require_once NINJA_KNP_PATH . 'includes/API/class-endpoints.php';

if (!function_exists('Ninja_KNP\ninja_knp_activate')) {
    function ninja_knp_activate() {
        Core\Ninja_KNP_Activator::activate();
    }
}
register_activation_hook(__FILE__, __NAMESPACE__ . '\ninja_knp_activate');

if (!function_exists('Ninja_KNP\ninja_knp_deactivate')) {
    function ninja_knp_deactivate() {
        Core\Ninja_KNP_Deactivator::deactivate();
    }
}
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\ninja_knp_deactivate');

if (!function_exists('Ninja_KNP\ninja_knp_init')) {
    function ninja_knp_init() {
        $plugin = Core\Ninja_KNP_Base::instance();
        $plugin->run();
    }
}
add_action('plugins_loaded', __NAMESPACE__ . '\ninja_knp_init');
