<?php
declare(strict_types=1);
namespace YektaSMS\Core\Application\Diagnostics;
use YektaSMS\Core\Application\Registry\GatewayRegistry; use YektaSMS\Core\Contracts\HealthCheckInterface;
final class DiagnosticsRunner
{
    public function __construct(private GatewayRegistry $registry) {}
    public function run(): array
    {
        $results=[];
        foreach ((array)apply_filters('yekta_sms_core_diagnostics_checks', []) as $check) {
            if ($check instanceof HealthCheckInterface) { $results[]=$check->run(); }
        }
        foreach ($this->registry->all() as $gateway) {
            $check = $gateway->makeHealthCheck();
            if ($check instanceof HealthCheckInterface) { $results[]=$check->run(); }
        }
        return $results;
    }
}
