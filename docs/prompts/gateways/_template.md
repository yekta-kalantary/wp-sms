# قالب پرامپت ساخت Gateway جدید

از این قالب برای ساخت gateway جدید استفاده کن.

```text
این مخزن monorepo اکوسیستم Yekta SMS است. ابتدا README.md و سپس docs/ را کامل بررسی کن. بعد pluginهای موجود را بخوان و الگوی معماری را استخراج کن. چیزی را حدس نزن. اگر مستندات رسمی provider کافی نیست، دقیق بگو چه مستندی کم است.

درخواست:
یک gateway جدید برای [نام provider] در مسیر geateway/ بساز.

قواعد:
1) فقط provider-specific logic داخل gateway باشد.
2) اگر contract عمومی جدید لازم است، اول docs/architecture و ADR را به‌روزرسانی کن.
3) health check، settings، adapter، request builder، response normalizer، error mapper، tests و handoff docs را کامل کن.
4) secretها را redact کن.
5) dependency غیرضروری اضافه نکن.
6) در پایان، گزارش نهایی را با فرمت اجباری README بده.
```
