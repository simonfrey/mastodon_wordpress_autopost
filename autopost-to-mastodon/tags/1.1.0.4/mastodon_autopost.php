<?php
/**
* Plugin Name: Mastodon Autopost
* Plugin URI: https://github.com/L1am0/mastodon_wordpress_autopost
* Description: A Wordpress Plugin that automatically posts your new articles to Mastodon
* Version: 1.1.0.4
* Author: L1am0
* Author URI: http://www.l1am0.eu
* License: GPL2
* Text Domain: autopost-to-mastodon
* Domain Path: /languages
*/


//Wordpress Security function
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


//Plugin internationalization hook
   require("internationalization.php");

//Get the mastodon api
    require("mastodon_wordpress_api/mastodon_wordpress_api.php");

	global $mastodon_api;
	$mastodon_api = new mastodon_wordpress_networking_api();

//Settings Page of Plugin
    require("settings_page.php");

//Meta box for single page - so choose for autopost
    require("post_meta_box.php");

//Published Post Notification with actually sending the post
    require("publish_post_notification.php");

    global $mastodon_post_handler;
    $mastodon_post_handler = new mastodon_post_handler();
