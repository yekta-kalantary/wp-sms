<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Compatibility;

final class Declarations
{
    public function declare(): void
    {
        add_action('before_woocommerce_init', static function (): void {
            if (!class_exists('Automattic\\WooCommerce\\Utilities\\FeaturesUtil')) {
                return;
            }

            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', YEKTA_SMS_INTEGRATION_WC_FILE, true);
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', YEKTA_SMS_INTEGRATION_WC_FILE, true);
        });
    }
}
