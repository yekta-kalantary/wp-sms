<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use YektaSMS\Integration\EDD\Config\EddSettings;

final class EddIntegrationSettingsTest extends TestCase
{
    public function testSanitizeRestrictsEnums(): void
    {
        $settings = new EddSettings();
        $sanitized = $settings->sanitize([
            'send_mode' => 'bad',
            'retry_policy' => 'always',
            'customer_phone_source' => 'user_meta',
        ]);

        self::assertSame('live', $sanitized['send_mode']);
        self::assertSame('always', $sanitized['retry_policy']);
        self::assertSame('user_meta', $sanitized['customer_phone_source']);
    }
}
