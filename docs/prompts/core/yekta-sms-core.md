# Prompt Pack — `yekta-sms-core`

این فایل یک پرامپت self-contained برای Codex GPT، Cursor یا هر AI Coding Agent دیگر است تا فقط بر اساس قراردادهای همین repo، سورس `yekta-sms-core` را تولید یا تکمیل کند.

---

## Prompt

```text
تو مسئول تولید یا تکمیل سورس پلاگین وردپرسی `yekta-sms-core` در monorepo اکوسیستم Yekta SMS هستی.

قبل از هر تغییری، این فایل‌ها را کامل بخوان:
1) `README.md`
2) `docs/architecture/plugin-boundaries.md`
3) `docs/architecture/contracts.md`
4) `docs/architecture/compatibility.md`
5) `docs/decisions/ADR-001-core-first.md`
6) `docs/handoff/core/yekta-sms-core.md`

قواعد قطعی:
1) از خودت چیزی حدس نزن.
2) فقط طبق Handoff Pack و docs همین repo کدنویسی کن.
3) اگر جایی مستندات رسمی WordPress برای implementation detail لازم بود، آن را بررسی کن و چیزی را حدس نزن.
4) کد را production-grade، ساده، ماژولار و قابل نگهداری بنویس.
5) هیچ منطق provider-specific یا WooCommerce-specific داخل core قرار نده.
6) همه‌ی متن‌های UI باید قابل ترجمه باشند.
7) dependency اضافی غیرضروری ممنوع است.
8) public contractها را بدون justification و بدون update docs تغییر نده.
9) قبل از پایان کار، تست‌ها و docs لازم را به‌روزرسانی کن.
10) اگر بخشی نیاز به تصمیم معماری جدید دارد، قبل از implementation آن را صریح گزارش کن.

هدف پلاگین:
ساخت هسته‌ی مرکزی Yekta SMS شامل:
- contracts عمومی
- registry برای gatewayها و integrationها
- active gateway resolution
- dispatch orchestration
- settings foundation
- secret resolution
- structured logging
- diagnostics
- admin foundation
- REST admin endpoints
- migrations/versioning

ساختار پوشه‌ی هدف:
- `core/yekta-sms-core.php`
- `core/uninstall.php`
- `core/src/Bootstrap/*`
- `core/src/Container/*`
- `core/src/Contracts/*`
- `core/src/Domain/*`
- `core/src/Application/*`
- `core/src/Infrastructure/*`
- `core/src/Admin/*`
- `core/src/Rest/*`
- `core/src/Support/*`
- `core/tests/*`

فایل‌های کلیدی که باید وجود داشته باشند:
- `src/Bootstrap/Plugin.php`
- `src/Bootstrap/Activation.php`
- `src/Bootstrap/Deactivation.php`
- `src/Container/Container.php`
- `src/Contracts/GatewayInterface.php`
- `src/Contracts/GatewayDefinitionInterface.php`
- `src/Contracts/MessageDispatcherInterface.php`
- `src/Contracts/LoggerInterface.php`
- `src/Contracts/SchedulerInterface.php`
- `src/Contracts/HealthCheckInterface.php`
- `src/Contracts/IntegrationDefinitionInterface.php`
- `src/Domain/MessageRequest.php`
- `src/Domain/DispatchResult.php`
- `src/Application/Registry/GatewayRegistry.php`
- `src/Application/Registry/IntegrationRegistry.php`
- `src/Application/Dispatch/ActiveGatewayResolver.php`
- `src/Application/Dispatch/MessageDispatcher.php`
- `src/Application/Config/SettingsRepository.php`
- `src/Application/Config/SecretResolver.php`
- `src/Application/Diagnostics/DiagnosticsRunner.php`
- `src/Application/Logging/DbLogger.php`
- `src/Infrastructure/Persistence/DispatchRepository.php`
- `src/Infrastructure/Persistence/LogRepository.php`
- `src/Admin/MenuRegistrar.php`
- `src/Rest/GatewaysController.php`
- `src/Rest/DiagnosticsController.php`
- `src/Rest/LogsController.php`
- `src/Rest/ToolsController.php`

قابلیت‌های اجباری:
- gateway registry
- integration registry
- active gateway selection
- dispatch pipeline
- dispatch audit storage
- structured logs
- diagnostics runner
- admin pages
- admin REST routes
- test-send/test-connection infrastructure
- retry scheduling abstraction

قیود فنی:
- فقط WordPress APIs رسمی
- Settings API برای تنظیمات
- REST API رسمی WordPress برای routeها
- هر route باید `permission_callback` داشته باشد
- sanitize/validate روی ورودی‌ها
- escape روی خروجی‌ها
- capability checks روی admin و REST
- nonce/permission checks برای عملیات حساس
- secretها باید redact شوند
- settings حساس باید autoload-friendly و محافظه‌کارانه مدیریت شوند
- public hook prefix باید `yekta_sms_` باشد
- function prefix باید `yekta_sms_` باشد
- هیچ endpoint provider-specific داخل core نباشد
- هیچ order/event logic مربوط به integrationها داخل core نباشد

Settings و option keys لازم:
- `yekta_sms_core_settings`
- `yekta_sms_core_version`
- `yekta_sms_core_db_version`

کلیدهای settings:
- `active_gateway`
- `dispatch_enabled`
- `log_level`
- `log_retention_days`
- `max_retry_attempts`
- `scheduler_preference`
- `mask_logs`
- `debug_mode`
- `capability_mode`

جدول‌های لازم:
1) `yekta_sms_dispatches`
2) `yekta_sms_logs`

حداقل فیلدهای dispatch table:
- شناسه
- زمان ایجاد/به‌روزرسانی
- provider slug
- source plugin
- source event
- source object type
- source object id
- recipient masked
- idempotency key
- attempt
- status
- retryable
- provider message id
- provider batch id
- error code
- error details json
- meta json
- correlation id

حداقل فیلدهای logs table:
- شناسه
- زمان
- level
- channel
- message
- context json
- correlation id
- source plugin
- source object type
- source object id

hookهای عمومی لازم:
- `yekta_sms_gateway_factories`
- `yekta_sms_integration_factories`
- `yekta_sms_core_diagnostics_checks`
- `yekta_sms_core_capability_map`
- `yekta_sms_log_context`
- `yekta_sms_scheduler_adapters`
- `yekta_sms_core_booted`
- `yekta_sms_before_dispatch`
- `yekta_sms_after_dispatch`
- `yekta_sms_dispatch_error`

شبه‌رفتار مورد انتظار:
- در `plugins_loaded` بوت شود
- requirementها را چک کند
- container را بسازد
- service providerها را register کند
- gatewayها و integrationها را از hookها جمع‌آوری کند
- registryها را initialize کند
- admin/REST/diagnostics را register کند

dispatch flow:
1) request را validate کن
2) اگر dispatch globally disabled است، terminal error بده
3) active gateway را resolve کن
4) capability required را بررسی کن
5) dispatch row pending بساز
6) قبل از dispatch log/action ثبت کن
7) gateway را صدا بزن
8) result را normalize و persist کن
9) log/action بعد از dispatch را ثبت کن
10) اگر retryable بود، retry schedule کن
11) result را برگردان

تست‌های اجباری:
- duplicate slug در registry
- active gateway resolution
- no active gateway
- dispatch disabled
- fake gateway dispatch success
- logger redaction
- secret resolver precedence
- REST permission denied
- diagnostics aggregation
- retry scheduling path

Negative paths:
- invalid request
- invalid gateway slug
- DB insert failure
- scheduler unavailable
- missing tables

اسناد و فایل‌هایی که در صورت تغییر باید به‌روزرسانی شوند:
- `docs/architecture/contracts.md`
- `docs/architecture/compatibility.md`
- `docs/handoff/core/yekta-sms-core.md`
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
- اضافه‌کردن dependency خارجی بدون ضرورت قطعی
- hard-code کردن gatewayها
- منطق WooCommerce
- منطق provider-specific
- تغییر خودسرانه‌ی public contractها
- دورزدن WordPress security patterns
- سورس پیچیده و overengineered
```
