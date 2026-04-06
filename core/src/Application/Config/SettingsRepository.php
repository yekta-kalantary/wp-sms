<?php
declare(strict_types=1);
namespace YektaSMS\Core\Application\Config;
use YektaSMS\Core\Support\Options;
final class SettingsRepository
{
    public function all(): array { return wp_parse_args((array) get_option(Options::SETTINGS, []), Options::defaults()); }
    public function get(string $key, $default=null) { $v=$this->all(); return $v[$key] ?? $default; }
    public function update(array $settings): bool { return update_option(Options::SETTINGS, $this->sanitize($settings), false); }
    public function sanitize(array $settings): array
    {
        $in=wp_parse_args($settings, Options::defaults());
        return [
            'active_gateway'=>sanitize_key((string)$in['active_gateway']),
            'dispatch_enabled'=>(bool)$in['dispatch_enabled'],
            'log_level'=>sanitize_key((string)$in['log_level']),
            'log_retention_days'=>max(1,(int)$in['log_retention_days']),
            'max_retry_attempts'=>max(0,(int)$in['max_retry_attempts']),
            'scheduler_preference'=>sanitize_key((string)$in['scheduler_preference']),
            'mask_logs'=>(bool)$in['mask_logs'],
            'debug_mode'=>(bool)$in['debug_mode'],
            'capability_mode'=>sanitize_key((string)$in['capability_mode']),
        ];
    }
}
