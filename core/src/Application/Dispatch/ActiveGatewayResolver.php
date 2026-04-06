<?php
declare(strict_types=1);
namespace YektaSMS\Core\Application\Dispatch;
use RuntimeException; use YektaSMS\Core\Application\Config\SettingsRepository; use YektaSMS\Core\Application\Registry\GatewayRegistry; use YektaSMS\Core\Contracts\GatewayInterface;
final class ActiveGatewayResolver
{
    public function __construct(private SettingsRepository $settings, private GatewayRegistry $registry) {}
    public function resolve(): GatewayInterface
    {
        $slug=(string)$this->settings->get('active_gateway','');
        if ($slug==='') throw new RuntimeException('No active gateway configured.');
        $def=$this->registry->get($slug);
        if (!$def) throw new RuntimeException(sprintf('Invalid active gateway slug: %s',$slug));
        return $def->makeGateway();
    }
}
