=== Wbcom Designs - BuddyPress Search ===
Contributors: vapvarun,wbcomdesigns
Donate link: https://wbcomdesigns.com/
Tags: comments, spam
Requires at least: 3.0.1
Tested up to: 6.2.0
Stable tag: 1.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

BuddyPress search allows you to search your community elements along with WordPress Post, Pages, and Custom Post Type.

== Description ==

BuddyPress search allows you to search your community elements along with WordPress Post, Pages, and Custom Post Type.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `buddypress-search.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= Does it will display commutative results for all BuddyPress Components? =

Yes, it will display all results along with custom post types which is enabled from backend settings.

== Changelog ==

= 1.4.0 =
* Fix:  sanitizing error
* Fix:  #44 - Search is not working properly
* Fix:  Plugin redirect issue when multi plugin activate the same time

= 1.3.0 =
* Fix: Managed admin ui option layout
* Fix: Fixed nonce verification issue
* Fix: (#32) Fixed widget not showing on front end
* Fix: (#34) Fixed new xprofile field is not searchable
* Fix: (#32) Fixed widgets not display while plugin is active

= 1.2.0 =
* Fix: Removed install plugin button from wrapper
* Fix: PHPCS Fixes

= 1.1.1 =
* Added Screenshots
* Fix: removed privacy echo

= 1.1.0 =
* Fix: Undefine index warning
* Fix: #21 - Console Error displaying after plugin activated
* Fix: (#19) Fixed - Live search not working in buddypress search widget
* Fix: (#7) Fixed typo change in plugin welcome page
* Fix: #12 - Database error when check the multi fields option

= 1.0.0 =
* Initial Release
