# قراردادهای عمومی اکوسیستم

## هدف
این سند قراردادهای عمومی‌ای را مشخص می‌کند که تمام pluginهای این monorepo باید از آن‌ها تبعیت کنند.

> این سند مرجع طراحی است، نه سورس‌کد.  
> هر تغییر ناسازگار در این سند باید با ADR و versioning روشن همراه باشد.

---

## 1) قراردادهای عمومی سطح Core

### 1.1) Gateway Definition Contract
هر gateway باید بتواند خود را به `core` معرفی کند.

#### حداقل فیلدها
- `slug`
- `label`
- `version`
- `factory`
- `supported_capabilities`
- `health_checker_factory`

#### مسئولیت
- معرفی gateway به registry
- ساخت instance واقعی gateway
- اعلام capabilityها

---

### 1.2) Gateway Runtime Contract
هر gateway در زمان اجرا باید این توانایی‌ها را در سطح قرارداد فراهم کند:

- بررسی پیکربندی
- اعلام availability
- اعلام capabilityها
- ارسال پیام
- اجرای health check
- نرمال‌سازی خروجی

#### خروجی‌های مورد انتظار
- نتیجه‌ی موفق/ناموفق
- retryable بودن یا نبودن
- شناسه‌ی provider
- خطای نرمال‌شده
- latency
- context امن برای log

---

### 1.3) Message Request Contract
هر integration باید در نهایت یک request عمومی برای `core` بسازد.

#### فیلدهای پایه
- `type`
- `recipients`
- `body_template`
- `provider_template_ref`
- `parameters`
- `source_plugin`
- `source_event`
- `source_object_type`
- `source_object_id`
- `correlation_id`
- `idempotency_key`
- `meta`

#### نکات
- `provider_template_ref` opaque است.
- `core` نباید معنی provider-specific آن را بداند.
- request باید تا حد ممکن provider-agnostic بماند.

---

### 1.4) Dispatch Result Contract

#### فیلدهای پایه
- `success`
- `retryable`
- `provider_slug`
- `provider_message_id`
- `provider_batch_id`
- `normalized_status`
- `normalized_message`
- `error_code`
- `error_details`
- `cost`
- `latency_ms`

---

### 1.5) Logger Contract

#### الزامات
- structured logging
- log levels مشخص
- redaction by default
- پشتیبانی از correlation id

#### سطوح پیشنهادی
- `debug`
- `info`
- `notice`
- `warning`
- `error`
- `critical`

---

### 1.6) Health Check Contract
هر جزء قابل‌بررسی باید بتواند نتیجه‌ی health check نرمال‌شده برگرداند.

#### فیلدهای خروجی
- `key`
- `label`
- `status`
- `summary`
- `details`
- `recommended_action`

---

## 2) قراردادهای ثبت در اکوسیستم

### 2.1) Gateway Registration
از طریق فیلتر یا registry mechanism در `core`

### 2.2) Integration Registration
از طریق فیلتر یا registry mechanism در `core`

### 2.3) Hook Prefix
همه‌ی hookهای عمومی باید با پیشوند زیر باشند:

- `yekta_sms_`

---

## 3) قرارداد نام‌گذاری

### 3.1) PHP Namespace
- `YektaSMS\...`

### 3.2) Function Prefix
- `yekta_sms_`

### 3.3) Option Prefix
- `yekta_sms_`

### 3.4) Meta Prefix
- `_yekta_sms_`

---

## 4) قرارداد backward compatibility

### 4.1) Public API
هر چیزی که صراحتاً به‌عنوان public contract معرفی شده، باید version-aware نگهداری شود.

### 4.2) تغییر ناسازگار
فقط با:
- ADR
- migration note
- changelog
- bump نسخه‌ی مناسب

### 4.3) Internal classes
کلاس‌های internal نباید بدون ضرورت public contract فرض شوند.

---

## 5) قرارداد امنیت

### الزامات حداقلی
- capability checks
- nonce / permission checks
- sanitize/validate روی ورودی
- escape روی خروجی
- secret redaction
- no raw sensitive logging

---

## 6) قرارداد تست

هر plugin جدید باید حداقل این سه لایه را پوشش دهد:

- unit tests
- integration tests
- negative/failure tests

اگر plugin با WooCommerce کار می‌کند:
- HPOS compatibility tests
- classic/modern flow tests

---

## 7) قاعده‌ی تغییر قرارداد

اگر لازم شد contract جدیدی اضافه شود:
1. ابتدا این سند به‌روزرسانی شود.
2. اگر تصمیم معماری جدید است، ADR ثبت شود.
3. بعد از آن implementation انجام شود.
