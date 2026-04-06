<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use YektaSMS\Gateway\SmsIr\Mapping\SmsIrErrorMapper;

final class SmsIrErrorMapperTest extends TestCase
{
    public function testMapsRateLimitAsRetryable(): void
    {
        $mapper = new SmsIrErrorMapper();
        $mapped = $mapper->mapProviderCode('429');

        self::assertTrue($mapped['retryable']);
        self::assertSame('rate_limited', $mapped['error_code']);
    }
}
