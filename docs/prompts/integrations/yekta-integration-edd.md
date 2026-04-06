# Prompt Pack — `yekta-integration-edd`

```text
تو مسئول توسعه‌ی integration رسمی Easy Digital Downloads برای اکوسیستم Yekta SMS هستی.

قبل از کدنویسی:
1) README.md
2) docs/architecture/*
3) ADR-001 و ADR-003
4) docs/handoff/integrations/yekta-integration-edd.md

قواعد قطعی:
- حدس نزن.
- از hookها و APIهای رسمی EDD استفاده کن.
- provider-specific logic ممنوع.
- direct SQL برای منطق order ممنوع.
- security اجباری: capability + nonce + sanitize/escape + masked logging.
- graceful degradation برای نبود core/EDD/gateway الزامی است.

هدف MVP:
- dependency checks
- trigger registration
- event mapping settings
- recipient resolution
- placeholder rendering
- idempotency
- manual resend
- order troubleshooting UI

eventهای MVP:
- customer.order.created
- customer.order.completed
- customer.order.refunded
- customer.order.failed
- customer.order.pending
- customer.order.revoked
- customer.order.note.added
- admin.order.created
- admin.order.completed
- admin.order.refunded
- admin.order.failed

optional/advanced (فقط feature-flagged):
- customer.subscription.*
- customer.license.*
```
