=== Back in stock notifications for WooCommerce ===
Contributors: getinnovationdev, wpsimplesolutions
Tags: woocommerce notifications, back in stock notifications, woocommerce back in stock, woocommerce back in stock notifications, woocommerce back in stock notification, woocommerce subscribe, subscribe out of stock, back in stock, woocommerce, out of stock, notify me stock
Requires at least: 5.8.6
Tested up to: 6.3.2
Requires PHP: 7.3
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Woocommerce subscribe system for out of stock products.

== Description ==
Back in stock notifications for WooCommerce it’s a WordPress plugin which extends WooCommerce by adding a Subscribe button to out of stock products. It supports simple and variable products.

The button opens a modal form which can be used to subscribe for Back In stock email notifications. When the product is back in stock the plugin sends a personalized email to subscribers.

= Main Features =
✔️ Manage the button colors
✔️ Personalize the modal/form
✔️ Personalize the emails body
✔️ Default emails content
✔️ Set up the mode: automatically / manually
✔️ Filter, generate and export subscribers in CSV format
✔️ View subscribers by product
✔️ Check each notification status
✔️ Pause specific products for sending notifications
✔️ Filter subscriptions by product
✔️ reCaptcha v2

= Merge Tags =
The emails can be personalized with merge tags.

* `[wsnm-first-name]` – The subscription’s first name.
* `[wsnm-last-name]` – The subscription’s last name.
* `[wsnm-email]` – The subscription’s email address.
* `[wsnm-product-title]` – The product title. If it’s a variation, then it also contains the variation name.
* `[wsnm-product-price]` – The product price, it also contains the currency.
* `[wsnm-product-quantity]` – The available quantity in stock. If the quantity is not managed, then it returns the string 'unlimited'.
* `[wsnm-product-url]` – The product’s URL. If it’s a variation, the variation is automatically selected.


== Installation ==
1. Upload `woo-stock-notify-me` to the `/wp-content/plugins/` directory
2. Activate the plugin through the `Plugins` menu in WordPress
3. Visit the setting page and follow the instructions

== Frequently Asked Questions ==
= What products type support? =
It supports two products types: simple and variable products.

= What does the manually mode? =
It gives more control over the notifications. The notifications are triggered manually by administrator directly from the product page. This is the default mode.

= What does the automatically mode? =
You need to update only the stock. The notifications are triggered automatically by the stock status. The notifications are sent when the product is back in stock. You can still pause specific products.

= I’ve still got questions. Where can I find answers? =
Check out our documentation [page](https://www.getinnovation.dev/wordpres-plugins/woocommerce-stock-notify-me/documentation/) for more details.

== Screenshots ==
1. Settings - Emails Templates
2. Settings - Subscription Form
3. Variation Product Type - Out of stock
4. Simple Product Type - Out of stock
5. Subscribe Form Modal
6. All Subscriptions
7. Settings - General
8. Merge Tags
9. Automatically Mode - NotificationSstatus
10. Manually Mode - Send Notifications

== Changelog ==
= 1.0.1 =
* Manage Button text and Modal Title
* Tested with the latest WP and WC version
= 1.0.0 =
* First version

== Upgrade Notice ==
= 1.0.1 =
Compatible with the latest WP and WC version.