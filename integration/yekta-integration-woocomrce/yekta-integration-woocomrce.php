<?php
/**
 * Plugin Name: Yekta Integration WooComrce
 * Description: WooCommerce integration for Yekta SMS Core.
 * Version: 0.1.0
 * Author: Yekta SMS
 * Text Domain: yekta-integration-woocomrce
 * Requires PHP: 7.4
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('YEKTA_SMS_INTEGRATION_WC_FILE', __FILE__);
define('YEKTA_SMS_INTEGRATION_WC_PATH', plugin_dir_path(__FILE__));
define('YEKTA_SMS_INTEGRATION_WC_VERSION', '0.1.0');
define('YEKTA_SMS_INTEGRATION_WC_SLUG', 'yekta-integration-woocomrce');

spl_autoload_register(
    static function (string $class): void {
        $prefix = 'YektaSMS\\Integration\\WooComrce\\';

        if (strpos($class, $prefix) !== 0) {
            return;
        }

        $relative = substr($class, strlen($prefix));
        $path = YEKTA_SMS_INTEGRATION_WC_PATH . 'src/' . str_replace('\\', '/', $relative) . '.php';

        if (file_exists($path)) {
            require_once $path;
        }
    }
);

add_action(
    'plugins_loaded',
    static function (): void {
        load_plugin_textdomain('yekta-integration-woocomrce', false, dirname(plugin_basename(__FILE__)) . '/languages');
        (new YektaSMS\Integration\WooComrce\Bootstrap\Plugin())->boot();
    }
);
