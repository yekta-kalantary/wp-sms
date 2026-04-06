<?php
declare(strict_types=1);

if (!function_exists('__')) {
    function __($text, $domain = null) {
        return $text;
    }
}

if (!function_exists('esc_html__')) {
    function esc_html__($text, $domain = null) {
        return $text;
    }
}

if (!function_exists('sanitize_key')) {
    function sanitize_key($key) {
        $key = strtolower((string) $key);
        return preg_replace('/[^a-z0-9_\-]/', '', $key) ?? '';
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($text) {
        return trim((string) $text);
    }
}

if (!function_exists('sanitize_textarea_field')) {
    function sanitize_textarea_field($text) {
        return trim((string) $text);
    }
}

if (!function_exists('get_option')) {
    function get_option($option, $default = false) {
        return $default;
    }
}

if (!function_exists('register_setting')) {
    function register_setting($option_group, $option_name, $args = []) {
        return true;
    }
}

if (!function_exists('wp_parse_args')) {
    function wp_parse_args($args, $defaults = []) {
        if (is_object($args)) {
            $args = get_object_vars($args);
        }

        if (!is_array($args)) {
            parse_str((string) $args, $args);
        }

        if (!is_array($defaults)) {
            $defaults = [];
        }

        return array_merge($defaults, $args);
    }
}


if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}

if (!defined('ARRAY_A')) {
    define('ARRAY_A', 'ARRAY_A');
}

if (!function_exists('apply_filters')) {
    function apply_filters($hook_name, $value) {
        return $value;
    }
}

if (!defined('YEKTA_SMS_INTEGRATION_WC_SLUG')) {
    define('YEKTA_SMS_INTEGRATION_WC_SLUG', 'yekta-integration-woocomrce');
}

if (!defined('YEKTA_SMS_INTEGRATION_WC_VERSION')) {
    define('YEKTA_SMS_INTEGRATION_WC_VERSION', '0.1.0');
}

if (!defined('YEKTA_SMS_GATEWAY_SMSIR_VERSION')) {
    define('YEKTA_SMS_GATEWAY_SMSIR_VERSION', '0.1.0');
}
