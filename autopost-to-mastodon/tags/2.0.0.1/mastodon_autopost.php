<?php
/**
* Plugin Name: Mastodon Autopost
* Plugin URI: https://github.com/L1am0/mastodon_wordpress_autopost
* Description: A Wordpress Plugin that automatically posts your new articles to Mastodon
* Version: 2.0.0.1
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
   require_once("MastodonOAuthPHP/theCodingCompany/HttpRequest.php");
   require_once("MastodonOAuthPHP/theCodingCompany/oAuth.php");
   require_once("MastodonOAuthPHP/theCodingCompany/Mastodon.php");

	global $mastodon_api;
	$mastodon_api = new \theCodingCompany\Mastodon();

//Enque js
    require("enque_scripts.php");

//Settings Page of Plugin
    require("settings_page.php");

//Meta box for single page - so choose for autopost
    require("post_meta_box.php");

//Published Post Notification with actually sending the post
    require("publish_post_notification.php");

    global $mastodon_post_handler;
    $mastodon_post_handler = new mastodon_post_handler();

//Ajax response handler
    require("ajax_handler.php");  

//Show login error messages
    require("generalNotices.php"); 