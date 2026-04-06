<?php
declare(strict_types=1);

namespace YektaSMS\Integration\EDD\Dispatch;

final class IdempotencyGuard
{
    private const PREFIX = '_yekta_sms_sent_';

    public function key(string $event, int $orderId, string $recipient, string $mappingVersion): string
    {
        return hash('sha256', implode('|', [YEKTA_SMS_INTEGRATION_EDD_SLUG, $event, (string) $orderId, $recipient, $mappingVersion]));
    }

    public function isDuplicate(int $orderId, string $key): bool
    {
        return (bool) edd_get_order_meta($orderId, self::PREFIX . $key, true);
    }

    public function mark(int $orderId, string $key, int $dispatchId): void
    {
        edd_update_order_meta($orderId, self::PREFIX . $key, (string) time());

        $history = edd_get_order_meta($orderId, '_yekta_sms_last_dispatch_ids', true);
        $ids = is_array($history) ? $history : [];
        $ids[] = $dispatchId;
        edd_update_order_meta($orderId, '_yekta_sms_last_dispatch_ids', array_values(array_unique($ids)));
    }
}
