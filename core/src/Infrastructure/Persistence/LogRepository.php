<?php
declare(strict_types=1);
namespace YektaSMS\Core\Infrastructure\Persistence;
use YektaSMS\Core\Support\Schema;
final class LogRepository
{
    public function insert(string $level, string $channel, string $message, array $context): bool
    {
        global $wpdb;
        return (bool)$wpdb->insert(Schema::logsTable(),[
            'created_at_gmt'=>current_time('mysql', true), 'level'=>$level, 'channel'=>$channel,
            'message'=>$message, 'context_json'=>wp_json_encode($context), 'correlation_id'=>(string)($context['correlation_id']??''),
            'source_plugin'=>(string)($context['source_plugin']??''), 'source_object_type'=>(string)($context['source_object_type']??''),
            'source_object_id'=>(string)($context['source_object_id']??''),
        ]);
    }
}
