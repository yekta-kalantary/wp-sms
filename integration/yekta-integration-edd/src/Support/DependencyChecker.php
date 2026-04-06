<?php
declare(strict_types=1);

namespace YektaSMS\Integration\EDD\Support;

final class DependencyChecker
{
    public function isCoreAvailable(): bool
    {
        return interface_exists('YektaSMS\\Core\\Contracts\\MessageDispatcherInterface')
            && interface_exists('YektaSMS\\Core\\Contracts\\IntegrationDefinitionInterface')
            && class_exists('YektaSMS\\Core\\Domain\\MessageRequest');
    }

    public function isEddAvailable(): bool
    {
        return function_exists('edd_get_order') && function_exists('edd_get_customer') && class_exists('Easy_Digital_Downloads');
    }

    public function hasActiveGateway(): bool
    {
        $settings = get_option('yekta_sms_core_settings', []);
        return is_array($settings) && !empty($settings['active_gateway']);
    }
}
