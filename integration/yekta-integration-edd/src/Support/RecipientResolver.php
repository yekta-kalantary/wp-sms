<?php
declare(strict_types=1);

namespace YektaSMS\Integration\EDD\Support;

final class RecipientResolver
{
    public function resolve(object $order, ?object $customer, array $mapping, array $settings): array
    {
        $recipientType = (string) ($mapping['recipient_type'] ?? 'customer');

        if ($recipientType === 'admin') {
            return $this->resolveAdmin($settings);
        }

        if ($recipientType === 'custom') {
            $normalized = $this->normalize((string) ($mapping['custom_phone'] ?? ''));
            return $normalized !== null ? [$normalized] : [];
        }

        $source = (string) ($settings['customer_phone_source'] ?? 'order_meta');
        $metaKey = (string) ($settings['customer_phone_meta_key'] ?? '_edd_phone_number');
        $raw = '';

        if ($source === 'customer_meta' && $customer !== null && !empty($customer->id)) {
            $raw = (string) edd_get_customer_meta((int) $customer->id, $metaKey, true);
        } elseif ($source === 'user_meta' && !empty($order->user_id)) {
            $raw = (string) get_user_meta((int) $order->user_id, $metaKey, true);
        } else {
            $raw = (string) edd_get_order_meta((int) ($order->id ?? 0), $metaKey, true);
        }

        $normalized = $this->normalize($raw);
        return $normalized !== null ? [$normalized] : [];
    }

    public function mask(string $phone): string
    {
        return strlen($phone) >= 7 ? substr($phone, 0, 4) . '***' . substr($phone, -3) : '***';
    }

    private function resolveAdmin(array $settings): array
    {
        $items = preg_split('/[\r\n,]+/', (string) ($settings['admin_phone_list'] ?? '')) ?: [];
        $phones = [];

        foreach ($items as $item) {
            $normalized = $this->normalize((string) $item);
            if ($normalized !== null) {
                $phones[] = $normalized;
            }
        }

        return array_values(array_unique($phones));
    }

    private function normalize(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (strpos($digits, '0098') === 0) {
            $digits = '0' . substr($digits, 4);
        } elseif (strpos($digits, '98') === 0) {
            $digits = '0' . substr($digits, 2);
        }

        if (preg_match('/^09\d{9}$/', $digits) !== 1) {
            return null;
        }

        return $digits;
    }
}
