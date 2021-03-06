=== LowerMedia iFrames On Demand ===
Contributors: hawkeye126
Donate link: http://lowermedia.net
Tags: optimization, iframes, multisite, speed, youtube, vimeo, soundcloud, dailymotion
Requires at least: 3.0.1
Tested up to: 4.3
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically add iFrame placeholder images, reduce page requests, decrease load time!

== Description ==

Reduce requests and optimize for speed!!! The iFrames On Demand plugin replaces all iFrames on the page with an image placeholder, when the image placeholder is clicked the image appears.  This works without any configuration for all iFrames and will pull in video thumbnails for YouTube, Vimeo and DailyMotion.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `lowermedia-iframes-on-demand` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enjoy

== Frequently Asked Questions ==

Q: How do I prevent iFrame on Demand Overlay on certain iFrames?

A: Add class='no-placeholder' to the iFrame tag


= Does this work automatically =

Yes

= How to I change the placement of the play button? =

Manually through a child theme or css plugin

== Changelog ==

= 1.0 =
* Add theme files

= 1.1.0 =
* Namespacing with class
* PHP/JS optimizations
* Manipulate iframes w/ content filter and add placeholder, then use js to add iframe back when clicked
* Incorporate dashicons
* Works much faster and saves much more requests and reduces much more load time

= 1.2.0 =
* Remove Dashicon (eliminate requests)
* Pure CSS Play Button Overlay (reduce load time)
* Feature: add the class 'no-placeholder' to your iframe in order to skip On Demand Overlay
