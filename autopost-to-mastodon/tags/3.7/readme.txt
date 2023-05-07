=== Mastodon Autopost ===
Contributors: l1am0, Hellexis
Tags: mastodon, Mastodon, Mastdon Autopost, federated web, GNU social, statusnet, social web, social media, auto post
Requires at least: 4.6
Tested up to: 6.1
Stable tag: 3.7
License: GPLv2
Donate link: https://paypal.me/51edpo
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
A Wordpress Plugin that automatically posts your new articles to Mastodon. The best: It is set and forget! 
 
== Description ==

A Wordpress Plugin that automatically posts your new articles to Mastodon. The best: It is set and forget! 
 
With Mastodon Autopost your post always get automatically posted to your Mastodon account.

Find the plugin settings: Settings > Mastodon Autpost

Just set your credentials and your post preference and lean back. The rest is done in the background and you don't have to care about it.

For any questions, do not hesitate to contact me:
*	Mail: mastodonautopost@simon-frey.com
*	Mastodon: indiehackers.social/@simon

Do you want to help translating this plugin in your language? [Visit the translation page](https://translate.wordpress.org/projects/wp-plugins/autopost-to-mastodon)

Please consider donating via [PayPal](https://paypal.me/51edpo) <3

== Frequently Asked Questions ==
 
= Can I decide per post if I want to autopost it? =
 
Yes. Since version 1.1 you see a settings box in every post.

= Does the plugin send you my login data? =
 
The plugin never transmits any data to me, or anyone else than the mastodon node you set in the settings!
 
== Screenshots ==
 
1. Welcome site if you are not logged in
2. Basic settings
3. Advanced settings

== Changelog ==

= 3.7 = 
* Add content warning from tags
* Fix hashtags

= 3.6 =
* Allow the plugin to work on custom post types (Thanks to [unicode-it](https://github.com/unicode-it))

= 3.5 =
* Use categorys as hashtags on posts (Thanks to [rseabra](https://github.com/rseabra))

= 3.4 =
* Fix for the manual added excerpt to also get encoded and remove html tags

= 3.3.4 =
* Remove toot editor javascript as we do not have a toot editor anymore

= 3.3.3 =
* Change permission for settings pages
* Update for HTML tags striping

= 3.3.2 =
* Remove HTML tags from excerpt (with php strip_tags())

= 3.3 =
* Dutch translation (Thanks to [Alex Belgraver](https://fediversum.nl))

= 3.2.7 =
* Auth workflow change

= 3.2.6 =
* Change permission for settings page to manage_options

= 3.2.5 =
* 5.0 Fix: Post only the tags of the post

= 3.2.4 =
* Add version check to prevent call to undefined 5.0 function

= 3.2.3 =
* fixup for tags
* remove hardcoded `[...]` from post excerpt
* different excerpt function
* Works with 5.0 and Gutenberg Editor
* Create future toot the moment it gets tooted (not upfront)

= 3.2.2 =
* revert change

= 3.2.1 =
* strip_shortcodes to don't toot them

= 3.2 =
* Escape HTML codes in the toots (Thanks to [ojdo](https://github.com/ojdo))
* Leave name for the the links to get nicer permalinks

= 3.1 =
* Avoid empty thumbnail path (Thanks to [ldidry](https://github.com/ldidry)) 

= 3.0 =
* Adapt to the base code of [kernox](https://github.com/kernox/Mastodon-Share-for-WordPress)
* More error handling

= 2.1 =
* Also toot post hashtags (Thanks to [jops](https://mastodon.bida.im/@jops))

= 2.0.9 =
* Added spanish translation

= 2.0.4 =
* Evaluates content HTML before tooting it
* Changed to official naming convention "Mastodon"
* Requires now minimum version 4.6 for translations via [the translation page](https://translate.wordpress.org/projects/wp-plugins/autopost-to-mastodon)

= 2.0.3 =
* Changed to wordpress HTTP library

= 2.0.2 =
* Add proper error messages

= 2.0.1 =
* Removed token prompt in favor of nativ div

= 2.0.0.2 =
* OAuth server communication bug

= 2.0.0.1 =
* Fixed ajax server data bug

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

= Kernox =
This project is baseds on [hellexis Mastodon Share Plugin](https://github.com/kernox/Mastodon-Share-for-WordPress). We are currently working together on a new one to combine our workforce.

= Graphics =
Thanks to 
*	[Ricardo Gomez Angel](https://unsplash.com/search/wall?photo=2WCT3mg5zlY) - Background Image (CC0)
*	[Flaticon](http://www.flaticon.com/free-icon/send_309395)- icon used in the logo (Flaticon Basis License)
*	[Wikipedia](https://commons.wikimedia.org/wiki/File:msatodon-logo.svg) - Mastodon logo used in the logo (CC0)
*	Bastien Ho & agentcobra - French translation and internationalization help
*	jorgesumle - Spanish translation


= Idea =
Special thanks to [Chris Riding](http://www.chrisridings.com/gnu-social-wordpress-plugin/) for inspiring this plugin with his original gnusocial project (GPLv2)
