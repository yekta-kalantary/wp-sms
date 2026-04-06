<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use YektaSMS\Integration\WooComrce\Support\PlaceholderRegistry;

final class PlaceholderRegistryTest extends TestCase
{
    public function testRenderReportsUnknownPlaceholder(): void
    {
        $registry = new PlaceholderRegistry();
        $result = $registry->render('Hello {order.id} {unknown.key}', ['order.id' => '1'], ['order.id']);

        self::assertTrue($result['ok']);
        self::assertContains('unknown.key', $result['unknown']);
    }

    public function testRenderBlocksOnMissingRequired(): void
    {
        $registry = new PlaceholderRegistry();
        $result = $registry->render('Hello {order.id}', ['order.id' => ''], ['order.id']);

        self::assertFalse($result['ok']);
    }
}
