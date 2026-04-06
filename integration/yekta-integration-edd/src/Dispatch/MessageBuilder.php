<?php
declare(strict_types=1);

namespace YektaSMS\Integration\EDD\Dispatch;

use YektaSMS\Core\Domain\MessageRequest;
use YektaSMS\Integration\EDD\Support\PlaceholderRegistry;

final class MessageBuilder
{
    public function __construct(private PlaceholderRegistry $placeholders)
    {
    }

    public function build(object $order, ?object $customer, string $event, array $mapping, array $recipients, string $idempotencyKey, array $context = []): array
    {
        $values = $this->placeholders->placeholders($order, $customer, $context);
        $rendered = $this->placeholders->render((string) ($mapping['body_template'] ?? ''), $values, ['order.id', 'status.current']);

        if (!$rendered['ok']) {
            return ['ok' => false, 'reason' => 'missing_required_placeholder'];
        }

        $request = new MessageRequest(
            type: (string) ($mapping['message_mode'] ?? 'body'),
            recipients: $recipients,
            bodyTemplate: (string) $rendered['body'],
            providerTemplateRef: (string) ($mapping['provider_template_ref'] ?? ''),
            parameters: is_array($mapping['parameter_map'] ?? null) ? $mapping['parameter_map'] : [],
            sourcePlugin: YEKTA_SMS_INTEGRATION_EDD_SLUG,
            sourceEvent: $event,
            sourceObjectType: 'order',
            sourceObjectId: (string) ($order->id ?? 0),
            correlationId: wp_generate_uuid4(),
            idempotencyKey: $idempotencyKey,
            meta: [
                'order_id' => (int) ($order->id ?? 0),
                'event' => $event,
                'unknown_placeholders' => $rendered['unknown'],
            ]
        );

        return ['ok' => true, 'request' => $request, 'warnings' => $rendered['unknown']];
    }
}
