<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use YektaSMS\Integration\EDD\Support\RecipientResolver;

final class EddIntegrationRecipientResolverTest extends TestCase
{
    public function testMaskHidesMiddleDigits(): void
    {
        $resolver = new RecipientResolver();
        self::assertSame('0912***789', $resolver->mask('09123456789'));
    }
}
