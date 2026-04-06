<?php
declare(strict_types=1);
namespace YektaSMS\Core\Contracts;
interface LoggerInterface { public function log(string $level, string $message, array $context = []): void; }
