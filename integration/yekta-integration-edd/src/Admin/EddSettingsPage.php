<?php
declare(strict_types=1);

namespace YektaSMS\Integration\EDD\Admin;

use YektaSMS\Integration\EDD\Config\EddSettings;
use YektaSMS\Integration\EDD\Config\EventMappingsRepository;
use YektaSMS\Integration\EDD\Support\DependencyChecker;

final class EddSettingsPage
{
    public function __construct(
        private EddSettings $settings,
        private EventMappingsRepository $mappings,
        private DependencyChecker $dependencies
    ) {
    }

    public function register(): void
    {
        add_action('admin_menu', [$this, 'addMenu']);
        add_action('admin_init', function (): void {
            $this->settings->register();
            $this->mappings->register();
        });
    }

    public function addMenu(): void
    {
        add_submenu_page(
            'edit.php?post_type=download',
            __('Yekta SMS', 'yekta-integration-edd'),
            __('Yekta SMS', 'yekta-integration-edd'),
            'manage_options',
            'yekta-sms-edd',
            [$this, 'render']
        );
    }

    public function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $settings = $this->settings->get();
        $mappings = $this->mappings->all();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Yekta SMS Easy Digital Downloads Integration', 'yekta-integration-edd'); ?></h1>
            <?php if (!$this->dependencies->hasActiveGateway()) : ?>
                <div class="notice notice-warning inline"><p><?php echo esc_html__('No active gateway is available in core. Dispatch is currently disabled.', 'yekta-integration-edd'); ?></p></div>
            <?php endif; ?>
            <form method="post" action="options.php">
                <?php settings_fields('yekta_sms_edd'); ?>
                <h2><?php echo esc_html__('General settings', 'yekta-integration-edd'); ?></h2>
                <p><label><input type="checkbox" name="<?php echo esc_attr(EddSettings::OPTION_KEY); ?>[enabled]" value="1" <?php checked(!empty($settings['enabled'])); ?> /> <?php echo esc_html__('Enable integration', 'yekta-integration-edd'); ?></label></p>
                <p><label><input type="checkbox" name="<?php echo esc_attr(EddSettings::OPTION_KEY); ?>[manual_resend_enabled]" value="1" <?php checked(!empty($settings['manual_resend_enabled'])); ?> /> <?php echo esc_html__('Enable manual resend from order view', 'yekta-integration-edd'); ?></label></p>
                <p><label><?php echo esc_html__('Customer phone meta key', 'yekta-integration-edd'); ?></label><br />
                    <input type="text" class="regular-text" name="<?php echo esc_attr(EddSettings::OPTION_KEY); ?>[customer_phone_meta_key]" value="<?php echo esc_attr((string) $settings['customer_phone_meta_key']); ?>" />
                </p>
                <p><label><?php echo esc_html__('Admin phone list', 'yekta-integration-edd'); ?></label><br />
                    <textarea name="<?php echo esc_attr(EddSettings::OPTION_KEY); ?>[admin_phone_list]" rows="3" cols="60"><?php echo esc_textarea((string) $settings['admin_phone_list']); ?></textarea>
                </p>

                <h2><?php echo esc_html__('Event mappings', 'yekta-integration-edd'); ?></h2>
                <?php foreach ($mappings as $event => $config) : ?>
                    <fieldset style="padding:12px;border:1px solid #dcdcde;margin-bottom:12px;">
                        <legend><strong><?php echo esc_html($event); ?></strong></legend>
                        <p><label><input type="checkbox" name="<?php echo esc_attr(EventMappingsRepository::OPTION_KEY); ?>[<?php echo esc_attr($event); ?>][enabled]" value="1" <?php checked(!empty($config['enabled'])); ?> /> <?php echo esc_html__('Enabled', 'yekta-integration-edd'); ?></label></p>
                        <p><label><?php echo esc_html__('Recipient type', 'yekta-integration-edd'); ?></label><br />
                            <select name="<?php echo esc_attr(EventMappingsRepository::OPTION_KEY); ?>[<?php echo esc_attr($event); ?>][recipient_type]">
                                <?php foreach (['customer', 'admin', 'custom'] as $recipientType) : ?>
                                    <option value="<?php echo esc_attr($recipientType); ?>" <?php selected((string) $config['recipient_type'], $recipientType); ?>><?php echo esc_html($recipientType); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                        <p><label><?php echo esc_html__('Body template', 'yekta-integration-edd'); ?></label><br />
                            <textarea name="<?php echo esc_attr(EventMappingsRepository::OPTION_KEY); ?>[<?php echo esc_attr($event); ?>][body_template]" rows="2" cols="80"><?php echo esc_textarea((string) $config['body_template']); ?></textarea>
                        </p>
                    </fieldset>
                <?php endforeach; ?>
                <?php submit_button(__('Save settings', 'yekta-integration-edd')); ?>
            </form>
        </div>
        <?php
    }
}
