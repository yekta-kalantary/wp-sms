# changelog.md

این فایل changelog مرکزی monorepo اکوسیستم `Yekta SMS` است.  
هدف آن ثبت تغییرات مهم به‌صورت ساختارمند، قابل‌پیگیری و release-oriented است.

> پیشنهاد: از همین فایل به‌عنوان changelog داخلی repo استفاده شود و در صورت نیاز برای هر plugin، changelog جداگانه نیز از روی آن مشتق شود.

---

## اصول

### 1) فقط تغییرات معنادار ثبت شوند
این موارد باید ثبت شوند:
- feature جدید
- fix مهم
- تغییر contract عمومی
- تغییر compatibility
- تغییر امنیتی
- migration
- تغییر release process
- deprecation

این موارد معمولاً نیاز به ثبت ندارند مگر اینکه اثر release داشته باشند:
- refactor خیلی کوچک
- تغییرات کاملاً داخلی بدون اثر observable
- typo جزئی در docs

### 2) فرمت ثابت بماند
برای هر release از این دسته‌بندی استفاده شود:
- Added
- Changed
- Fixed
- Deprecated
- Removed
- Security

### 3) نسخه‌دهی با SemVer
- `major` = breaking changes
- `minor` = feature backward-compatible
- `patch` = fix و بهبود بدون breaking change

---

## قالب ثبت release

```text
## [نسخه] - YYYY-MM-DD

### Added
- ...

### Changed
- ...

### Fixed
- ...

### Deprecated
- ...

### Removed
- ...

### Security
- ...
```

---

## Unreleased

### Added
- ساختار مستندات monorepo برای هدایت AI Agentها اضافه شد.
- فایل‌های `README.md`، `AGENTS.md` و `CONTRIBUTING.md` اضافه شدند.
- اسناد معماری پایه شامل `plugin-boundaries`، `contracts` و `compatibility` اضافه شدند.
- ADRهای اولیه برای Core-first، مرز gateway و مرز integration اضافه شدند.
- Handoff Pack واقعی برای این سه پلاگین اضافه شد:
  - `yekta-sms-core`
  - `yekta-geateway-smsir`
  - `yekta-integration-woocomrce`
- Prompt Pack واقعی برای همین سه پلاگین اضافه شد.
- اسناد release شامل QA checklist و support runbook اضافه شدند.
- قالب‌های prompt برای gateway، integration و core اضافه شدند.

### Changed
- پکیج docs starter به‌صورت مرحله‌ای کامل‌تر شد و از یک اسکلت ساده به یک بسته‌ی اجرایی برای Codex/Cursor تبدیل شد.

### Fixed
- موردی ثبت نشده است.

### Deprecated
- موردی ثبت نشده است.

### Removed
- موردی ثبت نشده است.

### Security
- قواعد redaction، capability checks، nonce/permission checks و policyهای امنیتی در docs رسمی repo تثبیت شدند.

---

## [1.0.0-docs-foundation] - TBD

### Added
- نخستین foundation مستنداتی repo برای توسعه‌ی انسانی و AI Agentها.

### Changed
- N/A

### Fixed
- N/A

### Deprecated
- N/A

### Removed
- N/A

### Security
- baseline policy برای مدیریت secret و log redaction تعریف شد.

---

## راهنمای update changelog

### وقتی `core` تغییر می‌کند
این موارد را بررسی کن:
- آیا contract عمومی تغییر کرده؟
- آیا migration اضافه شده؟
- آیا compatibility تغییر کرده؟
- آیا release note برای pluginهای وابسته لازم است؟

### وقتی `gateway` تغییر می‌کند
این موارد را بررسی کن:
- capability جدید؟
- endpoint جدید؟
- provider compatibility impact؟
- health check / auth / error mapping change؟

### وقتی `integration` تغییر می‌کند
این موارد را بررسی کن:
- triggerهای جدید؟
- mapping behavior change؟
- HPOS/Blocks compatibility impact؟
- privacy/consent behavior change؟

---

## قواعد نوشتن changelog

### بنویس:
- «چه چیزی برای release مهم است»
- «چه اثری روی کاربر/توسعه‌دهنده دارد»
- «آیا migration لازم است یا نه»
- «آیا breaking است یا نه»

### ننویس:
- توضیحات خیلی ریز implementation
- جزئیات نامربوط commitها
- متن مبهم مثل:
  - `بهبودها`
  - `تغییرات`
  - `رفع برخی مشکلات`

---

## اگر breaking change وجود داشت
حتماً این موارد را اضافه کن:
- Why
- Migration path
- Affected plugins
- Required manual actions

قالب پیشنهادی:

```text
### Changed
- قرارداد عمومی dispatch result تغییر کرد. این تغییر breaking است و gatewayهای سفارشی باید فیلدهای ... را update کنند.

### Migration Notes
- اگر gateway سفارشی دارید، فایل ... را به‌روزرسانی کنید.
- نسخه‌ی حداقل core موردنیاز: ...
```
