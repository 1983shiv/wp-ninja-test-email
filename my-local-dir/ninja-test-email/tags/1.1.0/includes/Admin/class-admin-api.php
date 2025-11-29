<?php
namespace Ninja_KNP\Admin;

use Ninja_KNP\Utils\Ninja_KNP_Helpers;
use Ninja_KNP\Utils\Ninja_KNP_Log_Manager;
use Ninja_KNP\Core\Ninja_KNP_Email_Tester;


if (!class_exists('Ninja_KNP\Admin\Ninja_KNP_Admin_API')) {
class Ninja_KNP_Admin_API {
    public static function send_test_email($request) {
        try {
            if (!current_user_can('manage_options')) {
                return new \WP_Error('rest_forbidden', esc_html__('Permission denied', 'ninja-test-email'), array('status' => 403));
            }
            
            $params = $request->get_json_params();
            
            if (!isset($params['to']) || empty($params['to'])) {
                return new \WP_Error('rest_invalid_data', esc_html__('Recipient email is required', 'ninja-test-email'), array('status' => 400));
            }
            
            $to = sanitize_email($params['to']);
            $subject = isset($params['subject']) ? sanitize_text_field($params['subject']) : '';
            $message = isset($params['message']) ? wp_kses_post($params['message']) : '';
            $format = isset($params['format']) ? sanitize_text_field($params['format']) : 'text';
            
            // Use EmailTester to send the email
            if ($format === 'html') {
                $result = Ninja_KNP_Email_Tester::send_html_test_email($to, $subject, $message);
            } else {
                $result = Ninja_KNP_Email_Tester::send_test_email($to, $subject, $message);
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
                return new \WP_Error('rest_forbidden', esc_html__('Permission denied', 'ninja-test-email'), array('status' => 403));
            }
            
            $stats = Ninja_KNP_Log_Manager::get_statistics();
            
            return new \WP_REST_Response(array(
                'success' => true,
                'stats'   => $stats,
            ), 200);
        } catch (\Exception $e) {
            return new \WP_Error('rest_error', $e->getMessage(), array('status' => 500));
        }
    }

    public static function get_logs($request) {
        try {
            if (!current_user_can('manage_options')) {
                return new \WP_Error('rest_forbidden', esc_html__('Permission denied', 'ninja-test-email'), array('status' => 403));
            }
            
            $params = $request->get_params();
            
            $args = array(
                'search'   => isset($params['search']) ? sanitize_text_field($params['search']) : '',
                'orderby'  => isset($params['orderby']) ? sanitize_text_field($params['orderby']) : 'time',
                'order'    => isset($params['order']) ? sanitize_text_field($params['order']) : 'DESC',
                'per_page' => isset($params['per_page']) ? absint($params['per_page']) : 10,
                'page'     => isset($params['page']) ? absint($params['page']) : 1,
            );
            
            $logs = Ninja_KNP_Log_Manager::get_logs($args);
            $total = Ninja_KNP_Log_Manager::get_total_count($args['search']);
            
            return new \WP_REST_Response(array(
                'success'    => true,
                'logs'       => $logs,
                'total'      => $total,
                'total_pages' => ceil($total / $args['per_page']),
                'current_page' => $args['page'],
                'per_page'   => $args['per_page'],
            ), 200);
        } catch (\Exception $e) {
            return new \WP_Error('rest_error', $e->getMessage(), array('status' => 500));
        }
    }

    public static function delete_log($request) {
        try {
            if (!current_user_can('manage_options')) {
                return new \WP_Error('rest_forbidden', esc_html__('Permission denied', 'ninja-test-email'), array('status' => 403));
            }
            
            $log_id = $request->get_param('id');
            
            if (empty($log_id)) {
                return new \WP_Error('rest_invalid_data', esc_html__('Log ID is required', 'ninja-test-email'), array('status' => 400));
            }
            
            $result = Ninja_KNP_Log_Manager::delete_log($log_id);
            
            if ($result) {
                return new \WP_REST_Response(array(
                    'success' => true,
                    'message' => esc_html__('Log deleted successfully', 'ninja-test-email'),
                ), 200);
            } else {
                return new \WP_Error('rest_delete_failed', esc_html__('Failed to delete log', 'ninja-test-email'), array('status' => 500));
            }
        } catch (\Exception $e) {
            return new \WP_Error('rest_error', $e->getMessage(), array('status' => 500));
        }
    }
}



}
