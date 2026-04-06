# Handoff Pack — `yekta-integration-edd`

## هدف
این پلاگین، رویدادهای رسمی Easy Digital Downloads را به `yekta-sms-core` متصل می‌کند و فقط مسئول integration orchestration است.

## dependencyها
- `yekta-sms-core`
- `Easy Digital Downloads`

### soft dependency
- حداقل یک gateway فعال در core

## مرزهای اجرایی
- تماس مستقیم با provider ممنوع
- منطق provider-specific ممنوع
- فقط APIهای رسمی EDD (`edd_get_order`, `edd_get_customer`, `edd_get_order_meta`, `edd_update_order_meta`, `edd_insert_payment_note`)

## event map (MVP)
customer:
- `customer.order.created`
- `customer.order.completed`
- `customer.order.refunded`
- `customer.order.failed`
- `customer.order.pending`
- `customer.order.revoked`
- `customer.order.note.added`

admin:
- `admin.order.created`
- `admin.order.completed`
- `admin.order.refunded`
- `admin.order.failed`

## triggerهای رسمی استفاده‌شده
- `edd_insert_payment`
- `edd_transition_order_status`
- `edd_insert_payment_note`

## settings schema
option: `yekta_sms_edd_settings`
- `enabled`
- `send_mode`
- `respect_opt_in`
- `write_order_notes`
- `retry_policy`
- `manual_resend_enabled`
- `customer_phone_source`
- `admin_phone_list`
- `non_critical_send_strategy`
- `customer_phone_meta_key`
- `customer_opt_in_meta_key`

option: `yekta_sms_edd_event_mappings`
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
- `dispatch_timing`

## idempotency
کلید: `hash(plugin + event + order_id + recipient + mapping_version)`

ذخیره:
- order meta marker با پیشوند `_yekta_sms_sent_`
- history در `_yekta_sms_last_dispatch_ids`

## admin tools
- صفحه تنظیمات در منوی Downloads
- order troubleshooting UI روی صفحه جزئیات order
- manual resend با capability + nonce

## graceful degradation
- نبود core: dormant + admin notice
- نبود EDD: dormant + admin notice
- نبود gateway فعال: settings فعال + dispatch غیرفعال + warning

## optional/advanced (خارج از MVP)
- Recurring Payments (`customer.subscription.*`)
- Software Licensing (`customer.license.*`)

این موارد باید feature-flagged بمانند.
