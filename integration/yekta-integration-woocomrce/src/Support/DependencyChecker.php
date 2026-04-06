<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Support;

final class DependencyChecker
{
    public function isCoreAvailable(): bool
    {
        return interface_exists('YektaSMS\\Core\\Contracts\\MessageDispatcherInterface')
            && interface_exists('YektaSMS\\Core\\Contracts\\IntegrationDefinitionInterface')
            && class_exists('YektaSMS\\Core\\Domain\\MessageRequest');
    }

    public function isWooAvailable(): bool
    {
        return function_exists('wc_get_order') && class_exists('WooCommerce');
    }

    public function hasActiveGateway(): bool
    {
        $settings = get_option('yekta_sms_core_settings', []);
        if (!is_array($settings)) {
            return false;
        }

        return !empty($settings['active_gateway']);
    }
}
