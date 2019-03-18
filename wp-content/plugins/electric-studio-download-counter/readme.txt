=== Plugin Name ===
Contributors: irvingswiftj, Matthew Burrows, Electric Studio, javorszky, Leeroy Rose
Tags: downloads, counter
Requires at least: 3.1
Tested up to: 4.1.2
Stable tag: 2.4
License: GPLv2 or later

Get Statistics about your downloads.

== Description ==

View how many people have downloaded what files from your site.

Features include:

*   Allows you to specify which file types to track.
*   View top ten downloads.
*   View this month's downloads.
*   View this week's downloads.
*   Use shortcode or a php function to display amount of times downloaded in the frontend of your site.

== Installation ==

Install from wordpress plugins directory.

Else, to install manually:

1. Upload the whole folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. An example of the available download stats.
2. Specify which file types you want to track.

== Changelog ==
= 2.4 =
* Bumping version number.
* Added ability to not track downloads based on IP address

= 2.3 =
* Moved code around to cater for how the options table stores options

= 2.2 =
* Removed console.log from js file that was causing errors

= 2.1 =
* Bumping version number.
* Fixing bug with image tracking.
* Fixing bug with not tracking if not logged in.
* Shout out to Svetlin Nakov for sending me fixes! Thanks, dude!
* Removed mentions of "derp" from the code. Although it is now a word according to the Oxford English Dictionary. Fact.
* Removed minified javascript file, because I want y'all to know how this thing works. And the file is tiny.

= 2.0.2 =
* Majox bugfix. Plugin now correctly counts downloads in WordPress versions 3.3 and up. Did not test below 3.3, use at your own risk.

= 2.0.1 =
* Minor bugfix due so it works on 3.5

= 2.0 =
* Complete rewrite of the plugin to sort out best practices and conform to WordPress coding standards
* resolved issues of "not working". It does now.
* uses the same data, so update should be painless, but please, PLEASE, make a backup of your data
* to make a backup of your data, look at a plugin named 'WP-DB-Backup' (yes, I know it hasn't been updated in 2 years, but it works perfectly.)

= 1.1 =
* Fixed notices that appeared in debugging mode
* Widget now included

= 1.0 =
* Code is now Object Orientated style to be consistant with our other plugins
* Nonces have been added for security on the AJAX functions
* Javascript now respects target="_blank" on tracked links - Thanks to Philouxera for bringing this to my attention
* Download Counter is now a menu page rather than an option page.
* Count Shortcode added [downloadcount link="filename.txt"]. Users that want to build this into their template can do with `echo $esdc->get_count("filename.txt");` - Thanks to 'Biyan' for suggesting this

= 0.7.4 =
* Fixed Jquery bug

= 0.7.3 =
* Wordpress 3.3.1 compatible

= 0.7.2 =
* Searching for downloads between different dates are no longer limited to 30 results.

= 0.7.1 =

* Fixed 'not counting bug'

= 0.7 =

* 2 Bug Fixes
* Search for Dates

= 0.5 =
* Beta version release.

== Upgrade Notice ==

= 1.1 =
* Minor Bug fixes

= 1.0 =
* Code is now Object Orientated style to be consistant with our other plugins
* Nonces have been added for security on the AJAX functions
* Javascript now respects target="_blank" on tracked links
* Download Counter is now a menu page rather than an option page.
* Shortcode added

= 0.7.4 =
Fixed jquery bug

= 0.7.3 =
Wordpress 3.3.1 compatible

= 0.7.2 =
Download search improved

= 0.7.1 =
Fixes issues with not counting.

= 0.7 =
Bug fixes and new functionality

= 0.5 =
This is the beta version.


