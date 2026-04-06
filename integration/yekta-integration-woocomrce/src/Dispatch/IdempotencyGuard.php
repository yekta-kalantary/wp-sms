<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Dispatch;

use WC_Order;

final class IdempotencyGuard
{
    private const PREFIX = '_yekta_sms_sent_';

    public function key(string $event, int $orderId, string $recipient, string $mappingVersion): string
    {
        return hash('sha256', implode('|', [YEKTA_SMS_INTEGRATION_WC_SLUG, $event, (string) $orderId, $recipient, $mappingVersion]));
    }

    public function isDuplicate(WC_Order $order, string $key): bool
    {
        return (bool) $order->get_meta(self::PREFIX . $key, true);
    }

    public function mark(WC_Order $order, string $key, int $dispatchId): void
    {
        $order->update_meta_data(self::PREFIX . $key, (string) time());
        $history = $order->get_meta('_yekta_sms_last_dispatch_ids', true);
        $ids = is_array($history) ? $history : [];
        $ids[] = $dispatchId;
        $order->update_meta_data('_yekta_sms_last_dispatch_ids', array_values(array_unique($ids)));
        $order->save();
    }
}
