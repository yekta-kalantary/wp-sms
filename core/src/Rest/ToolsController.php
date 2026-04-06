<?php
declare(strict_types=1);
namespace YektaSMS\Core\Rest;
use WP_Error; use WP_REST_Request; use YektaSMS\Core\Contracts\MessageDispatcherInterface; use YektaSMS\Core\Domain\MessageRequest; use YektaSMS\Core\Support\Capabilities;
final class ToolsController
{
    public function __construct(private MessageDispatcherInterface $dispatcher) {}
    public function registerRoutes(): void
    {
        register_rest_route('yekta-sms/v1','/tools/test-send',[
            'methods'=>'POST', 'permission_callback'=>fn()=>Capabilities::can('use_tools'),
            'args'=>['recipient'=>['required'=>true,'validate_callback'=>fn($v)=>is_string($v)&&$v!==''],'message'=>['required'=>true,'validate_callback'=>fn($v)=>is_string($v)&&$v!=='']],
            'callback'=>function(WP_REST_Request $request){
                $nonce=$request->get_header('x_wp_nonce');
                if (!wp_verify_nonce((string)$nonce,'wp_rest')) { return new WP_Error('forbidden', __('Invalid nonce.','yekta-sms-core'), ['status'=>403]); }
                $r=new MessageRequest('test',[(string)$request['recipient']],(string)$request['message'],'',[],'yekta-sms-core','manual_test','tool','0',wp_generate_uuid4(),wp_generate_uuid4());
                return $this->dispatcher->dispatch($r);
            },
        ]);
    }
}
