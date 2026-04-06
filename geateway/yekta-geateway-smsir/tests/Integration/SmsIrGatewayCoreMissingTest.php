<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use YektaSMS\Gateway\SmsIr\Support\Requirements;

final class SmsIrGatewayCoreMissingTest extends TestCase
{
    public function testCoreContractPresenceCheckReturnsBoolean(): void
    {
        $requirements = new Requirements();
        self::assertIsBool($requirements->isCoreAvailable());
    }
}
