=== Yekta Integration EDD ===
Contributors: yekta
Requires at least: 6.6
Tested up to: 6.8
Requires PHP: 8.1
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easy Digital Downloads integration plugin for Yekta SMS Core.

== Description ==

This plugin binds official Easy Digital Downloads order events to Yekta SMS Core dispatch contracts.

Features:
- dependency checks for core and EDD
- event mapping per EDD event
- recipient resolution and placeholder rendering
- idempotency guard per order/event/recipient
- secure manual resend from EDD order troubleshooting area
- graceful degradation when core, EDD, or active gateway is missing

== Installation ==

1. Upload the plugin directory to `/wp-content/plugins/`.
2. Activate `Yekta SMS Core`.
3. Activate at least one Yekta gateway plugin.
4. Activate this plugin.
5. Configure settings in `Downloads > Yekta SMS`.

== Changelog ==

= 0.1.0 =
* Initial MVP release for EDD core event integration.
