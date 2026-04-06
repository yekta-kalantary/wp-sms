<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Support;

use WC_Order;

final class RecipientResolver
{
    public function resolve(WC_Order $order, array $mapping, array $settings): array
    {
        $type = (string) ($mapping['recipient_type'] ?? 'customer');

        if ($type === 'admin') {
            return $this->resolveAdmin($settings);
        }

        if ($type === 'custom') {
            $phone = $this->normalize((string) ($mapping['custom_phone'] ?? ''));
            return $phone !== null ? [$phone] : [];
        }

        $source = (string) ($mapping['phone_source'] ?? ($settings['customer_phone_source'] ?? 'billing_phone'));
        $raw = '';

        if ($source === 'shipping_phone') {
            $raw = (string) $order->get_shipping_phone();
        } elseif ($source === 'meta') {
            $raw = (string) $order->get_meta('_yekta_sms_opt_phone', true);
        } else {
            $raw = (string) $order->get_billing_phone();
        }

        $phone = $this->normalize($raw);
        return $phone !== null ? [$phone] : [];
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
            $phone = $this->normalize($item);
            if ($phone !== null) {
                $phones[] = $phone;
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
