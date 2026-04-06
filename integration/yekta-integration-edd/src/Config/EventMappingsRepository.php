<?php
declare(strict_types=1);

namespace YektaSMS\Integration\EDD\Config;

final class EventMappingsRepository
{
    public const OPTION_KEY = 'yekta_sms_edd_event_mappings';

    public function register(): void
    {
        register_setting('yekta_sms_edd', self::OPTION_KEY, [$this, 'sanitize']);
    }

    public function all(): array
    {
        $saved = get_option(self::OPTION_KEY, []);
        $saved = is_array($saved) ? $saved : [];
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
        return sha1((string) (wp_json_encode($this->get($event)) ?: $event));
    }

    public function defaults(): array
    {
        $events = [
            'customer.order.created',
            'customer.order.completed',
            'customer.order.refunded',
            'customer.order.failed',
            'customer.order.pending',
            'customer.order.revoked',
            'customer.order.note.added',
            'admin.order.created',
            'admin.order.completed',
            'admin.order.refunded',
            'admin.order.failed',
        ];

        $defaults = [];
        foreach ($events as $event) {
            $isAdmin = strpos($event, 'admin.') === 0;
            $defaults[$event] = [
                'enabled' => false,
                'recipient_type' => $isAdmin ? 'admin' : 'customer',
                'phone_source' => $isAdmin ? 'admin_phone_list' : 'customer_phone_source',
                'custom_phone' => '',
                'message_mode' => 'body',
                'provider_template_ref' => '',
                'body_template' => 'Order #{order.id} status: {status.current}',
                'parameter_map' => [],
                'require_opt_in' => !$isAdmin,
                'retry_enabled' => true,
                'add_order_note' => false,
                'dispatch_timing' => 'immediate',
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
        return [
            'enabled' => !empty($config['enabled']),
            'recipient_type' => in_array(($config['recipient_type'] ?? ''), ['customer', 'admin', 'custom'], true) ? (string) $config['recipient_type'] : 'customer',
            'phone_source' => in_array(($config['phone_source'] ?? ''), ['customer_phone_source', 'admin_phone_list', 'custom_phone'], true) ? (string) $config['phone_source'] : 'customer_phone_source',
            'custom_phone' => sanitize_text_field((string) ($config['custom_phone'] ?? '')),
            'message_mode' => in_array(($config['message_mode'] ?? ''), ['body', 'provider_template'], true) ? (string) $config['message_mode'] : 'body',
            'provider_template_ref' => sanitize_text_field((string) ($config['provider_template_ref'] ?? '')),
            'body_template' => sanitize_textarea_field((string) ($config['body_template'] ?? '')),
            'parameter_map' => is_array($config['parameter_map'] ?? null) ? $config['parameter_map'] : [],
            'require_opt_in' => !empty($config['require_opt_in']),
            'retry_enabled' => !empty($config['retry_enabled']),
            'add_order_note' => !empty($config['add_order_note']),
            'dispatch_timing' => in_array(($config['dispatch_timing'] ?? ''), ['immediate', 'after_order'], true) ? (string) $config['dispatch_timing'] : 'immediate',
        ];
    }
}
