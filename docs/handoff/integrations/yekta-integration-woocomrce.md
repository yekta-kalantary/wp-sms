# Handoff Pack — `yekta-integration-woocomrce`

## 1) هدف
این پلاگین باید eventهای WooCommerce را به سرویس پیامکی Yekta SMS متصل کند و mapping، recipient resolution، placeholder rendering، duplicate prevention و manual resend را فراهم کند.

---

## 2) dependencyها
- `yekta-sms-core`
- `WooCommerce`

### soft dependency
- حداقل یک gateway فعال و configured در core

---

## 3) ساختار پوشه‌ی پیشنهادی

```text
integration/yekta-integration-woocomrce/
├─ yekta-integration-woocomrce.php
├─ readme.txt
├─ languages/
├─ src/
│  ├─ Bootstrap/
│  ├─ Compatibility/
│  ├─ Registration/
│  ├─ Config/
│  ├─ Triggers/
│  ├─ Dispatch/
│  ├─ Domain/
│  ├─ Consent/
│  ├─ Admin/
│  └─ Support/
└─ tests/
   ├─ Unit/
   ├─ Integration/
   └─ Fixtures/
```

---

## 4) فایل‌های موردنیاز
- `yekta-integration-woocomrce.php`
- `src/Bootstrap/Plugin.php`
- `src/Compatibility/Declarations.php`
- `src/Support/DependencyChecker.php`
- `src/Config/WooSettings.php`
- `src/Config/EventMappingsRepository.php`
- `src/Triggers/HookRegistrar.php`
- `src/Dispatch/WooSmsOrchestrator.php`
- `src/Dispatch/MessageBuilder.php`
- `src/Dispatch/IdempotencyGuard.php`
- `src/Support/RecipientResolver.php`
- `src/Support/PlaceholderRegistry.php`
- `src/Consent/ConsentManager.php`
- `src/Admin/WooSettingsPage.php`
- `src/Admin/OrderMetaBox.php`
- `src/Admin/ManualResendController.php`

---

## 5) مسئولیت‌ها

### Compatibility
- اعلام HPOS compatibility
- اعلام blocks compatibility در صورت درگیر بودن با checkout extension

### Dependency
- تشخیص نبودن core
- تشخیص نبودن WooCommerce
- تشخیص نبودن gateway فعال

### Config
- نگهداری تنظیمات integration
- نگهداری event mappings

### Triggers
- ثبت hookهای WooCommerce
- تبدیل eventهای raw به event keyهای داخلی

### Dispatch
- ساخت MessageRequest عمومی
- placeholder rendering
- recipient resolution
- idempotency
- dispatch از طریق core

### Admin
- UI تنظیمات
- مدیریت mappingها
- preview / resend / troubleshooting

---

## 6) non-responsibilities
- تماس مستقیم با provider
- auth/config provider
- transport logic
- shared logging infrastructure
- public contract definition خارج از core

---

## 7) compatibility requirements

### الزامی
- HPOS-safe
- no direct SQL برای order logic
- no post-based order data access برای flow اصلی
- استفاده از WooCommerce CRUD APIs

### اگر checkout extension فعال شد
- blocks compatibility declaration
- بررسی classic و modern flow
- field handling با الگوی رسمی Woo

---

## 8) event map پیشنهادی

### customer events
- `customer.order.placed`
- `customer.order.paid`
- `customer.order.processing`
- `customer.order.completed`
- `customer.order.on_hold`
- `customer.order.cancelled`
- `customer.order.failed`
- `customer.order.refunded`
- `customer.note.added`

### admin events
- `admin.order.placed`
- `admin.order.paid`

---

## 9) candidate trigger list
- `woocommerce_payment_complete`
- `woocommerce_order_status_changed`
- `woocommerce_order_refunded`
- `woocommerce_new_customer_note`

### optional / advanced
- `woocommerce_checkout_order_processed`
- `woocommerce_store_api_checkout_order_processed`

---

## 10) settings schema

### option: `yekta_sms_wc_settings`
- `enabled`
- `send_mode`
- `respect_opt_in`
- `write_order_notes`
- `retry_policy`
- `manual_resend_enabled`
- `customer_phone_source`
- `admin_phone_list`

### option: `yekta_sms_wc_event_mappings`
هر event یک object config دارد:
- `enabled`
- `recipient_type`
- `phone_source`
- `message_mode`
- `provider_template_ref`
- `body_template`
- `parameter_map`
- `require_opt_in`
- `retry_enabled`
- `add_order_note`

---

## 11) placeholder groups
- `order.*`
- `customer.*`
- `billing.*`
- `shipping.*`
- `store.*`
- `site.*`
- `payment.*`
- `status.*`

### policy
- placeholder ناشناخته در admin warning بدهد
- placeholder حیاتیِ خالی در runtime باعث block شدن send و log warning شود

---

## 12) recipient resolution
باید بتواند این حالت‌ها را پشتیبانی کند:
- شماره مشتری از billing phone
- شماره مشتری از sourceهای future-safe
- شماره ادمین از settings
- شماره custom در mapping

### rules
- normalize phone
- reject invalid phone
- no send if recipient missing
- masked logging

---

## 13) idempotency strategy

### هدف
جلوگیری از ارسال تکراری در eventهای تکرارشونده یا status oscillation

### کلید پیشنهادی
```text
hash(plugin + event + order_id + recipient + mapping_version)
```

### storage
- order meta marker
- ارجاع به dispatch idهای core

### force resend
- فقط از مسیر manual resend
- باید audit trail ثبت شود

---

## 14) flow در سطح شبه‌کد

### event handling
```text
on Woo event:
  map raw hook to internal event key
  load mapping
  if mapping disabled -> return
  resolve order object via Woo APIs
  resolve recipient
  if recipient missing -> log and return
  check consent if required
  build idempotency key
  if duplicate and not forced -> log and return
  build MessageRequest
  dispatch through core
  persist order markers
  optionally add order note
```

### manual resend
```text
manual_resend(order_id, event_key):
  check capability
  verify nonce
  load mapping
  build forced dispatch
  bypass duplicate guard with audit
  dispatch through core
  store resend reference
```

---

## 15) admin interfaces

### settings page
- enable/disable integration
- global behavior
- event cards
- recipient config
- message mode
- template/body
- placeholder help
- preview area
- validation state

### order admin UI
- latest dispatches
- last errors
- resend buttons
- status summary
- mapping/event summary

---

## 16) security rules
- manual resend فقط با capability و nonce
- order IDs validate شوند
- inputهای settings sanitize شوند
- outputها escape شوند
- شماره‌ها masked نمایش داده شوند
- PII غیرضروری ذخیره نشود

---

## 17) consent/privacy
- برای transactional flow، opt-in می‌تواند optional باشد
- اگر mapping نیاز به opt-in دارد، absence آن باید send را block کند
- state آن باید قابل audit باشد
- consent field اگر در checkout اضافه شود باید با flow رسمی Woo سازگار باشد

---

## 18) logging و troubleshooting
لاگ باید این contextها را داشته باشد:
- order id
- event key
- recipient type
- masked phone
- mapping version
- duplicate blocked reason
- dispatch result
- retry state

---

## 19) graceful degradation

### اگر WooCommerce غایب بود
- plugin dormant
- no trigger registration
- admin notice

### اگر core غایب بود
- plugin dormant
- admin notice

### اگر gateway فعال نبود
- settings و mappingها قابل مشاهده باشند
- dispatchها غیرفعال باشند
- warning واضح نمایش داده شود

---

## 20) test cases

### unit
- event mapping resolution
- placeholder rendering
- recipient resolution
- idempotency key generation
- consent checks

### integration
- processing send
- completed send
- refunded send
- customer note send
- manual resend success
- order note write behavior

### negative
- gateway unavailable
- core missing
- Woo missing
- missing phone
- invalid template config
- duplicate event fire
- nonce/capability failure on resend

### compatibility
- HPOS on
- HPOS off
- classic checkout
- modern blocks flow
- guest checkout

---

## 21) edge cases
- draft order در blocks
- payment complete بعد از status change
- status oscillation
- partial refund
- custom order statuses
- guest without phone
- order object در HPOS با screen behavior متفاوت
- resend روی event disabled

---

## 22) release checklist
- dependency guards working
- HPOS declaration present
- no direct order SQL
- trigger coverage tested
- duplicate prevention tested
- manual resend secured
- admin UI usable
- logs actionable

---

## 23) definition of done
- event mappings ذخیره می‌شوند
- رویدادهای اصلی dispatch می‌کنند
- duplicate prevention کار می‌کند
- resend کار می‌کند
- order troubleshooting UI usable است
- plugin در نبود dependencyها fatal نمی‌دهد

---

## 24) verdict
**Ready with Assumptions**
