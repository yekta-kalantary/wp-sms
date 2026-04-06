<?php
declare(strict_types=1);

namespace YektaSMS\Gateway\SmsIr\Http;

use InvalidArgumentException;
use YektaSMS\Core\Domain\MessageRequest;

final class SmsIrRequestFactory
{
    public const BASE_URL = 'https://api.sms.ir/v1';

    public function buildForDispatch(MessageRequest $request, array $settings): array
    {
        $type = strtolower($request->type);
        $line = (string) ($settings['default_line_number'] ?? '');

        if (in_array($type, ['templated', 'verify'], true)) {
            return [
                'operation' => 'verify',
                'method' => 'POST',
                'path' => '/send/verify',
                'payload' => [
                    'Mobile' => (string) ($request->recipients[0] ?? ''),
                    'TemplateId' => (int) $request->providerTemplateRef,
                    'Parameters' => $request->parameters,
                ],
            ];
        }

        if (in_array($type, ['delivery_status_query', 'status_query'], true)) {
            $messageId = (string) ($request->parameters['message_id'] ?? '');

            if ($messageId === '') {
                throw new InvalidArgumentException('Missing message_id for status query.');
            }

            return [
                'operation' => 'status_query',
                'method' => 'GET',
                'path' => '/send/' . rawurlencode($messageId),
                'payload' => null,
            ];
        }

        if ($line === '') {
            throw new InvalidArgumentException('Missing default line number for text dispatch.');
        }

        return [
            'operation' => 'bulk_text',
            'method' => 'POST',
            'path' => '/send/bulk',
            'payload' => [
                'lineNumber' => $line,
                'MessageText' => $request->bodyTemplate,
                'Mobiles' => array_values(array_map('strval', $request->recipients)),
                'SendDateTime' => null,
            ],
        ];
    }

    public function buildHealthRequest(string $strategy): array
    {
        if ($strategy === 'line') {
            return ['operation' => 'list_lines', 'method' => 'GET', 'path' => '/line', 'payload' => null];
        }

        return ['operation' => 'check_credit', 'method' => 'GET', 'path' => '/credit', 'payload' => null];
    }

    public function buildHeaders(array $settings): array
    {
        return [
            'Accept' => (string) $settings['header_accept_mode'],
            'Content-Type' => 'application/json',
            'X-API-KEY' => (string) $settings['api_key'],
        ];
    }

    public function url(string $path): string
    {
        return rtrim(self::BASE_URL, '/') . '/' . ltrim($path, '/');
    }
}
