<?php
declare(strict_types=1);

namespace YektaSMS\Integration\EDD\Bootstrap;

use YektaSMS\Integration\EDD\Admin\EddSettingsPage;
use YektaSMS\Integration\EDD\Admin\ManualResendController;
use YektaSMS\Integration\EDD\Admin\OrderMetaBox;
use YektaSMS\Integration\EDD\Compatibility\FeatureFlags;
use YektaSMS\Integration\EDD\Config\EddSettings;
use YektaSMS\Integration\EDD\Config\EventMappingsRepository;
use YektaSMS\Integration\EDD\Consent\ConsentManager;
use YektaSMS\Integration\EDD\Dispatch\EddSmsOrchestrator;
use YektaSMS\Integration\EDD\Dispatch\IdempotencyGuard;
use YektaSMS\Integration\EDD\Dispatch\MessageBuilder;
use YektaSMS\Integration\EDD\Registration\IntegrationFactory;
use YektaSMS\Integration\EDD\Support\DependencyChecker;
use YektaSMS\Integration\EDD\Support\PlaceholderRegistry;
use YektaSMS\Integration\EDD\Support\RecipientResolver;
use YektaSMS\Integration\EDD\Triggers\HookRegistrar;

final class Plugin
{
    public function boot(): void
    {
        $deps = new DependencyChecker();
        $settings = new EddSettings();
        $mappings = new EventMappingsRepository();

        (new FeatureFlags())->register();
        (new EddSettingsPage($settings, $mappings, $deps))->register();

        if (!$deps->isCoreAvailable()) {
            add_action('admin_notices', [$this, 'renderCoreMissingNotice']);
            return;
        }

        add_filter('yekta_sms_integration_factories', static function (array $definitions): array {
            $definitions[] = (new IntegrationFactory())->makeDefinition();
            return $definitions;
        });

        if (!$deps->isEddAvailable()) {
            add_action('admin_notices', [$this, 'renderEddMissingNotice']);
            return;
        }

        add_action('yekta_sms_core_booted', function ($container) use ($settings, $mappings, $deps): void {
            $dispatcher = $container->get('dispatcher');
            $logger = $container->get('logger');

            $orchestrator = new EddSmsOrchestrator(
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
            (new OrderMetaBox($mappings, $settings))->register();

            if (!$deps->hasActiveGateway()) {
                add_action('admin_notices', [$this, 'renderGatewayMissingNotice']);
            }
        });
    }

    public function renderCoreMissingNotice(): void
    {
        if (current_user_can('manage_options')) {
            echo '<div class="notice notice-warning"><p>' . esc_html__('Yekta EDD Integration requires Yekta SMS Core plugin to be active.', 'yekta-integration-edd') . '</p></div>';
        }
    }

    public function renderEddMissingNotice(): void
    {
        if (current_user_can('manage_options')) {
            echo '<div class="notice notice-warning"><p>' . esc_html__('Yekta EDD Integration requires Easy Digital Downloads to be active.', 'yekta-integration-edd') . '</p></div>';
        }
    }

    public function renderGatewayMissingNotice(): void
    {
        if (current_user_can('manage_options')) {
            echo '<div class="notice notice-warning"><p>' . esc_html__('No active Yekta SMS gateway is configured. Dispatch is disabled, but mappings remain editable.', 'yekta-integration-edd') . '</p></div>';
        }
    }
}
