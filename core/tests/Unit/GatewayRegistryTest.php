<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase; use YektaSMS\Core\Application\Registry\GatewayRegistry; use YektaSMS\Core\Contracts\GatewayDefinitionInterface; use YektaSMS\Core\Contracts\GatewayInterface; use YektaSMS\Core\Contracts\HealthCheckInterface;
final class GatewayRegistryTest extends TestCase
{
    public function test_duplicate_slug_throws(): void
    {
        $r=new GatewayRegistry(); $d=new class implements GatewayDefinitionInterface { public function getSlug(): string{return 'x';} public function getLabel(): string{return 'X';} public function getVersion(): string{return '1';} public function makeGateway(): GatewayInterface{throw new RuntimeException();} public function makeHealthCheck(): ?HealthCheckInterface{return null;} public function getSupportedCapabilities(): array{return[];} };
        $r->register($d); $this->expectException(InvalidArgumentException::class); $r->register($d);
    }
}
