<?php
declare(strict_types=1);
namespace YektaSMS\Core\Support;
final class Version
{
    public static function plugin(): string { return defined('YEKTA_SMS_CORE_VERSION') ? YEKTA_SMS_CORE_VERSION : '0.1.0'; }
    public static function db(): string { return defined('YEKTA_SMS_CORE_DB_VERSION') ? YEKTA_SMS_CORE_DB_VERSION : '1'; }
}
