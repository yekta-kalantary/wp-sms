# سیاست سازگاری

## هدف
این سند قواعد سازگاری اکوسیستم Yekta SMS را برای WordPress، WooCommerce، gatewayها و نسخه‌های داخلی repo تعیین می‌کند.

---

## 1) سازگاری WordPress

### الزامات
- Plugin API رسمی WordPress رعایت شود.
- Settings API برای تنظیمات استفاده شود.
- REST routes فقط با WordPress REST API تعریف شوند.
- routeهای REST باید `permission_callback` داشته باشند.
- security best practices رعایت شوند.

### ممنوعیت‌ها
- endpoint registration غیررسمی
- bypass کردن capability checks
- اتکا به behaviorهای undocumented

---

## 2) سازگاری WooCommerce

### برای integrationهای ووکامرس
- HPOS-safe باشند.
- برای order access فقط WooCommerce CRUD APIs استفاده شود.
- direct SQL برای منطق سفارش ممنوع است.
- استفاده از object/post behaviorهای قدیمی فقط با justification و compatibility layer مجاز است.

### اگر checkout دخیل است
- modern checkout / blocks باید بررسی شود.
- اگر plugin checkout را extend می‌کند، compatibility declaration و testing لازم است.

---

## 3) سازگاری داخلی بین pluginها

### Core
- مرجع contracts و shared services است.
- تغییر public contract در core ممکن است gatewayها و integrationها را تحت‌تأثیر قرار دهد.

### Gateway
- فقط به public contractهای core متکی باشد.
- نباید به implementation internal core وابسته شود.

### Integration
- فقط به public contractهای core متکی باشد.
- نباید به internals gateway تکیه کند.

---

## 4) سازگاری نسخه‌ای

### سیاست نسخه‌دهی پیشنهادی
- semantic versioning
- `major` برای breaking changes
- `minor` برای featureهای backward-compatible
- `patch` برای fixها

### قاعده
اگر contract عمومی تغییر کرد:
- impact analysis لازم است
- changelog لازم است
- migration note لازم است

---

## 5) سازگاری با dependencyهای غایب

هر plugin باید graceful degradation داشته باشد.

### نمونه‌ها
- نبودن `core`
- نبودن WooCommerce
- نبودن gateway فعال
- نبودن قابلیت لازم در provider

### رفتار مورد انتظار
- no fatal
- admin notice روشن
- disable کردن فقط همان feature وابسته
- امکان عیب‌یابی

---

## 6) سازگاری با multisite

### حداقل انتظار
- settings و state هر سایت مستقل باشند
- activation/deactivation رفتار ایمن داشته باشد
- plugin در network env دچار fatal نشود

### در فاز فعلی
- network-wide control center در scope نیست
- shared config بین سایت‌ها در scope نیست

---

## 7) سازگاری با localization

### الزامات
- تمام متن‌های UI قابل ترجمه باشند
- text domain روشن باشد
- پیام‌های error/success human-readable باشند
- technical details از user-facing message جدا باشند

---

## 8) سازگاری داده‌ای

### Settings schema
- version-aware باشد
- sanitize callbacks روشن داشته باشد

### Custom tables
- migration version مشخص
- تغییر schema فقط با migration کنترل‌شده
- destructive migration در نسخه‌های اولیه ممنوع مگر با justification صریح

### Order meta / plugin meta
- keyها prefix‌دار باشند
- فقط داده‌های لازم ذخیره شوند
- PII اضافی ذخیره نشود

---

## 9) ماتریس بررسی سازگاری برای PR یا Agent Output

قبل از نهایی‌شدن هر تغییر، این موارد بررسی شوند:

- [ ] با WordPress APIs رسمی سازگار است
- [ ] capability/nonce/permission رعایت شده
- [ ] backward compatibility بررسی شده
- [ ] dependencyهای لازم validate شده
- [ ] graceful degradation وجود دارد
- [ ] اگر Woo integration است، HPOS بررسی شده
- [ ] اگر checkout دخیل است، blocks بررسی شده
- [ ] docs لازم به‌روزرسانی شده
- [ ] tests لازم اضافه شده

---

## 10) قاعده‌ی نهایی

اگر تغییری از نظر فنی کار می‌کند ولی compatibility را تضعیف می‌کند،  
آن تغییر هنوز **قابل قبول نیست**.
