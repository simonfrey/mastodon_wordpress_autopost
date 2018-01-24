=== Mastodon Autopost ===
Contributors: l1am0
Tags: mastodon, Mastodon, Mastdon Autopost, federated web, GNU social, statusnet, social web, social media, auto post
Requires at least: 3.0.1
Tested up to: 4.9.2
Stable tag: 2.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
A Wordpress Plugin that automatically posts your new articles to Mastodon. The best: It is set and forget! 
 
== Description ==
 
With GNUsocial Autopost your post always get automatically posted to your Mastodon account.

There are two formats available: 
Post title + Post URL + Hashtags
Post title + Post Excerpt + Post URL + Hashtags

Find the plugin settings: Settings > Mastodon Autpost Settings

Just set your credentials and your post preference and lean back. The rest is done in the background and you don't have to care about it.

For any questions, do not hesitate to contact me:
*	Mail: mastodonautopost@l1am0.eu
*	XMPP: l1am0@trashserver.net

Do you want to help translating this plugin in your language? [Visit the translation page](https://translate.wordpress.org/projects/wp-plugins/autopost-to-mastodon)

== Frequently Asked Questions ==
 
= Can I decide per post if I want to autopost it? =
 
Yes. Since version 1.1 you see a settings box in every post.

= Does the plugin send you my login data? =
 
The plugin never transmits any data to me, or anyone else than the mastodon node you set in the settings!
 
== Screenshots ==
 
1. Mastodon Autopost settings page

== Changelog ==
= 2.0 =
* Changed backend libary
* Now working with oauth
* OTP and 2FA working
* Different post formats available
* Set post visibility
* Suggest mastodon server
* Works now also with pages
* Test settings working again
* The toot notification shows the url of the tooted post
* For syndication: The plugin attaches the posted url to the post meta data. The tag is "mastodonAutopostLastSuccessfullPostURL"

= 1.3 =
*	Improved Feedback on post toot
*	Test Settings button, to check server settings
*	Global Hashtags that get added to every toot
*	Minor UX improvements
= 1.1.0.4 =
*	Fixed settings page bug
= 1.1.0.3 =
*	Added information to settings page
= 1.1.0.2 =
*	Updated translation: Français
= 1.1.0.1 =
*	New translation: Français
= 1.1 =
*	Decide per post if you want to toot it to mastodon
= 1.0.1 & 1.0.1.5 =
*	Added easy method for internationalization (get_text)
*	New translation: Deutsch
= 1.0.1 & 1.0.1.1 =
*	Show notfication if post gets tooted
= 1.0 =
*	Inital Plugin
*	New Functions: post on update, post only on publishing
 
== Credits ==

= Mastodon PHP API =
This project is using the [MastodonOAauthPHP libary](https://github.com/TheCodingCompany/MastodonOAuthPHP)

= Graphics =
Thanks to 
*	[Ricardo Gomez Angel](https://unsplash.com/search/wall?photo=2WCT3mg5zlY) - Background Image (CC0)
*	[Flaticon](http://www.flaticon.com/free-icon/send_309395)- icon used in the logo (Flaticon Basis License)
*	[Wikipedia](https://commons.wikimedia.org/wiki/File:msatodon-logo.svg) - Mastodon logo used in the logo (CC0)
*	Bastien Ho & agentcobra - French translation and internationalization help


= Idea =
Special thanks to [Chris Riding](http://www.chrisridings.com/gnu-social-wordpress-plugin/) for inspiring this plugin with his original gnusocial project (GPLv2)
