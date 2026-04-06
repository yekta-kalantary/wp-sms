<?php
declare(strict_types=1);
namespace YektaSMS\Core\Contracts;
interface GatewayDefinitionInterface { public function getSlug(): string; public function getLabel(): string; public function getVersion(): string; public function makeGateway(): GatewayInterface; public function makeHealthCheck(): ?HealthCheckInterface; public function getSupportedCapabilities(): array; }
