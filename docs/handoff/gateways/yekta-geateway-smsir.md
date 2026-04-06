# Handoff Pack — `yekta-geateway-smsir`

## 1) هدف
این پلاگین باید SMS.ir را به‌عنوان یک gateway استاندارد به `yekta-sms-core` متصل کند.  
این پلاگین فقط یک **provider adapter** است و نباید هیچ منطق WooCommerce یا integration-specific در خود داشته باشد.

---

## 2) dependencyها
- `yekta-sms-core`
- WordPress HTTP API
- بدون dependency به WooCommerce

---

## 3) ساختار پوشه‌ی پیشنهادی

```text
geateway/yekta-geateway-smsir/
├─ yekta-geateway-smsir.php
├─ readme.txt
├─ languages/
├─ src/
│  ├─ Bootstrap/
│  ├─ Registration/
│  ├─ Gateway/
│  ├─ Config/
│  ├─ Http/
│  ├─ Mapping/
│  ├─ Admin/
│  └─ Support/
└─ tests/
   ├─ Unit/
   ├─ Integration/
   └─ Fixtures/
```

---

## 4) فایل‌های موردنیاز
- `yekta-geateway-smsir.php`
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

---

## 5) مسئولیت‌ها

### Registration
- معرفی gateway به core از طریق registry/filter

### Config
- خواندن settings
- validate پیکربندی
- policy مربوط به mode و timeout

### HTTP
- ساخت request
- headerها
- ارسال request به API

### Mapping
- نرمال‌سازی response
- map کردن errorها به taxonomy عمومی core

### Health
- connection test
- line/credit checks
- report کردن pass/warn/fail

---

## 6) non-responsibilities
- event mapping
- placeholder rendering
- WooCommerce triggers
- duplicate prevention
- dispatch shared logic
- generic admin logging UI

---

## 7) قرارداد gateway

### metadata لازم
- `slug = smsir`
- `label = SMS.ir`
- `version`
- `supported_capabilities`
- `health_checker`

### capabilityهای لازم
- `single_text`
- `bulk_text`
- `templated`
- `delivery_status_query`
- `check_credit`
- `list_lines`
- `sandbox_mode`

---

## 8) settings schema

### option: `yekta_sms_gateway_smsir_settings`
- `enabled`
- `mode`
- `api_key`
- `default_line_number`
- `request_timeout`
- `connectivity_check_strategy`
- `mask_message_content`
- `header_accept_mode`

### قواعد validate
- `enabled` بولی
- `mode` فقط `production` یا `sandbox`
- `api_key` non-empty when enabled
- `default_line_number` برای متن آزاد معتبر باشد
- `request_timeout` در بازه‌ی امن باشد

---

## 9) boundary با core
این plugin باید فقط از public contractهای core استفاده کند:
- gateway registration contract
- gateway runtime contract
- logger contract
- health check contract
- config/settings foundation if exposed through core admin

این plugin نباید:
- به internal classهای undocumented core وابسته شود
- behavior داخلی dispatcher را فرض بگیرد

---

## 10) provider API responsibilities

### مسیرهای موردنیاز
- send bulk
- send verify
- delivery status query
- credit check
- line list

### auth
- header-based API key

### noteهای مهم
- response parsing باید tolerant باشد
- doc inconsistencyها باید annotate شوند
- headerها و methodها hard-coded خام و بدون abstraction نباشند

---

## 11) flow در سطح شبه‌کد

### registration
```text
on core registry filter:
  return definition for smsir gateway
```

### send
```text
send(request):
  load settings
  validate config
  determine endpoint by request.type
  build payload
  build headers
  call HTTP client
  parse body
  map provider status
  normalize result
  return DispatchResult-compatible data
```

### health check
```text
run_health_check():
  validate minimal config
  call lightweight endpoint
  optionally call line list
  return pass/warn/fail with remediation
```

---

## 12) request modes

### plain/bulk text
استفاده برای:
- متن ساده
- چند گیرنده
- test send text

### templated / verify
استفاده برای:
- template/provider pattern
- parameterized transactional messages

### status query
استفاده برای:
- بررسی وضعیت message id

---

## 13) response normalization rules

### expected normalized fields
- `success`
- `retryable`
- `provider_slug`
- `provider_message_id`
- `provider_batch_id`
- `normalized_status`
- `normalized_message`
- `error_code`
- `error_details`
- `cost`
- `latency_ms`

### parse failure policy
- اگر response parse نشد:
  - terminal یا retryable بسته به context transport
  - log detail redacted
  - خطای نرمال‌شده برگردد

---

## 14) error mapping
provider errorها باید به taxonomy عمومی core map شوند، مثل:
- auth failure
- account/config issue
- invalid recipient
- insufficient credit
- template not found
- blacklist
- rate limited
- provider internal error
- transport timeout

---

## 15) retry policy classification

### retryable
- timeout
- temporary connectivity issue
- rate limit
- provider internal error

### non-retryable
- invalid API key
- invalid mobile
- template not found
- blacklist
- insufficient credit
- disabled account

---

## 16) logging redaction rules
هرگز raw log نشود:
- API key
- full auth headers
- full OTP/template parameters
- full message body در حالت غیر debug

اجازه‌ی log:
- endpoint path
- HTTP status
- provider status code
- latency
- message id
- batch id
- masked recipient

---

## 17) graceful degradation
اگر `yekta-sms-core` فعال نبود:
- plugin fatal ندهد
- gateway register نشود
- admin notice مناسب نمایش داده شود
- behavior dormant داشته باشد

---

## 18) admin UX موردنیاز
- section تنظیمات gateway
- فیلد API key masked
- mode selector
- timeout
- default line
- test connection panel
- provider notes / warnings

---

## 19) test cases

### unit
- config validation
- endpoint selection
- payload building
- response normalization
- error mapping

### integration
- successful bulk send
- successful verify send
- successful status query
- health check success

### negative
- invalid api key
- timeout
- malformed response
- rate limit
- insufficient credit
- template missing
- blacklist

---

## 20) edge cases
- sandbox docs با production ناهمگن هستند
- verify request ambiguity
- response shape تغییر جزئی دارد
- line number missing
- provider success flag هست ولی data ناقص است
- health check pass است ولی send fail می‌شود

---

## 21) release checklist
- core dependency guard working
- gateway registered correctly
- health check usable
- success/error normalization tested
- secrets redacted
- sandbox behavior documented

---

## 22) definition of done
- gateway در core register می‌شود
- send text کار می‌کند
- send verify کار می‌کند
- status query کار می‌کند
- health check کار می‌کند
- no raw secret logging

---

## 23) verdict
**Ready with Assumptions**
