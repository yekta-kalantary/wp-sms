<?php
declare(strict_types=1);
namespace YektaSMS\Core\Support;
final class Requirements
{
    public function passes(): bool { return version_compare(PHP_VERSION, '7.4', '>='); }
}
