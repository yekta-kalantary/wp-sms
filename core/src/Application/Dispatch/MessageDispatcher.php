<?php
declare(strict_types=1);
namespace YektaSMS\Core\Application\Dispatch;
use RuntimeException; use YektaSMS\Core\Application\Config\SettingsRepository; use YektaSMS\Core\Contracts\LoggerInterface; use YektaSMS\Core\Contracts\MessageDispatcherInterface; use YektaSMS\Core\Contracts\SchedulerInterface; use YektaSMS\Core\Domain\DispatchResult; use YektaSMS\Core\Domain\MessageRequest; use YektaSMS\Core\Infrastructure\Persistence\DispatchRepository; use YektaSMS\Core\Support\Capabilities;
final class MessageDispatcher implements MessageDispatcherInterface
{
    public function __construct(private SettingsRepository $settings, private ActiveGatewayResolver $resolver, private DispatchRepository $dispatches, private LoggerInterface $logger, private ?SchedulerInterface $scheduler = null) {}
    public function dispatch(MessageRequest $request): DispatchResult
    {
        $request->validate();
        if (!(bool)$this->settings->get('dispatch_enabled', true)) { throw new RuntimeException('Dispatch is disabled.'); }
        if (!Capabilities::can('manage_settings')) { throw new RuntimeException('Capability denied for dispatch.'); }
        $gateway=$this->resolver->resolve();
        if (!$gateway->isConfigured() || !$gateway->isAvailable()) { throw new RuntimeException('Gateway is not configured or unavailable.'); }
        $dispatchId=$this->dispatches->createPending($request, $this->settings->get('active_gateway',''));
        if ($dispatchId === 0) { throw new RuntimeException('Could not persist pending dispatch.'); }
        do_action('yekta_sms_before_dispatch', $request);
        $this->logger->log('info', 'Dispatch started', ['correlation_id'=>$request->correlationId, 'source_plugin'=>$request->sourcePlugin, 'source_event'=>$request->sourceEvent]);
        try {
            $result=$gateway->dispatch($request);
            $this->dispatches->markResult($dispatchId, $result);
            do_action('yekta_sms_after_dispatch', $request, $result);
            if ($result->retryable && $this->scheduler) {
                $scheduled=$this->scheduler->scheduleRetry(['dispatch_id'=>$dispatchId,'correlation_id'=>$request->correlationId], 60);
                if (!$scheduled) { $this->logger->log('warning', 'Retry scheduling unavailable', ['dispatch_id'=>$dispatchId]); }
            }
            return $result;
        } catch (\Throwable $e) {
            do_action('yekta_sms_dispatch_error', $request, $e);
            $this->logger->log('error', 'Dispatch exception', ['exception'=>$e->getMessage(),'correlation_id'=>$request->correlationId]);
            return DispatchResult::failure($this->settings->get('active_gateway',''), 'error', $e->getMessage(), 'dispatch_exception', true);
        }
    }
}
