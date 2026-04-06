<?php
/**
 * Plugin Name: Yekta SMS Core
 * Description: Core contracts and orchestration for Yekta SMS ecosystem.
 * Version: 0.1.0
 * Author: Yekta SMS
 * Text Domain: yekta-sms-core
 * Requires PHP: 7.4
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('YEKTA_SMS_CORE_FILE', __FILE__);
define('YEKTA_SMS_CORE_PATH', plugin_dir_path(__FILE__));
define('YEKTA_SMS_CORE_URL', plugin_dir_url(__FILE__));
define('YEKTA_SMS_CORE_VERSION', '0.1.0');
define('YEKTA_SMS_CORE_DB_VERSION', '1');

spl_autoload_register(
    static function (string $class): void {
        $prefix = 'YektaSMS\\Core\\';

        if (strpos($class, $prefix) !== 0) {
            return;
        }

        $relative = substr($class, strlen($prefix));
        $path = YEKTA_SMS_CORE_PATH . 'src/' . str_replace('\\', '/', $relative) . '.php';

        if (file_exists($path)) {
            require_once $path;
        }
    }
);

register_activation_hook(__FILE__, ['YektaSMS\\Core\\Bootstrap\\Activation', 'run']);
register_deactivation_hook(__FILE__, ['YektaSMS\\Core\\Bootstrap\\Deactivation', 'run']);

add_action(
    'plugins_loaded',
    static function (): void {
        load_plugin_textdomain('yekta-sms-core', false, dirname(plugin_basename(__FILE__)) . '/languages');
        (new YektaSMS\Core\Bootstrap\Plugin())->boot();
    }
);
