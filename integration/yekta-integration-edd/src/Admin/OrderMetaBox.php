<?php
declare(strict_types=1);

namespace YektaSMS\Integration\EDD\Admin;

use YektaSMS\Integration\EDD\Config\EddSettings;
use YektaSMS\Integration\EDD\Config\EventMappingsRepository;

final class OrderMetaBox
{
    public function __construct(private EventMappingsRepository $mappings, private EddSettings $settings)
    {
    }

    public function register(): void
    {
        add_action('edd_view_order_details_sidebar_after', [$this, 'render']);
    }

    public function render(int $orderId): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $history = edd_get_order_meta($orderId, '_yekta_sms_last_dispatch_ids', true);
        $history = is_array($history) ? $history : [];

        echo '<div class="postbox"><h3>' . esc_html__('Yekta SMS', 'yekta-integration-edd') . '</h3><div class="inside">';
        echo '<p><strong>' . esc_html__('Dispatch history IDs', 'yekta-integration-edd') . '</strong></p>';
        echo '<p>' . esc_html(implode(', ', array_map('strval', $history))) . '</p>';

        $settings = $this->settings->get();
        if (empty($settings['manual_resend_enabled'])) {
            echo '<p>' . esc_html__('Manual resend is disabled in settings.', 'yekta-integration-edd') . '</p></div></div>';
            return;
        }

        echo '<hr/><p><strong>' . esc_html__('Manual resend', 'yekta-integration-edd') . '</strong></p>';

        foreach (array_keys($this->mappings->all()) as $event) {
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="margin-bottom:6px;">';
            wp_nonce_field('yekta_sms_edd_resend');
            echo '<input type="hidden" name="action" value="yekta_sms_edd_manual_resend"/>';
            echo '<input type="hidden" name="order_id" value="' . esc_attr((string) $orderId) . '"/>';
            echo '<input type="hidden" name="event" value="' . esc_attr($event) . '"/>';
            echo '<button class="button button-small" type="submit">' . esc_html(sprintf(__('Resend: %s', 'yekta-integration-edd'), $event)) . '</button>';
            echo '</form>';
        }

        echo '</div></div>';
    }
}
