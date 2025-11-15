<?php
/**
 * Plugin Name: Ninja Test Email
 * Plugin URI: https://github.com/1983shiv/ninja-test-email
 * Description: Modern WP Test Email Plugin
 * Version: 1.0.0
 * Author: Shiv Srivastava
 * Author URI: mailto:ninjatech.app@gmail.com
 * License: GPL v2 or later
 * Text Domain: ninja-test-email
 * Requires PHP: 7.4
 *
 * @package NinjaTestEmail
 */

namespace NinjaTestEmail;

if (!defined('WPINC')) {
    die;
}

// define('NINJA_TEST_EMAIL_VERSION', '1.0.0');
define('NINJA_TEST_EMAIL_VERSION', time());
define('NINJA_TEST_EMAIL_PATH', plugin_dir_path(__FILE__));
define('NINJA_TEST_EMAIL_URL', plugin_dir_url(__FILE__));
define('NINJA_TEST_EMAIL_FILE', __FILE__);
define('NINJA_TEST_EMAIL_BASENAME', plugin_basename(__FILE__));
define('NINJA_TEST_EMAIL_SLUG', 'ninja-test-email');

// Load Composer autoloader if available
if (file_exists(NINJA_TEST_EMAIL_PATH . 'vendor/autoload.php')) {
    require_once NINJA_TEST_EMAIL_PATH . 'vendor/autoload.php';
}

// Load core files manually
require_once NINJA_TEST_EMAIL_PATH . 'includes/Utils/trait-singleton.php';
require_once NINJA_TEST_EMAIL_PATH . 'includes/Utils/class-helpers.php';
require_once NINJA_TEST_EMAIL_PATH . 'includes/Utils/class-log-manager.php';
require_once NINJA_TEST_EMAIL_PATH . 'includes/Core/class-loader.php';
require_once NINJA_TEST_EMAIL_PATH . 'includes/Core/class-base.php';
require_once NINJA_TEST_EMAIL_PATH . 'includes/Core/class-activator.php';
require_once NINJA_TEST_EMAIL_PATH . 'includes/Core/class-deactivator.php';
require_once NINJA_TEST_EMAIL_PATH . 'includes/Core/class-email-tester.php';
require_once NINJA_TEST_EMAIL_PATH . 'includes/Core/class-email-logger.php';
require_once NINJA_TEST_EMAIL_PATH . 'includes/Admin/class-admin.php';
require_once NINJA_TEST_EMAIL_PATH . 'includes/Admin/class-admin-api.php';
require_once NINJA_TEST_EMAIL_PATH . 'includes/Frontend/class-frontend.php';
require_once NINJA_TEST_EMAIL_PATH . 'includes/Frontend/class-frontend-api.php';
require_once NINJA_TEST_EMAIL_PATH . 'includes/API/class-rest-controller.php';
require_once NINJA_TEST_EMAIL_PATH . 'includes/API/class-endpoints.php';

function activate_ninja_test_email() {
    Core\Activator::activate();
}
register_activation_hook(__FILE__, __NAMESPACE__ . '\activate_ninja_test_email');

function deactivate_ninja_test_email() {
    Core\Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\deactivate_ninja_test_email');

function init_ninja_test_email() {
    $plugin = Core\Base::instance();
    $plugin->run();
}
add_action('plugins_loaded', __NAMESPACE__ . '\init_ninja_test_email');
