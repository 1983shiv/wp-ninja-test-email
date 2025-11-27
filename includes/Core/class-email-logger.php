<?php
/**
 * Email Logger Class
 * 
 * Captures and logs all outgoing emails via phpmailer_init hook
 *
 * @package Ninja_KNP\Core
 */

namespace Ninja_KNP\Core;

use Ninja_KNP\Utils\Ninja_KNP_Singleton;
use Ninja_KNP\Utils\Ninja_KNP_Log_Manager;
use Ninja_KNP\Core\Ninja_KNP_Loader;

if (!class_exists('Ninja_KNP\Core\Ninja_KNP_Email_Logger')) {
    class Ninja_KNP_Email_Logger {
        use Ninja_KNP_Singleton;

    protected $loader;

    /**
     * Constructor
     *
     * @param Ninja_KNP_Loader $loader The loader instance
     */
    private function __construct(Ninja_KNP_Loader $loader) {
        $this->loader = $loader;
        $this->register_hooks();
    }

    /**
     * Register WordPress hooks
     */
    private function register_hooks() {
        $this->loader->add_action('phpmailer_init', $this, 'capture_email', 10, 1);
    }

    /**
     * Capture email details from PHPMailer
     *
     * @param object $phpmailer PHPMailer instance
     */
    public function capture_email($phpmailer) {
        try {
            // Get recipient email addresses
            $to_emails = array();
            
            // Get all recipients (To, CC, BCC)
            foreach ($phpmailer->getToAddresses() as $address) {
                $to_emails[] = $address[0];
            }
            
            // If no To addresses, try other recipient types
            if (empty($to_emails)) {
                foreach ($phpmailer->getCcAddresses() as $address) {
                    $to_emails[] = $address[0];
                }
            }
            
            if (empty($to_emails)) {
                foreach ($phpmailer->getBccAddresses() as $address) {
                    $to_emails[] = $address[0];
                }
            }

            // Join multiple recipients
            $to_email = implode(', ', $to_emails);

            // Get subject
            $subject = $phpmailer->Subject;

            // Get body (check if HTML or plain text)
            $body = !empty($phpmailer->Body) ? $phpmailer->Body : $phpmailer->AltBody;

            // Prepare log data
            $log_data = array(
                'to_email' => $to_email,
                'subject'  => $subject,
                'body'     => $body,
                'status'   => 'Sent', // Will be updated to 'Failed' if wp_mail returns false
            );

            // Insert log
            $log_id = Ninja_KNP_Log_Manager::insert_log($log_data);

            if ($log_id) {
                // Store log ID temporarily for potential status update
                $phpmailer->log_id = $log_id;
            }

        } catch (\Exception $e) {
            // Silently catch errors to prevent breaking email sending
        }
    }

    /**
     * Update log status after email attempt
     *
     * @param \WP_Error|array|null $result Result of wp_mail
     */
    public static function update_log_status($result) {
        // This method can be used if we want to track failures
        // For now, we log all emails as "Sent" when they're attempted
    }
}
}