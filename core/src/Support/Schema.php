<?php
declare(strict_types=1);
namespace YektaSMS\Core\Support;
final class Schema
{
    public static function dispatchesTable(): string { global $wpdb; return $wpdb->prefix . 'yekta_sms_dispatches'; }
    public static function logsTable(): string { global $wpdb; return $wpdb->prefix . 'yekta_sms_logs'; }
    public static function createSql(): array
    {
        global $wpdb; $c = $wpdb->get_charset_collate();
        $d = 'CREATE TABLE ' . self::dispatchesTable() . " (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,created_at_gmt datetime NOT NULL,updated_at_gmt datetime NOT NULL,provider_slug varchar(100) NOT NULL,source_plugin varchar(100) NOT NULL,source_event varchar(100) NOT NULL,source_object_type varchar(100) NOT NULL,source_object_id varchar(100) NOT NULL,recipient_masked varchar(100) NOT NULL,idempotency_key varchar(190) NOT NULL,attempt int(11) NOT NULL DEFAULT 1,status varchar(30) NOT NULL,retryable tinyint(1) NOT NULL DEFAULT 0,provider_message_id varchar(190) DEFAULT NULL,provider_batch_id varchar(190) DEFAULT NULL,error_code varchar(100) DEFAULT NULL,error_details_json longtext NULL,meta_json longtext NULL,correlation_id varchar(190) NOT NULL,PRIMARY KEY (id),KEY provider_slug (provider_slug),KEY correlation_id (correlation_id),KEY idempotency_key (idempotency_key)) $c;";
        $l = 'CREATE TABLE ' . self::logsTable() . " (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,created_at_gmt datetime NOT NULL,level varchar(20) NOT NULL,channel varchar(100) NOT NULL,message text NOT NULL,context_json longtext NULL,correlation_id varchar(190) NOT NULL,source_plugin varchar(100) DEFAULT NULL,source_object_type varchar(100) DEFAULT NULL,source_object_id varchar(100) DEFAULT NULL,PRIMARY KEY (id),KEY level (level),KEY correlation_id (correlation_id)) $c;";
        return [$d, $l];
    }
}
