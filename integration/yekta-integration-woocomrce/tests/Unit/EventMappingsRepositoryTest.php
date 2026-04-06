<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use YektaSMS\Integration\WooComrce\Config\EventMappingsRepository;

final class EventMappingsRepositoryTest extends TestCase
{
    public function testSanitizeRejectsUnknownRecipientType(): void
    {
        $repo = new EventMappingsRepository();
        $sanitized = $repo->sanitize([
            'customer.order.placed' => [
                'enabled' => true,
                'recipient_type' => 'unknown',
            ],
        ]);

        self::assertSame('customer', $sanitized['customer.order.placed']['recipient_type']);
    }
}
