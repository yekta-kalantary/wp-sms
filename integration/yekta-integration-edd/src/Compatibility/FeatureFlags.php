<?php
declare(strict_types=1);

namespace YektaSMS\Integration\EDD\Compatibility;

final class FeatureFlags
{
    public function register(): void
    {
        add_filter('yekta_sms_edd_feature_flags', [$this, 'defaults']);
    }

    public function defaults(array $flags): array
    {
        $flags['recurring_payments'] = class_exists('EDD_Recurring');
        $flags['software_licensing'] = class_exists('EDD_Software_Licensing');
        return $flags;
    }
}
