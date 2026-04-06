<?php
declare(strict_types=1);

namespace YektaSMS\Gateway\SmsIr\Gateway;

use InvalidArgumentException;
use YektaSMS\Core\Contracts\GatewayInterface;
use YektaSMS\Core\Domain\DispatchResult;
use YektaSMS\Core\Domain\MessageRequest;
use YektaSMS\Gateway\SmsIr\Config\SmsIrSettings;
use YektaSMS\Gateway\SmsIr\Http\SmsIrHttpClient;
use YektaSMS\Gateway\SmsIr\Http\SmsIrRequestFactory;
use YektaSMS\Gateway\SmsIr\Mapping\SmsIrResponseNormalizer;

final class SmsIrGateway implements GatewayInterface
{
    private const CAPABILITIES = ['single_text', 'bulk_text', 'templated', 'delivery_status_query', 'check_credit', 'list_lines', 'sandbox_mode'];

    private SmsIrSettings $settings;
    private SmsIrRequestFactory $requestFactory;
    private SmsIrHttpClient $httpClient;
    private SmsIrResponseNormalizer $normalizer;

    public function __construct(
        SmsIrSettings $settings,
        SmsIrRequestFactory $requestFactory,
        SmsIrHttpClient $httpClient,
        SmsIrResponseNormalizer $normalizer
    ) {
        $this->settings = $settings;
        $this->requestFactory = $requestFactory;
        $this->httpClient = $httpClient;
        $this->normalizer = $normalizer;
    }

    public function isConfigured(): bool
    {
        $validated = $this->settings->getValidated();

        return $validated['valid'] && (bool) $validated['settings']['enabled'];
    }

    public function isAvailable(): bool
    {
        return $this->isConfigured();
    }

    public function supports(string $capability): bool
    {
        return in_array($capability, self::CAPABILITIES, true);
    }

    public function getCapabilities(): array
    {
        return self::CAPABILITIES;
    }

    public function dispatch(MessageRequest $request): DispatchResult
    {
        $validated = $this->settings->getValidated();

        if (!$validated['valid']) {
            return new DispatchResult(
                false,
                false,
                'smsir',
                '',
                '',
                'invalid_config',
                __('Invalid SMS.ir gateway configuration.', 'yekta-geateway-smsir'),
                'invalid_config',
                ['errors' => $validated['errors']],
                null,
                0
            );
        }

        try {
            $spec = $this->requestFactory->buildForDispatch($request, $validated['settings']);
            $httpResult = $this->httpClient->request(
                [
                    'method' => $spec['method'],
                    'url' => $this->requestFactory->url($spec['path']),
                    'payload' => $spec['payload'],
                ],
                $this->requestFactory->buildHeaders($validated['settings']),
                (int) $validated['settings']['request_timeout']
            );

            return $this->normalizer->normalize($httpResult, (string) $spec['operation']);
        } catch (InvalidArgumentException $e) {
            return DispatchResult::failure('smsir', 'invalid_request', $e->getMessage(), 'invalid_request', false);
        }
    }
}
