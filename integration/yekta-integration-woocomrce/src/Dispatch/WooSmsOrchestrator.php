<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Dispatch;

use YektaSMS\Core\Contracts\LoggerInterface;
use YektaSMS\Core\Contracts\MessageDispatcherInterface;
use YektaSMS\Integration\WooComrce\Config\EventMappingsRepository;
use YektaSMS\Integration\WooComrce\Config\WooSettings;
use YektaSMS\Integration\WooComrce\Consent\ConsentManager;
use YektaSMS\Integration\WooComrce\Support\DependencyChecker;
use YektaSMS\Integration\WooComrce\Support\RecipientResolver;

final class WooSmsOrchestrator
{
    public function __construct(
        private MessageDispatcherInterface $dispatcher,
        private ?LoggerInterface $logger,
        private WooSettings $settings,
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
            $this->log('warning', 'Dispatch skipped: no active gateway.', ['order_id' => $orderId, 'event' => $event]);
            return;
        }

        $mapping = $this->mappings->get($event);
        if (empty($mapping) || empty($mapping['enabled'])) {
            return;
        }

        $order = wc_get_order($orderId);
        if (!$order instanceof \WC_Order) {
            $this->log('warning', 'Dispatch skipped: order not found.', ['order_id' => $orderId, 'event' => $event]);
            return;
        }

        $resolvedRecipients = $this->recipients->resolve($order, $mapping, $global);
        if (empty($resolvedRecipients)) {
            $this->log('warning', 'Dispatch skipped: recipient missing.', ['order_id' => $orderId, 'event' => $event]);
            return;
        }

        if (!$this->consent->canSend($order, (bool) ($mapping['require_opt_in'] ?? false), (bool) ($global['respect_opt_in'] ?? false))) {
            $this->log('notice', 'Dispatch skipped: opt-in required.', ['order_id' => $orderId, 'event' => $event]);
            return;
        }

        foreach ($resolvedRecipients as $recipient) {
            $key = $this->idempotency->key($event, $orderId, $recipient, $this->mappings->mappingVersion($event));
            if (!$forced && $this->idempotency->isDuplicate($order, $key)) {
                $this->log('notice', 'Duplicate dispatch blocked.', ['order_id' => $orderId, 'event' => $event, 'recipient' => $this->recipients->mask($recipient)]);
                continue;
            }

            $built = $this->messageBuilder->build($order, $event, $mapping, [$recipient], $key, $context);
            if (!$built['ok']) {
                $this->log('warning', 'Dispatch blocked by placeholder policy.', ['order_id' => $orderId, 'event' => $event]);
                continue;
            }

            $result = $this->dispatcher->dispatch($built['request']);
            if (!empty($mapping['add_order_note']) || !empty($global['write_order_notes'])) {
                $order->add_order_note(
                    sprintf(
                        /* translators: 1: event key, 2: recipient mask */
                        __('Yekta SMS dispatch for %1$s to %2$s.', 'yekta-integration-woocomrce'),
                        $event,
                        $this->recipients->mask($recipient)
                    )
                );
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
