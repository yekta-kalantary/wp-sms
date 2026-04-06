<?php
declare(strict_types=1);
namespace YektaSMS\Core\Admin;
use YektaSMS\Core\Admin\Pages\DashboardPage; use YektaSMS\Core\Admin\Pages\DiagnosticsPage; use YektaSMS\Core\Admin\Pages\GatewaysPage; use YektaSMS\Core\Admin\Pages\LogsPage; use YektaSMS\Core\Admin\Pages\ToolsPage; use YektaSMS\Core\Support\Capabilities;
final class MenuRegistrar
{
    public function __construct(private DashboardPage $dashboard, private GatewaysPage $gateways, private LogsPage $logs, private DiagnosticsPage $diagnostics, private ToolsPage $tools) {}
    public function register(): void
    {
        if (!Capabilities::can('view_dashboard')) { return; }
        add_menu_page(__('Yekta SMS','yekta-sms-core'), __('Yekta SMS','yekta-sms-core'), 'manage_options', 'yekta-sms-core', [$this->dashboard,'render'], 'dashicons-email-alt2');
        add_submenu_page('yekta-sms-core', __('Gateways','yekta-sms-core'), __('Gateways','yekta-sms-core'), 'manage_options', 'yekta-sms-core-gateways', [$this->gateways,'render']);
        add_submenu_page('yekta-sms-core', __('Logs','yekta-sms-core'), __('Logs','yekta-sms-core'), 'manage_options', 'yekta-sms-core-logs', [$this->logs,'render']);
        add_submenu_page('yekta-sms-core', __('Diagnostics','yekta-sms-core'), __('Diagnostics','yekta-sms-core'), 'manage_options', 'yekta-sms-core-diagnostics', [$this->diagnostics,'render']);
        add_submenu_page('yekta-sms-core', __('Tools','yekta-sms-core'), __('Tools','yekta-sms-core'), 'manage_options', 'yekta-sms-core-tools', [$this->tools,'render']);
    }
}
