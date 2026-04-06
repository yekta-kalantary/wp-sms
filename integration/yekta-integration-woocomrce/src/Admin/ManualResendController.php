<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Admin;

use YektaSMS\Integration\WooComrce\Config\WooSettings;
use YektaSMS\Integration\WooComrce\Dispatch\WooSmsOrchestrator;

final class ManualResendController
{
    public function __construct(private WooSmsOrchestrator $orchestrator, private WooSettings $settings)
    {
    }

    public function register(): void
    {
        add_action('admin_post_yekta_sms_wc_manual_resend', [$this, 'handle']);
    }

    public function handle(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Permission denied.', 'yekta-integration-woocomrce'));
        }

        check_admin_referer('yekta_sms_wc_resend');

        $settings = $this->settings->get();
        if (empty($settings['manual_resend_enabled'])) {
            wp_die(esc_html__('Manual resend is disabled.', 'yekta-integration-woocomrce'));
        }

        $orderId = isset($_POST['order_id']) ? absint($_POST['order_id']) : 0;
        $event = isset($_POST['event']) ? sanitize_text_field(wp_unslash((string) $_POST['event'])) : '';

        if ($orderId <= 0 || $event === '') {
            wp_die(esc_html__('Invalid resend payload.', 'yekta-integration-woocomrce'));
        }

        $this->orchestrator->handle($event, $orderId, ['manual_resend' => true], true);

        $redirect = wp_get_referer();
        wp_safe_redirect($redirect ?: admin_url('admin.php?page=wc-orders'));
        exit;
    }
}
