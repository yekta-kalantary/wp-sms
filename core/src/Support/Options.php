<?php
declare(strict_types=1);
namespace YektaSMS\Core\Support;
final class Options
{
    public const SETTINGS = 'yekta_sms_core_settings';
    public const VERSION = 'yekta_sms_core_version';
    public const DB_VERSION = 'yekta_sms_core_db_version';
    public static function defaults(): array
    {
        return [
            'active_gateway' => '', 'dispatch_enabled' => true, 'log_level' => 'info', 'log_retention_days' => 30,
            'max_retry_attempts' => 3, 'scheduler_preference' => 'wp_cron', 'mask_logs' => true,
            'debug_mode' => false, 'capability_mode' => 'strict',
        ];
    }
}
