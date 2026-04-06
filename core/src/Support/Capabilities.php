<?php
declare(strict_types=1);
namespace YektaSMS\Core\Support;
final class Capabilities
{
    public const MANAGE = 'manage_options';
    public static function map(): array
    {
        return (array) apply_filters('yekta_sms_core_capability_map', [
            'view_dashboard' => self::MANAGE, 'manage_settings' => self::MANAGE,
            'view_logs' => self::MANAGE, 'run_diagnostics' => self::MANAGE, 'use_tools' => self::MANAGE,
        ]);
    }
    public static function can(string $permission): bool
    {
        $map = self::map();
        return current_user_can($map[$permission] ?? self::MANAGE);
    }
}
