<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Config;

final class WooSettings
{
    public const OPTION_KEY = 'yekta_sms_wc_settings';

    public function defaults(): array
    {
        return [
            'enabled' => false,
            'send_mode' => 'live',
            'respect_opt_in' => true,
            'write_order_notes' => true,
            'retry_policy' => 'inherit_core',
            'manual_resend_enabled' => true,
            'customer_phone_source' => 'billing_phone',
            'admin_phone_list' => '',
        ];
    }

    public function get(): array
    {
        $saved = get_option(self::OPTION_KEY, []);
        if (!is_array($saved)) {
            $saved = [];
        }

        return array_merge($this->defaults(), $saved);
    }

    public function register(): void
    {
        register_setting('yekta_sms_wc', self::OPTION_KEY, [$this, 'sanitize']);
    }

    public function sanitize($input): array
    {
        $input = is_array($input) ? $input : [];
        $defaults = $this->defaults();

        return [
            'enabled' => !empty($input['enabled']),
            'send_mode' => in_array(($input['send_mode'] ?? ''), ['live', 'dry_run'], true) ? (string) $input['send_mode'] : $defaults['send_mode'],
            'respect_opt_in' => !empty($input['respect_opt_in']),
            'write_order_notes' => !empty($input['write_order_notes']),
            'retry_policy' => in_array(($input['retry_policy'] ?? ''), ['inherit_core', 'never', 'always'], true) ? (string) $input['retry_policy'] : $defaults['retry_policy'],
            'manual_resend_enabled' => !empty($input['manual_resend_enabled']),
            'customer_phone_source' => in_array(($input['customer_phone_source'] ?? ''), ['billing_phone', 'shipping_phone', 'meta'], true) ? (string) $input['customer_phone_source'] : $defaults['customer_phone_source'],
            'admin_phone_list' => sanitize_textarea_field((string) ($input['admin_phone_list'] ?? '')),
        ];
    }
}
