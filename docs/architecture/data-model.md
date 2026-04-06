# data-model.md

## هدف
این سند مدل داده‌ی اکوسیستم `Yekta SMS` را در سطح طراحی توضیح می‌دهد.  
این سند مرجع storage decisions است تا AI Agentها و توسعه‌دهنده‌ها برای table، option، meta و transient چیزی را حدس نزنند.

> این سند سورس migration نیست.  
> این سند فقط مرجع طراحی و قرارداد داده است.

---

## 1) دسته‌بندی داده‌ها

در این اکوسیستم، داده‌ها در این دسته‌ها قرار می‌گیرند:

1. **Core Options**
2. **Gateway Options**
3. **Integration Options**
4. **Core Custom Tables**
5. **Integration Object Meta**
6. **Transient / Cache**
7. **Derived / Runtime Data**

---

## 2) Core Options

### 2.1) `yekta_sms_core_settings`
نوع: option array

#### کلیدهای پایه
- `active_gateway`
- `dispatch_enabled`
- `log_level`
- `log_retention_days`
- `max_retry_attempts`
- `scheduler_preference`
- `mask_logs`
- `debug_mode`
- `capability_mode`

#### قواعد
- sanitize callback روشن
- فقط keyهای مجاز پذیرفته شوند
- option باید version-aware باشد
- secret در این option ذخیره نشود مگر واقعاً shared و ضروری باشد

---

### 2.2) `yekta_sms_core_version`
نوع: scalar option  
هدف:
- نگهداری version فعلی plugin

### 2.3) `yekta_sms_core_db_version`
نوع: scalar option  
هدف:
- نگهداری version schema/migration

---

## 3) Gateway Options

### 3.1) `yekta_sms_gateway_smsir_settings`
نوع: option array

#### کلیدها
- `enabled`
- `mode`
- `api_key`
- `default_line_number`
- `request_timeout`
- `connectivity_check_strategy`
- `mask_message_content`
- `header_accept_mode`

#### قواعد
- `api_key` secret است
- در UI باید masked نمایش داده شود
- در logs نباید raw ثبت شود
- این option نباید داخل core settings merge شود

---

## 4) Integration Options

### 4.1) `yekta_sms_wc_settings`
نوع: option array

#### کلیدها
- `enabled`
- `send_mode`
- `respect_opt_in`
- `write_order_notes`
- `retry_policy`
- `manual_resend_enabled`
- `customer_phone_source`
- `admin_phone_list`

---

### 4.2) `yekta_sms_wc_event_mappings`
نوع: option array / structured mapping set

#### هر mapping باید حداقل این فیلدها را داشته باشد
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

#### قواعد
- کلید event باید stable باشد
- event config باید validate شود
- mappingها باید versionable باشند
- placeholderها باید در admin validate شوند

---

## 5) Core Custom Tables

### 5.1) جدول `yekta_sms_dispatches`

هدف:
- audit trail برای dispatchها
- support برای idempotency
- support برای retries
- support برای troubleshooting

#### فیلدهای حداقلی
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

#### توضیح فیلدها
- `provider_slug`: gateway انتخاب‌شده
- `source_plugin`: مثل `yekta-integration-woocomrce`
- `source_event`: event domain
- `source_object_type`: مثل `order`
- `source_object_id`: شناسه‌ی شیء domain
- `recipient_masked`: شماره mask شده
- `idempotency_key`: کلید جلوگیری از ارسال تکراری
- `attempt`: شماره‌ی تلاش
- `status`: pending/success/failed/skipped/retry_scheduled
- `retryable`: بولی
- `provider_message_id`: شناسه‌ی provider
- `provider_batch_id`: شناسه‌ی batch در صورت وجود
- `error_code`: taxonomy عمومی
- `error_details_json`: جزئیات machine-readable
- `meta_json`: context اضافی redacted
- `correlation_id`: trace id

#### constraints پیشنهادی
- index روی `correlation_id`
- index روی `source_object_type + source_object_id`
- index روی `provider_slug`
- unique یا semi-unique strategy برای `idempotency_key` بسته به مدل retry

---

### 5.2) جدول `yekta_sms_logs`

هدف:
- structured logs
- support debugging
- support diagnostics correlation

#### فیلدهای حداقلی
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

#### قواعد
- `context_json` باید redacted باشد
- secretها نباید raw ذخیره شوند
- PII فقط در حد لازم و ترجیحاً masked ذخیره شود

#### indexهای پیشنهادی
- `created_at_gmt`
- `level`
- `correlation_id`
- `source_object_type + source_object_id`

---

## 6) Integration Object Meta

### 6.1) WooCommerce Order Meta

برای integration ووکامرس، این metaها مجازند:

- `_yekta_sms_sent_{event_hash}`
- `_yekta_sms_last_dispatch_ids`
- `_yekta_sms_opt_in`
- `_yekta_sms_last_error`

#### هدف هر meta
- `sent_{event_hash}`: marker برای idempotency
- `last_dispatch_ids`: ارجاع به dispatchهای اخیر
- `opt_in`: وضعیت consent در صورت استفاده
- `last_error`: خلاصه‌ی آخرین failure برای troubleshooting

#### قواعد
- metaها باید prefix‌دار باشند
- PII غیرضروری ذخیره نشود
- raw body یا secretها در meta ذخیره نشوند

---

## 7) Transient / Cache

### مجاز
- cache نتایج health check
- cache diagnostics کوتاه‌عمر
- derived admin summaries که non-authoritative هستند

### غیرمجاز
- نگهداری state حیاتی فقط در transient
- نگهداری dispatch audit فقط در transient
- نگهداری secret فقط در transient به‌عنوان source of truth

---

## 8) Runtime-only Data

بعضی داده‌ها نباید persist شوند مگر با دلیل روشن:

- raw unredacted provider response
- full message content در همه‌ی مسیرها
- OTP values
- full auth headers
- secretهای خام
- full personal data غیرضروری

---

## 9) داده‌های حساس

### secretها
- API key
- هر credential دیگر provider

### PII
- phone number
- customer-identifying context
- order-linked personal fields

### policy
- secretها raw log نشوند
- phoneها masked log شوند
- body/message فقط در صورت نیاز و با redaction نگهداری شود
- debugging نباید باعث نشت data شود

---

## 10) idempotency data model

### هدف
جلوگیری از dispatch تکراری برای یک event/domain context

### منابع داده
- `idempotency_key` در dispatch table
- order meta marker در integration ووکامرس

### ساختار مفهومی key
```text
hash(plugin + event + object_id + recipient + mapping_version)
```

### policy
- forced resend باید key جدید یا bypass audited داشته باشد
- retry نباید به‌اشتباه duplicate واقعی تلقی شود

---

## 11) migration policy

### قواعد
- migrationها incremental باشند
- schema version جدا از plugin version باشد
- migrationها idempotent باشند
- data migrationها و schema migrationها در طراحی از هم تفکیک شوند

### وقتی data model تغییر می‌کند
باید این‌ها بررسی شوند:
- backward compatibility
- changelog
- release checklist
- handoff docs
- prompt docs
- اگر لازم است ADR

---

## 12) retention policy

### logs
- retention مبتنی بر setting
- purge job باید قابل‌پیاده‌سازی باشد

### dispatches
- retention policy باید محافظه‌کارانه‌تر باشد چون audit/troubleshooting مهم است

### note
Retention نهایی business decision هم هست و باید قبل از production release نهایی شود.

---

## 13) نمونه‌ی تقسیم داده‌ها بین لایه‌ها

### core
- settings عمومی
- tables
- logs
- dispatch audit

### gateway
- settings provider
- health cache transient

### integration
- event mappings
- integration settings
- object meta برای idempotency و troubleshooting

---

## 14) anti-patternها

این‌ها نباید اتفاق بیفتند:
- ذخیره‌ی provider config در core shared option بدون مرز روشن
- ذخیره‌ی raw secret در logs
- ذخیره‌ی raw OTP در order meta
- reliance روی transient به‌عنوان source of truth
- استفاده از post meta برای dispatch audit عمومی
- data duplication بی‌دلیل بین table و meta

---

## 15) قاعده‌ی نهایی

اگر درباره‌ی محل ذخیره‌ی یک داده شک داشتی، این سه پرسش را بپرس:
1. این داده shared است یا plugin-specific؟
2. این داده audit/troubleshooting-critical است یا cache؟
3. این داده حساس است یا نه؟

پاسخ به این سه پرسش باید تعیین کند:
- option
- custom table
- object meta
- transient
- یا اصلاً عدم ذخیره‌سازی
