<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use YektaSMS\Integration\WooComrce\Support\DependencyChecker;

final class DependencyCheckerTest extends TestCase
{
    public function testGatewayMissingWithoutCoreSetting(): void
    {
        $checker = new DependencyChecker();
        self::assertFalse($checker->hasActiveGateway());
    }
}
