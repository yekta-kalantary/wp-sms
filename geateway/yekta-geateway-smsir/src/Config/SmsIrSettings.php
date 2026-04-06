<?php
declare(strict_types=1);

namespace YektaSMS\Gateway\SmsIr\Config;

final class SmsIrSettings
{
    public const OPTION_KEY = 'yekta_sms_gateway_smsir_settings';
    public const MODE_PRODUCTION = 'production';
    public const MODE_SANDBOX = 'sandbox';

    public function defaults(): array
    {
        return [
            'enabled' => false,
            'mode' => self::MODE_PRODUCTION,
            'api_key' => '',
            'default_line_number' => '',
            'request_timeout' => 15,
            'connectivity_check_strategy' => 'credit',
            'mask_message_content' => true,
            'header_accept_mode' => 'application/json',
        ];
    }

    public function getRaw(): array
    {
        $saved = get_option(self::OPTION_KEY, []);
        if (!is_array($saved)) {
            $saved = [];
        }

        return array_merge($this->defaults(), $saved);
    }

    public function sanitize(array $input): array
    {
        $defaults = $this->defaults();

        $mode = isset($input['mode']) ? sanitize_key((string) $input['mode']) : $defaults['mode'];
        if (!in_array($mode, [self::MODE_PRODUCTION, self::MODE_SANDBOX], true)) {
            $mode = $defaults['mode'];
        }

        $timeout = isset($input['request_timeout']) ? (int) $input['request_timeout'] : (int) $defaults['request_timeout'];
        $timeout = max(3, min(60, $timeout));

        $connectivityCheck = isset($input['connectivity_check_strategy'])
            ? sanitize_key((string) $input['connectivity_check_strategy'])
            : (string) $defaults['connectivity_check_strategy'];

        if (!in_array($connectivityCheck, ['credit', 'line', 'credit_then_line'], true)) {
            $connectivityCheck = (string) $defaults['connectivity_check_strategy'];
        }

        $accept = isset($input['header_accept_mode'])
            ? sanitize_text_field((string) $input['header_accept_mode'])
            : (string) $defaults['header_accept_mode'];

        if ($accept === '') {
            $accept = (string) $defaults['header_accept_mode'];
        }

        return [
            'enabled' => !empty($input['enabled']),
            'mode' => $mode,
            'api_key' => trim((string) ($input['api_key'] ?? '')),
            'default_line_number' => trim((string) ($input['default_line_number'] ?? '')),
            'request_timeout' => $timeout,
            'connectivity_check_strategy' => $connectivityCheck,
            'mask_message_content' => !array_key_exists('mask_message_content', $input) || !empty($input['mask_message_content']),
            'header_accept_mode' => $accept,
        ];
    }

    public function getValidated(): array
    {
        $sanitized = $this->sanitize($this->getRaw());
        return $this->validate($sanitized);
    }

    public function validate(array $settings): array
    {
        $errors = [];

        if (!is_bool($settings['enabled'])) {
            $errors[] = ['code' => 'invalid_enabled', 'field' => 'enabled', 'message' => __('Enabled must be boolean.', 'yekta-geateway-smsir')];
        }

        if (!in_array($settings['mode'], [self::MODE_PRODUCTION, self::MODE_SANDBOX], true)) {
            $errors[] = ['code' => 'invalid_mode', 'field' => 'mode', 'message' => __('Mode must be production or sandbox.', 'yekta-geateway-smsir')];
        }

        if ((bool) $settings['enabled'] && $settings['api_key'] === '') {
            $errors[] = ['code' => 'missing_api_key', 'field' => 'api_key', 'message' => __('API key is required when gateway is enabled.', 'yekta-geateway-smsir')];
        }

        if ((int) $settings['request_timeout'] < 3 || (int) $settings['request_timeout'] > 60) {
            $errors[] = ['code' => 'invalid_timeout', 'field' => 'request_timeout', 'message' => __('Request timeout must be between 3 and 60 seconds.', 'yekta-geateway-smsir')];
        }

        if ($settings['default_line_number'] !== '' && !preg_match('/^[0-9]{5,20}$/', $settings['default_line_number'])) {
            $errors[] = ['code' => 'invalid_line_number', 'field' => 'default_line_number', 'message' => __('Default line number must be numeric.', 'yekta-geateway-smsir')];
        }

        return [
            'valid' => count($errors) === 0,
            'settings' => $settings,
            'errors' => $errors,
        ];
    }
}
