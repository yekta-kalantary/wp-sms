<?php
declare(strict_types=1);
namespace YektaSMS\Core\Application\Logging;
use YektaSMS\Core\Contracts\LoggerInterface; use YektaSMS\Core\Infrastructure\Persistence\LogRepository;
final class DbLogger implements LoggerInterface
{
    public function __construct(private LogRepository $repository, private LogContextNormalizer $normalizer, private string $channel='core') {}
    public function log(string $level, string $message, array $context = []): void { $this->repository->insert($level, $this->channel, $message, $this->normalizer->normalize($context)); }
}
