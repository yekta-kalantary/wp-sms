# چک‌لیست QA و پذیرش

## هدف
این سند چک‌لیست حداقلی QA برای releaseهای اکوسیستم Yekta SMS را مشخص می‌کند.

---

## 1) چک‌لیست سراسری

### 1.1) نصب و فعال‌سازی
- [ ] plugin بدون fatal نصب می‌شود
- [ ] activation hook بدون خطا اجرا می‌شود
- [ ] deactivation hook بدون خطا اجرا می‌شود
- [ ] اگر dependency غایب است، notice مناسب نمایش داده می‌شود

### 1.2) امنیت
- [ ] capability checks برقرار است
- [ ] nonce برای عملیات حساس وجود دارد
- [ ] REST permission checks برقرار است
- [ ] sanitize/escape رعایت شده
- [ ] secretها در log افشا نمی‌شوند

### 1.3) لاگ و عیب‌یابی
- [ ] logها context کافی دارند
- [ ] correlation id قابل ردیابی است
- [ ] failure pathها log می‌شوند
- [ ] اطلاعات حساس redact شده‌اند

### 1.4) سازگاری
- [ ] dependencyها درست تشخیص داده می‌شوند
- [ ] plugin در نبود dependency fatal نمی‌دهد
- [ ] backward compatibility بررسی شده
- [ ] multisite basic activation بررسی شده

---

## 2) چک‌لیست Core

- [ ] registryها gateway/integration را درست ثبت می‌کنند
- [ ] active gateway resolution درست کار می‌کند
- [ ] dispatch pipeline نتیجه‌ی نرمال‌شده می‌دهد
- [ ] logs table درست کار می‌کند
- [ ] dispatch audit table درست کار می‌کند
- [ ] diagnostics page pass/warn/fail را درست نشان می‌دهد
- [ ] test connection / test send infrastructure امن است

---

## 3) چک‌لیست Gateway

- [ ] gateway در core register می‌شود
- [ ] settings gateway ذخیره و sanitize می‌شوند
- [ ] health check کار می‌کند
- [ ] auth error درست map می‌شود
- [ ] timeout / rate limit درست classify می‌شود
- [ ] success response درست normalize می‌شود
- [ ] malformed response درست handle می‌شود
- [ ] sandbox/test mode رفتار قابل‌فهم دارد

---

## 4) چک‌لیست Integration

- [ ] dependency checker درست کار می‌کند
- [ ] triggerها فقط در شرایط معتبر register می‌شوند
- [ ] mapping settings ذخیره و validate می‌شوند
- [ ] recipient resolution درست عمل می‌کند
- [ ] placeholder rendering درست عمل می‌کند
- [ ] duplicate prevention فعال است
- [ ] manual resend امن است
- [ ] failureها در order/admin/log قابل‌ردیابی هستند

---

## 5) چک‌لیست اختصاصی WooCommerce

- [ ] HPOS compatibility بررسی شده
- [ ] order access فقط از Woo CRUD APIs استفاده می‌کند
- [ ] classic flow بررسی شده
- [ ] modern/blocks flow بررسی شده
- [ ] status change triggers درست کار می‌کنند
- [ ] refunded/failed/cancelled flows بررسی شده
- [ ] guest checkout بررسی شده
- [ ] order admin screen integration در HPOS مشکل ندارد

---

## 6) Negative Test Checklist

- [ ] no active gateway
- [ ] invalid config
- [ ] missing phone
- [ ] invalid template
- [ ] provider 401
- [ ] provider 429
- [ ] provider 500
- [ ] timeout
- [ ] duplicate trigger
- [ ] dependency missing
- [ ] permission denied

---

## 7) Release Gate

هیچ release نباید تأیید شود مگر اینکه:
- [ ] critical defect باز نداشته باشد
- [ ] docs لازم به‌روزرسانی شده باشد
- [ ] changelog تکمیل شده باشد
- [ ] QA checklist کامل شده باشد
- [ ] assumptions باقی‌مانده شفاف ثبت شده باشند
