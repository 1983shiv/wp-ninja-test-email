<?php
/**
 * Email Tester Class
 * 
 * Handles sending test emails with validation and error handling
 *
 * @package NinjaTestEmail\Core
 */

namespace NinjaTestEmail\Core;

class EmailTester {
    
    /**
     * Send a test email
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $message Email message body
     * @return array Result array with success status and message
     */
    public static function send_test_email($to, $subject = '', $message = '') {
        // Validate email address
        $validation = self::validate_email($to);
        if (!$validation['valid']) {
            return array(
                'success' => false,
                'message' => $validation['error']
            );
        }

        // Set default subject if empty
        if (empty($subject)) {
            $subject = 'Test Email from ' . get_bloginfo('name');
        }

        // Set default message if empty
        if (empty($message)) {
            $message = self::get_default_message();
        }

        // Sanitize inputs
        $to = sanitize_email($to);
        $subject = sanitize_text_field($subject);
        $message = wp_kses_post($message);

        // Attempt to send email
        $sent = wp_mail($to, $subject, $message);

        if ($sent) {
            return array(
                'success' => true,
                'message' => sprintf(
                    __('Test email sent successfully to %s', 'ninja-test-email'),
                    $to
                )
            );
        } else {
            return array(
                'success' => false,
                'message' => __('Failed to send test email. Please check your email configuration.', 'ninja-test-email')
            );
        }
    }

    /**
     * Validate email address
     *
     * @param string $email Email address to validate
     * @return array Validation result with valid flag and error message
     */
    public static function validate_email($email) {
        if (empty($email)) {
            return array(
                'valid' => false,
                'error' => __('Email address is required.', 'ninja-test-email')
            );
        }

        if (!is_email($email)) {
            return array(
                'valid' => false,
                'error' => __('Invalid email address format.', 'ninja-test-email')
            );
        }

        return array(
            'valid' => true,
            'error' => ''
        );
    }

    /**
     * Get default test email message
     *
     * @return string Default message content
     */
    private static function get_default_message() {
        $site_name = get_bloginfo('name');
        $site_url = get_bloginfo('url');
        $current_time = current_time('mysql');

        $message = sprintf(
            "This is a test email from %s.\n\n",
            $site_name
        );
        $message .= sprintf(
            "Site URL: %s\n",
            $site_url
        );
        $message .= sprintf(
            "Sent at: %s\n\n",
            $current_time
        );
        $message .= "If you received this email, your WordPress installation can send emails successfully.\n\n";
        $message .= "---\n";
        $message .= sprintf(
            "This email was sent by the Ninja Test Email plugin from %s",
            $site_name
        );

        return $message;
    }

    /**
     * Send test email with HTML format
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $message Email message body
     * @return array Result array with success status and message
     */
    public static function send_html_test_email($to, $subject = '', $message = '') {
        // Validate email address
        $validation = self::validate_email($to);
        if (!$validation['valid']) {
            return array(
                'success' => false,
                'message' => $validation['error']
            );
        }

        // Set default subject if empty
        if (empty($subject)) {
            $subject = 'Test Email from ' . get_bloginfo('name');
        }

        // Set default message if empty
        if (empty($message)) {
            $message = self::get_default_html_message();
        }

        // Sanitize inputs
        $to = sanitize_email($to);
        $subject = sanitize_text_field($subject);
        $message = wp_kses_post($message);

        // Set content type to HTML
        add_filter('wp_mail_content_type', function() {
            return 'text/html';
        });

        // Attempt to send email
        $sent = wp_mail($to, $subject, $message);

        // Reset content type
        remove_filter('wp_mail_content_type', function() {
            return 'text/html';
        });

        if ($sent) {
            return array(
                'success' => true,
                'message' => sprintf(
                    __('HTML test email sent successfully to %s', 'ninja-test-email'),
                    $to
                )
            );
        } else {
            return array(
                'success' => false,
                'message' => __('Failed to send HTML test email. Please check your email configuration.', 'ninja-test-email')
            );
        }
    }

    /**
     * Get default HTML test email message
     *
     * @return string Default HTML message content
     */
    private static function get_default_html_message() {
        $site_name = get_bloginfo('name');
        $site_url = get_bloginfo('url');
        $current_time = current_time('mysql');

        $message = '<!DOCTYPE html>';
        $message .= '<html><head><meta charset="UTF-8"></head><body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">';
        $message .= '<div style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; border: 1px solid #ddd;">';
        $message .= '<h2 style="color: #0073aa;">Test Email from ' . esc_html($site_name) . '</h2>';
        $message .= '<p><strong>Site URL:</strong> <a href="' . esc_url($site_url) . '">' . esc_html($site_url) . '</a></p>';
        $message .= '<p><strong>Sent at:</strong> ' . esc_html($current_time) . '</p>';
        $message .= '<div style="background-color: #fff; padding: 15px; margin: 20px 0; border-left: 4px solid #0073aa;">';
        $message .= '<p>If you received this email, your WordPress installation can send emails successfully.</p>';
        $message .= '</div>';
        $message .= '<hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">';
        $message .= '<p style="font-size: 12px; color: #666;">This email was sent by the Ninja Test Email plugin from ' . esc_html($site_name) . '</p>';
        $message .= '</div></body></html>';

        return $message;
    }
}
