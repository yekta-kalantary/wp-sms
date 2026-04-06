<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use YektaSMS\Integration\EDD\Config\EventMappingsRepository;

final class EddIntegrationEventMappingsRepositoryTest extends TestCase
{
    public function testDefaultsContainCoreEvents(): void
    {
        $repository = new EventMappingsRepository();
        $defaults = $repository->defaults();

        self::assertArrayHasKey('customer.order.created', $defaults);
        self::assertArrayHasKey('customer.order.completed', $defaults);
        self::assertArrayHasKey('admin.order.failed', $defaults);
    }
}
