# ADR-002: مرز Gateway = Provider Adapter Only

## وضعیت
Accepted

## زمینه
Gatewayها باید بتوانند providerهای مختلف را به هسته وصل کنند، بدون آن‌که domain logic یا integration logic را در خود نگه دارند.

## تصمیم
Gateway فقط مسئول این موارد است:
- auth/config provider
- request construction
- response normalization
- error mapping
- health check
- provider capability declaration

Gateway مسئول این موارد نیست:
- event mapping
- business rule
- domain-specific orchestration
- direct integration behavior

## پیامد
- gatewayها ساده‌تر و قابل‌تعویض‌تر می‌شوند
- تست آن‌ها متمرکزتر می‌شود
- leakage منطق محصولی به لایه‌ی provider کاهش می‌یابد
