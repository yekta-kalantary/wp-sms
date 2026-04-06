# Prompt Pack — `yekta-integration-woocomrce`

این فایل یک پرامپت self-contained برای Codex GPT، Cursor یا هر AI Coding Agent دیگر است تا فقط بر اساس قراردادهای همین repo، سورس `yekta-integration-woocomrce` را تولید یا تکمیل کند.

---

## Prompt

```text
تو مسئول تولید یا تکمیل سورس پلاگین وردپرسی `yekta-integration-woocomrce` در monorepo اکوسیستم Yekta SMS هستی.

قبل از هر تغییری، این فایل‌ها را کامل بخوان:
1) `README.md`
2) `docs/architecture/plugin-boundaries.md`
3) `docs/architecture/contracts.md`
4) `docs/architecture/compatibility.md`
5) `docs/decisions/ADR-001-core-first.md`
6) `docs/decisions/ADR-003-integration-boundary.md`
7) `docs/handoff/integrations/yekta-integration-woocomrce.md`

قواعد قطعی:
1) از خودت چیزی حدس نزن.
2) فقط طبق Handoff Pack و docs همین repo کدنویسی کن.
3) اگر برای hookها، HPOS یا checkout flow به مستندات رسمی WooCommerce نیاز است، آن‌ها را بررسی کن و چیزی را حدس نزن.
4) کد را production-grade، ساده، HPOS-safe و maintainable بنویس.
5) هیچ منطق provider-specific داخل این پلاگین نیاور.
6) همه‌ی متن‌های UI باید قابل ترجمه باشند.
7) dependency اضافی غیرضروری ممنوع است.
8) برای order access فقط از WooCommerce CRUD APIs استفاده کن.
9) direct SQL برای منطق سفارش ممنوع است.
10) اگر contract عمومی جدید لازم بود، قبل از implementation آن را گزارش کن.

هدف پلاگین:
اتصال eventهای WooCommerce به `yekta-sms-core` شامل:
- dependency checks
- compatibility declarations
- trigger registration
- event mapping settings
- recipient resolution
- placeholder rendering
- idempotency
- manual resend
- order troubleshooting UI

ساختار پوشه‌ی هدف:
- `integration/yekta-integration-woocomrce/yekta-integration-woocomrce.php`
- `integration/yekta-integration-woocomrce/src/Bootstrap/*`
- `integration/yekta-integration-woocomrce/src/Compatibility/*`
- `integration/yekta-integration-woocomrce/src/Registration/*`
- `integration/yekta-integration-woocomrce/src/Config/*`
- `integration/yekta-integration-woocomrce/src/Triggers/*`
- `integration/yekta-integration-woocomrce/src/Dispatch/*`
- `integration/yekta-integration-woocomrce/src/Domain/*`
- `integration/yekta-integration-woocomrce/src/Consent/*`
- `integration/yekta-integration-woocomrce/src/Admin/*`
- `integration/yekta-integration-woocomrce/src/Support/*`
- `integration/yekta-integration-woocomrce/tests/*`

فایل‌های کلیدی:
- `src/Bootstrap/Plugin.php`
- `src/Compatibility/Declarations.php`
- `src/Support/DependencyChecker.php`
- `src/Config/WooSettings.php`
- `src/Config/EventMappingsRepository.php`
- `src/Triggers/HookRegistrar.php`
- `src/Dispatch/WooSmsOrchestrator.php`
- `src/Dispatch/MessageBuilder.php`
- `src/Dispatch/IdempotencyGuard.php`
- `src/Support/RecipientResolver.php`
- `src/Support/PlaceholderRegistry.php`
- `src/Consent/ConsentManager.php`
- `src/Admin/WooSettingsPage.php`
- `src/Admin/OrderMetaBox.php`
- `src/Admin/ManualResendController.php`

dependencyها:
- `yekta-sms-core`
- `WooCommerce`

soft dependency:
- حداقل یک gateway فعال در core

compatibility requirements:
- HPOS-safe
- no direct SQL برای order logic
- no post-based order access برای flow اصلی
- استفاده از WooCommerce CRUD APIs
- اگر checkout extension در scope است، blocks/classic compatibility را رعایت کن

event map پیشنهادی:
customer:
- `customer.order.placed`
- `customer.order.paid`
- `customer.order.processing`
- `customer.order.completed`
- `customer.order.on_hold`
- `customer.order.cancelled`
- `customer.order.failed`
- `customer.order.refunded`
- `customer.note.added`

admin:
- `admin.order.placed`
- `admin.order.paid`

candidate trigger list:
- `woocommerce_payment_complete`
- `woocommerce_order_status_changed`
- `woocommerce_order_refunded`
- `woocommerce_new_customer_note`

optional/advanced:
- `woocommerce_checkout_order_processed`
- `woocommerce_store_api_checkout_order_processed`

settings schema:
option: `yekta_sms_wc_settings`
- `enabled`
- `send_mode`
- `respect_opt_in`
- `write_order_notes`
- `retry_policy`
- `manual_resend_enabled`
- `customer_phone_source`
- `admin_phone_list`

option: `yekta_sms_wc_event_mappings`
برای هر event:
- `enabled`
- `recipient_type`
- `phone_source`
- `message_mode`
- `provider_template_ref`
- `body_template`
- `parameter_map`
- `require_opt_in`
- `retry_enabled`
- `add_order_note`

placeholder groups:
- `order.*`
- `customer.*`
- `billing.*`
- `shipping.*`
- `store.*`
- `site.*`
- `payment.*`
- `status.*`

placeholder policy:
- placeholder ناشناخته در admin warning بدهد
- placeholder حیاتیِ خالی در runtime باعث block شدن send و log warning شود

recipient resolution:
- customer phone از sourceهای مجاز
- admin phone از settings
- custom phone از mapping
- normalize phone
- invalid phone را reject کن
- no send if recipient missing
- masked logging

idempotency strategy:
- برای هر dispatch یک کلید یکتا بساز بر پایه‌ی plugin + event + order_id + recipient + mapping_version
- markerها را در order meta نگه دار
- به dispatch idهای core لینک بده
- force resend فقط از مسیر manual resend مجاز است

شبه‌رفتار:
event handling:
1) raw Woo hook را به event key داخلی map کن
2) mapping را load کن
3) اگر mapping disabled است، return
4) order object را از Woo APIs بگیر
5) recipient را resolve کن
6) اگر recipient نیست، log و return
7) اگر opt-in لازم است، consent را check کن
8) idempotency key را بساز
9) اگر duplicate بود و forced نبود، log و return
10) MessageRequest عمومی بساز
11) از طریق core dispatch کن
12) markerها و audit را ذخیره کن
13) در صورت نیاز order note اضافه کن

manual resend:
1) capability check
2) nonce check
3) order/event validation
4) forced dispatch
5) bypass duplicate guard با audit
6) dispatch از طریق core
7) ذخیره‌ی resend reference

admin UI لازم:
- settings page
- event cards
- recipient config
- message mode
- template/body fields
- placeholder help
- validation state
- preview area
- order admin UI برای dispatch history و resend

security rules:
- manual resend فقط با capability و nonce
- order ids validate شوند
- settings sanitize شوند
- outputs escape شوند
- phoneها masked نمایش داده شوند
- PII غیرضروری ذخیره نشود

graceful degradation:
اگر WooCommerce غایب بود:
- plugin dormant
- no trigger registration
- admin notice

اگر core غایب بود:
- plugin dormant
- admin notice

اگر gateway فعال نبود:
- settings قابل مشاهده باشند
- mappingها قابل مدیریت باشند
- dispatchها غیرفعال باشند
- warning واضح نمایش داده شود

تست‌های اجباری:
- event mapping resolution
- placeholder rendering
- recipient resolution
- idempotency key generation
- consent checks
- processing send
- completed send
- refunded send
- customer note send
- manual resend success
- gateway unavailable
- core missing
- Woo missing
- missing phone
- duplicate event fire
- nonce/capability failure on resend
- HPOS on
- HPOS off
- classic flow
- modern/blocks flow
- guest checkout

فایل‌هایی که در صورت تغییر باید به‌روزرسانی شوند:
- `docs/handoff/integrations/yekta-integration-woocomrce.md`
- `docs/architecture/compatibility.md`
- `docs/architecture/contracts.md` اگر contract عمومی تغییر کرد
- `docs/release/qa-checklist.md`
- `docs/release/changelog.md` اگر وجود دارد

خروجی نهایی تو باید این ساختار را داشته باشد:
1) Summary
2) Assumptions
3) Risks
4) Files Created/Updated
5) Tests Added/Updated
6) Docs Updated
7) Remaining Validation Items

ممنوعیت‌ها:
- provider-specific API calls مستقیم
- direct SQL برای order logic
- reliance روی internals شکننده‌ی Woo بدون justification
- dependency خارجی غیرضروری
- دورزدن core contracts
- ذخیره‌ی بی‌دلیل PII
- حدس‌زدن درباره‌ی hookها و flowهای WooCommerce
```
