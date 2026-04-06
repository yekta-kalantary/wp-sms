<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use YektaSMS\Integration\WooComrce\Dispatch\IdempotencyGuard;

final class IdempotencyGuardTest extends TestCase
{
    public function testKeyChangesWhenRecipientChanges(): void
    {
        $guard = new IdempotencyGuard();
        $a = $guard->key('customer.order.paid', 10, '09120000000', 'v1');
        $b = $guard->key('customer.order.paid', 10, '09121111111', 'v1');

        self::assertNotSame($a, $b);
    }
}
