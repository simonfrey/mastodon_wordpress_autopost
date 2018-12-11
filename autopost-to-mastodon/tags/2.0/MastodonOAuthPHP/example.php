<?php
/**
 * Intellectual Property of #Mastodon
 * 
 * @copyright (c) 2017, #Mastodon
 * @author V.A. (Victor) Angelier <victor@thecodingcompany.se>
 * @version 1.0
 * @license http://www.apache.org/licenses/GPL-compatibility.html GPL
 * 
 */
require_once("autoload.php");

$t = new \theCodingCompany\Mastodon();

/**
 * Create a new App and get the client_id and client_secret
 */
$token_info = $t->createApp("MyCoolAppName", "http://www.internet.com");


//Get the authorization url
$auth_url = $t->getAuthUrl();
/*
 * 1) Send the above URL '$auth_url' to the user. The need to authorize your App. 
 * 2) When they authorized your app, they will receive a token. The authorization token.
 * 3) Put the authorization token in the request below to exchange it for a bearer token.
 */

//Request the bearer token
$token_info = $t->getAccessToken("7c47d0c636314a1dff21reryyy5edf91884856dc0f78148f848d475136");

/**
 * The above '$token_info' will now be an array with the info like below. (If successfull)
 * No these are not real, your right.
 * 
    {
        "client_id": "87885c2bf1a9d9845345345318d1eeeb1e48bb676aa747d3216adb96f07",
        "client_secret": "a1284899df5250bd345345f5fb971a5af5c520ca2c3e4ce10c203f81c6",
        "bearer": "77e0daa7f252941ae8343543653454f4de8ca7ae087caec4ba85a363d5e08de0d"
    }
 */

/**
 * Authenticate a user by username and password and receive the bearer token
 */
$bearer_token = $t->authUser("vangelier@hotmail.com", "MySecretP@ssW0rd");

/**
 * Get the userinfo by authentication
 */

$user_info = $t->getUser("vangelier@hotmail.com", "MySecretP@ssW0rd");

/**
 * Get user followers / following
 */
$followers = $t->authenticate("vangelier@hotmail.com", "MySecretP@ssW0rd")
                ->getFollowers();

/**
 * Get user statusses
 */
$statusses = $t->authenticate("vangelier@hotmail.com", "MySecretP@ssW0rd")
                ->getStatuses();

