<?php
declare(strict_types=1);
namespace YektaSMS\Core\Application\Logging;
final class PhoneRedactor
{
    public function redact(string $value): string { return preg_replace('/(\+?\d{2,4})\d{4,7}(\d{2})/', '$1****$2', $value) ?: $value; }
}
