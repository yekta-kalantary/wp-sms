# ADR-003: مرز Integration = Event Binding and Orchestration Only

## وضعیت
Accepted

## زمینه
Integrationها باید بتوانند رویدادهای افزونه‌های ثالث را به سرویس پیامک وصل کنند، بدون آن‌که provider-specific شوند.

## تصمیم
Integration فقط مسئول این موارد است:
- dependency checks
- compatibility declarations
- trigger registration
- event mapping
- recipient resolution
- placeholder rendering
- idempotency
- manual resend orchestration

Integration مسئول این موارد نیست:
- تماس مستقیم با provider
- auth/config provider
- shared dispatch infrastructure
- transport layer behavior

## پیامد
- integrationها provider-agnostic می‌مانند
- reuse با gatewayهای مختلف ممکن می‌شود
- توسعه‌ی integrationهای بعدی ساده‌تر می‌شود
