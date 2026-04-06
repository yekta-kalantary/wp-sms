<?php
declare(strict_types=1);

namespace YektaSMS\Gateway\SmsIr\Admin;

use YektaSMS\Gateway\SmsIr\Config\SmsIrSettings;

final class SettingsSection
{
    private SmsIrSettings $settings;

    public function __construct(SmsIrSettings $settings)
    {
        $this->settings = $settings;
    }

    public function register(): void
    {
        add_action('admin_init', function (): void {
            register_setting(SmsIrSettings::OPTION_KEY, SmsIrSettings::OPTION_KEY, [
                'sanitize_callback' => function ($input): array {
                    return $this->settings->sanitize(is_array($input) ? $input : []);
                },
                'default' => $this->settings->defaults(),
                'type' => 'array',
            ]);
        });
    }
}
