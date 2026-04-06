<?php
declare(strict_types=1);

namespace YektaSMS\Integration\EDD\Admin;

use YektaSMS\Integration\EDD\Config\EddSettings;
use YektaSMS\Integration\EDD\Dispatch\EddSmsOrchestrator;

final class ManualResendController
{
    public function __construct(private EddSmsOrchestrator $orchestrator, private EddSettings $settings)
    {
    }

    public function register(): void
    {
        add_action('admin_post_yekta_sms_edd_manual_resend', [$this, 'handle']);
    }

    public function handle(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Permission denied.', 'yekta-integration-edd'));
        }

        check_admin_referer('yekta_sms_edd_resend');

        $settings = $this->settings->get();
        if (empty($settings['manual_resend_enabled'])) {
            wp_die(esc_html__('Manual resend is disabled.', 'yekta-integration-edd'));
        }

        $orderId = isset($_POST['order_id']) ? absint($_POST['order_id']) : 0;
        $event = isset($_POST['event']) ? sanitize_text_field(wp_unslash((string) $_POST['event'])) : '';

        if ($orderId <= 0 || $event === '') {
            wp_die(esc_html__('Invalid resend payload.', 'yekta-integration-edd'));
        }

        $this->orchestrator->handle($event, $orderId, ['manual_resend' => true], true);

        $redirect = add_query_arg(['page' => 'edd-payment-history', 'view' => 'view-order-details', 'id' => $orderId], admin_url('edit.php?post_type=download'));
        wp_safe_redirect($redirect);
        exit;
    }
}
