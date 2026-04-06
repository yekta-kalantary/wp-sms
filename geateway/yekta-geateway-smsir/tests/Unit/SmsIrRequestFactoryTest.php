<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use YektaSMS\Core\Domain\MessageRequest;
use YektaSMS\Gateway\SmsIr\Http\SmsIrRequestFactory;

final class SmsIrRequestFactoryTest extends TestCase
{
    public function testSelectsVerifyEndpoint(): void
    {
        $factory = new SmsIrRequestFactory();
        $request = new MessageRequest('templated', ['989121234567'], '', '1234', ['Code' => '1234'], 'p', 'e', 't', '1', 'c', 'i');

        $spec = $factory->buildForDispatch($request, ['default_line_number' => '3000']);

        self::assertSame('/send/verify', $spec['path']);
    }
}
