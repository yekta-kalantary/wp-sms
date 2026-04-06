<?php
declare(strict_types=1);
namespace YektaSMS\Core\Contracts;
use YektaSMS\Core\Domain\DispatchResult; use YektaSMS\Core\Domain\MessageRequest;
interface MessageDispatcherInterface { public function dispatch(MessageRequest $request): DispatchResult; }
