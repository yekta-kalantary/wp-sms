<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use YektaSMS\Integration\EDD\Support\DependencyChecker;

final class EddIntegrationDependencyCheckerTest extends TestCase
{
    public function testGatewayMissingWithoutCoreSetting(): void
    {
        $checker = new DependencyChecker();
        self::assertFalse($checker->hasActiveGateway());
    }
}
