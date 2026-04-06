<?php
declare(strict_types=1);
namespace YektaSMS\Core\Contracts;
interface IntegrationDefinitionInterface { public function getSlug(): string; public function getLabel(): string; public function getVersion(): string; }
