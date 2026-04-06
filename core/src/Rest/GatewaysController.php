<?php
declare(strict_types=1);
namespace YektaSMS\Core\Rest;
use WP_REST_Request; use YektaSMS\Core\Application\Registry\GatewayRegistry; use YektaSMS\Core\Support\Capabilities;
final class GatewaysController
{
    public function __construct(private GatewayRegistry $registry) {}
    public function registerRoutes(): void
    {
        register_rest_route('yekta-sms/v1','/gateways',['methods'=>'GET','permission_callback'=>fn()=>Capabilities::can('manage_settings'),'callback'=>fn()=>array_map(fn($d)=>['slug'=>$d->getSlug(),'label'=>$d->getLabel(),'version'=>$d->getVersion()],$this->registry->all())]);
    }
}
