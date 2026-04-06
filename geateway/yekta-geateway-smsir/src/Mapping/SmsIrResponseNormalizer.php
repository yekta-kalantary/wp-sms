<?php
declare(strict_types=1);

namespace YektaSMS\Gateway\SmsIr\Mapping;

use YektaSMS\Core\Domain\DispatchResult;

final class SmsIrResponseNormalizer
{
    private SmsIrErrorMapper $errorMapper;

    public function __construct(SmsIrErrorMapper $errorMapper)
    {
        $this->errorMapper = $errorMapper;
    }

    public function normalize(array $httpResult, string $operation): DispatchResult
    {
        if (!$httpResult['ok']) {
            $mapped = $this->errorMapper->mapTransportError((string) $httpResult['transport_error']);

            return new DispatchResult(
                false,
                (bool) $mapped['retryable'],
                'smsir',
                '',
                '',
                'transport_error',
                __('Transport error while calling SMS.ir.', 'yekta-geateway-smsir'),
                (string) $mapped['error_code'],
                ['operation' => $operation],
                null,
                (int) $httpResult['latency_ms']
            );
        }

        $body = json_decode((string) $httpResult['body'], true);

        if (!is_array($body)) {
            return new DispatchResult(
                false,
                false,
                'smsir',
                '',
                '',
                'malformed_response',
                __('Malformed JSON response from SMS.ir.', 'yekta-geateway-smsir'),
                'malformed_response',
                ['operation' => $operation, 'http_status' => (int) $httpResult['http_status']],
                null,
                (int) $httpResult['latency_ms']
            );
        }

        $providerCode = (string) ($body['status'] ?? $httpResult['http_status']);
        $mapped = $this->errorMapper->mapProviderCode($providerCode);
        $data = isset($body['data']) && is_array($body['data']) ? $body['data'] : [];

        $success = ((int) $httpResult['http_status'] >= 200 && (int) $httpResult['http_status'] < 300)
            && !in_array($mapped['error_code'], ['auth_failure', 'provider_internal_error', 'invalid_request', 'provider_error'], true);

        return new DispatchResult(
            $success,
            $success ? false : (bool) $mapped['retryable'],
            'smsir',
            (string) ($data['messageId'] ?? $data['message_id'] ?? ''),
            (string) ($data['packId'] ?? $data['batch_id'] ?? ''),
            $success ? 'sent' : 'failed',
            (string) ($body['message'] ?? ($success ? __('Message sent successfully.', 'yekta-geateway-smsir') : __('SMS.ir rejected request.', 'yekta-geateway-smsir'))),
            $success ? '' : (string) $mapped['error_code'],
            [
                'operation' => $operation,
                'provider_status_code' => $providerCode,
                'http_status' => (int) $httpResult['http_status'],
            ],
            isset($data['cost']) ? (float) $data['cost'] : null,
            (int) $httpResult['latency_ms']
        );
    }
}
