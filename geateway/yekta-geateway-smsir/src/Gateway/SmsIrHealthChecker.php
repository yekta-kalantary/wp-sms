<?php
declare(strict_types=1);

namespace YektaSMS\Gateway\SmsIr\Gateway;

use YektaSMS\Core\Contracts\HealthCheckInterface;
use YektaSMS\Gateway\SmsIr\Config\SmsIrSettings;
use YektaSMS\Gateway\SmsIr\Http\SmsIrHttpClient;
use YektaSMS\Gateway\SmsIr\Http\SmsIrRequestFactory;

final class SmsIrHealthChecker implements HealthCheckInterface
{
    private SmsIrSettings $settings;
    private SmsIrRequestFactory $requestFactory;
    private SmsIrHttpClient $httpClient;

    public function __construct(SmsIrSettings $settings, SmsIrRequestFactory $requestFactory, SmsIrHttpClient $httpClient)
    {
        $this->settings = $settings;
        $this->requestFactory = $requestFactory;
        $this->httpClient = $httpClient;
    }

    public function run(): array
    {
        $validated = $this->settings->getValidated();
        if (!$validated['valid']) {
            return [
                'status' => 'fail',
                'message' => __('SMS.ir configuration is invalid.', 'yekta-geateway-smsir'),
                'remediation' => __('Review SMS.ir settings and save valid API key/mode.', 'yekta-geateway-smsir'),
                'details' => $validated['errors'],
            ];
        }

        $strategy = (string) $validated['settings']['connectivity_check_strategy'];
        $healthRequest = $this->requestFactory->buildHealthRequest($strategy === 'credit_then_line' ? 'credit' : $strategy);

        $result = $this->httpClient->request(
            [
                'method' => $healthRequest['method'],
                'url' => $this->requestFactory->url($healthRequest['path']),
                'payload' => null,
            ],
            $this->requestFactory->buildHeaders($validated['settings']),
            (int) $validated['settings']['request_timeout']
        );

        if (!$result['ok'] || (int) $result['http_status'] >= 400) {
            return [
                'status' => 'fail',
                'message' => __('SMS.ir connectivity check failed.', 'yekta-geateway-smsir'),
                'remediation' => __('Verify API key, connectivity, and firewall rules.', 'yekta-geateway-smsir'),
                'details' => [
                    'strategy' => $strategy,
                    'http_status' => (int) ($result['http_status'] ?? 0),
                ],
            ];
        }

        return [
            'status' => 'pass',
            'message' => __('SMS.ir connectivity check passed.', 'yekta-geateway-smsir'),
            'remediation' => '',
            'details' => ['strategy' => $strategy, 'http_status' => (int) $result['http_status']],
        ];
    }
}
