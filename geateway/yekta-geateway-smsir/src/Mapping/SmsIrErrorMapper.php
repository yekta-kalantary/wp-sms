<?php
declare(strict_types=1);

namespace YektaSMS\Gateway\SmsIr\Mapping;

final class SmsIrErrorMapper
{
    public function mapProviderCode(string $code): array
    {
        $catalog = [
            '401' => ['error_code' => 'auth_failure', 'retryable' => false],
            '403' => ['error_code' => 'account_disabled', 'retryable' => false],
            '422' => ['error_code' => 'invalid_request', 'retryable' => false],
            '429' => ['error_code' => 'rate_limited', 'retryable' => true],
            '500' => ['error_code' => 'provider_internal_error', 'retryable' => true],
            '1001' => ['error_code' => 'invalid_mobile', 'retryable' => false],
            '1002' => ['error_code' => 'invalid_api_key', 'retryable' => false],
            '1006' => ['error_code' => 'insufficient_credit', 'retryable' => false],
            '1010' => ['error_code' => 'blacklist', 'retryable' => false],
            '1011' => ['error_code' => 'template_not_found', 'retryable' => false],
        ];

        return $catalog[$code] ?? ['error_code' => 'provider_error', 'retryable' => false];
    }

    public function mapTransportError(string $error): array
    {
        $message = strtolower($error);

        if (strpos($message, 'timeout') !== false || strpos($message, 'timed out') !== false) {
            return ['error_code' => 'transport_timeout', 'retryable' => true];
        }

        if (strpos($message, 'could not resolve host') !== false || strpos($message, 'connection') !== false) {
            return ['error_code' => 'transport_connectivity', 'retryable' => true];
        }

        return ['error_code' => 'transport_error', 'retryable' => true];
    }
}
