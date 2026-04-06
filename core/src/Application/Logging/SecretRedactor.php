<?php
declare(strict_types=1);
namespace YektaSMS\Core\Application\Logging;
final class SecretRedactor
{
    public function redact(string $value): string
    {
        if (strlen($value) <= 4) { return '****'; }
        return substr($value, 0, 2) . str_repeat('*', max(4, strlen($value)-4)) . substr($value, -2);
    }
}
