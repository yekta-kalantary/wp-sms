<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use YektaSMS\Integration\WooComrce\Config\WooSettings;

final class WooSettingsTest extends TestCase
{
    public function testSanitizeFallsBackForInvalidSendMode(): void
    {
        $settings = new WooSettings();
        $sanitized = $settings->sanitize(['send_mode' => 'bad']);

        self::assertSame('live', $sanitized['send_mode']);
    }
}
