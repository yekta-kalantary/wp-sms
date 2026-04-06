<?php
declare(strict_types=1);
namespace YektaSMS\Core\Contracts;
interface HealthCheckInterface { public function run(): array; }
