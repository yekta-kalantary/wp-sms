<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Admin;

use YektaSMS\Integration\WooComrce\Config\EventMappingsRepository;

final class OrderMetaBox
{
    public function __construct(private EventMappingsRepository $mappings)
    {
    }

    public function register(): void
    {
        add_action('add_meta_boxes', [$this, 'add']);
        add_action('add_meta_boxes_woocommerce_page_wc-orders', [$this, 'add']);
    }

    public function add(): void
    {
        add_meta_box('yekta-sms-wc-order', __('Yekta SMS', 'yekta-integration-woocomrce'), [$this, 'render'], wc_get_page_screen_id('shop-order'), 'side');
    }

    public function render($postOrOrder): void
    {
        $order = $postOrOrder instanceof \WC_Order ? $postOrOrder : wc_get_order((int) ($postOrOrder->ID ?? 0));

        if (!$order instanceof \WC_Order) {
            echo esc_html__('Order not available.', 'yekta-integration-woocomrce');
            return;
        }

        $history = $order->get_meta('_yekta_sms_last_dispatch_ids', true);
        $history = is_array($history) ? $history : [];

        echo '<p><strong>' . esc_html__('Dispatch history IDs', 'yekta-integration-woocomrce') . '</strong></p>';
        echo '<p>' . esc_html(implode(', ', array_map('strval', $history))) . '</p>';

        echo '<hr/>';
        echo '<p><strong>' . esc_html__('Manual resend', 'yekta-integration-woocomrce') . '</strong></p>';
        foreach (array_keys($this->mappings->all()) as $event) {
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="margin-bottom:6px;">';
            wp_nonce_field('yekta_sms_wc_resend');
            echo '<input type="hidden" name="action" value="yekta_sms_wc_manual_resend"/>';
            echo '<input type="hidden" name="order_id" value="' . esc_attr((string) $order->get_id()) . '"/>';
            echo '<input type="hidden" name="event" value="' . esc_attr($event) . '"/>';
            echo '<button class="button button-small" type="submit">' . esc_html(sprintf(__('Resend: %s', 'yekta-integration-woocomrce'), $event)) . '</button>';
            echo '</form>';
        }
    }
}
