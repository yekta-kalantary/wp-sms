# security-model.md

## هدف
این سند مدل امنیتی اکوسیستم `Yekta SMS` را تعریف می‌کند تا توسعه‌دهنده‌ها و AI Agentها برای:
- capabilityها
- permission checks
- nonce rules
- secret handling
- logging redaction
- PII policy
- threat model
- abuse prevention

چارچوب واحد و قابل اجرا داشته باشند.

> این سند مرجع طراحی و policy است، نه implementation detail نهایی.  
> هر تغییر مهم امنیتی باید در changelog و در صورت لزوم در ADR یا release docs نیز بازتاب پیدا کند.

---

## 1) اصول امنیتی پایه

### 1.1) Secure by Default
رفتار پیش‌فرض باید امن باشد، نه راحت‌تر اما ناامن‌تر.

### 1.2) Least Privilege
هر capability فقط باید حداقل دسترسی لازم را بدهد.

### 1.3) No Trust Without Validation
هیچ ورودی‌ای بدون validate/sanitize وارد سیستم نشود.

### 1.4) Escape on Output
تمام خروجی‌های UI و admin باید در زمان خروجی escape شوند.

### 1.5) Secrets Must Never Leak
secretها نباید در UI، log، support bundle یا error dump به‌صورت raw ظاهر شوند.

### 1.6) Observability Without Exposure
سیستم باید debug و troubleshoot شود، اما بدون افشای داده‌ی حساس.

---

## 2) دارایی‌های حساس

### 2.1) Secretها
- API key provider
- هر credential یا token آینده
- هر config محرمانه‌ی integrationهای خارجی

### 2.2) داده‌های شخصی
- شماره تلفن
- داده‌های قابل اتصال به سفارش/کاربر
- contextهایی که به شخص خاص قابل نسبت‌دادن هستند

### 2.3) داده‌های عملیاتی حساس
- dispatch history
- failure traces
- provider response summaries
- idempotency markers
- internal diagnostics context

---

## 3) threat model

### 3.1) تهدیدهای اصلی

#### A) افشای secret
ریسک‌ها:
- log شدن raw API key
- نمایش raw secret در admin
- export یا support bundle ناامن
- exception messageهای ناامن

#### B) دسترسی غیرمجاز ادمین/کاربر
ریسک‌ها:
- user بدون capability مناسب
- REST endpoint بدون permission callback مناسب
- manual resend بدون authorization

#### C) CSRF
ریسک‌ها:
- test send
- test connection
- save settings
- manual resend
- admin tools

#### D) PII leakage
ریسک‌ها:
- full phone در logs
- raw message body
- OTP/template params
- order-linked data در contexts

#### E) ارسال ناخواسته یا تکراری
ریسک‌ها:
- triggerهای تکراری
- resend بدون audit
- retry نامعتبر
- bypass شدن duplicate prevention

#### F) misuse / abuse
ریسک‌ها:
- test send spam
- resend abuse
- excessive diagnostics calls
- brute forcing configuration or provider endpoints

#### G) compatibility-driven security regressions
ریسک‌ها:
- feature جدید بدون capability map
- integration جدید با hookهای نادرست
- Woo flow جدید که bypass permission path ایجاد کند

---

## 4) capability model

### 4.1) capabilityهای پیشنهادی
- `manage_yekta_sms`
- `manage_yekta_sms_settings`
- `manage_yekta_sms_woocommerce`
- `view_yekta_sms_logs`
- `manage_yekta_sms_diagnostics`
- `send_yekta_sms_test`

### 4.2) نگاشت کاربردی capabilityها

| Capability | دسترسی |
|---|---|
| `manage_yekta_sms` | دسترسی پایه به بخش Yekta SMS |
| `manage_yekta_sms_settings` | تنظیمات core و gateway |
| `manage_yekta_sms_woocommerce` | تنظیمات و mapping ووکامرس |
| `view_yekta_sms_logs` | مشاهده‌ی logs و dispatch traces |
| `manage_yekta_sms_diagnostics` | اجرای diagnostics و health checks |
| `send_yekta_sms_test` | test send / manual resend / ابزارهای حساس ارسال |

### 4.3) قاعده
داشتن دسترسی به admin menu به‌تنهایی به معنی اجازه‌ی انجام همه‌ی actions نیست.  
هر action حساس باید capability خاص خود را جداگانه بررسی کند.

---

## 5) authorization model

### 5.1) admin screens
هر صفحه‌ی admin باید:
- capability guard داشته باشد
- در صورت نبود دسترسی، load نشود
- data حساس را پیش از check capability آماده نکند

### 5.2) REST routes
هر route باید:
- `permission_callback` مستقل داشته باشد
- به route-level capability مناسب متکی باشد
- request params را validate کند
- response را فقط با داده‌ی مجاز برگرداند

### 5.3) manual actions
مثل:
- test send
- test connection
- resend
- purge logs
- rerun diagnostics

باید:
- capability check داشته باشند
- nonce check داشته باشند
- audit trail ایجاد کنند اگر state-sensitive هستند

---

## 6) nonce / CSRF policy

### 6.1) چه چیزهایی nonce لازم دارند
- save settings
- test send
- test connection
- manual resend
- log purge
- diagnostics rerun
- هر POST-like admin action

### 6.2) policy
- nonce باید action-specific باشد
- verify failure باید graceful و loggable باشد
- nonce به‌تنهایی جای capability check را نمی‌گیرد

---

## 7) input validation & sanitization policy

### 7.1) اصل
Validation و sanitization جدا از هم دیده شوند.

### 7.2) نوع داده‌ها

#### slug / key
- فقط safelist / regex مجاز

#### enum
- فقط از مجموعه‌ی مجاز

#### phone
- normalize
- validate
- invalid → reject

#### message text / template body
- sanitize مناسب textarea
- placeholder validation جداگانه

#### template refs
- validate as opaque provider ref
- type-safe handling

#### numeric config
- bounds check
- absint / int logic

#### arrays / mapping objects
- schema-based validation
- unknown keys reject یا ignore کنترل‌شده

### 7.3) ممنوعیت‌ها
- sanitize خام به‌جای validation
- قبول keyهای ناشناخته در settings حساس
- trust کردن request payloadهای admin چون «از admin آمده‌اند»

---

## 8) output escaping policy

### 8.1) admin UI
- textها escaped
- attributeها escaped
- URLs escaped
- textareas escaped
- HTML محدود فقط در صورت whitelist روشن

### 8.2) logs viewer
- JSON pretty view باید escaped باشد
- raw HTML از log context ممنوع

### 8.3) notices
- user-facing messageها باید safe render شوند
- technical detailها در panel جدا و escaped نمایش داده شوند

---

## 9) secret handling policy

### 9.1) storage
- secretهای provider در optionهای اختصاصی plugin مربوطه
- secretها در shared core option merge نشوند مگر ضرورت روشن
- optionهای حساس باید با رویکرد محافظه‌کارانه نسبت به autoload مدیریت شوند

### 9.2) UI
- secretها masked نشان داده شوند
- اگر از constant/env آمده‌اند:
  - UI read-only
  - indicator روشن برای source

### 9.3) logs
هرگز raw log نشود:
- API keys
- auth headers
- full provider credentials
- token-like strings

### 9.4) exports / support bundle
- secretها باید حذف یا redact شوند
- bundle نباید برای بازیابی credential قابل استفاده باشد

---

## 10) PII handling policy

### 10.1) allowed
- masked phone
- object reference
- event key
- mapping summary
- limited troubleshooting context

### 10.2) restricted
- full phone number
- full personal names همراه با data حساس
- body contentهای حساس
- OTP values
- full customer data snapshot

### 10.3) principle
فقط داده‌ای را ذخیره کن که برای:
- dispatch
- audit
- troubleshooting
- compatibility

واقعاً لازم است.

---

## 11) logging redaction policy

### 11.1) redact-by-default
همه‌ی logger paths باید redaction-aware باشند.

### 11.2) فهرست redaction
- secret values
- phone numbers → masked
- template params → masked یا omitted
- body preview → محدود
- auth headers → removed

### 11.3) debug mode
حتی در debug mode:
- raw secret ممنوع
- raw OTP ممنوع
- raw auth headers ممنوع

### 11.4) support bundle
- فقط redacted logs
- فقط context لازم
- هیچ payload خام حساس

---

## 12) plugin-specific security rules

### 12.1) Core
- registry mutation فقط در bootstrap معتبر
- dispatch tools فقط برای کاربران مجاز
- diagnostics فقط برای کاربران مجاز
- logs فقط برای viewers مجاز
- migrations باید permission-safe و activation-safe باشند

### 12.2) Gateway
- provider config validation
- no raw secret display
- no integration state assumptions
- connection testها rate-aware و limited

### 12.3) Integration
- event-driven dispatch باید duplicate-safe باشد
- manual resend باید audit شود
- order-linked UI باید least-privilege باشد
- PII از order context بیش از حد لازم استخراج نشود

---

## 13) WooCommerce-specific security considerations

### 13.1) order access
- استفاده از CRUD APIs
- عدم reliance روی legacy post behavior به‌عنوان مسیر اصلی

### 13.2) admin order actions
- resend یا tools روی order screen باید capability و nonce داشته باشند

### 13.3) checkout extensions
اگر integration checkout را extend می‌کند:
- input validation روشن
- no unsafe persistence
- consent state واضح
- blocks/classic flows به‌صورت جدا بررسی شوند

---

## 14) abuse prevention

### 14.1) test send
- rate limiting منطقی بر اساس user/session
- audit trail
- capability restricted

### 14.2) diagnostics
- excessive rerun کنترل شود
- expensive checks cache کوتاه‌عمر داشته باشند

### 14.3) resend
- forced resend فقط برای user مجاز
- log actor + timestamp + target context
- duplicate bypass فقط با audit

---

## 15) auditability

### باید ثبت شود
- چه کسی action حساس را اجرا کرد
- روی چه object/contextی
- چه زمانی
- نتیجه چه بود
- correlation id چه بود

### actionهای مهم
- test send
- test connection
- manual resend
- settings save
- migration execution
- diagnostics rerun
- log purge

---

## 16) dependency missing / degraded mode security

اگر dependency غایب بود:
- plugin نباید fallback ناامن انجام دهد
- featureهای وابسته باید غیرفعال شوند
- admin notice روشن نمایش داده شود
- no silent partial unsafe behavior

---

## 17) error handling policy

### user-facing
- human-readable
- بدون افشای secret
- بدون dump فنی خام

### technical
- machine-readable code
- correlation id
- redacted context
- provider summary کنترل‌شده

---

## 18) secure defaults checklist

- dispatch by default فقط وقتی config معتبر است
- test tools only for privileged users
- logs masked by default
- diagnostics accessible only to privileged users
- resend disabled unless explicit feature enable/config
- unknown settings keys rejected
- invalid phone rejected
- unsupported capability blocked safely

---

## 19) security review checklist

قبل از merge هر feature بررسی شود:
- [ ] capability map رعایت شده
- [ ] nonce checks روی actions حساس وجود دارد
- [ ] route permissions درست‌اند
- [ ] sanitize/validate انجام شده
- [ ] outputs escaped هستند
- [ ] secretها redact می‌شوند
- [ ] PII حداقلی است
- [ ] manual actions audit می‌شوند
- [ ] degraded mode امن است
- [ ] logs افشاگر نیستند

---

## 20) incident response notes

اگر incident امنیتی یا data leak suspected شد:
1. log exposure scope را تعیین کن
2. secret exposure را بررسی کن
3. affected plugin/version را مشخص کن
4. temporary mitigation را اعمال کن
5. changelog/security note را آماده کن
6. اگر لازم بود credential rotation recommendation بده
7. root cause را در docs/release و support runbook منعکس کن

---

## 21) قاعده‌ی نهایی

اگر بین این دو مجبور به انتخاب شدی:
- observability یا privacy
- convenience یا authorization
- debug detail یا secret safety

همیشه این‌ها را انتخاب کن:
- privacy-aware observability
- authorization
- secret safety
