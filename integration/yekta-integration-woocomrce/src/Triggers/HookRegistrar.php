<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Triggers;

use YektaSMS\Integration\WooComrce\Dispatch\WooSmsOrchestrator;

final class HookRegistrar
{
    public function __construct(private WooSmsOrchestrator $orchestrator)
    {
    }

    public function register(): void
    {
        add_action('woocommerce_payment_complete', function (int $orderId): void {
            $this->orchestrator->handle('customer.order.paid', $orderId);
            $this->orchestrator->handle('admin.order.paid', $orderId);
        }, 20, 1);

        add_action('woocommerce_checkout_order_processed', function (int $orderId): void {
            $this->orchestrator->handle('customer.order.placed', $orderId);
            $this->orchestrator->handle('admin.order.placed', $orderId);
        }, 20, 1);

        add_action('woocommerce_store_api_checkout_order_processed', function (\WC_Order $order): void {
            $this->orchestrator->handle('customer.order.placed', (int) $order->get_id());
            $this->orchestrator->handle('admin.order.placed', (int) $order->get_id());
        }, 20, 1);

        add_action('woocommerce_order_status_changed', function (int $orderId, string $from, string $to): void {
            $map = [
                'processing' => 'customer.order.processing',
                'completed' => 'customer.order.completed',
                'on-hold' => 'customer.order.on_hold',
                'cancelled' => 'customer.order.cancelled',
                'failed' => 'customer.order.failed',
            ];

            if (isset($map[$to])) {
                $this->orchestrator->handle($map[$to], $orderId, ['from_status' => $from, 'to_status' => $to]);
            }
        }, 20, 4);

        add_action('woocommerce_order_refunded', function (int $orderId): void {
            $this->orchestrator->handle('customer.order.refunded', $orderId);
        }, 20, 1);

        add_action('woocommerce_new_customer_note', function (array $data): void {
            $orderId = isset($data['order_id']) ? absint($data['order_id']) : 0;
            if ($orderId > 0) {
                $this->orchestrator->handle('customer.note.added', $orderId, ['customer_note' => (string) ($data['customer_note'] ?? '')]);
            }
        }, 20, 1);
    }
}
