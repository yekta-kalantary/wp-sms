# ecosystem-overview.md

## هدف
این سند نمای کلان اکوسیستم `Yekta SMS` را توضیح می‌دهد تا توسعه‌دهنده‌ها و AI Agentها قبل از ورود به جزئیات implementation، مدل ذهنی مشترکی از ساختار سیستم، جریان داده، مرز مسئولیت‌ها و وابستگی‌ها داشته باشند.

---

## 1) تعریف اکوسیستم

اکوسیستم `Yekta SMS` یک معماری پلاگینی برای WordPress است که با سه لایه‌ی اصلی کار می‌کند:

1. **Core**
2. **Gateway Plugins**
3. **Integration Plugins**

هدف این معماری این است که:
- منطق مشترک فقط یک‌بار در `core` پیاده‌سازی شود
- providerهای مختلف بدون تغییر در core قابل اضافه‌کردن باشند
- integrationهای مختلف بدون تغییر در gatewayها قابل اضافه‌کردن باشند

---

## 2) اجزای اصلی

### 2.1) Core
مسئول:
- contracts عمومی
- registry
- active gateway resolution
- dispatch orchestration
- settings foundation
- logging
- diagnostics
- admin foundation
- REST admin infrastructure
- migration/versioning
- scheduler abstraction

### 2.2) Gateway Plugins
مسئول:
- provider-specific auth/config
- request building
- response normalization
- provider error mapping
- health check
- capability declaration

### 2.3) Integration Plugins
مسئول:
- event binding به افزونه‌ی ثالث
- mapping و orchestration
- recipient resolution
- placeholder rendering
- idempotency
- manual resend
- consent/privacy behavior در صورت نیاز domain

---

## 3) محدوده‌ی فاز فعلی

### در scope
- `yekta-sms-core`
- `yekta-geateway-smsir`
- `yekta-integration-woocomrce`

### خارج از scope فعلی
- inbound SMS
- campaign management
- failover multi-gateway
- integrations دیگر
- analytics پیشرفته
- queue dashboard کامل

---

## 4) ساختار repo

```text
.
├─ core/
├─ geateway/
│  └─ yekta-geateway-smsir/
├─ integration/
│  └─ yekta-integration-woocomrce/
└─ docs/
```

---

## 5) جهت وابستگی

```text
integration -> core
gateway -> core
core -/-> gateway
core -/-> integration
gateway -/-> integration
integration -/-> gateway internals
```

### نتیجه
- core فقط public contract ارائه می‌دهد
- gateway و integration فقط به آن contractها تکیه می‌کنند
- coupling افقی بین pluginهای جانبی ممنوع است

---

## 6) نمای معماری متنی

```text
WooCommerce Events
   ↓
yekta-integration-woocomrce
   - trigger registration
   - event mapping
   - placeholder rendering
   - recipient resolution
   - idempotency
   ↓
yekta-sms-core
   - contracts
   - registry
   - active gateway resolution
   - dispatch orchestration
   - logging
   - diagnostics
   ↓
yekta-geateway-smsir
   - provider adapter
   - auth
   - request builder
   - response normalization
   ↓
SMS.ir API
```

---

## 7) جریان‌های اصلی سیستم

### 7.1) Bootstrap Flow
1. WordPress pluginها load می‌شوند
2. `core` boot می‌شود
3. `core` registryها را آماده می‌کند
4. gatewayها و integrationها خود را register می‌کنند
5. admin / REST / diagnostics آماده می‌شود

### 7.2) Send Flow
1. یک integration یک event domain را تشخیص می‌دهد
2. mapping را resolve می‌کند
3. recipient را پیدا می‌کند
4. duplicate check انجام می‌دهد
5. `MessageRequest` عمومی می‌سازد
6. request را به core می‌دهد
7. core gateway فعال را resolve می‌کند
8. gateway adapter provider call را انجام می‌دهد
9. نتیجه normalize می‌شود
10. dispatch و log ذخیره می‌شوند

### 7.3) Error Flow
- validation error → terminal
- dependency missing → terminal + admin notice
- gateway config error → terminal
- transport timeout → retryable
- provider rate limit → retryable
- duplicate detected → skipped/audited
- provider rejection → non-retryable

### 7.4) Diagnostics Flow
1. مدیر از admin diagnostics را اجرا می‌کند
2. core checkها را اجرا می‌کند
3. gateway health checkها optional اجرا می‌شوند
4. نتیجه‌ی pass/warn/fail نمایش داده می‌شود

---

## 8) اجزای مفهومی

### 8.1) MessageRequest
نماینده‌ی request عمومی برای ارسال پیامک است.  
این شیء باید provider-agnostic باشد.

### 8.2) DispatchResult
نماینده‌ی نتیجه‌ی نرمال‌شده‌ی dispatch است.  
این نتیجه باید برای logging، retry و troubleshooting قابل‌استفاده باشد.

### 8.3) Gateway Definition
شیئی که metadata و factory یک gateway را به core معرفی می‌کند.

### 8.4) Health Check Result
خروجی نرمال‌شده‌ی هر check برای diagnostics و support.

---

## 9) مرزهای طراحی

### core نباید بداند
- endpointهای provider
- payloadهای provider
- hookهای WooCommerce
- business ruleهای integration

### gateway نباید بداند
- order statuses
- event mapping
- placeholderها
- Woo order flow

### integration نباید بداند
- auth provider
- endpointهای provider
- transport behavior جز در حد نتیجه‌ی نرمال‌شده
- internals gateway

---

## 10) اصول معماری

### 10.1) Contract-driven
هر extension باید از public contractهای `core` تبعیت کند.

### 10.2) Core-first
اگر قابلیت عمومی است، ابتدا باید در core تعریف شود.

### 10.3) Minimal coupling
pluginها فقط از interfaceهای لازم استفاده کنند.

### 10.4) Graceful degradation
نبود dependency نباید fatal بدهد.

### 10.5) Observability-first
هر dispatch باید traceable باشد.

### 10.6) Security by default
secretها redact شوند، permission checks برقرار باشند، و ورودی/خروجی امن باشند.

---

## 11) نقش admin UI

Admin UI در این اکوسیستم فقط برای «تنظیم» نیست.  
باید این کارها را هم ممکن کند:

- setup
- test connection
- test send
- diagnostics
- log inspection
- troubleshooting
- manual resend

---

## 12) نقش docs در این پروژه

در این monorepo، docs فقط توضیح جانبی نیستند؛ بخشی از قرارداد توسعه‌اند.

### نتیجه
اگر یکی از موارد زیر تغییر کند:
- contract عمومی
- compatibility
- handoff
- prompt pack
- release expectations

باید docs نیز به‌روزرسانی شوند.

---

## 13) رشد آینده‌ی اکوسیستم

این معماری برای رشد آینده طراحی شده است:

### Gatewayهای آینده
- `yekta-geateway-melipayamak`
- `yekta-geateway-kavehnegar`

### Integrationهای آینده
- `yekta-integration-gravityform`
- `yekta-integration-edd`
- `yekta-integration-learndash`

### قابلیت‌های آینده
- routing rules
- failover
- queue UI
- analytics
- inbound/webhook layer

---

## 14) قاعده‌ی نهایی

اگر در طراحی feature جدید بین این دو شک وجود داشت:
- این منطق باید shared باشد؟
- یا فقط مخصوص یک plugin است؟

اول این پرسش را پاسخ بده.  
اگر shared است، جای آن **core** است.  
اگر provider-specific است، جای آن **gateway** است.  
اگر event/domain-specific است، جای آن **integration** است.
