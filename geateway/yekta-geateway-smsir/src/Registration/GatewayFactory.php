<?php
declare(strict_types=1);

namespace YektaSMS\Gateway\SmsIr\Registration;

use YektaSMS\Core\Contracts\GatewayDefinitionInterface;
use YektaSMS\Core\Contracts\GatewayInterface;
use YektaSMS\Core\Contracts\HealthCheckInterface;
use YektaSMS\Gateway\SmsIr\Config\SmsIrSettings;
use YektaSMS\Gateway\SmsIr\Gateway\SmsIrGateway;
use YektaSMS\Gateway\SmsIr\Gateway\SmsIrHealthChecker;
use YektaSMS\Gateway\SmsIr\Http\SmsIrHttpClient;
use YektaSMS\Gateway\SmsIr\Http\SmsIrRequestFactory;
use YektaSMS\Gateway\SmsIr\Mapping\SmsIrErrorMapper;
use YektaSMS\Gateway\SmsIr\Mapping\SmsIrResponseNormalizer;

final class GatewayFactory
{
    public function makeDefinition(): GatewayDefinitionInterface
    {
        return new class () implements GatewayDefinitionInterface {
            public function getSlug(): string { return 'smsir'; }
            public function getLabel(): string { return 'SMS.ir'; }
            public function getVersion(): string { return (string) YEKTA_SMS_GATEWAY_SMSIR_VERSION; }
            public function getSupportedCapabilities(): array
            {
                return ['single_text', 'bulk_text', 'templated', 'delivery_status_query', 'check_credit', 'list_lines', 'sandbox_mode'];
            }
            public function makeGateway(): GatewayInterface
            {
                $settings = new SmsIrSettings();
                $requestFactory = new SmsIrRequestFactory();
                return new SmsIrGateway($settings, $requestFactory, new SmsIrHttpClient(), new SmsIrResponseNormalizer(new SmsIrErrorMapper()));
            }
            public function makeHealthCheck(): ?HealthCheckInterface
            {
                $settings = new SmsIrSettings();
                $requestFactory = new SmsIrRequestFactory();
                return new SmsIrHealthChecker($settings, $requestFactory, new SmsIrHttpClient());
            }
        };
    }
}
