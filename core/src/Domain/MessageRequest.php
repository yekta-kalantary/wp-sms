<?php
declare(strict_types=1);
namespace YektaSMS\Core\Domain;
use InvalidArgumentException;
final class MessageRequest
{
    public function __construct(public string $type, public array $recipients, public string $bodyTemplate, public string $providerTemplateRef, public array $parameters, public string $sourcePlugin, public string $sourceEvent, public string $sourceObjectType, public string $sourceObjectId, public string $correlationId, public string $idempotencyKey, public array $meta = []) {}
    public function validate(): void
    {
        if ($this->type === '' || $this->sourcePlugin === '' || $this->sourceEvent === '' || $this->correlationId === '' || $this->idempotencyKey === '' || empty($this->recipients)) {
            throw new InvalidArgumentException('Invalid message request.');
        }
    }
}
