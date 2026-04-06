<?php
declare(strict_types=1);

namespace YektaSMS\Integration\EDD\Dispatch;

use YektaSMS\Core\Contracts\LoggerInterface;
use YektaSMS\Core\Contracts\MessageDispatcherInterface;
use YektaSMS\Integration\EDD\Config\EddSettings;
use YektaSMS\Integration\EDD\Config\EventMappingsRepository;
use YektaSMS\Integration\EDD\Consent\ConsentManager;
use YektaSMS\Integration\EDD\Support\DependencyChecker;
use YektaSMS\Integration\EDD\Support\RecipientResolver;

final class EddSmsOrchestrator
{
    public function __construct(
        private MessageDispatcherInterface $dispatcher,
        private ?LoggerInterface $logger,
        private EddSettings $settings,
        private EventMappingsRepository $mappings,
        private RecipientResolver $recipients,
        private ConsentManager $consent,
        private IdempotencyGuard $idempotency,
        private MessageBuilder $messageBuilder,
        private DependencyChecker $deps
    ) {
    }

    public function handle(string $event, int $orderId, array $context = [], bool $forced = false): void
    {
        $global = $this->settings->get();

        if (empty($global['enabled'])) {
            return;
        }

        if (!$this->deps->hasActiveGateway()) {
            $this->log('warning', 'Dispatch skipped: no active gateway.', ['event' => $event, 'order_id' => $orderId]);
            return;
        }

        $mapping = $this->mappings->get($event);
        if (empty($mapping) || empty($mapping['enabled'])) {
            return;
        }

        $order = edd_get_order($orderId);
        if (!is_object($order)) {
            $this->log('warning', 'Dispatch skipped: order not found.', ['event' => $event, 'order_id' => $orderId]);
            return;
        }

        $customer = !empty($order->customer_id) ? edd_get_customer((int) $order->customer_id) : null;
        $resolvedRecipients = $this->recipients->resolve($order, is_object($customer) ? $customer : null, $mapping, $global);

        if (empty($resolvedRecipients)) {
            $this->log('warning', 'Dispatch skipped: recipient missing.', ['event' => $event, 'order_id' => $orderId]);
            return;
        }

        if (!$this->consent->canSend($order, is_object($customer) ? $customer : null, !empty($mapping['require_opt_in']), !empty($global['respect_opt_in']), (string) $global['customer_opt_in_meta_key'])) {
            $this->log('notice', 'Dispatch skipped: opt-in required.', ['event' => $event, 'order_id' => $orderId]);
            return;
        }

        foreach ($resolvedRecipients as $recipient) {
            $idempotencyKey = $this->idempotency->key($event, $orderId, $recipient, $this->mappings->mappingVersion($event));
            if (!$forced && $this->idempotency->isDuplicate($orderId, $idempotencyKey)) {
                $this->log('notice', 'Duplicate dispatch blocked.', ['event' => $event, 'order_id' => $orderId, 'recipient' => $this->recipients->mask($recipient)]);
                continue;
            }

            $built = $this->messageBuilder->build($order, is_object($customer) ? $customer : null, $event, $mapping, [$recipient], $idempotencyKey, $context);
            if (!$built['ok']) {
                $this->log('warning', 'Dispatch blocked by placeholder policy.', ['event' => $event, 'order_id' => $orderId]);
                continue;
            }

            $result = $this->dispatcher->dispatch($built['request']);
            $dispatchId = method_exists($result, 'getDispatchId') ? (int) $result->getDispatchId() : 0;
            $this->idempotency->mark($orderId, $idempotencyKey, $dispatchId);

            if (!empty($mapping['add_order_note']) || !empty($global['write_order_notes'])) {
                $note = sprintf(
                    /* translators: 1: event key, 2: recipient masked phone */
                    __('Yekta SMS dispatch for %1$s to %2$s.', 'yekta-integration-edd'),
                    $event,
                    $this->recipients->mask($recipient)
                );
                edd_insert_payment_note($orderId, $note);
            }
        }
    }

    private function log(string $level, string $message, array $context = []): void
    {
        if ($this->logger !== null) {
            $this->logger->log($level, $message, $context);
        }
    }
}
