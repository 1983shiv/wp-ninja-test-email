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
        $this->loader->add_action('wp_mail_failed', $this, 'log_failed_email', 10, 1);
        $this->loader->add_filter('wp_mail', $this, 'mark_email_sent', 999, 1);
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
                'status'   => 'Pending', // Will be updated to 'Sent' or 'Failed' after send attempt
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
     * Log failed email
     *
     * @param \WP_Error $wp_error The WP_Error object from wp_mail_failed
     */
    public function log_failed_email($wp_error) {
        global $wpdb;
        
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        try {
            // Get the most recent log entry (should be the one we just created)
            $table_name = $wpdb->prefix . 'ninja_knp_email_logs';
            
            $last_log = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT id FROM {$table_name} WHERE status = %s ORDER BY time DESC LIMIT 1",
                    'Pending'
                ),
                ARRAY_A
            );
            
            if ($last_log) {
                // Update status to Failed and store error message
                $error_message = $wp_error->get_error_message();
                
                $wpdb->update(
                    $table_name,
                    array(
                        'status' => 'Failed',
                        'body' => $wpdb->get_var(
                            $wpdb->prepare(
                                "SELECT body FROM {$table_name} WHERE id = %d",
                                $last_log['id']
                            )
                        ) . "\n\n--- ERROR ---\n" . $error_message
                    ),
                    array('id' => $last_log['id']),
                    array('%s', '%s'),
                    array('%d')
                );
            }
        } catch (\Exception $e) {
            // Silently catch errors
        }
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
    }
    
    /**
     * Mark email as successfully sent
     * This runs after wp_mail returns true
     *
     * @param array $atts Email attributes from wp_mail
     * @return array Return unmodified attributes
     */
    public function mark_email_sent($atts) {
        global $wpdb;
        
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        try {
            $table_name = $wpdb->prefix . 'ninja_knp_email_logs';
            
            // Update the most recent Pending email to Sent
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$table_name} SET status = %s WHERE status = %s ORDER BY time DESC LIMIT 1",
                    'Sent',
                    'Pending'
                )
            );
        } catch (\Exception $e) {
            // Silently catch errors
        }
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        
        // Return unmodified attributes so wp_mail continues normally
        return $atts;
    }
    
    /**
     * Update log status after email attempt
     *
     * @param \WP_Error|array|null $result Result of wp_mail
     */
    public static function update_log_status($result) {
        // This method can be used if we want to track failures
        // For now, we rely on wp_mail_failed hook to catch failures
        // Successful emails will remain as 'Pending' which we'll update to 'Sent'
    }
}
}