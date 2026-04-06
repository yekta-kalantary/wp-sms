<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Bootstrap;

use YektaSMS\Integration\WooComrce\Admin\ManualResendController;
use YektaSMS\Integration\WooComrce\Admin\OrderMetaBox;
use YektaSMS\Integration\WooComrce\Admin\WooSettingsPage;
use YektaSMS\Integration\WooComrce\Compatibility\Declarations;
use YektaSMS\Integration\WooComrce\Config\EventMappingsRepository;
use YektaSMS\Integration\WooComrce\Config\WooSettings;
use YektaSMS\Integration\WooComrce\Consent\ConsentManager;
use YektaSMS\Integration\WooComrce\Dispatch\IdempotencyGuard;
use YektaSMS\Integration\WooComrce\Dispatch\MessageBuilder;
use YektaSMS\Integration\WooComrce\Dispatch\WooSmsOrchestrator;
use YektaSMS\Integration\WooComrce\Registration\IntegrationFactory;
use YektaSMS\Integration\WooComrce\Support\DependencyChecker;
use YektaSMS\Integration\WooComrce\Support\PlaceholderRegistry;
use YektaSMS\Integration\WooComrce\Support\RecipientResolver;
use YektaSMS\Integration\WooComrce\Triggers\HookRegistrar;

final class Plugin
{
    public function boot(): void
    {
        $deps = new DependencyChecker();
        $settings = new WooSettings();
        $mappings = new EventMappingsRepository();

        (new Declarations())->declare();
        (new WooSettingsPage($settings, $mappings))->register();

        if (!$deps->isCoreAvailable()) {
            add_action('admin_notices', [$this, 'renderCoreMissingNotice']);
            return;
        }

        add_filter('yekta_sms_integration_factories', static function (array $definitions): array {
            $definitions[] = (new IntegrationFactory())->makeDefinition();
            return $definitions;
        });

        if (!$deps->isWooAvailable()) {
            add_action('admin_notices', [$this, 'renderWooMissingNotice']);
            return;
        }

        add_action('yekta_sms_core_booted', function ($container) use ($settings, $mappings, $deps): void {
            $dispatcher = $container->get('dispatcher');
            $logger = $container->get('logger');

            $orchestrator = new WooSmsOrchestrator(
                $dispatcher,
                $logger,
                $settings,
                $mappings,
                new RecipientResolver(),
                new ConsentManager(),
                new IdempotencyGuard(),
                new MessageBuilder(new PlaceholderRegistry()),
                $deps
            );

            (new HookRegistrar($orchestrator))->register();
            (new ManualResendController($orchestrator, $settings))->register();
            (new OrderMetaBox($mappings))->register();

            if (!$deps->hasActiveGateway()) {
                add_action('admin_notices', [$this, 'renderGatewayMissingNotice']);
            }
        });
    }

    public function renderCoreMissingNotice(): void
    {
        if (current_user_can('manage_options')) {
            echo '<div class="notice notice-warning"><p>' . esc_html__('Yekta WooCommerce Integration requires Yekta SMS Core plugin to be active.', 'yekta-integration-woocomrce') . '</p></div>';
        }
    }

    public function renderWooMissingNotice(): void
    {
        if (current_user_can('manage_options')) {
            echo '<div class="notice notice-warning"><p>' . esc_html__('Yekta WooCommerce Integration requires WooCommerce to be active.', 'yekta-integration-woocomrce') . '</p></div>';
        }
    }

    public function renderGatewayMissingNotice(): void
    {
        if (current_user_can('manage_options')) {
            echo '<div class="notice notice-warning"><p>' . esc_html__('No active Yekta SMS gateway is configured. Dispatch is disabled, but mappings remain editable.', 'yekta-integration-woocomrce') . '</p></div>';
        }
    }
}
