<?php
declare(strict_types=1);

namespace YektaSMS\Integration\WooComrce\Admin;

use YektaSMS\Integration\WooComrce\Config\EventMappingsRepository;
use YektaSMS\Integration\WooComrce\Config\WooSettings;

final class WooSettingsPage
{
    public function __construct(private WooSettings $settings, private EventMappingsRepository $mappings)
    {
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
            'woocommerce',
            __('Yekta SMS', 'yekta-integration-woocomrce'),
            __('Yekta SMS', 'yekta-integration-woocomrce'),
            'manage_options',
            'yekta-sms-wc',
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
            <h1><?php echo esc_html__('Yekta SMS WooCommerce Integration', 'yekta-integration-woocomrce'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('yekta_sms_wc'); ?>
                <h2><?php echo esc_html__('General', 'yekta-integration-woocomrce'); ?></h2>
                <label><input type="checkbox" name="<?php echo esc_attr(WooSettings::OPTION_KEY); ?>[enabled]" value="1" <?php checked(!empty($settings['enabled'])); ?> /> <?php echo esc_html__('Enable integration', 'yekta-integration-woocomrce'); ?></label>
                <p>
                    <label><?php echo esc_html__('Admin phone list', 'yekta-integration-woocomrce'); ?></label><br />
                    <textarea name="<?php echo esc_attr(WooSettings::OPTION_KEY); ?>[admin_phone_list]" rows="3" cols="60"><?php echo esc_textarea((string) $settings['admin_phone_list']); ?></textarea>
                </p>
                <h2><?php echo esc_html__('Event mappings', 'yekta-integration-woocomrce'); ?></h2>
                <?php foreach ($mappings as $event => $config) : ?>
                    <fieldset style="padding:12px;border:1px solid #dcdcde;margin-bottom:12px;">
                        <legend><strong><?php echo esc_html($event); ?></strong></legend>
                        <label><input type="checkbox" name="<?php echo esc_attr(EventMappingsRepository::OPTION_KEY); ?>[<?php echo esc_attr($event); ?>][enabled]" value="1" <?php checked(!empty($config['enabled'])); ?> /> <?php echo esc_html__('Enabled', 'yekta-integration-woocomrce'); ?></label>
                        <p>
                            <label><?php echo esc_html__('Recipient type', 'yekta-integration-woocomrce'); ?></label><br />
                            <select name="<?php echo esc_attr(EventMappingsRepository::OPTION_KEY); ?>[<?php echo esc_attr($event); ?>][recipient_type]">
                                <?php foreach (['customer', 'admin', 'custom'] as $item) : ?>
                                    <option value="<?php echo esc_attr($item); ?>" <?php selected($config['recipient_type'], $item); ?>><?php echo esc_html($item); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                        <p>
                            <label><?php echo esc_html__('Body template', 'yekta-integration-woocomrce'); ?></label><br />
                            <textarea name="<?php echo esc_attr(EventMappingsRepository::OPTION_KEY); ?>[<?php echo esc_attr($event); ?>][body_template]" rows="2" cols="80"><?php echo esc_textarea((string) $config['body_template']); ?></textarea>
                        </p>
                    </fieldset>
                <?php endforeach; ?>
                <?php submit_button(__('Save settings', 'yekta-integration-woocomrce')); ?>
            </form>
        </div>
        <?php
    }
}
