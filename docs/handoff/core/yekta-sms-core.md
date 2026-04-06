# Handoff Pack — `yekta-sms-core`

## 1) هدف
این پلاگین هسته‌ی مرکزی اکوسیستم Yekta SMS است و باید تمام قراردادهای عمومی، registryها، dispatch orchestration، logging، diagnostics، settings foundation و admin foundation را فراهم کند.

---

## 2) dependencyها
- WordPress
- بدون dependency سخت به WooCommerce
- بدون dependency سخت به هیچ gateway خاص
- pluginهای جانبی باید به public contractهای همین پلاگین متکی باشند، نه به implementation داخلی آن

---

## 3) ساختار پوشه‌ی پیشنهادی

```text
core/
├─ yekta-sms-core.php
├─ uninstall.php
├─ readme.txt
├─ languages/
├─ assets/
├─ src/
│  ├─ Bootstrap/
│  ├─ Container/
│  ├─ Contracts/
│  ├─ Domain/
│  ├─ Application/
│  ├─ Infrastructure/
│  ├─ Admin/
│  ├─ Rest/
│  └─ Support/
└─ tests/
   ├─ Unit/
   ├─ Integration/
   └─ Fixtures/
```

---

## 4) فایل‌های موردنیاز

### فایل‌های bootstrap
- `yekta-sms-core.php`
- `src/Bootstrap/Plugin.php`
- `src/Bootstrap/Activation.php`
- `src/Bootstrap/Deactivation.php`
- `uninstall.php`

### container و registration
- `src/Container/Container.php`
- `src/Bootstrap/ServiceProvider.php`
- `src/Bootstrap/CoreInfrastructureProvider.php`
- `src/Bootstrap/CoreConfigProvider.php`
- `src/Bootstrap/CoreDispatchProvider.php`
- `src/Bootstrap/CoreLoggingProvider.php`
- `src/Bootstrap/CoreAdminProvider.php`
- `src/Bootstrap/CoreRestProvider.php`
- `src/Bootstrap/CoreDiagnosticsProvider.php`

### contracts عمومی
- `src/Contracts/GatewayInterface.php`
- `src/Contracts/GatewayDefinitionInterface.php`
- `src/Contracts/MessageDispatcherInterface.php`
- `src/Contracts/LoggerInterface.php`
- `src/Contracts/SchedulerInterface.php`
- `src/Contracts/HealthCheckInterface.php`
- `src/Contracts/IntegrationDefinitionInterface.php`

### domain / application
- `src/Domain/MessageRequest.php`
- `src/Domain/DispatchResult.php`
- `src/Application/Registry/GatewayRegistry.php`
- `src/Application/Registry/IntegrationRegistry.php`
- `src/Application/Dispatch/ActiveGatewayResolver.php`
- `src/Application/Dispatch/MessageDispatcher.php`
- `src/Application/Config/SettingsRepository.php`
- `src/Application/Config/SecretResolver.php`
- `src/Application/Diagnostics/DiagnosticsRunner.php`

### persistence / logging
- `src/Infrastructure/Persistence/DispatchRepository.php`
- `src/Infrastructure/Persistence/LogRepository.php`
- `src/Application/Logging/DbLogger.php`
- `src/Application/Logging/LogContextNormalizer.php`
- `src/Application/Logging/PhoneRedactor.php`
- `src/Application/Logging/SecretRedactor.php`

### admin / rest
- `src/Admin/MenuRegistrar.php`
- `src/Admin/Pages/DashboardPage.php`
- `src/Admin/Pages/GatewaysPage.php`
- `src/Admin/Pages/LogsPage.php`
- `src/Admin/Pages/DiagnosticsPage.php`
- `src/Admin/Pages/ToolsPage.php`
- `src/Rest/GatewaysController.php`
- `src/Rest/DiagnosticsController.php`
- `src/Rest/LogsController.php`
- `src/Rest/ToolsController.php`

### support / versioning
- `src/Support/Requirements.php`
- `src/Support/Version.php`
- `src/Support/Capabilities.php`
- `src/Support/Options.php`
- `src/Support/Schema.php`

---

## 5) مسئولیت هر بخش

### Bootstrap
- بارگذاری plugin
- requirement checks
- ساخت container
- رجیستر کردن serviceها
- init کردن admin / REST / diagnostics
- جمع‌کردن gatewayها و integrationها از طریق filterها

### Contracts
- تعریف تمام APIهای عمومی برای pluginهای اکوسیستم

### Registry
- نگهداری gatewayها و integrationهای ثبت‌شده
- جلوگیری از duplicate slug
- ارائه‌ی lookup by slug

### Dispatch
- resolve کردن gateway فعال
- validate کردن request
- capability check
- dispatch
- persist کردن نتیجه
- logging
- retry scheduling

### Config
- خواندن و نوشتن settings
- resolve کردن secretها با policy مشخص

### Logging
- لاگ ساختاریافته
- redaction
- پشتیبانی از correlation id

### Diagnostics
- اجرای checkهای pass/warn/fail
- ارائه‌ی remediation text

### Admin / REST
- UI مشترک
- routeهای admin-only
- test send / test connection / logs / diagnostics

---

## 6) قراردادهای عمومی

### GatewayDefinitionInterface
وظیفه:
- معرفی metadata gateway
- ساخت instance gateway
- ساخت health checker
- اعلام capabilityها

### GatewayInterface
وظیفه:
- تشخیص configured بودن
- تشخیص available بودن
- ارسال request عمومی
- اعلام capabilityها

### MessageDispatcherInterface
وظیفه:
- گرفتن `MessageRequest`
- resolve کردن gateway فعال
- اجرای dispatch
- بازگرداندن `DispatchResult`

### LoggerInterface
وظیفه:
- ثبت log با level و context نرمال‌شده

### HealthCheckInterface
وظیفه:
- برگرداندن نتیجه‌ی نرمال‌شده‌ی pass/warn/fail

---

## 7) hook map

### Filters
- `yekta_sms_gateway_factories`
- `yekta_sms_integration_factories`
- `yekta_sms_core_diagnostics_checks`
- `yekta_sms_core_capability_map`
- `yekta_sms_log_context`
- `yekta_sms_scheduler_adapters`

### Actions
- `yekta_sms_core_booted`
- `yekta_sms_before_dispatch`
- `yekta_sms_after_dispatch`
- `yekta_sms_dispatch_error`

---

## 8) settings keys

### option: `yekta_sms_core_settings`
- `active_gateway`
- `dispatch_enabled`
- `log_level`
- `log_retention_days`
- `max_retry_attempts`
- `scheduler_preference`
- `mask_logs`
- `debug_mode`
- `capability_mode`

### option: `yekta_sms_core_version`
### option: `yekta_sms_core_db_version`

---

## 9) data model

### جدول `yekta_sms_dispatches`
فیلدهای حداقلی:
- `id`
- `created_at_gmt`
- `updated_at_gmt`
- `provider_slug`
- `source_plugin`
- `source_event`
- `source_object_type`
- `source_object_id`
- `recipient_masked`
- `idempotency_key`
- `attempt`
- `status`
- `retryable`
- `provider_message_id`
- `provider_batch_id`
- `error_code`
- `error_details_json`
- `meta_json`
- `correlation_id`

### جدول `yekta_sms_logs`
فیلدهای حداقلی:
- `id`
- `created_at_gmt`
- `level`
- `channel`
- `message`
- `context_json`
- `correlation_id`
- `source_plugin`
- `source_object_type`
- `source_object_id`

---

## 10) flow در سطح شبه‌کد

### bootstrap
```text
on plugins_loaded:
  load textdomain
  check requirements
  build container
  register service providers
  collect gateway factories
  collect integration factories
  initialize registries
  register admin pages
  register REST controllers
  fire yekta_sms_core_booted
```

### dispatch
```text
dispatch(request):
  validate request shape
  if dispatch disabled -> return terminal error
  gateway = resolve active gateway
  if gateway missing -> return terminal error
  if capability unsupported -> return terminal error
  create pending dispatch row
  log before_dispatch
  result = gateway.send(request)
  update dispatch row
  log result
  if result.retryable:
    schedule retry
  return result
```

---

## 11) REST / admin interfaces

### صفحات admin
- نمای کلی
- gatewayها
- لاگ‌ها
- دیاگنستیک
- ابزارها

### routeهای REST
- `/yekta-sms/v1/gateways`
- `/yekta-sms/v1/diagnostics/run`
- `/yekta-sms/v1/logs`
- `/yekta-sms/v1/tools/test-send`
- `/yekta-sms/v1/tools/test-connection`

### قواعد امنیتی
- هر route باید `permission_callback` داشته باشد
- فقط برای userهای مجاز
- sanitize و validate روی request params
- escape روی خروجی UI
- nonce/cookie auth برای عملیات admin

---

## 12) قابلیت‌ها و دسترسی‌ها

### capabilityهای پیشنهادی
- `manage_yekta_sms`
- `manage_yekta_sms_settings`
- `view_yekta_sms_logs`
- `manage_yekta_sms_diagnostics`
- `send_yekta_sms_test`

---

## 13) logging و diagnostics

### logging
- structured
- redacted
- correlation id mandatory
- no raw secret
- no unnecessary PII

### diagnostics checks
- core version
- DB schema version
- registry health
- active gateway state
- tables existence
- REST registration
- scheduler availability
- dependency states

---

## 14) security rules
- secretها در optionهای حساس با autoload کم یا غیرفعال
- API keyها هرگز raw log نشوند
- inputها validate/sanitize شوند
- outputها escape شوند
- capability checks everywhere
- عملیات حساس با nonce

---

## 15) migration / versioning
- migrationها incremental و idempotent باشند
- schema version جدا از plugin version نگه داشته شود
- data migrationها جدا از schema migrationها باشند
- تغییر public contract بدون ADR مجاز نیست

---

## 16) test cases

### unit
- registry duplicate slug
- active gateway resolution
- dispatch disabled
- secret resolver precedence
- logger redaction
- diagnostics result aggregation

### integration
- REST permission denied
- REST success path
- tables created on activation
- fake gateway dispatch
- retry scheduling path

### negative
- no active gateway
- invalid gateway slug
- invalid request
- DB insert failure
- scheduler unavailable

---

## 17) edge cases
- gateway active است ولی plugin آن حذف شده
- جدول dispatches وجود ندارد
- migration ناقص اجرا شده
- plugin در multisite network فعال می‌شود
- dispatch در حین retry به duplicate برخورد می‌کند

---

## 18) release checklist
- activation/deactivation safe
- uninstall path documented
- public contracts reviewed
- logs redacted
- diagnostics usable
- REST routes permission-protected
- fake gateway end-to-end test passing

---

## 19) definition of done
- fake gateway می‌تواند register شود
- dispatch pipeline انتهابه‌انتها کار می‌کند
- logs و dispatches پایدار ذخیره می‌شوند
- diagnostics usable است
- plugin در نبود gateway/integration fatal نمی‌دهد

---

## 20) verdict
**Ready with Assumptions**
