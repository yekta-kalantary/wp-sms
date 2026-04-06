<?php
declare(strict_types=1);
namespace YektaSMS\Core\Rest;
use YektaSMS\Core\Application\Diagnostics\DiagnosticsRunner; use YektaSMS\Core\Support\Capabilities;
final class DiagnosticsController
{
    public function __construct(private DiagnosticsRunner $runner) {}
    public function registerRoutes(): void
    {
        register_rest_route('yekta-sms/v1','/diagnostics',['methods'=>'GET','permission_callback'=>fn()=>Capabilities::can('run_diagnostics'),'callback'=>fn()=>$this->runner->run()]);
    }
}
