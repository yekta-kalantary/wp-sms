<?php
declare(strict_types=1);

namespace YektaSMS\Gateway\SmsIr\Bootstrap;

use YektaSMS\Gateway\SmsIr\Admin\SettingsSection;
use YektaSMS\Gateway\SmsIr\Config\SmsIrSettings;
use YektaSMS\Gateway\SmsIr\Registration\GatewayFactory;
use YektaSMS\Gateway\SmsIr\Support\Requirements;

final class Plugin
{
    public function boot(): void
    {
        $requirements = new Requirements();

        if (!$requirements->isCoreAvailable()) {
            add_action('admin_notices', [$this, 'renderCoreMissingNotice']);
            return;
        }

        (new SettingsSection(new SmsIrSettings()))->register();

        add_filter('yekta_sms_gateway_factories', function (array $definitions): array {
            $definitions[] = (new GatewayFactory())->makeDefinition();
            return $definitions;
        });
    }

    public function renderCoreMissingNotice(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        echo '<div class="notice notice-warning"><p>';
        echo esc_html__('Yekta Gateway SMS.ir requires Yekta SMS Core plugin to be active.', 'yekta-geateway-smsir');
        echo '</p></div>';
    }
}
