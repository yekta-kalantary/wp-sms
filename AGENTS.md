# AGENTS.md

این فایل دستور اجرایی سریع برای AI Agentها در monorepo اکوسیستم `Yekta SMS` است.  
اگر agent فرصت یا context محدود دارد، این فایل را قبل از هر چیز بخواند.  
اگر نیاز به جزئیات بیشتری داشت، بعد از این فایل باید `README.md` و سپس `docs/` را بخواند.

---

## 1) اولویت خواندن فایل‌ها

ترتیب اجباری خواندن:

1. `AGENTS.md`
2. `README.md`
3. `docs/architecture/plugin-boundaries.md`
4. `docs/architecture/contracts.md`
5. `docs/architecture/compatibility.md`
6. ADR مرتبط
7. Handoff مربوط به همان plugin
8. Prompt مربوط به همان plugin

---

## 2) ماهیت این repo

این repo یک **monorepo** برای اکوسیستم پلاگینی Yekta SMS است.

ساختار اصلی:
- `core/`
- `geateway/`
- `integration/`
- `docs/`

قاعده‌ی اصلی:
- `core` = هسته و قراردادهای عمومی
- `gateway` = فقط provider adapter
- `integration` = فقط event binding / orchestration

---

## 3) قوانین غیرقابل‌مذاکره

### 3.1) چیزی را حدس نزن
- hook
- endpoint
- setting key
- capability
- data flow
- provider behavior
- WooCommerce flow

اگر مطمئن نیستی:
- docs repo را بخوان
- اگر کافی نبود، کمبود را صریح گزارش کن

### 3.2) مرزهای معماری را نشکن
- provider-specific logic داخل `core` ممنوع
- integration logic داخل `gateway` ممنوع
- provider API call مستقیم داخل `integration` ممنوع
- domain logic خاص integration داخل `core` ممنوع

### 3.3) امنیت اجباری است
- capability checks
- nonce / permission checks
- sanitize / validate input
- escape output
- no raw secret logging
- no unnecessary PII storage

### 3.4) سازگاری مهم‌تر از سرعت است
- WordPress APIs رسمی
- WooCommerce CRUD APIs برای order access
- HPOS-safe بودن برای integrationهای ووکامرس
- graceful degradation در نبود dependency

---

## 4) قبل از هر تغییر چه‌کار کن

قبل از هر کدنویسی:

1. request را طبقه‌بندی کن:
   - Core Feature
   - Gateway Plugin
   - Integration Plugin
   - Refactor / Hardening / QA

2. اثر معماری را بررسی کن:
   - آیا contract عمومی جدید لازم است؟
   - آیا docs باید تغییر کند؟
   - آیا migration لازم است؟
   - آیا test matrix باید تغییر کند؟

3. plugin یا مسیر هدف را مشخص کن.

4. فایل Handoff مربوط را بخوان.

5. اگر prompt pack مربوط وجود دارد، همان را به‌عنوان مرجع اجرایی اصلی در نظر بگیر.

---

## 5) اگر درخواست از نوع Gateway بود

باید:
- فقط در `geateway/` کار کنی
- provider docs را بررسی کنی
- settings، health check، adapter، error mapper و tests را اضافه کنی
- منطق integration وارد نکنی
- در صورت نیاز docs و handoff را به‌روزرسانی کنی

---

## 6) اگر درخواست از نوع Integration بود

باید:
- فقط در `integration/` کار کنی
- docs افزونه‌ی ثالث را بررسی کنی
- triggerها و compatibility requirements را پیدا کنی
- mapping، placeholder، recipient resolution، idempotency و tests را بسازی
- provider-specific logic وارد نکنی

اگر integration مربوط به WooCommerce بود:
- HPOS-safe باش
- direct SQL برای order logic نزن
- تا حد ممکن از CRUD APIs استفاده کن
- classic و modern flow را بررسی کن

---

## 7) اگر درخواست از نوع Core بود

باید:
- اول بررسی کنی feature عمومی است یا نه
- اگر عمومی است، قبل از implementation docs و در صورت نیاز ADR را update کنی
- public contractها را بدون ثبت اثر تغییر، دست‌کاری نکنی
- هیچ dependency اضافی غیرضروری اضافه نکنی

---

## 8) فایل‌های مرجع مهم

### معماری
- `docs/architecture/plugin-boundaries.md`
- `docs/architecture/contracts.md`
- `docs/architecture/compatibility.md`

### تصمیم‌ها
- `docs/decisions/ADR-001-core-first.md`
- `docs/decisions/ADR-002-gateway-boundary.md`
- `docs/decisions/ADR-003-integration-boundary.md`

### handoffهای واقعی
- `docs/handoff/core/yekta-sms-core.md`
- `docs/handoff/gateways/yekta-geateway-smsir.md`
- `docs/handoff/integrations/yekta-integration-woocomrce.md`

### prompt packهای واقعی
- `docs/prompts/core/yekta-sms-core.md`
- `docs/prompts/gateways/yekta-geateway-smsir.md`
- `docs/prompts/integrations/yekta-integration-woocomrce.md`

---

## 9) فرمت اجباری پاسخ نهایی agent

هر پاسخ نهایی باید این بخش‌ها را داشته باشد:

1. Summary
2. Assumptions
3. Risks
4. Files Created/Updated
5. Tests Added/Updated
6. Docs Updated
7. Remaining Validation Items

---

## 10) Definition of Done کوتاه

هیچ کاری تمام‌شده نیست مگر اینکه:
- معماری نقض نشده باشد
- security رعایت شده باشد
- dependencyها کنترل شده باشند
- tests اضافه یا به‌روزرسانی شده باشند
- docs لازم به‌روزرسانی شده باشند
- assumptions باقی‌مانده شفاف ثبت شده باشند

---

## 11) اگر اطلاعات کافی نیست

به‌جای حدس‌زدن، این ساختار را برگردان:

```text
- اطلاعات کافی نیست.
- برای ادامه این موارد باید مشخص شوند:
  1) ...
  2) ...
- بخش‌های پرریسک:
  1) ...
  2) ...
- بخش‌هایی که فعلاً با اطمینان قابل انجام‌اند:
  1) ...
  2) ...
```

---

## 12) دستور نهایی

اگر بین این دو مجبور به انتخاب شدی:
- سرعت یا درستی
- حدس یا مستندات
- پیاده‌سازی سریع یا حفظ مرز معماری

همیشه این‌ها را انتخاب کن:
- درستی
- مستندات
- حفظ مرز معماری
