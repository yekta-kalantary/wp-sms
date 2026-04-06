<?php
declare(strict_types=1);
namespace YektaSMS\Core\Contracts;
use YektaSMS\Core\Domain\DispatchResult; use YektaSMS\Core\Domain\MessageRequest;
interface GatewayInterface { public function isConfigured(): bool; public function isAvailable(): bool; public function supports(string $capability): bool; public function getCapabilities(): array; public function dispatch(MessageRequest $request): DispatchResult; }
