# قالب پرامپت ساخت Integration جدید

```text
این مخزن monorepo اکوسیستم Yekta SMS است. ابتدا README.md و سپس docs/ را کامل بررسی کن. pluginهای موجود و معماری repo را تحلیل کن. چیزی را حدس نزن. اگر مستندات رسمی افزونه‌ی ثالث کافی نیست، دقیق بگو چه چیزی کم است.

درخواست:
یک integration جدید برای [نام افزونه] در مسیر integration/ بساز.

قواعد:
1) فقط event binding و orchestration domain داخل integration باشد.
2) provider-specific logic ممنوع است.
3) اگر contract عمومی جدید لازم است، اول docs/architecture و ADR را به‌روزرسانی کن.
4) mapping settings، placeholderها، recipient resolution، idempotency، tests و handoff docs را اضافه کن.
5) compatibility risks را ثبت کن.
6) در پایان گزارش نهایی را با فرمت اجباری README بده.
```
