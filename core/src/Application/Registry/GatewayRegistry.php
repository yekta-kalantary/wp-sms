<?php
declare(strict_types=1);
namespace YektaSMS\Core\Application\Registry;
use InvalidArgumentException; use YektaSMS\Core\Contracts\GatewayDefinitionInterface;
final class GatewayRegistry
{
    private array $definitions=[];
    public function register(GatewayDefinitionInterface $definition): void
    {
        $slug=$definition->getSlug();
        if (isset($this->definitions[$slug])) { throw new InvalidArgumentException(sprintf('Duplicate gateway slug: %s',$slug)); }
        $this->definitions[$slug]=$definition;
    }
    public function get(string $slug): ?GatewayDefinitionInterface { return $this->definitions[$slug] ?? null; }
    public function all(): array { return array_values($this->definitions); }
}
