<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use YektaSMS\Gateway\SmsIr\Config\SmsIrSettings;

final class SmsIrSettingsTest extends TestCase
{
    public function testValidateRequiresApiKeyWhenEnabled(): void
    {
        $settings = new SmsIrSettings();
        $validated = $settings->validate($settings->sanitize(['enabled' => true, 'api_key' => '']));

        self::assertFalse($validated['valid']);
        self::assertSame('missing_api_key', $validated['errors'][0]['code']);
    }
}
