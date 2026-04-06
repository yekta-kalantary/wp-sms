<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Support;

use WC_Order;

final class PlaceholderRegistry
{
    public function placeholders(WC_Order $order, array $context = []): array
    {
        return [
            'order.id' => (string) $order->get_id(),
            'order.number' => (string) $order->get_order_number(),
            'order.total' => (string) $order->get_total(),
            'customer.id' => (string) $order->get_customer_id(),
            'customer.email' => (string) $order->get_billing_email(),
            'billing.phone' => (string) $order->get_billing_phone(),
            'billing.first_name' => (string) $order->get_billing_first_name(),
            'shipping.first_name' => (string) $order->get_shipping_first_name(),
            'store.name' => (string) get_bloginfo('name'),
            'site.url' => (string) site_url(),
            'payment.method' => (string) $order->get_payment_method_title(),
            'status.from' => (string) ($context['from_status'] ?? ''),
            'status.to' => (string) ($context['to_status'] ?? $order->get_status()),
            'status.current' => (string) $order->get_status(),
            'customer.note' => (string) ($context['customer_note'] ?? ''),
        ];
    }

    public function render(string $template, array $placeholders, array $required = []): array
    {
        preg_match_all('/\{([a-z0-9_.]+)\}/i', $template, $matches);
        $unknown = [];

        foreach ($matches[1] as $token) {
            if (!array_key_exists($token, $placeholders)) {
                $unknown[] = $token;
            }
        }

        foreach ($required as $requiredKey) {
            if (!isset($placeholders[$requiredKey]) || trim((string) $placeholders[$requiredKey]) === '') {
                return ['ok' => false, 'body' => '', 'unknown' => $unknown, 'missing_required' => [$requiredKey]];
            }
        }

        $body = preg_replace_callback('/\{([a-z0-9_.]+)\}/i', static function (array $match) use ($placeholders): string {
            return array_key_exists($match[1], $placeholders) ? (string) $placeholders[$match[1]] : '';
        }, $template) ?: '';

        return ['ok' => true, 'body' => $body, 'unknown' => array_values(array_unique($unknown)), 'missing_required' => []];
    }
}
