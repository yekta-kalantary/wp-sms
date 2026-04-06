<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase; use YektaSMS\Core\Application\Logging\LogContextNormalizer; use YektaSMS\Core\Application\Logging\PhoneRedactor; use YektaSMS\Core\Application\Logging\SecretRedactor;
final class LoggerRedactionTest extends TestCase
{ public function test_redaction(): void { $n=new LogContextNormalizer(new PhoneRedactor(), new SecretRedactor()); $ctx=$n->normalize(['phone'=>'09123456789','api_secret'=>'secret-value']); $this->assertStringNotContainsString('secret-value',$ctx['api_secret']); } }
