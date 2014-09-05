=== amazon-product-price ===
Contributors: aloktiwari
Donate link: http://wptricksbook.com/donate
Tags: amazon aws, amzon product price
Requires at least: 3.0.1
Tested up to: 4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

This plugin used to show the price of amazon product price in post or pages.

Enter the shortcode in post or pages " [price asin='B00KQ7SBTE'] " 
asin is found in amazon product detail page URL highlighted text is
asin e.g => http://www.amazon.com/Gopro-Water-Resistant-Case-Camkix/dp/B00KQ7SBTE/ie=UTF8?m=A3KD0OO9H1T8D3&keywords=gopro+case

Go in Admin Panel under setting tab "Amazon Setting" there enter the credentials which provided by amazon.

== Installation ==


1. Upload 'amazon-product-price' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `<?php do_shortcode("[price asin='B00KQ7SBTE']"); ?>` in your custom templates
4. Only put the shortcode in post or pages.
5. Go in Admin Panel under setting tab "Amazon Setting" there enter the credentials which provided by amazon.

== Screenshots ==

1. The options screen.
2. The output. A bunch of thumbnails. Simple yet powerful.