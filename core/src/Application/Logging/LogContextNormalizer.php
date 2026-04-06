<?php
declare(strict_types=1);
namespace YektaSMS\Core\Application\Logging;
final class LogContextNormalizer
{
    public function __construct(private PhoneRedactor $phoneRedactor, private SecretRedactor $secretRedactor) {}
    public function normalize(array $context): array
    {
        array_walk_recursive($context, function (&$value, $key): void {
            if (!is_scalar($value)) return;
            $s=(string)$value;
            if (strpos((string)$key, 'secret') !== false || strpos((string)$key, 'token') !== false || strpos((string)$key, 'password') !== false) { $value=$this->secretRedactor->redact($s); return; }
            if (strpos((string)$key, 'phone') !== false || strpos((string)$key, 'recipient') !== false) { $value=$this->phoneRedactor->redact($s); }
        });
        return (array) apply_filters('yekta_sms_log_context', $context);
    }
}
