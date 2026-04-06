<?php
declare(strict_types=1);

namespace YektaSMS\Integration\EDD\Triggers;

use YektaSMS\Integration\EDD\Dispatch\EddSmsOrchestrator;

final class HookRegistrar
{
    public function __construct(private EddSmsOrchestrator $orchestrator)
    {
    }

    public function register(): void
    {
        add_action('edd_insert_payment', function (int $orderId): void {
            $this->orchestrator->handle('customer.order.created', $orderId);
            $this->orchestrator->handle('admin.order.created', $orderId);
        }, 20, 1);

        add_action('edd_transition_order_status', function (string $from, string $to, int $orderId): void {
            $statusMap = [
                'complete' => ['customer.order.completed', 'admin.order.completed'],
                'refunded' => ['customer.order.refunded', 'admin.order.refunded'],
                'failed' => ['customer.order.failed', 'admin.order.failed'],
                'pending' => ['customer.order.pending'],
                'revoked' => ['customer.order.revoked'],
            ];

            if (!isset($statusMap[$to])) {
                return;
            }

            foreach ($statusMap[$to] as $event) {
                $this->orchestrator->handle($event, $orderId, ['from_status' => $from, 'to_status' => $to]);
            }
        }, 20, 3);

        add_action('edd_insert_payment_note', function (int $noteId, int $orderId, string $note): void {
            $this->orchestrator->handle('customer.order.note.added', $orderId, ['note_id' => $noteId, 'order_note' => $note]);
        }, 20, 3);
    }
}
