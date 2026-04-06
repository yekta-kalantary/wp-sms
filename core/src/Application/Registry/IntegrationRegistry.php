<?php
declare(strict_types=1);
namespace YektaSMS\Core\Application\Registry;
use InvalidArgumentException; use YektaSMS\Core\Contracts\IntegrationDefinitionInterface;
final class IntegrationRegistry
{
    private array $definitions=[];
    public function register(IntegrationDefinitionInterface $definition): void
    {
        $slug=$definition->getSlug();
        if (isset($this->definitions[$slug])) { throw new InvalidArgumentException(sprintf('Duplicate integration slug: %s',$slug)); }
        $this->definitions[$slug]=$definition;
    }
    public function all(): array { return array_values($this->definitions); }
}
