<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Consent;

use WC_Order;

final class ConsentManager
{
    public function canSend(WC_Order $order, bool $requireOptIn, bool $globalRespectOptIn): bool
    {
        if (!$requireOptIn && !$globalRespectOptIn) {
            return true;
        }

        return (bool) $order->get_meta('_yekta_sms_opt_in', true);
    }
}
