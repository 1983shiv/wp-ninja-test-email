<?php
namespace Ninja_KNP\Utils;


if (!class_exists('Ninja_KNP\Utils\Ninja_KNP_Helpers')) {
class Ninja_KNP_Helpers {
    public static function sanitize_array($array) {
        if (!is_array($array)) {
            return array();
        }

        $sanitized = array();

        foreach ($array as $key => $value) {
            $key = sanitize_key($key);

            if (is_array($value)) {
                $sanitized[$key] = self::sanitize_array($value);
            } elseif (is_bool($value)) {
                $sanitized[$key] = $value;
            } elseif ($value === 'true') {
                $sanitized[$key] = true;
            } elseif ($value === 'false') {
                $sanitized[$key] = false;
            } else {
                $sanitized[$key] = sanitize_text_field($value);
            }
        }

        return $sanitized;
    }

    public static function current_user_can_manage() {
        return current_user_can('manage_options');
    }

    public static function get_option($key, $default = null) {
        $options = get_option('ninja_knp_options', array());
        return isset($options[$key]) ? $options[$key] : $default;
    }

    public static function update_option($key, $value) {
        $options = get_option('ninja_knp_options', array());
        $options[$key] = $value;
        return update_option('ninja_knp_options', $options);
    }

    public static function delete_option($key) {
        $options = get_option('ninja_knp_options', array());

        if (isset($options[$key])) {
            unset($options[$key]);
            return update_option('ninja_knp_options', $options);
        }

        return false;
    }
}



}
