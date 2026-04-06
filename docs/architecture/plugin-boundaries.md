# مرزهای پلاگین‌ها

## هدف
این سند مرز مسئولیت بین `core`، `gateway`ها و `integration`ها را تثبیت می‌کند تا هیچ AI Agent یا توسعه‌دهنده‌ای منطق را در لایه‌ی اشتباه پیاده‌سازی نکند.

---

## 1) اصل اصلی

### 1.1) Core-first
- تمام قراردادهای عمومی در `core` تعریف می‌شوند.
- هیچ `gateway` یا `integration` نباید قرارداد عمومی جدید را به‌صورت محلی برای خودش اختراع کند.
- اگر قابلیتی ماهیت عمومی دارد، اول باید در `core` طراحی شود.

### 1.2) Decoupling
- `core` نباید به provider خاصی hard-coupled باشد.
- `core` نباید به WooCommerce یا افزونه‌ی ثالث خاصی hard-coupled باشد.
- `gateway` نباید منطق WooCommerce یا هیچ integration دیگری را بداند.
- `integration` نباید منطق provider-specific را بداند.

### 1.3) Extension-safe design
- افزودن gateway جدید نباید باعث تغییر اجباری در integrationها شود.
- افزودن integration جدید نباید باعث تغییر اجباری در gatewayها شود.
- افزودن feature عمومی نباید بدون ثبت در docs و contracts انجام شود.

---

## 2) مرز مسئولیت `core`

### مجاز
- contracts و interfaces عمومی
- registry برای gatewayها و integrationها
- active gateway resolution
- dispatch orchestration
- logging
- diagnostics
- settings foundation
- capability model
- health checks
- REST admin infrastructure
- migration/versioning
- scheduler abstraction

### غیرمجاز
- endpointهای provider-specific
- mappingهای WooCommerce
- placeholderهای domain-specific مخصوص یک integration
- business rules مخصوص gateway خاص
- منطق checkout/order مربوط به integrationها

---

## 3) مرز مسئولیت `gateway`

### مجاز
- auth/config provider
- request building
- response normalization
- provider error mapping
- health check
- sandbox/test mode handling
- capability declaration بر اساس provider

### غیرمجاز
- WooCommerce hooks
- event orchestration
- recipient resolution از domainهای integration
- core-level logging UI
- business ruleهای محصول

---

## 4) مرز مسئولیت `integration`

### مجاز
- dependency checks برای افزونه‌ی ثالث
- compatibility declarations
- trigger registration
- event mapping
- placeholder resolution
- recipient resolution
- idempotency guard
- manual resend orchestration
- consent/opt-in handling اگر domain لازم داشته باشد

### غیرمجاز
- تماس مستقیم با provider
- auth/config provider
- transport retry classification عمومی
- تعریف قرارداد عمومی خارج از core
- تغییر رفتار gatewayها

---

## 5) Dependency Direction

```text
integration -> core
gateway -> core
core -/-> integration
core -/-> gateway
gateway -/-> integration
integration -/-> gateway internals
```

---

## 6) تصمیم‌گیری برای feature جدید

اگر feature جدید مطرح شد، قبل از اجرا این پرسش‌ها پاسخ داده شوند:

1. آیا این قابلیت برای بیش از یک plugin قابل استفاده است؟
2. آیا باید contract عمومی جدید اضافه شود؟
3. آیا این feature domain-specific است؟
4. آیا منطق آن باید نزدیک provider باشد یا نزدیک integration؟
5. آیا این تغییر backward compatibility را تحت‌تأثیر قرار می‌دهد؟

### نتیجه‌گیری
- اگر پاسخ «عمومی» است → ابتدا `core`
- اگر پاسخ «provider-specific» است → `gateway`
- اگر پاسخ «domain event specific` است → `integration`

---

## 7) نشانه‌های طراحی اشتباه

اگر هرکدام از موارد زیر رخ داد، طراحی احتمالاً مرزها را نقض کرده است:

- gateway برای خودش event mapping تعریف کند
- integration مستقیم endpoint provider را صدا بزند
- core نام endpoint یا payload provider را بداند
- integration برای خودش logger shared بسازد
- gateway برای order status منطق داشته باشد
- feature عمومی فقط در یک integration پیاده‌سازی شود

---

## 8) قاعده‌ی نهایی

اگر بین سرعت و حفظ مرزهای معماری تعارض وجود داشت،  
باید **مرزهای معماری** حفظ شوند.
