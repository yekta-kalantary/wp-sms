<?php
declare(strict_types=1);

namespace YektaSMS\Gateway\SmsIr\Support;

final class Requirements
{
    public function isCoreAvailable(): bool
    {
        return interface_exists('YektaSMS\\Core\\Contracts\\GatewayDefinitionInterface')
            && interface_exists('YektaSMS\\Core\\Contracts\\GatewayInterface')
            && class_exists('YektaSMS\\Core\\Domain\\DispatchResult')
            && class_exists('YektaSMS\\Core\\Domain\\MessageRequest');
    }
}
