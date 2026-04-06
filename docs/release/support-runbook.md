# راهنمای پشتیبانی و عیب‌یابی

## هدف
این سند برای تیم پشتیبانی، QA و AI Agentها است تا در مواجهه با خطاها، مسیر استاندارد عیب‌یابی را طی کنند.

---

## 1) اطلاعاتی که همیشه باید جمع‌آوری شوند

قبل از هر تحلیل، این موارد جمع‌آوری شوند:

- نسخه‌ی WordPress
- نسخه‌ی PHP
- نسخه‌ی WooCommerce
- نسخه‌ی `core`
- نسخه‌ی gateway
- نسخه‌ی integration
- active gateway
- وضعیت HPOS
- وضعیت classic/blocks checkout
- correlation id یا dispatch id
- زمان تقریبی رخداد
- نتیجه‌ی latest diagnostics

---

## 2) پرسش‌های اولیه

### 2.1) آیا dependencyها فعال‌اند؟
- آیا `core` فعال است؟
- آیا gateway مربوطه فعال است؟
- آیا integration مربوطه فعال است؟
- آیا WooCommerce فعال است؟

### 2.2) آیا gateway پیکربندی شده است؟
- API key وارد شده؟
- mode درست است؟
- test connection موفق است؟

### 2.3) آیا mapping فعال است؟
- event موردنظر فعال شده؟
- recipient درست تنظیم شده؟
- message mode معتبر است؟
- template/body معتبر است؟

---

## 3) مسیر تحلیل خطا

### سناریو A: هیچ پیامکی ارسال نمی‌شود
1. dependencyها را بررسی کن
2. active gateway را بررسی کن
3. test connection را اجرا کن
4. mapping event را بررسی کن
5. phone source را بررسی کن
6. logs را با correlation id یا order id بررسی کن
7. duplicate prevention را بررسی کن

### سناریو B: فقط بعضی eventها پیامک نمی‌فرستند
1. mapping همان event را بررسی کن
2. placeholderهای همان event را بررسی کن
3. phone availability را بررسی کن
4. consent/opt-in requirement را بررسی کن
5. status transition واقعی را بررسی کن

### سناریو C: خطای provider وجود دارد
1. HTTP status را بررسی کن
2. provider status/error code را بررسی کن
3. auth/config را بررسی کن
4. timeout/rate-limit را بررسی کن
5. اگر مستندات provider مبهم است، note آن را ثبت کن

### سناریو D: پیامک تکراری ارسال شده
1. idempotency key generation را بررسی کن
2. order meta markers را بررسی کن
3. retry chain را بررسی کن
4. manual resend history را بررسی کن
5. oscillating status changes را بررسی کن

---

## 4) چه چیزهایی نباید در تیکت پشتیبانی درخواست شوند
- secret کامل
- API key کامل
- OTP واقعی کاربر
- raw personal data غیرضروری
- full unredacted logs

---

## 5) چه چیزهایی باید در support bundle باشد
- plugin versions
- diagnostics summary
- redacted logs
- dispatch summary
- event mapping summary
- environment flags
- known assumptions/warnings

---

## 6) تصمیم‌گیری برای escalation

### به تیم توسعه ارجاع شود اگر:
- error reproducible است
- mapping درست است ولی dispatch اشتباه انجام می‌شود
- compatibility issue با HPOS/Blocks مشاهده شد
- provider response parsing ظاهراً اشتباه است
- migration/schema issue وجود دارد

### به تیم محصول ارجاع شود اگر:
- رفتار مورد انتظار مبهم است
- event policy هنوز مشخص نیست
- wording یا UX فعلی باعث اشتباه مکرر کاربران شده
- consent/privacy behavior نیاز به تصمیم محصولی دارد

---

## 7) قالب پاسخ پشتیبانی داخلی

```text
- Summary:
- Affected plugin(s):
- Environment:
- Reproduction steps:
- Diagnostics summary:
- Relevant dispatch/log refs:
- Current hypothesis:
- Blockers / missing info:
- Recommended next action:
```
