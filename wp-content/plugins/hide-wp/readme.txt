=== Plugin Name ===
Contributors: kursorA
Donate link: http://www.wp-supersonic.com/donate/donate-hide-wp
Tags: security, antyspam, spam, protect, save resources
Requires at least: 4.0
Tested up to: 4.4.2
Stable tag: 1.0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Hide WP

== Description ==

With this plugin You can hide all Wordpress elements. Even for human it will be hard to recognize that You site using Wordpress.

= Major features =
* hide (change URLs) all sensitive Wordpress elements, like:
* wp-admin
* wp-login.php
* wp-includes
* plugins
* themes
* wp-content
* media
* make internal links relative 

= Example site with Hide WP plugin =
* [Site 1](http://www.wp-supersonic.com/ "www.wp-supersonic.com") [Admin area](http://www.wp-supersonic.com/nc-admin/ "www.wp-supersonic.com/nc-admin/")



= Warning =

This plugin may harm your site if your site uses plugins or theme which uses wordpress functions in not proper way. 

In example: function [site_url](https://codex.wordpress.org/Function_Reference/site_url).
The right way: *$url = site_url( '/secrets/' );*
**The wrong way:** *$url = site_url() . '/secrets/';*

If your site is broken after this plugin activation go to [FAQ](https://wordpress.org/plugins/hide-wp/faq/) for instructions.
 
== Installation ==

1. Upload zip archive content to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Administration area and choose SuperSonic from menu.
4. Configure plugin.

== Frequently Asked Questions ==

= What to do when my site is broken? =

1. Delete hide-wp directory from wp-content/plugins.
2. Ramove all content between '# BEGIN Hide WP' and '# END Hide WP' from .htaccess file.


== Screenshots ==

1. Header configuration
2. Login configuration
3. wp-admin configuration
4. wp-includes configuration
5. Plugins configuration
6. Comments configuration
7. Themes configuration
8. Media configuration
9. Home dir configuration
10. Other configuration

== Changelog ==

= 1.0.5 =
* Fixed notices

= 1.0.4 =
* Changed login handling
* Added default configuration for new installs

= 1.0.3 =
* Added W3 Total Cache support
* Added message about Pretty Permalinks are required
* Bug fixed in script and css sources

= 1.0.2 =
* Fixed bug in script and style sources (trailingslashit removed from src)
* Rewrite configuration after theme switching

= 1.0.1 =
* Added options for changing or hide generator meta tag
* Added option for disable X-Pingback HTTP header

= 1.0.0 =
* Initial version

== Upgrade Notice ==

