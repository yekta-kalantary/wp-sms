<?php
/**
 * Plugin Name: Yekta Gateway SMS.ir
 * Description: SMS.ir gateway adapter for Yekta SMS Core.
 * Version: 0.1.0
 * Author: Yekta SMS
 * Text Domain: yekta-geateway-smsir
 * Requires PHP: 7.4
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('YEKTA_SMS_GATEWAY_SMSIR_FILE', __FILE__);
define('YEKTA_SMS_GATEWAY_SMSIR_PATH', plugin_dir_path(__FILE__));
define('YEKTA_SMS_GATEWAY_SMSIR_VERSION', '0.1.0');

spl_autoload_register(
    static function (string $class): void {
        $prefix = 'YektaSMS\\Gateway\\SmsIr\\';

        if (strpos($class, $prefix) !== 0) {
            return;
        }

        $relative = substr($class, strlen($prefix));
        $path = YEKTA_SMS_GATEWAY_SMSIR_PATH . 'src/' . str_replace('\\', '/', $relative) . '.php';

        if (file_exists($path)) {
            require_once $path;
        }
    }
);

add_action(
    'plugins_loaded',
    static function (): void {
        load_plugin_textdomain('yekta-geateway-smsir', false, dirname(plugin_basename(__FILE__)) . '/languages');
        (new YektaSMS\Gateway\SmsIr\Bootstrap\Plugin())->boot();
    }
);
