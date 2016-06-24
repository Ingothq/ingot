=== Ingot ===
Contributors: Shelob9
Tags: a/b testing, multi-varitant testing, ab testing, ab test, a/b testing, cro, content rate optimization, split tests, split testing, easy digital downloads, woocommerce, woo, edd, give, givewp
Donate link: https://ingothq.com
Requires at least: 4.4.0
Tested up to: 4.5
Stable tag: 1.3.0
License: GPL v2+

A/B testing made easy for WordPress - Do less, convert more.

== Description ==
[Ingot](https://ingothq.com/) is the simplest way to make your WordPress site perform better for your business — sell more, get more sign ups, get more out of your online marketing. Easy to use, affordable, and requires no 3rd party services.

A/B testing is a proven technique to increase conversions on your site, but traditionally it has required a lot of traffic, a connection to a costly 3rd-party service and an expert to read the results. Busy WordPress site managers don't have time for that? Who does? Ingot changes all of that. Ingot takes a few minutes to setup, and then it just works, constantly evolving your site to find the results that convert best.

Don't worry that you made the wrong choice when you chose your call to action text, site headline, or pricing, let Ingot find the best performing option. Don't worry about missing out on sales because you didn't have time to test, use Ingot -- the easy, yet surprisingly affordable A/B Testing solution for WordPress.

> Stop trying to be perfect, use Ingot today and make your website smarter.
> * [Get support for Ingot](https://ingothq.com/customersupport/)
> * [Learn how to use Ingot by reading our documentation](https://ingothq.com/documentation/)

= Features =
* Works with any amount of traffic, our A/B testing algorithm is optimized to work with small sites, and large sites a like.
* Test call to action button text -- A/B, split tests and multi-variant.
* Test call to action button color -- A/B, split tests and multi-variant.
* Find the best headline to drive sales, using A/B testing with WooCommerce or Easy Digital Downloads.
* Find the best pricing structure by split testing pricing with WooCommerce or Easy Digital Downloads (coming soon).
* Increase donations, by A/B testing your non-profit's messaging -- works with Give.
* Requires no third-party API.
* One price -- don't pay per test
* Developers can create any type of A/B Test using our simple REST API, and/ or using a destination test that registers conversions using a hook.

== Installation ==

= Minimum Requirements =

* WordPress 4.4 or greater
* PHP version 5.5 or greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install of Ingot, log in to your WordPress dashboard, navigate to the Plugins menu and click "Add New".

In the search field type "Ingot" and click Search Plugins. Once you have found the plugin you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by simply clicking "Install Now".

= Manual installation =

The manual installation method involves downloading our donation plugin and uploading it to your server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Frequently Asked Questions ==
For more FAQs go to [https://ingothq.com/documentation/ingot-faq/](https://ingothq.com/documentation/ingot-faq/)

= How Hard Is It To Implement A Test? =
Not hard at all. Can you fill out a form? Can you insert a shortcode into a page? Then you can use Ingot.

= Is Ingot Free? =
Ingot is free to try for two weeks. Once your trial has expired, you can choose one of our plans.

= Will Ingot break my site if my trial or subscription expires? =
No, not at all, that would be terrible. If you do not have an active subscription, Ingot will output the first variation from a test group and will not register any stats. This way your site still has valid HTML and looks right. BTW, this is the same way we always treat bots, which need valid HTML -- since a bot may be Google -- but we don't want to track them.

= Does Ingot work with a caching system ? =
Yes. Many sites use a static HTML cache, which could be an issue if we didn't work around them. Ingot works around this by using a small JavaScript file to check that any tests on the page are unused, and if needed updating the HTML on the page via AJAX. This doesn't cause a full page refresh or anything, Ingot only updates the part of the page that needs updated.

== Screenshots ==

1. Creating a new A/B test group: choose a name

2. Creating a new A/B test group: choose a type

3. Creating a new A/B test group: call to action button test settings

4. Creating a new A/B test group: Destination test settings

== Changelog ==

= 1.3.1.1 =
* Fix destination test loading in premium mode.
= 1.3.1 =
* Minor bug fixes
* Support for testing forms.

= 1.3.0 =
ADDED: Support For Paid Memberships Pro
FIXED: Activation bugs

= 1.2.0 =
ADDED: A/B testing for Give
FIXED: Bug preventing proper session tracking for destination A/B tests
FIXED: Ensure that bots do not trigger an iteration of an A/B test always.
FIXED: Ensure that when in random A/B testing mode, that we record iterations properly.
ADDED: Show group stats for average of all A/B tests in stats view.

= 1.1.0 =
* ADDED: Shortcode insert button for adding A/B tests from to post editor
* ADDED: Destination Tests type A/B tests with support for WooCommerce, Easy Digital Downloads and Give
* IMPROVED: User interface test creation flow
* FIXED: Output of button color test


= 1.0.2 =
* ADDED: Copy to clipboard for shortcode
* FIXED: Display conversion rates properly for A/B tests
* Minor bug fixes

= 1.0.1 =
* Dump old tables if upgrading from beta
* Don't assume path for dependencies

# 1.0.0
* Bower for dependencies instead of using CDNs
* UI Improvements
* Bug fixes

= 0.4.0 =
* Improve A/B Test UI
* Refactor testing algorithm/crud/API

= 0.3.0 =
* Improve UI

= 0.2.2 =
* Improved compatibility with WordPress 4.4
* Plugin header meta data
* Show version number in admin header

= 0.2.1 =
* Price testing for Easy Digital Downloads -- still experimental
* Major UI improvements

= 0.2.0.1 =
* Simpler verification of click tracking A/B tests.
* Better handling of failed click tracking A/B tests.

= 0.2.0 =
Major refactor and overhaul of UI

= 0.1.0 =
Initial release of our A/B testing plugin
