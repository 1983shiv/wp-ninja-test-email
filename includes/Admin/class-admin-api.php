<?php
namespace NinjaTestEmail\Admin;

use NinjaTestEmail\Utils\Helpers;
use NinjaTestEmail\Utils\LogManager;
use NinjaTestEmail\Core\EmailTester;

class Admin_API {
    public static function get_settings($request) {
        try {
            if (!current_user_can('manage_options')) {
                return new \WP_Error('rest_forbidden', 'Permission denied', array('status' => 403));
            }
            
            $settings = get_option('ninja_test_email_options', array());
            
            if (!is_array($settings)) {
                $settings = array();
            }
            
            // Ensure default settings are always present
            $defaults = array(
                'enabled'          => true,
                'admin_capability' => 'manage_options',
            );
            
            $settings = array_merge($defaults, $settings);
            
            return new \WP_REST_Response(array(
                'success'  => true,
                'settings' => $settings,
            ), 200);
        } catch (\Exception $e) {
            return new \WP_Error('rest_error', $e->getMessage(), array('status' => 500));
        }
    }

    public static function update_settings($request) {
        try {
            if (!current_user_can('manage_options')) {
                return new \WP_Error('rest_forbidden', 'Permission denied', array('status' => 403));
            }
            
            $settings = $request->get_json_params();
            
            if (empty($settings)) {
                return new \WP_Error('rest_invalid_data', 'Invalid settings data', array('status' => 400));
            }
            
            $sanitized_settings = Helpers::sanitize_array($settings);
            $updated = update_option('ninja_test_email_options', $sanitized_settings);
            
            if ($updated || get_option('ninja_test_email_options') === $sanitized_settings) {
                return new \WP_REST_Response(array(
                    'success'  => true,
                    'message'  => 'Settings updated successfully',
                    'settings' => $sanitized_settings,
                ), 200);
            }
            
            return new \WP_Error('rest_update_failed', 'Failed to update settings', array('status' => 500));
        } catch (\Exception $e) {
            return new \WP_Error('rest_error', $e->getMessage(), array('status' => 500));
        }
    }

    public static function send_test_email($request) {
        try {
            if (!current_user_can('manage_options')) {
                return new \WP_Error('rest_forbidden', 'Permission denied', array('status' => 403));
            }
            
            $params = $request->get_json_params();
            
            if (!isset($params['to']) || empty($params['to'])) {
                return new \WP_Error('rest_invalid_data', 'Recipient email is required', array('status' => 400));
            }
            
            $to = sanitize_email($params['to']);
            $subject = isset($params['subject']) ? sanitize_text_field($params['subject']) : '';
            $message = isset($params['message']) ? wp_kses_post($params['message']) : '';
            $format = isset($params['format']) ? sanitize_text_field($params['format']) : 'text';
            
            // Use EmailTester to send the email
            if ($format === 'html') {
                $result = EmailTester::send_html_test_email($to, $subject, $message);
            } else {
                $result = EmailTester::send_test_email($to, $subject, $message);
            }
            
            if ($result['success']) {
                return new \WP_REST_Response(array(
                    'success' => true,
                    'message' => $result['message'],
                ), 200);
            } else {
                return new \WP_Error('rest_send_failed', $result['message'], array('status' => 500));
            }
        } catch (\Exception $e) {
            return new \WP_Error('rest_error', $e->getMessage(), array('status' => 500));
        }
    }

    public static function get_log_statistics($request) {
        try {
            if (!current_user_can('manage_options')) {
                return new \WP_Error('rest_forbidden', 'Permission denied', array('status' => 403));
            }
            
            $stats = LogManager::get_statistics();
            
            return new \WP_REST_Response(array(
                'success' => true,
                'stats'   => $stats,
            ), 200);
        } catch (\Exception $e) {
            return new \WP_Error('rest_error', $e->getMessage(), array('status' => 500));
        }
    }
}
