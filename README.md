# Woo Expected Delivery Date

**License:** GNU General Public License Version 3 (GPLv3)

A WooCommerce extension that displays an expected delivery date next to each shipping method at checkout. Store owners configure the number of business days (weekends excluded) for each shipping method, and the plugin calculates the estimated delivery date based on the shopper's current date.

The plugin also surfaces a friendly reminder on the cart and checkout pages showing how much more a shopper needs to add to qualify for free shipping (based on the minimum order amount configured in your Free Shipping methods).

## Requirements
- WordPress with WooCommerce installed and active.
- PHP 7.4+.

## Installation
1. Download or clone this repository.
2. Copy `woo-expected-delivery-date.php` into your WordPress installation under `wp-content/plugins/`.
3. In the WordPress admin, go to **Plugins → Installed Plugins** and activate **Woo Expected Delivery Date**.

## Configuration
The expected delivery setting is available per shipping method instance inside WooCommerce shipping zones.

1. In the WordPress admin, open **WooCommerce → Settings → Shipping**.
2. Select a **Shipping zone**, then click **Edit** for a shipping method (e.g., Flat rate, Free shipping, Local pickup, or any other method).
3. In the method settings form, locate the field **Expected delivery (business days)**.
4. Enter the number of business days from the next business day (0 allows same-day arrival when appropriate). Weekends are automatically skipped.
5. Save the shipping method and repeat for any other methods.

## How it works
- The plugin adds an **Expected delivery (business days)** numeric field to every shipping method instance.
- At checkout, the plugin calculates the delivery date by counting forward the configured number of business days starting from the next business day (skipping Saturdays and Sundays).
- The expected delivery date is displayed beneath the shipping method label in the format `j M Y` (e.g., `15 Dec 2025`).
- If a Free Shipping method in the shopper's zone requires a minimum order amount, the cart and checkout will display how much more the shopper needs to spend to unlock free delivery.

## Updating or removing the plugin
- To update, replace `woo-expected-delivery-date.php` in `wp-content/plugins/` with the new version and reactivate if necessary.
- To remove, deactivate the plugin from **Plugins → Installed Plugins** and delete the plugin file.

## License
This plugin is released under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.html).
