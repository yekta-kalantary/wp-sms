<?php
declare(strict_types=1);
namespace YektaSMS\Core\Contracts;
interface SchedulerInterface { public function scheduleRetry(array $payload, int $delaySeconds): bool; }
