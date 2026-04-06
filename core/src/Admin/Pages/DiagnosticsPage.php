<?php
declare(strict_types=1);
namespace YektaSMS\Core\Admin\Pages;
final class DiagnosticsPage { public function render(): void { echo '<div class="wrap"><h1>' . esc_html__('Diagnostics','yekta-sms-core') . '</h1></div>'; } }
