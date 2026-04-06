# Prompt Pack — `yekta-geateway-smsir`

این فایل یک پرامپت self-contained برای Codex GPT، Cursor یا هر AI Coding Agent دیگر است تا فقط بر اساس قراردادهای همین repo، سورس `yekta-geateway-smsir` را تولید یا تکمیل کند.

---

## Prompt

```text
تو مسئول تولید یا تکمیل سورس پلاگین وردپرسی `yekta-geateway-smsir` در monorepo اکوسیستم Yekta SMS هستی.

قبل از هر تغییری، این فایل‌ها را کامل بخوان:
1) `README.md`
2) `docs/architecture/plugin-boundaries.md`
3) `docs/architecture/contracts.md`
4) `docs/architecture/compatibility.md`
5) `docs/decisions/ADR-001-core-first.md`
6) `docs/decisions/ADR-002-gateway-boundary.md`
7) `docs/handoff/gateways/yekta-geateway-smsir.md`

قواعد قطعی:
1) از خودت چیزی حدس نزن.
2) فقط طبق Handoff Pack و docs همین repo کدنویسی کن.
3) اگر برای جزئیات implementation به مستندات رسمی SMS.ir یا WordPress نیاز است، آن‌ها را بررسی کن و چیزی را حدس نزن.
4) کد را production-grade، ساده و adapter-oriented بنویس.
5) هیچ منطق WooCommerce یا integration-specific داخل این پلاگین نیاور.
6) همه‌ی متن‌های UI باید قابل ترجمه باشند.
7) dependency اضافی غیرضروری ممنوع است.
8) secretها نباید raw log شوند.
9) اگر contract عمومی جدید لازم بود، قبل از implementation آن را گزارش کن.
10) اگر مستندات provider مبهم یا متناقض بود، آن را صریح ثبت کن.

هدف پلاگین:
اتصال SMS.ir به `yekta-sms-core` از طریق یک gateway adapter شامل:
- gateway registration
- settings/config validation
- health check
- send text / bulk
- send templated/verify
- delivery status query
- response normalization
- provider error mapping
- sandbox/test mode handling

ساختار پوشه‌ی هدف:
- `geateway/yekta-geateway-smsir/yekta-geateway-smsir.php`
- `geateway/yekta-geateway-smsir/src/Bootstrap/*`
- `geateway/yekta-geateway-smsir/src/Registration/*`
- `geateway/yekta-geateway-smsir/src/Gateway/*`
- `geateway/yekta-geateway-smsir/src/Config/*`
- `geateway/yekta-geateway-smsir/src/Http/*`
- `geateway/yekta-geateway-smsir/src/Mapping/*`
- `geateway/yekta-geateway-smsir/src/Admin/*`
- `geateway/yekta-geateway-smsir/src/Support/*`
- `geateway/yekta-geateway-smsir/tests/*`

فایل‌های کلیدی:
- `src/Bootstrap/Plugin.php`
- `src/Registration/GatewayFactory.php`
- `src/Gateway/SmsIrGateway.php`
- `src/Gateway/SmsIrHealthChecker.php`
- `src/Config/SmsIrSettings.php`
- `src/Http/SmsIrHttpClient.php`
- `src/Http/SmsIrRequestFactory.php`
- `src/Mapping/SmsIrResponseNormalizer.php`
- `src/Mapping/SmsIrErrorMapper.php`
- `src/Admin/SettingsSection.php`
- `src/Support/Requirements.php`

dependencyها:
- `yekta-sms-core`
- WordPress HTTP API
- بدون dependency به WooCommerce

قابلیت‌های اجباری:
- `single_text`
- `bulk_text`
- `templated`
- `delivery_status_query`
- `check_credit`
- `list_lines`
- `sandbox_mode`

settings schema:
option: `yekta_sms_gateway_smsir_settings`

کلیدها:
- `enabled`
- `mode`
- `api_key`
- `default_line_number`
- `request_timeout`
- `connectivity_check_strategy`
- `mask_message_content`
- `header_accept_mode`

قواعد validate:
- `enabled` بولی
- `mode` فقط `production` یا `sandbox`
- `api_key` وقتی plugin enabled است الزامی
- `request_timeout` باید bounded باشد
- `default_line_number` در مسیر text لازم است
- invalid config باید به‌صورت human-readable و machine-readable surface شود

boundary با core:
- فقط از public contractهای core استفاده کن
- به internal classهای undocumented core وابسته نشو
- dispatch orchestration را خودت بازتعریف نکن
- logging shared UI را خودت نساز

behaviorهای لازم:
1) gateway خود را در core register کن
2) request type را به endpoint مناسب map کن
3) payload و headerها را بساز
4) HTTP call را با WordPress HTTP API انجام بده
5) response را parse کن
6) success/failure را normalize کن
7) provider errors را به taxonomy عمومی core map کن
8) health check pass/warn/fail برگردان
9) اگر core غایب بود، graceful degradation داشته باش

شبه‌رفتار:
registration:
- در hook/registry مربوط به core یک gateway definition برگردان

send flow:
1) config را load و validate کن
2) endpoint را از روی request.type تعیین کن
3) payload را بساز
4) headers را بساز
5) HTTP call انجام بده
6) body را parse کن
7) provider status را map کن
8) DispatchResult-compatible data برگردان

health check flow:
1) minimal config را validate کن
2) lightweight endpoint را صدا بزن
3) در صورت نیاز line/credit را هم چک کن
4) pass/warn/fail + remediation برگردان

retry classification:
retryable:
- timeout
- temporary connectivity issue
- rate limit
- provider internal error

non-retryable:
- invalid API key
- invalid mobile
- template not found
- blacklist
- insufficient credit
- disabled account

logging redaction rules:
هرگز raw log نشود:
- API key
- full auth headers
- full OTP/template parameter values
- full message content در حالت عادی

اجازه‌ی log:
- endpoint path
- HTTP status
- provider status code
- latency
- provider message id
- provider batch id
- masked recipient

graceful degradation:
اگر `yekta-sms-core` فعال نبود:
- plugin fatal ندهد
- gateway register نشود
- admin notice مناسب نمایش داده شود
- plugin در حالت dormant بماند

تست‌های اجباری:
- config validation
- endpoint selection
- payload building
- successful bulk send
- successful verify send
- successful status query
- auth failure
- timeout
- malformed response
- rate limit
- insufficient credit
- template missing
- blacklist
- health check success/failure
- core missing path

فایل‌هایی که در صورت تغییر باید به‌روزرسانی شوند:
- `docs/handoff/gateways/yekta-geateway-smsir.md`
- `docs/architecture/contracts.md` اگر contract عمومی تغییر کرد
- `docs/architecture/compatibility.md` اگر compatibility impact داشت
- `docs/release/qa-checklist.md`
- `docs/release/changelog.md` اگر وجود دارد

خروجی نهایی تو باید این ساختار را داشته باشد:
1) Summary
2) Assumptions
3) Risks
4) Files Created/Updated
5) Tests Added/Updated
6) Docs Updated
7) Remaining Validation Items

ممنوعیت‌ها:
- منطق WooCommerce
- event mapping
- direct integration logic
- bypass کردن core dispatcher contracts
- dependency خارجی غیرضروری
- افشای secret در logs
- حدس‌زدن درباره‌ی provider API
```
