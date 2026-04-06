<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Registration;

use YektaSMS\Core\Contracts\IntegrationDefinitionInterface;

final class IntegrationFactory
{
    public function makeDefinition(): IntegrationDefinitionInterface
    {
        return new class () implements IntegrationDefinitionInterface {
            public function getSlug(): string
            {
                return YEKTA_SMS_INTEGRATION_WC_SLUG;
            }

            public function getLabel(): string
            {
                return 'WooCommerce';
            }

            public function getVersion(): string
            {
                return (string) YEKTA_SMS_INTEGRATION_WC_VERSION;
            }
        };
    }
}
