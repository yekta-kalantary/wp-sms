# CONTRIBUTING.md

این فایل قواعد مشارکت در monorepo اکوسیستم `Yekta SMS` را مشخص می‌کند.  
این قواعد هم برای توسعه‌دهنده‌ی انسانی معتبرند و هم برای AI Agentهایی مثل Codex GPT و Cursor.

---

## 1) هدف

هدف این فایل این است که هر تغییر در repo:
- قابل‌ردیابی باشد
- مرزهای معماری را حفظ کند
- امنیت و compatibility را نقض نکند
- بدون update مستندات وارد پروژه نشود
- با QA مناسب merge شود

---

## 2) قبل از شروع هر کار

قبل از هر تغییر، این فایل‌ها را به‌ترتیب بخوان:

1. `AGENTS.md`
2. `README.md`
3. `docs/architecture/plugin-boundaries.md`
4. `docs/architecture/contracts.md`
5. `docs/architecture/compatibility.md`
6. ADR مرتبط
7. Handoff مربوط به plugin هدف
8. Prompt مربوط به plugin هدف، اگر وجود دارد

### اصل
هیچ تغییری نباید بدون فهم مرزهای `core / gateway / integration` شروع شود.

---

## 3) نوع تغییر را اول مشخص کن

هر تغییر باید در یکی از این دسته‌ها قرار بگیرد:

- `core-feature`
- `gateway-new`
- `gateway-update`
- `integration-new`
- `integration-update`
- `refactor`
- `hardening`
- `qa`
- `docs`
- `release`

اگر نوع تغییر مشخص نشود، طراحی، QA و update docs مبهم می‌شوند.

---

## 4) branch naming

الگوی پیشنهادی branch:

```text
type/scope-short-description
```

### مثال‌ها
- `core-feature/dispatch-retry-policy`
- `gateway-new/melipayamak`
- `gateway-update/smsir-health-check`
- `integration-new/gravityforms`
- `integration-update/woocommerce-order-notes`
- `refactor/core-registry-cleanup`
- `docs/plugin-boundaries-update`
- `release/v1-0-0-rc1`

### قواعد
- کوتاه و روشن
- حروف کوچک
- با `-` جدا شود
- scope مبهم نباشد

---

## 5) workflow استاندارد تغییر

### مرحله 1: تحلیل
- request را طبقه‌بندی کن
- اثر آن را روی معماری بررسی کن
- dependencyها را مشخص کن
- ریسک‌ها را ثبت کن
- docs لازم را شناسایی کن

### مرحله 2: تصمیم معماری
اگر تغییر:
- contract عمومی جدید می‌خواهد
- behavior shared را تغییر می‌دهد
- compatibility را عوض می‌کند
- data model را تغییر می‌دهد

باید قبل از implementation:
- docs معماری update شود
- در صورت نیاز ADR جدید ثبت شود

### مرحله 3: implementation
- فقط در scope مجاز تغییر بده
- از public contractها تبعیت کن
- چیزی را حدس نزن
- dependency غیرضروری اضافه نکن
- تست‌ها را فراموش نکن

### مرحله 4: QA
- unit / integration / negative path
- compatibility checks
- security checks
- regression checks

### مرحله 5: docs
- handoff
- prompt pack
- architecture docs
- changelog
- release notes

### مرحله 6: merge readiness
فقط وقتی تغییر آماده‌ی merge است که:
- کد کامل باشد
- تست‌ها اضافه شده باشند
- docs به‌روزرسانی شده باشند
- assumptions باقی‌مانده ثبت شده باشند

---

## 6) قواعد commit message

الگوی پیشنهادی:

```text
type(scope): summary
```

### مثال‌ها
- `feat(core): add active gateway resolver`
- `feat(gateway-smsir): add health checker`
- `feat(integration-woocommerce): add idempotency guard`
- `fix(core): redact secrets in logger`
- `refactor(core): simplify registry boot flow`
- `docs(repo): add prompt packs for current plugins`
- `test(gateway-smsir): cover malformed response path`
- `chore(release): prepare rc1 checklist`

### typeهای مجاز
- `feat`
- `fix`
- `refactor`
- `docs`
- `test`
- `chore`
- `perf`
- `security`

### قواعد
- خلاصه روشن و کوتاه
- scope واقعی
- commitهای بی‌معنا مثل `update` یا `changes` ممنوع

---

## 7) قواعد Pull Request

هر PR باید این بخش‌ها را داشته باشد:

### 7.1) Summary
چه چیزی تغییر کرده و چرا

### 7.2) Scope
این تغییر مربوط به کدام بخش است:
- core
- gateway
- integration
- docs
- release

### 7.3) Architecture impact
- آیا contract عمومی تغییر کرده؟
- آیا docs معماری update شده؟
- آیا ADR لازم بوده؟

### 7.4) Files changed
فهرست فایل‌های مهم ایجادشده/ویرایش‌شده

### 7.5) Tests
چه تست‌هایی اضافه یا به‌روزرسانی شده‌اند

### 7.6) Risks
ریسک‌های شناخته‌شده

### 7.7) Remaining validation items
چیزهایی که هنوز باید با مستندات رسمی یا تست بیشتر validate شوند

---

## 8) قواعد مخصوص AI Agentها

اگر contributor یک AI Agent است، باید:
- قبل از کدنویسی plan بدهد
- اگر اطلاعات کافی نیست، صریح بگوید
- فرض‌ها را شماره‌گذاری کند
- docs را فراموش نکند
- خروجی نهایی را با فرمت استاندارد repo بدهد

### AI Agent حق ندارد
- بدون تحلیل مستقیم کدنویسی کند
- hook یا endpoint را حدس بزند
- public contract را خودسرانه عوض کند
- docs لازم را نادیده بگیرد
- dependency خارجی غیرضروری اضافه کند

---

## 9) update اجباری مستندات

### وقتی `core` تغییر می‌کند
باید بررسی شود:
- `docs/architecture/contracts.md`
- `docs/architecture/compatibility.md`
- ADR مرتبط
- handoff مربوط به core
- prompt مربوط به core
- QA checklist
- changelog/release docs

### وقتی `gateway` جدید یا تغییر gateway داریم
باید بررسی شود:
- handoff gateway
- prompt gateway
- compatibility docs اگر impact داشت
- QA checklist
- changelog/release docs

### وقتی `integration` جدید یا تغییر integration داریم
باید بررسی شود:
- handoff integration
- prompt integration
- compatibility docs
- QA checklist
- changelog/release docs

---

## 10) Definition of Done برای merge

هیچ PR نباید merge شود مگر اینکه:

### کد
- [ ] scope روشن باشد
- [ ] معماری نقض نشده باشد
- [ ] dependency غیرضروری اضافه نشده باشد
- [ ] متن‌های UI قابل ترجمه باشند

### امنیت
- [ ] capability checks رعایت شده باشند
- [ ] nonce/permission checks رعایت شده باشند
- [ ] sanitize/escape رعایت شده باشد
- [ ] secretها redact شده باشند

### سازگاری
- [ ] graceful degradation وجود داشته باشد
- [ ] dependency guards وجود داشته باشد
- [ ] backward compatibility بررسی شده باشد
- [ ] اگر Woo integration است، HPOS-safe باشد

### تست
- [ ] unit test
- [ ] integration test
- [ ] negative path test
- [ ] compatibility checks در صورت نیاز

### مستندات
- [ ] docs لازم update شده باشند
- [ ] handoff update شده باشد
- [ ] prompt update شده باشد اگر لازم بود
- [ ] assumptions باقی‌مانده ثبت شده باشند

---

## 11) چک‌لیست review

Reviewer باید این موارد را بررسی کند:

### معماری
- آیا تغییر در لایه‌ی درست انجام شده؟
- آیا مرز core/gateway/integration حفظ شده؟
- آیا contract عمومی بدون مستندات تغییر کرده؟

### امنیت
- آیا permission/capability checks وجود دارد؟
- آیا secretها امن هستند؟
- آیا logها redacted هستند؟

### سازگاری
- آیا dependencyها درست مدیریت شده‌اند؟
- آیا WooCommerce integration از CRUD APIs استفاده می‌کند؟
- آیا HPOS/blocks impact بررسی شده؟

### کیفیت
- آیا کد بیش از حد پیچیده نشده؟
- آیا تست‌ها معنی‌دارند؟
- آیا failure pathها پوشش دارند؟

### docs
- آیا docs لازم update شده‌اند؟
- آیا handoff و prompt مرتبط هنوز معتبرند؟

---

## 12) قواعد refactor

Refactor مجاز است فقط اگر:
- behavior عمومی را بدون justification تغییر ندهد
- contract را نشکند
- تست‌ها را خراب نکند
- readability/maintainability را بهتر کند

اگر refactor باعث تغییر observable behavior می‌شود، دیگر فقط refactor نیست و باید مثل feature/fix کامل مستند شود.

---

## 13) قواعد release

برای release:
- changelog باید کامل باشد
- QA checklist باید تکمیل شده باشد
- support runbook باید معتبر باشد
- assumptions و known limitations باید ثبت شده باشند
- version bump باید با نوع تغییر سازگار باشد

---

## 14) اگر اطلاعات کافی نیست

به‌جای حدس‌زدن، contributor باید این ساختار را تحویل دهد:

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

## 15) قاعده‌ی نهایی

اگر بین این گزینه‌ها مجبور به انتخاب شدی:
- سرعت یا کیفیت
- حدس یا مستندات
- پیاده‌سازی سریع یا حفظ معماری

همیشه این‌ها را انتخاب کن:
- کیفیت
- مستندات
- حفظ معماری

---

## 15) QA commandهای اجباری قبل از PR

قبل از ارسال PR این commandها را در ریشه monorepo اجرا کن:

```bash
composer install
composer lint
composer format-check
composer stan
composer test
```

اگر نیاز به auto-fix بود:

```bash
composer format
```

### بسته‌بندی محلی برای validation release

```bash
composer package
```

خروجی ZIP فقط برای validation/release artifact است و نباید داخل git commit شود.
