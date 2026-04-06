<?php
declare(strict_types=1);
namespace YektaSMS\Core\Bootstrap;
use YektaSMS\Core\Support\Options; use YektaSMS\Core\Support\Schema; use YektaSMS\Core\Support\Version;
final class Activation
{
    public static function run(): void
    {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        foreach (Schema::createSql() as $sql) { dbDelta($sql); }
        add_option(Options::SETTINGS, Options::defaults(), '', false);
        update_option(Options::VERSION, Version::plugin(), false);
        update_option(Options::DB_VERSION, Version::db(), false);
    }
}
