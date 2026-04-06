# اکوسیستم پلاگینی Yekta SMS

این مخزن، **monorepo** رسمی اکوسیستم `Yekta SMS` است.  
هدف این پروژه، ساخت یک زیرساخت ماژولار برای ارسال پیامک در وردپرس است؛ به‌گونه‌ای که:

- `core` فقط هسته و قراردادهای عمومی را نگه دارد.
- `gateway`ها فقط آداپتور ارائه‌دهنده‌ی پیامک باشند.
- `integration`ها فقط رویدادهای افزونه‌های ثالث را به سرویس پیامکی متصل کنند.

این README علاوه بر راهنمای پروژه، به‌عنوان **دستور اجرایی برای AI Agentها** نیز عمل می‌کند؛ مخصوصاً برای استفاده در **Codex GPT** و **Cursor**.

---

## 1) ساختار مخزن

```text
.
├─ core/
├─ geateway/
│  └─ yekta-geateway-smsir/
├─ integration/
│  └─ yekta-integration-woocomrce/
├─ docs/
│  ├─ architecture/
│  ├─ handoff/
│  ├─ decisions/
│  ├─ prompts/
│  └─ release/
├─ scripts/
├─ tests/
└─ README.md
```

### توضیح مسیرها

- `core/`
  - هسته‌ی اصلی اکوسیستم
  - قراردادها، registry، dispatch، logging، diagnostics، admin foundation

- `geateway/`
  - همه‌ی gateway pluginها
  - هر gateway فقط provider-specific logic دارد

- `integration/`
  - همه‌ی integration pluginها
  - هر integration فقط event binding و orchestration سمت افزونه‌ی ثالث را دارد

- `docs/`
  - مستندات رسمی داخلی پروژه
  - هر AI Agent باید قبل از تغییرات، این پوشه را بررسی و در صورت نیاز به‌روزرسانی کند

- `scripts/`
  - اسکریپت‌های کمکی توسعه، build، QA، release

- `tests/`
  - تست‌های مشترک، fixtureها، و ابزارهای آزمون بین‌پلاگینی

---

## 2) اصل معماری غیرقابل‌مذاکره

### Core-first
- هیچ gateway یا integration نباید منطق مشترک را داخل خودش تکرار کند.
- تمام قراردادهای عمومی باید در `core` تعریف شوند.
- `core` نباید به provider یا integration خاص hard-coupled باشد.

### Decoupling
- gateway نباید منطق WooCommerce یا هیچ integration دیگری داشته باشد.
- integration نباید منطق provider-specific داشته باشد.
- اگر feature جدید نیاز به contract عمومی دارد، اول باید در `core` طراحی شود.

### Extensibility
- هر قابلیت جدید باید با نگاه توسعه‌پذیر طراحی شود.
- افزودن gateway جدید نباید core را بشکند.
- افزودن integration جدید نباید gatewayها را تغییر دهد.

### Maintainability
- کلاس‌ها single responsibility باشند.
- فایل‌ها کوچک و روشن بمانند.
- از overengineering پرهیز شود.
- dependency غیرضروری ممنوع است.

### Security
- ورودی‌ها validate/sanitize شوند.
- خروجی‌ها escape شوند.
- دسترسی‌ها با capability check کنترل شوند.
- عملیات حساس با nonce و permission checks محافظت شوند.
- secretها هرگز نباید در log به‌صورت خام ذخیره شوند.

### WooCommerce Compatibility
- هر integration ووکامرس باید HPOS-safe باشد.
- استفاده از direct SQL یا post-based order access برای منطق سفارش ممنوع است.
- برای order access فقط WooCommerce CRUD APIs مجازند.

---

## 3) قرارداد کاری برای AI Agentها

این بخش برای **Codex GPT**، **Cursor** و هر AI Agent دیگری است که روی این repo کار می‌کند.

### مأموریت عامل
هر زمان از شما خواسته شد که:
- یک gateway جدید اضافه کنید
- یک integration جدید اضافه کنید
- core را توسعه دهید
- feature جدیدی به یکی از پلاگین‌ها اضافه کنید

باید دقیقاً طبق این قرارداد عمل کنید.

---

## 4) Agent Execution Policy

### 4.1) قبل از هر تغییر

AI Agent **باید** این کارها را انجام دهد:

1. ساختار repo را بررسی کند.
2. `README.md` را کامل بخواند.
3. پوشه‌ی `docs/` را بررسی کند.
4. pluginهای موجود را بررسی کند تا الگوی فعلی پروژه را بفهمد.
5. مشخص کند درخواست جدید از کدام نوع است:
   - تغییر در `core`
   - افزودن `gateway`
   - افزودن `integration`
   - توسعه‌ی plugin موجود
6. اگر برای اجرا به مستندات رسمی نیاز است، آن را صریحاً اعلام کند.
7. اگر اطلاعات کافی برای اجرای درست وجود ندارد، حدس نزند؛ بلکه کمبود را شفاف گزارش کند.

### 4.2) Agent حق ندارد

- بدون تحلیل، مستقیم شروع به تولید سورس کند.
- provider-specific logic را وارد `core` کند.
- integration-specific logic را وارد gateway کند.
- بدون update کردن مستندات معماری، قرارداد جدید اضافه کند.
- dependency جدید غیرضروری اضافه کند.
- بدون تست یا بدون criteria روشن کار را تمام‌شده اعلام کند.
- hook، endpoint، setting key یا flow را حدس بزند.

### 4.3) Agent موظف است

- ابتدا scope را تحلیل کند.
- اثر تغییر را روی معماری بررسی کند.
- اگر نیاز به تغییر contract عمومی وجود دارد، اول آن را در `docs/` ثبت کند.
- تغییرات را مرحله‌بندی کند.
- فایل‌های لازم را ایجاد یا اصلاح کند.
- تست‌ها را اضافه یا به‌روزرسانی کند.
- changelog/release note را در صورت نیاز به‌روزرسانی کند.
- در پایان، گزارش دقیق کار انجام‌شده و موارد باقی‌مانده را ارائه دهد.

---

## 5) روال استاندارد وقتی می‌گویم «یک پلاگین جدید اضافه کن»

وقتی مالک پروژه می‌گوید:

- «یک gateway جدید اضافه کن»
- «یک integration جدید اضافه کن»
- «فلان قابلیت را به core اضافه کن»

AI Agent باید این workflow را اجرا کند:

### مرحله 1: طبقه‌بندی درخواست
مشخص کن درخواست از کدام نوع است:

- **Gateway Plugin**
- **Integration Plugin**
- **Core Feature**
- **Cross-cutting Change**
- **Refactor / Hardening / QA**

### مرحله 2: بررسی اثر معماری
مشخص کن:
- آیا contract جدید لازم است؟
- آیا settings جدید لازم است؟
- آیا data model جدید لازم است؟
- آیا migration لازم است؟
- آیا docs جدید لازم است؟
- آیا test matrix باید گسترش پیدا کند؟

### مرحله 3: بررسی مستندات لازم
اگر مستندات کافی داخل repo وجود ندارد، باید صریحاً اعلام کنی که چه چیزی لازم است. مثال:

- مستندات رسمی API gateway
- مستندات hookهای افزونه‌ی ثالث
- مستندات HPOS / Checkout Blocks
- ساختار داده‌ی eventها
- محدودیت‌های provider

### مرحله 4: تولید Plan
Agent باید قبل از کدنویسی، این خروجی را تولید کند:

- Summary
- Scope
- Assumptions
- Risks
- Files to create/update
- Tests to add/update
- Docs to create/update
- Open questions

### مرحله 5: اجرا
بعد از plan:
- فایل‌ها را ایجاد/ویرایش کن
- contractها را رعایت کن
- dependencyها را کنترل کن
- تست‌ها را بنویس
- docs را به‌روز کن

### مرحله 6: گزارش نهایی
در پایان باید دقیقاً گزارش کنی:

- چه فایل‌هایی ایجاد شد
- چه فایل‌هایی تغییر کرد
- چه تست‌هایی اضافه شد
- چه assumptionsی باقی مانده
- چه چیزهایی هنوز نیاز به validation با مستندات رسمی دارند

---

## 6) قوانین خاص افزودن Gateway جدید

اگر درخواست از نوع **Gateway Plugin** بود، AI Agent باید این چک‌لیست را اجرا کند:

### 6.1) خروجی‌های لازم
- پوشه‌ی plugin جدید در `geateway/`
- فایل اصلی plugin
- bootstrap
- registration/factory
- gateway implementation
- config/settings
- health check
- request builder
- response normalizer
- error mapper
- tests
- docs/handoff
- docs/prompt

### 6.2) سوالات اجباری که باید از مستندات پاسخ داده شوند
- endpointها چیست؟
- auth mechanism چیست؟
- template/verify flow چیست؟
- bulk/text flow چیست؟
- status query چگونه است؟
- rate limit / timeout / retry policy چگونه باید تفسیر شود؟
- sandbox/test mode وجود دارد یا نه؟
- responseهای موفق و خطا چه شکلی هستند؟

### 6.3) اگر مستندات رسمی کافی نبود
عامل باید:
- این کمبود را صریحاً گزارش کند
- بخش‌های مبهم را فهرست کند
- فرض‌ها را شماره‌گذاری کند
- نواحی پرریسک را جدا کند
- implementation را فقط تا جایی جلو ببرد که فرض‌ها کنترل‌شده باشند

### 6.4) ممنوعیت‌ها
- قراردادن منطق integration در gateway
- قراردادن business rules در gateway
- bypass کردن core dispatcher
- hard-code کردن behavior خارج از مستندات

---

## 7) قوانین خاص افزودن Integration جدید

اگر درخواست از نوع **Integration Plugin** بود، AI Agent باید این چک‌لیست را اجرا کند:

### 7.1) خروجی‌های لازم
- پوشه‌ی plugin جدید در `integration/`
- فایل اصلی plugin
- dependency checker
- compatibility declarations
- trigger registrar
- mapping/settings UI
- placeholder layer
- recipient resolver
- idempotency layer
- manual tools اگر لازم باشد
- tests
- docs/handoff
- docs/prompt

### 7.2) سوالات اجباری که باید از مستندات پاسخ داده شوند
- hookهای رسمی و پایدار کدام‌اند؟
- data access رسمی چگونه است؟
- آیا flow مدرن/blocks دارد؟
- آیا object model خاصی دارد؟
- آیا custom tables یا compatibility requirements دارد؟
- کدام eventها برای ارسال پیامک مناسب‌ترند؟
- duplicate trigger risk کجاست؟

### 7.3) اگر integration مربوط به WooCommerce بود
عامل باید این موارد را بررسی کند:
- HPOS compatibility
- modern checkout / blocks compatibility
- order CRUD access
- admin screen differences
- status transition behavior
- manual resend behavior
- consent/privacy implications

### 7.4) ممنوعیت‌ها
- provider-specific code
- direct SQL برای order logic
- reliance روی hookهای شکننده بدون justification
- ذخیره‌ی بی‌جهت PII

---

## 8) قوانین خاص توسعه‌ی Core

اگر درخواست از نوع **Core Feature** بود، AI Agent باید قبل از اجرا تشخیص دهد:

- آیا این feature عمومی است یا فقط موردنیاز یک plugin خاص؟
- آیا باید contract جدید اضافه شود؟
- آیا تغییر public API داریم؟
- آیا backward compatibility تحت تأثیر قرار می‌گیرد؟
- آیا migration لازم است؟
- آیا gatewayها یا integrationهای فعلی باید سازگار شوند؟

### اگر تغییر عمومی بود
Agent باید:
1. ابتدا contract و اثر معماری را در `docs/architecture/` ثبت کند.
2. اگر تصمیم معماری جدید است، یک ADR در `docs/decisions/` اضافه کند.
3. بعد از آن سراغ پیاده‌سازی برود.

---

## 9) ساختار مستندات اجباری

هر AI Agent هنگام ایجاد feature یا plugin جدید باید در صورت نیاز این فایل‌ها را بسازد یا به‌روزرسانی کند:

```text
docs/
├─ architecture/
│  ├─ ecosystem-overview.md
│  ├─ contracts.md
│  ├─ data-model.md
│  └─ compatibility.md
├─ handoff/
│  ├─ core/
│  ├─ gateways/
│  └─ integrations/
├─ decisions/
│  ├─ ADR-001-core-first.md
│  └─ ADR-xxx-*.md
├─ prompts/
│  ├─ core/
│  ├─ gateways/
│  └─ integrations/
└─ release/
   ├─ changelog.md
   ├─ qa-checklist.md
   └─ release-checklist.md
```

### حداقل مستندات برای هر plugin جدید
- `docs/handoff/...`
- `docs/prompts/...`
- در صورت نیاز:
  - `docs/architecture/contracts.md`
  - `docs/architecture/compatibility.md`
  - ADR جدید

---

## 10) Definition of Done سراسری

هیچ AI Agent حق ندارد کاری را «تمام‌شده» اعلام کند مگر اینکه همه‌ی موارد زیر برقرار باشند:

### 10.1) کدنویسی
- فایل‌های لازم ایجاد شده باشند
- ساختار پروژه حفظ شده باشد
- قراردادهای معماری نقض نشده باشند
- متن‌های UI قابل ترجمه باشند

### 10.2) امنیت
- capability checks رعایت شده باشند
- nonce / permission checks برای عملیات حساس اعمال شده باشند
- sanitize/escape رعایت شده باشد
- secretها redact شده باشند

### 10.3) سازگاری
- dependencyها کنترل شده باشند
- graceful degradation وجود داشته باشد
- compatibility notes ثبت شده باشند
- backward compatibility بررسی شده باشد

### 10.4) کیفیت
- تست‌های لازم اضافه یا به‌روز شده باشند
- edge caseها پوشش داده شده باشند
- failure pathها بررسی شده باشند

### 10.5) مستندات
- docs لازم به‌روزرسانی شده باشند
- handoff pack اضافه شده باشد
- prompt pack اضافه شده باشد
- اگر assumption وجود دارد، صریح ثبت شده باشد

### 10.6) گزارش خروجی
عامل باید در پایان این بخش‌ها را تحویل دهد:
- Summary
- Files changed
- Tests added/updated
- Docs added/updated
- Risks
- Assumptions
- Remaining validation items

---

## 11) قرارداد نام‌گذاری

### 11.1) اسلاگ‌ها
فعلاً این repo از این اسلاگ‌ها استفاده می‌کند:

- `yekta-sms-core`
- `yekta-geateway-smsir`
- `yekta-integration-woocomrce`

### 11.2) هشدار
در اسلاگ‌های `geateway` و `woocomrce` typo وجود دارد.  
تا وقتی مالک پروژه صریحاً تصمیم به migration نگرفته، AI Agent **حق تغییر خودسرانه‌ی slugها را ندارد**.

### 11.3) اگر قرار شد typoها اصلاح شوند
عامل باید:
- impact analysis بنویسد
- migration plan بنویسد
- backward compatibility implications را ثبت کند
- readme/docs/release notes را به‌روزرسانی کند

---

## 12) قرارداد فایل و کد

### 12.1) قواعد عمومی
- یک کلاس اصلی در هر فایل
- مسئولیت واحد
- namespace روشن
- helperهای پراکنده‌ی بدون مرز ممنوع
- dependency injection ساده و روشن
- facadeهای بی‌دلیل ممنوع
- magic behavior بی‌دلیل ممنوع

### 12.2) متن‌های UI
همه‌ی متن‌های رابط کاربری باید:
- قابل ترجمه باشند
- hard-coded و بدون i18n wrapper نباشند

### 12.3) تنظیمات
- settings با ساختار روشن
- keyها version-aware
- sanitize callback روشن
- secretها masked

### 12.4) لاگ
- structured logs
- correlation id
- redaction پیش‌فرض
- no raw secret
- no unnecessary PII

---

## 13) چک‌لیست سریع برای Agent

### اگر گفتیم «یک gateway جدید اضافه کن»
باید:
- docs provider را بررسی کنی
- contract impact را بررسی کنی
- plugin جدید بسازی
- settings + health check + adapter + tests + docs را اضافه کنی
- نتیجه را گزارش کنی

### اگر گفتیم «یک integration جدید اضافه کن»
باید:
- docs افزونه‌ی ثالث را بررسی کنی
- triggerها و flowها را شناسایی کنی
- compatibility risks را ثبت کنی
- plugin جدید + mapping + tests + docs را اضافه کنی

### اگر گفتیم «این feature را به core اضافه کن»
باید:
- بررسی کنی که feature عمومی هست یا نه
- اگر عمومی است اول docs/ADR را به‌روزرسانی کنی
- بعد پیاده‌سازی، تست و گزارش نهایی را انجام دهی

---

## 14) فرمت اجباری پاسخ AI Agent

هر AI Agent باید پاسخ نهایی خود را با این ساختار بدهد:

```text
1) خلاصه
2) تحلیل تغییر
3) فرض‌ها
4) ریسک‌ها
5) فایل‌های ایجادشده/تغییریافته
6) تست‌های اضافه/تغییریافته
7) مستندات اضافه/تغییریافته
8) مواردی که هنوز نیاز به validation دارند
9) گام بعدی پیشنهادی
```

---

## 15) الگوی درخواست برای Codex GPT و Cursor

### 15.1) الگوی عمومی
این prompt را به عامل بده و فقط بخش درخواست را عوض کن:

```text
این مخزن monorepo اکوسیستم Yekta SMS است. ابتدا README.md و سپس پوشه docs/ را کامل بررسی کن. بعد ساختار pluginهای موجود را بخوان و الگوی معماری پروژه را استخراج کن. قبل از هر تغییری، request را طبقه‌بندی کن و اثر آن را روی core/gateway/integration مشخص کن. چیزی را حدس نزن. اگر مستندات رسمی لازم است و داخل repo موجود نیست، دقیق بگو چه مستندی نیاز است. اگر اطلاعات کافی بود، ابتدا plan بده، سپس تغییرات را اعمال کن، تست‌ها و docs لازم را اضافه کن، و در پایان گزارش نهایی را با فرمت اجباری README تحویل بده.

درخواست:
[اینجا دقیقاً بنویس چه چیزی می‌خواهی]
```

### 15.2) مثال برای gateway جدید
```text
درخواست:
یک gateway جدید برای [نام provider] اضافه کن. اول بررسی کن آیا contract جدیدی در core لازم است یا نه. اگر لازم بود docs و ADR را به‌روزرسانی کن. سپس plugin را در مسیر geateway/ بساز. health check، settings، adapter، response normalizer، error mapper، tests و handoff docs را کامل کن. اگر مستندات رسمی API provider کم یا مبهم بود، صریح گزارش کن.
```

### 15.3) مثال برای integration جدید
```text
درخواست:
یک integration جدید برای [نام افزونه] اضافه کن. ابتدا hookها، data flow، compatibility requirements و risks را از روی ساختار repo و مستندات رسمی بررسی کن. اگر نیاز به contract جدید در core بود، اول docs را به‌روزرسانی کن. سپس plugin را در مسیر integration/ بساز. mapping settings، placeholderها، recipient resolution، idempotency، tests و handoff docs را اضافه کن.
```

### 15.4) مثال برای feature در core
```text
درخواست:
قابلیت [نام قابلیت] را به core اضافه کن. ابتدا مشخص کن این قابلیت عمومی است یا فقط برای یک plugin خاص لازم است. اگر عمومی است، contracts و docs/architecture را به‌روزرسانی کن. سپس implementation، migration احتمالی، tests و release notes را تکمیل کن.
```

---

## 16) وقتی اطلاعات کافی نیست

اگر اطلاعات کافی برای اجرای امن و درست وجود نداشت، AI Agent باید به‌جای حدس‌زدن، این خروجی را بدهد:

```text
- اطلاعات کافی نیست.
- برای ادامه این موارد باید مشخص شوند:
  1) ...
  2) ...
  3) ...
- بدون این موارد، این بخش‌ها پرریسک هستند:
  1) ...
  2) ...
- بخش‌هایی که فعلاً می‌توانم با اطمینان انجام دهم:
  1) ...
  2) ...
```

---

## 17) پیشنهاد ساختار پوشه‌ی docs برای شروع

```text
docs/
├─ architecture/
│  ├─ ecosystem-overview.md
│  ├─ plugin-boundaries.md
│  ├─ contracts.md
│  ├─ data-model.md
│  ├─ security-model.md
│  └─ compatibility.md
├─ handoff/
│  ├─ core/
│  │  └─ yekta-sms-core.md
│  ├─ gateways/
│  │  └─ yekta-geateway-smsir.md
│  └─ integrations/
│     └─ yekta-integration-woocomrce.md
├─ decisions/
│  ├─ ADR-001-core-first.md
│  ├─ ADR-002-gateway-boundary.md
│  └─ ADR-003-integration-boundary.md
├─ prompts/
│  ├─ core/
│  ├─ gateways/
│  └─ integrations/
└─ release/
   ├─ changelog.md
   ├─ qa-checklist.md
   ├─ release-checklist.md
   └─ support-runbook.md
```

---

## 18) قاعده‌ی نهایی

اگر عامل بین «سریع‌نویسی» و «درست‌نویسی» مجبور به انتخاب شد،  
باید **درست‌نویسی** را انتخاب کند.

اگر بین «حدس‌زدن» و «درخواست مستندات لازم» مجبور به انتخاب شد،  
باید **درخواست مستندات لازم** را انتخاب کند.

اگر بین «پیاده‌سازی feature خاص داخل یک plugin» و «افزودن contract درست در core» مجبور به انتخاب شد،  
باید **مرزهای معماری** را حفظ کند.

---

## 19) وضعیت فعلی پروژه

### در scope فعلی
- `core`
- `yekta-geateway-smsir`
- `yekta-integration-woocomrce`

### هدف فاز فعلی
- تثبیت معماری
- آماده‌سازی handoff برای AI coding agentها
- ساخت foundation درست برای توسعه‌ی pluginهای بعدی

---

## 20) یادداشت برای مالک پروژه

اگر می‌خواهی از این README حداکثر استفاده را بگیری، در کنار آن این فایل‌ها را هم به repo اضافه کن:

1. `docs/architecture/plugin-boundaries.md`
2. `docs/architecture/contracts.md`
3. `docs/architecture/compatibility.md`
4. `docs/release/qa-checklist.md`
5. `docs/release/support-runbook.md`

بدون این فایل‌ها هم AI Agent می‌تواند کار را شروع کند،  
اما با این فایل‌ها دقت، سرعت و ثبات تصمیم‌ها بیشتر می‌شود.
---

## 7) QA و CI/CD

### پیش‌نیاز توسعه
- PHP `8.1+`
- Composer

### نصب dependencyهای توسعه
```bash
composer install
```

### commandهای استاندارد QA
```bash
composer lint         # PHPCS (WPCS)
composer format       # PHPCBF auto-fix
composer format-check # PHPCBF dry-run
composer stan         # PHPStan
composer test         # PHPUnit
composer qa           # lint + stan + test
```

### بسته‌بندی پلاگین‌ها
```bash
composer package
```

خروجی‌ها در `dist/` ساخته می‌شوند:
- `dist/yekta-sms-core.zip`
- `dist/yekta-geateway-smsir.zip`
- `dist/yekta-integration-woocomrce.zip`

> ZIPها artifact هستند و نباید به‌صورت خودکار commit شوند.

### GitHub Actions
- `CI` روی `pull_request` و `push` به `main` اجرا می‌شود و QA را enforce می‌کند.
- `Package Plugins` فقط روی `push` به `main` اجرا می‌شود، بعد از QA بسته‌ها را می‌سازد و artifact منتشر می‌کند.
