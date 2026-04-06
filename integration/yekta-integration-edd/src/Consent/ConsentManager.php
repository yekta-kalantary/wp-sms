<?php
declare(strict_types=1);

namespace YektaSMS\Integration\EDD\Consent;

final class ConsentManager
{
    public function canSend(object $order, ?object $customer, bool $requireOptIn, bool $globalRespectOptIn, string $optInMetaKey): bool
    {
        if (!$requireOptIn && !$globalRespectOptIn) {
            return true;
        }

        $orderId = (int) ($order->id ?? 0);
        if ($orderId > 0 && edd_get_order_meta($orderId, $optInMetaKey, true)) {
            return true;
        }

        if ($customer !== null && !empty($customer->id)) {
            return (bool) edd_get_customer_meta((int) $customer->id, $optInMetaKey, true);
        }

        return false;
    }
}
