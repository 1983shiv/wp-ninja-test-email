<?php
/**
 * Log Manager Class
 * 
 * Handles all database operations for email logs
 *
 * @package NinjaTestEmail\Utils
 */

namespace NinjaTestEmail\Utils;

class LogManager {
    
    /**
     * Get table name with WordPress prefix
     *
     * @return string Full table name
     */
    public static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'ninja_test_email_logs';
    }

    /**
     * Insert a new email log entry
     *
     * @param array $data Log data (to_email, subject, body, status)
     * @return int|false Log ID on success, false on failure
     */
    public static function insert_log($data) {
        global $wpdb;
        $table_name = self::get_table_name();

        $insert_data = array(
            'time'      => current_time('mysql'),
            'to_email'  => isset($data['to_email']) ? sanitize_email($data['to_email']) : '',
            'subject'   => isset($data['subject']) ? sanitize_text_field($data['subject']) : '',
            'body'      => isset($data['body']) ? wp_kses_post($data['body']) : '',
            'status'    => isset($data['status']) ? sanitize_text_field($data['status']) : 'Sent',
        );

        $result = $wpdb->insert(
            $table_name,
            $insert_data,
            array('%s', '%s', '%s', '%s', '%s')
        );

        if ($result === false) {
            error_log('Ninja Test Email: Failed to insert log - ' . $wpdb->last_error);
            return false;
        }

        return $wpdb->insert_id;
    }

    /**
     * Get all logs with optional search, sorting, and pagination
     *
     * @param array $args Query arguments
     * @return array Array of log objects
     */
    public static function get_logs($args = array()) {
        global $wpdb;
        $table_name = self::get_table_name();

        $defaults = array(
            'search'   => '',
            'orderby'  => 'time',
            'order'    => 'DESC',
            'per_page' => 10,
            'page'     => 1,
        );

        $args = wp_parse_args($args, $defaults);

        // Build WHERE clause for search
        $where = '';
        if (!empty($args['search'])) {
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $where = $wpdb->prepare(
                ' WHERE to_email LIKE %s OR subject LIKE %s OR body LIKE %s',
                $search,
                $search,
                $search
            );
        }

        // Validate orderby
        $allowed_orderby = array('time', 'to_email', 'subject', 'status');
        $orderby = in_array($args['orderby'], $allowed_orderby, true) ? $args['orderby'] : 'time';

        // Validate order
        $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';

        // Calculate offset
        $per_page = absint($args['per_page']);
        $page = absint($args['page']);
        $offset = ($page - 1) * $per_page;

        // Build and execute query
        $query = "SELECT * FROM {$table_name}{$where} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";
        $results = $wpdb->get_results(
            $wpdb->prepare($query, $per_page, $offset),
            OBJECT
        );

        return $results;
    }

    /**
     * Get total count of logs
     *
     * @param string $search Optional search term
     * @return int Total count
     */
    public static function get_total_count($search = '') {
        global $wpdb;
        $table_name = self::get_table_name();

        if (!empty($search)) {
            $search = '%' . $wpdb->esc_like($search) . '%';
            $count = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$table_name} WHERE to_email LIKE %s OR subject LIKE %s OR body LIKE %s",
                    $search,
                    $search,
                    $search
                )
            );
        } else {
            $count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
        }

        return absint($count);
    }

    /**
     * Get a single log by ID
     *
     * @param int $log_id Log ID
     * @return object|null Log object or null if not found
     */
    public static function get_log_by_id($log_id) {
        global $wpdb;
        $table_name = self::get_table_name();

        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", absint($log_id)),
            OBJECT
        );
    }

    /**
     * Delete a log by ID
     *
     * @param int $log_id Log ID
     * @return bool True on success, false on failure
     */
    public static function delete_log($log_id) {
        global $wpdb;
        $table_name = self::get_table_name();

        $result = $wpdb->delete(
            $table_name,
            array('id' => absint($log_id)),
            array('%d')
        );

        return $result !== false;
    }

    /**
     * Delete logs older than specified days
     *
     * @param int $days Number of days (default 30)
     * @return int|false Number of rows deleted or false on failure
     */
    public static function delete_old_logs($days = 30) {
        global $wpdb;
        $table_name = self::get_table_name();

        $days = absint($days);
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $result = $wpdb->query(
            $wpdb->prepare("DELETE FROM {$table_name} WHERE time < %s", $date)
        );

        if ($result !== false) {
            error_log("Ninja Test Email: Deleted {$result} old log entries older than {$days} days");
        }

        return $result;
    }

    /**
     * Delete all logs
     *
     * @return int|false Number of rows deleted or false on failure
     */
    public static function delete_all_logs() {
        global $wpdb;
        $table_name = self::get_table_name();

        return $wpdb->query("TRUNCATE TABLE {$table_name}");
    }

    /**
     * Get statistics about logs
     *
     * @return array Statistics data
     */
    public static function get_statistics() {
        global $wpdb;
        $table_name = self::get_table_name();

        $total = self::get_total_count();
        
        // Get count by status
        $status_counts = $wpdb->get_results(
            "SELECT status, COUNT(*) as count FROM {$table_name} GROUP BY status",
            OBJECT_K
        );

        // Get today's count
        $today_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE DATE(time) = %s",
                current_time('Y-m-d')
            )
        );

        // Get this week's count
        $week_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE time >= %s",
                date('Y-m-d H:i:s', strtotime('-7 days'))
            )
        );

        // Get this month's count
        $month_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE time >= %s",
                date('Y-m-d H:i:s', strtotime('-30 days'))
            )
        );

        return array(
            'total'        => absint($total),
            'today'        => absint($today_count),
            'week'         => absint($week_count),
            'month'        => absint($month_count),
            'by_status'    => $status_counts,
        );
    }
}
