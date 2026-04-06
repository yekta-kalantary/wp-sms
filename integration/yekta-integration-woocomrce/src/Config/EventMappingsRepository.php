<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Config;

final class EventMappingsRepository
{
    public const OPTION_KEY = 'yekta_sms_wc_event_mappings';

    public function register(): void
    {
        register_setting('yekta_sms_wc', self::OPTION_KEY, [$this, 'sanitize']);
    }

    public function all(): array
    {
        $saved = get_option(self::OPTION_KEY, []);
        if (!is_array($saved)) {
            $saved = [];
        }

        $defaults = $this->defaults();
        foreach ($defaults as $event => $defaultConfig) {
            $saved[$event] = isset($saved[$event]) && is_array($saved[$event])
                ? $this->sanitizeSingle(array_merge($defaultConfig, $saved[$event]))
                : $defaultConfig;
        }

        return $saved;
    }

    public function get(string $event): array
    {
        $all = $this->all();
        return $all[$event] ?? [];
    }

    public function mappingVersion(string $event): string
    {
        return sha1(wp_json_encode($this->get($event)) ?: $event);
    }

    public function defaults(): array
    {
        $events = [
            'customer.order.placed','customer.order.paid','customer.order.processing','customer.order.completed',
            'customer.order.on_hold','customer.order.cancelled','customer.order.failed','customer.order.refunded','customer.note.added',
            'admin.order.placed','admin.order.paid',
        ];

        $defaults = [];
        foreach ($events as $event) {
            $isAdmin = strpos($event, 'admin.') === 0;
            $defaults[$event] = [
                'enabled' => false,
                'recipient_type' => $isAdmin ? 'admin' : 'customer',
                'phone_source' => $isAdmin ? 'admin_phone_list' : 'billing_phone',
                'custom_phone' => '',
                'message_mode' => 'body',
                'provider_template_ref' => '',
                'body_template' => 'Order #{order.id} status is {status.to}.',
                'parameter_map' => [],
                'require_opt_in' => false,
                'retry_enabled' => true,
                'add_order_note' => false,
            ];
        }

        return $defaults;
    }

    public function sanitize($input): array
    {
        $input = is_array($input) ? $input : [];
        $sanitized = [];

        foreach ($this->defaults() as $event => $defaultConfig) {
            $sanitized[$event] = $this->sanitizeSingle(isset($input[$event]) && is_array($input[$event]) ? $input[$event] : $defaultConfig);
        }

        return $sanitized;
    }

    private function sanitizeSingle(array $config): array
    {
        $recipientType = in_array(($config['recipient_type'] ?? ''), ['customer', 'admin', 'custom'], true) ? (string) $config['recipient_type'] : 'customer';
        $phoneSource = in_array(($config['phone_source'] ?? ''), ['billing_phone', 'shipping_phone', 'admin_phone_list', 'custom_phone', 'meta'], true) ? (string) $config['phone_source'] : 'billing_phone';
        $mode = in_array(($config['message_mode'] ?? ''), ['body', 'provider_template'], true) ? (string) $config['message_mode'] : 'body';

        return [
            'enabled' => !empty($config['enabled']),
            'recipient_type' => $recipientType,
            'phone_source' => $phoneSource,
            'custom_phone' => sanitize_text_field((string) ($config['custom_phone'] ?? '')),
            'message_mode' => $mode,
            'provider_template_ref' => sanitize_text_field((string) ($config['provider_template_ref'] ?? '')),
            'body_template' => sanitize_textarea_field((string) ($config['body_template'] ?? '')),
            'parameter_map' => is_array($config['parameter_map'] ?? null) ? $config['parameter_map'] : [],
            'require_opt_in' => !empty($config['require_opt_in']),
            'retry_enabled' => !empty($config['retry_enabled']),
            'add_order_note' => !empty($config['add_order_note']),
        ];
    }
}
