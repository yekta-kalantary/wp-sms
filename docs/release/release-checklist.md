# release-checklist.md

این فایل چک‌لیست نهایی release برای monorepo اکوسیستم `Yekta SMS` است.  
هیچ release نباید بدون عبور از این چک‌لیست منتشر شود.

---

## 1) دسته‌بندی release

قبل از هر چیز مشخص کن release از چه نوعی است:

- `major`
- `minor`
- `patch`
- `release-candidate`
- `docs-foundation`
- `security-hotfix`

### پرسش‌های اولیه
- آیا breaking change داریم؟
- آیا migration داریم؟
- آیا dependency minimum version تغییر کرده؟
- آیا compatibility impact داریم؟
- آیا release چند plugin را هم‌زمان تحت‌تأثیر قرار می‌دهد؟

---

## 2) scope release

باید دقیقاً مشخص شود این release شامل کدام بخش‌ها است:

- [ ] `core`
- [ ] `gateway`
- [ ] `integration`
- [ ] `docs`
- [ ] `qa`
- [ ] `security`

اگر چند بخش درگیرند، impact آن‌ها باید در changelog روشن باشد.

---

## 3) pre-release architecture checks

- [ ] مرز `core / gateway / integration` نقض نشده است
- [ ] اگر contract عمومی تغییر کرده، docs update شده است
- [ ] اگر تصمیم معماری جدید بوده، ADR ثبت شده است
- [ ] compatibility impact بررسی شده است
- [ ] backward compatibility بررسی شده است
- [ ] dependency direction همچنان درست است

---

## 4) code readiness

- [ ] scope تغییر شفاف است
- [ ] فایل‌های جدید در مسیر درست repo قرار گرفته‌اند
- [ ] naming convention رعایت شده است
- [ ] متن‌های UI قابل ترجمه هستند
- [ ] dependency خارجی غیرضروری اضافه نشده است
- [ ] code review داخلی انجام شده است
- [ ] refactorهای همراه با feature باعث مبهم‌شدن release نشده‌اند

---

## 5) security readiness

- [ ] capability checks بررسی شده‌اند
- [ ] nonce / permission checks برای عملیات حساس برقرارند
- [ ] sanitize / validate روی ورودی‌ها رعایت شده است
- [ ] escape روی خروجی‌ها رعایت شده است
- [ ] secretها raw log نمی‌شوند
- [ ] log redaction بررسی شده است
- [ ] PII غیرضروری ذخیره نمی‌شود
- [ ] endpointها یا routeهای جدید permission-safe هستند

---

## 6) compatibility readiness

### عمومی
- [ ] WordPress compatibility بررسی شده است
- [ ] dependencyهای لازم درست مدیریت می‌شوند
- [ ] pluginها در نبود dependency fatal نمی‌دهند
- [ ] multisite basic behavior بررسی شده است

### اگر integration مربوط به WooCommerce است
- [ ] HPOS-safe بودن بررسی شده است
- [ ] direct SQL برای order logic وجود ندارد
- [ ] WooCommerce CRUD APIs استفاده شده‌اند
- [ ] classic flow بررسی شده است
- [ ] modern/blocks flow بررسی شده است
- [ ] order admin compatibility بررسی شده است

### اگر gateway تغییر کرده است
- [ ] auth/config paths بررسی شده‌اند
- [ ] health check معتبر است
- [ ] error mapping بررسی شده است
- [ ] timeout/rate-limit behavior بررسی شده است

---

## 7) testing readiness

### unit
- [ ] تست‌های واحد مرتبط اضافه یا update شده‌اند

### integration
- [ ] تست‌های integration مرتبط اضافه یا update شده‌اند

### negative paths
- [ ] failure pathهای اصلی پوشش دارند

### regression
- [ ] regression روی featureهای قبلی بررسی شده است

### compatibility
- [ ] compatibility matrix لازم بررسی شده است

### manual QA
- [ ] QA checklist اجرا شده است
- [ ] سناریوهای مهم دستی بررسی شده‌اند

---

## 8) docs readiness

- [ ] `README.md` در صورت نیاز update شده است
- [ ] `AGENTS.md` در صورت نیاز update شده است
- [ ] `CONTRIBUTING.md` در صورت نیاز update شده است
- [ ] docs معماری update شده‌اند
- [ ] handoff مربوط update شده است
- [ ] prompt مربوط update شده است
- [ ] `docs/release/qa-checklist.md` بازبینی شده است
- [ ] `docs/release/support-runbook.md` بازبینی شده است
- [ ] `docs/release/changelog.md` update شده است

---

## 9) plugin-specific release checks

### core
- [ ] migrationها بررسی شده‌اند
- [ ] registryها سالم‌اند
- [ ] dispatch pipeline سالم است
- [ ] diagnostics قابل استفاده است
- [ ] public contract changes مستند شده‌اند

### gateway
- [ ] provider adapter درست register می‌شود
- [ ] settings validate می‌شوند
- [ ] send success path تست شده است
- [ ] send failure path تست شده است
- [ ] health check تست شده است
- [ ] graceful degradation در نبود core بررسی شده است

### integration
- [ ] dependency guardها کار می‌کنند
- [ ] triggerها درست register می‌شوند
- [ ] mappingها معتبرند
- [ ] recipient resolution درست است
- [ ] duplicate prevention کار می‌کند
- [ ] manual resend امن است
- [ ] troubleshooting UI کافی است

---

## 10) release notes readiness

- [ ] نوع release مشخص شده است
- [ ] breaking changes شفاف نوشته شده‌اند
- [ ] migration notes در صورت نیاز آماده شده‌اند
- [ ] known limitations ثبت شده‌اند
- [ ] support impact مشخص شده است
- [ ] next steps در صورت نیاز ثبت شده‌اند

---

## 11) packaging readiness

- [ ] workflow `Package Plugins` برای شاخه `main` سبز است
- [ ] artifact با نام `yekta-plugin-packages` تولید شده است
- [ ] نسخه‌ها هماهنگ شده‌اند
- [ ] headerها و metadata درست هستند
- [ ] فایل‌های لازم در بسته موجودند
- [ ] فایل‌های غیرضروری در بسته نیستند
- [ ] ZIPها داخل git commit نشده‌اند
- [ ] docs مرتبط کنار release قابل‌دسترسی هستند

---

## 12) final approval gate

هیچ release نباید نهایی شود مگر اینکه:

- [ ] changelog کامل شده باشد
- [ ] QA checklist تکمیل شده باشد
- [ ] release checklist تکمیل شده باشد
- [ ] assumptions باقی‌مانده شفاف باشند
- [ ] blocker باز نداشته باشیم
- [ ] rollback strategy مشخص باشد

---

## 13) rollback check

قبل از release باید این‌ها مشخص باشند:
- [ ] اگر release مشکل داشت، rollback چگونه انجام می‌شود
- [ ] migrationها destructive نیستند یا rollback note دارند
- [ ] نسخه‌ی پایدار قبلی مشخص است
- [ ] dependency compatibility در rollback هم در نظر گرفته شده است

---

## 14) قالب گزارش نهایی release

```text
- Release Type:
- Scope:
- Affected Plugins:
- Breaking Changes:
- Migration Required:
- QA Status:
- Docs Status:
- Known Risks:
- Rollback Plan:
- Approval Status:
```

---

## 15) قاعده‌ی نهایی

اگر release از نظر فنی build می‌شود ولی:
- docs ناقص است
- QA ناقص است
- compatibility نامطمئن است
- security بازبینی نشده

آن release هنوز **آماده‌ی انتشار نیست**.
