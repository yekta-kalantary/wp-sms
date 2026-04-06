<?php
declare(strict_types=1);
namespace YektaSMS\Core\Domain;
final class DispatchResult
{
    public function __construct(public bool $success, public bool $retryable, public string $providerSlug, public string $providerMessageId, public string $providerBatchId, public string $normalizedStatus, public string $normalizedMessage, public string $errorCode, public array $errorDetails, public ?float $cost, public int $latencyMs) {}
    public static function failure(string $provider, string $status, string $message, string $code = 'dispatch_failure', bool $retryable = false): self
    { return new self(false, $retryable, $provider, '', '', $status, $message, $code, [], null, 0); }
}
