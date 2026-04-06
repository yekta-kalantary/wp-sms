<?php
declare(strict_types=1);

namespace YektaSMS\Integration\EDD\Support;

final class PlaceholderRegistry
{
    public function placeholders(object $order, ?object $customer = null, array $context = []): array
    {
        $statusFromContext = (string) ($context['to_status'] ?? '');

        return [
            'order.id' => (string) ($order->id ?? 0),
            'order.number' => (string) ($order->order_number ?? ''),
            'order.total' => (string) ($order->total ?? ''),
            'customer.id' => (string) ($order->customer_id ?? ''),
            'customer.email' => (string) ($order->email ?? ''),
            'customer.name' => (string) ($customer->name ?? ''),
            'payment.gateway' => (string) ($order->gateway ?? ''),
            'download.count' => (string) (is_array($order->items ?? null) ? count($order->items) : 0),
            'store.name' => (string) get_bloginfo('name'),
            'site.url' => (string) site_url(),
            'status.from' => (string) ($context['from_status'] ?? ''),
            'status.to' => $statusFromContext,
            'status.current' => $statusFromContext !== '' ? $statusFromContext : (string) ($order->status ?? ''),
            'order.note' => (string) ($context['order_note'] ?? ''),
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
                return ['ok' => false, 'body' => '', 'unknown' => array_values(array_unique($unknown)), 'missing_required' => [$requiredKey]];
            }
        }

        $body = preg_replace_callback('/\{([a-z0-9_.]+)\}/i', static function (array $match) use ($placeholders): string {
            return array_key_exists($match[1], $placeholders) ? (string) $placeholders[$match[1]] : '';
        }, $template) ?: '';

        return ['ok' => true, 'body' => $body, 'unknown' => array_values(array_unique($unknown)), 'missing_required' => []];
    }
}
