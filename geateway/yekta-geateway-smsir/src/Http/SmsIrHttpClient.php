<?php
declare(strict_types=1);

namespace YektaSMS\Gateway\SmsIr\Http;

final class SmsIrHttpClient
{
    public function request(array $request, array $headers, int $timeout): array
    {
        $start = microtime(true);
        $response = wp_remote_request(
            $request['url'],
            [
                'method' => $request['method'],
                'headers' => $headers,
                'timeout' => $timeout,
                'body' => $request['payload'] === null ? null : wp_json_encode($request['payload']),
            ]
        );
        $latencyMs = (int) round((microtime(true) - $start) * 1000);

        if (is_wp_error($response)) {
            return [
                'ok' => false,
                'transport_error' => $response->get_error_message(),
                'http_status' => 0,
                'body' => '',
                'latency_ms' => $latencyMs,
            ];
        }

        return [
            'ok' => true,
            'transport_error' => '',
            'http_status' => (int) wp_remote_retrieve_response_code($response),
            'body' => (string) wp_remote_retrieve_body($response),
            'latency_ms' => $latencyMs,
        ];
    }
}
