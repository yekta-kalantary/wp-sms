<?php
declare(strict_types=1);
namespace YektaSMS\Core\Bootstrap;
use YektaSMS\Core\Admin\MenuRegistrar; use YektaSMS\Core\Admin\Pages\DashboardPage; use YektaSMS\Core\Admin\Pages\DiagnosticsPage; use YektaSMS\Core\Admin\Pages\GatewaysPage; use YektaSMS\Core\Admin\Pages\LogsPage; use YektaSMS\Core\Admin\Pages\ToolsPage; use YektaSMS\Core\Application\Config\SecretResolver; use YektaSMS\Core\Application\Config\SettingsRepository; use YektaSMS\Core\Application\Diagnostics\DiagnosticsRunner; use YektaSMS\Core\Application\Dispatch\ActiveGatewayResolver; use YektaSMS\Core\Application\Dispatch\MessageDispatcher; use YektaSMS\Core\Application\Logging\DbLogger; use YektaSMS\Core\Application\Logging\LogContextNormalizer; use YektaSMS\Core\Application\Logging\PhoneRedactor; use YektaSMS\Core\Application\Logging\SecretRedactor; use YektaSMS\Core\Application\Registry\GatewayRegistry; use YektaSMS\Core\Application\Registry\IntegrationRegistry; use YektaSMS\Core\Container\Container; use YektaSMS\Core\Infrastructure\Persistence\DispatchRepository; use YektaSMS\Core\Infrastructure\Persistence\LogRepository; use YektaSMS\Core\Rest\DiagnosticsController; use YektaSMS\Core\Rest\GatewaysController; use YektaSMS\Core\Rest\LogsController; use YektaSMS\Core\Rest\ToolsController; use YektaSMS\Core\Support\Requirements;
final class Plugin
{
    public function boot(): void
    {
        if (!(new Requirements())->passes()) { return; }
        $c = new Container();
        $c->singleton('settings', fn()=>new SettingsRepository());
        $c->singleton('gateway_registry', fn()=>new GatewayRegistry());
        $c->singleton('integration_registry', fn()=>new IntegrationRegistry());
        $c->singleton('secret_resolver', fn($c)=>new SecretResolver($c->get('settings')));
        $c->singleton('logger', fn()=>new DbLogger(new LogRepository(), new LogContextNormalizer(new PhoneRedactor(), new SecretRedactor())));
        $c->singleton('dispatcher', fn($c)=>new MessageDispatcher($c->get('settings'), new ActiveGatewayResolver($c->get('settings'), $c->get('gateway_registry')), new DispatchRepository(), $c->get('logger')));
        $c->singleton('diagnostics', fn($c)=>new DiagnosticsRunner($c->get('gateway_registry')));
        $this->registerFactories($c->get('gateway_registry'), $c->get('integration_registry'));
        add_action('admin_menu', fn()=> (new MenuRegistrar(new DashboardPage(), new GatewaysPage(), new LogsPage(), new DiagnosticsPage(), new ToolsPage()))->register());
        add_action('rest_api_init', fn()=> (new GatewaysController($c->get('gateway_registry')))->registerRoutes());
        add_action('rest_api_init', fn()=> (new DiagnosticsController($c->get('diagnostics')))->registerRoutes());
        add_action('rest_api_init', fn()=> (new LogsController())->registerRoutes());
        add_action('rest_api_init', fn()=> (new ToolsController($c->get('dispatcher')))->registerRoutes());
        do_action('yekta_sms_core_booted', $c);
    }
    private function registerFactories(GatewayRegistry $gateways, IntegrationRegistry $integrations): void
    {
        foreach ((array)apply_filters('yekta_sms_gateway_factories', []) as $definition) {
            if ($definition instanceof \YektaSMS\Core\Contracts\GatewayDefinitionInterface) { $gateways->register($definition); }
        }
        foreach ((array)apply_filters('yekta_sms_integration_factories', []) as $definition) {
            if ($definition instanceof \YektaSMS\Core\Contracts\IntegrationDefinitionInterface) { $integrations->register($definition); }
        }
    }
}
