<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use YektaSMS\Integration\EDD\Support\PlaceholderRegistry;

final class EddIntegrationPlaceholderRegistryTest extends TestCase
{
    public function testRenderCollectsUnknownTokens(): void
    {
        $registry = new PlaceholderRegistry();
        $rendered = $registry->render('Hi {order.id} {unknown.token}', ['order.id' => '25']);

        self::assertTrue($rendered['ok']);
        self::assertSame(['unknown.token'], $rendered['unknown']);
    }
}
