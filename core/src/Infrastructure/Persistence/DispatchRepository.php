<?php
declare(strict_types=1);
namespace YektaSMS\Core\Infrastructure\Persistence;
use YektaSMS\Core\Domain\DispatchResult; use YektaSMS\Core\Domain\MessageRequest; use YektaSMS\Core\Support\Schema;
final class DispatchRepository
{
    public function createPending(MessageRequest $request, string $providerSlug): int
    {
        global $wpdb;
        $ok=$wpdb->insert(Schema::dispatchesTable(),[
            'created_at_gmt'=>current_time('mysql', true),'updated_at_gmt'=>current_time('mysql', true),
            'provider_slug'=>$providerSlug,'source_plugin'=>$request->sourcePlugin,'source_event'=>$request->sourceEvent,
            'source_object_type'=>$request->sourceObjectType,'source_object_id'=>$request->sourceObjectId,
            'recipient_masked'=>substr((string)$request->recipients[0],0,3).'***','idempotency_key'=>$request->idempotencyKey,
            'attempt'=>1,'status'=>'pending','retryable'=>0,'correlation_id'=>$request->correlationId,
        ]);
        return $ok ? (int)$wpdb->insert_id : 0;
    }
    public function markResult(int $id, DispatchResult $result): bool
    {
        global $wpdb;
        return (bool)$wpdb->update(Schema::dispatchesTable(),[
            'updated_at_gmt'=>current_time('mysql', true), 'status'=>$result->normalizedStatus,'retryable'=>$result->retryable?1:0,
            'provider_message_id'=>$result->providerMessageId,'provider_batch_id'=>$result->providerBatchId,
            'error_code'=>$result->errorCode,'error_details_json'=>wp_json_encode($result->errorDetails),
        ],['id'=>$id]);
    }
}
