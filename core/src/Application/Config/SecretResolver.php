<?php
declare(strict_types=1);
namespace YektaSMS\Core\Application\Config;
final class SecretResolver
{
    public function __construct(private SettingsRepository $settings) {}
    public function resolve(string $key): string
    {
        $env=getenv(strtoupper($key)); if (is_string($env)&&$env!=='') return $env;
        if (defined(strtoupper($key))) { $v=constant(strtoupper($key)); if (is_string($v)&&$v!=='') return $v; }
        return (string)$this->settings->get($key, '');
    }
}
