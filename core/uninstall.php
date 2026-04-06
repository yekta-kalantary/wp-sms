<?php
/**
 * Uninstall for Yekta SMS Core.
 */
declare(strict_types=1);

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('yekta_sms_core_settings');
delete_option('yekta_sms_core_version');
delete_option('yekta_sms_core_db_version');
