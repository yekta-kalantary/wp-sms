<?php
declare(strict_types=1);
namespace YektaSMS\Core\Rest;
use YektaSMS\Core\Support\Capabilities; use YektaSMS\Core\Support\Schema;
final class LogsController
{
    public function registerRoutes(): void
    {
        register_rest_route('yekta-sms/v1','/logs',['methods'=>'GET','permission_callback'=>fn()=>Capabilities::can('view_logs'),'callback'=>function(){ global $wpdb; return $wpdb->get_results('SELECT * FROM '.Schema::logsTable().' ORDER BY id DESC LIMIT 100', ARRAY_A); }]);
    }
}
