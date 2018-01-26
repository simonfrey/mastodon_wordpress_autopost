# MastodonOAuthPHP
PHP Library for Mastodon REST API

## What's in it?

* App creation.
* Full oAuth implementation to authorize your App by users.
* Create and get authorization token, access token, client_id, client_secret and bearer token.
* Authenticate users
* Get user information
* Get user followers and following
* Get user status
* Post status update


## Installation using Composer

```
composer require thecodingcompany/php-mastodon
```

## Questions and example?

Yes, mail me at: vangelier at hotmail dot com
Contact me on #Twitter @digital_human
Contact me on #Mastodon https://mastodon.social/@digitalhuman

## Get started

### Step 1

First step is you need to create a so called App. This app represents your 'service'. With this app you provide services to users or use Mastodon for other reasons.

To create an App is as simple as:

```
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

$serializedData = serialize($token_info);

// save the special tokens to a file, so you don't lose them
file_put_contents('mastodon_creds', $serializedData); // this will save it in the same folder as this file
?>
```

The parameter ```$token_info``` now has your 'client_id' and 'client_secret'. This information is important for the rest of your life ;). Store it in a file, DB or array. You need this everytime you communicate with Mastodon.

### Step 2

Now you (your app) wants to provide services to a user. For this the user needs to authorize your app. Else you can't help him/her. To do this you need to redirect the user, with your tokens to Mastodon and ask for permission so to say. And example:

```
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

$recoveredData = file_get_contents('mastodon_creds');

// unserializing to get actual array
$recoveredArray = unserialize($recoveredData);

$t = new \theCodingCompany\Mastodon();

/**
 * We now have a client_id and client_secret. Set the domain and provide the library with your App's client_id and secret.
 */
$t->setMastodonDomain("mastodon.social"); // Set the mastodon domain, you can remove this line if you're using mastodon.social as it's the default

$t->setCredentials($recoveredArray); // use the keys from the file we stored in Step 1

/**
* Now that is set we can get the Authorization URL and redirect the user to Mastodon
* After the user approves your App, it will return with an Access Token.
*/
$auth_url = $t->getAuthUrl();
header("Location: {$auth_url}", true);
exit;

```

### Step 3

So you now have 3 tokens. The client_id, client_secret and the users access_token. Now exchange the access token for a bearer token and you are done. Save these tokens!

```
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

$recoveredData = file_get_contents('mastodon_creds');

// unserializing to get actual array
$recoveredArray = unserialize($recoveredData);

$t = new \theCodingCompany\Mastodon();

/**
 * We now have a client_id and client_secret. Set the domain and provide the library with your App's client_id and secret.
 */
$t->setMastodonDomain("mastodon.social"); // Set the mastodon domain, you can remove this line if you're using mastodon.social as it's the default

$t->setCredentials(recoveredArray); // use the keys from the file we stored in Step 1

$token_info = $t->getAccessToken("7c47d0c636314a1dff21reryyy5edf91884856dc0f78148f848d475136"); //The access token you received in step 2 from the user.

/**
 * The above '$token_info' will now give you a bearer token (If successfull), you also need to store that and keep it safe!
 * 
*/
```

## Step 4

To then post a status, you just do this:

```
require_once("autoload.php");

$t = new \theCodingCompany\Mastodon();

$t->setMastodonDomain(website address); // change this to whatever Mastodon instance you're using, or remove it entirely if you're using mastodon.social (as it's the default)

$t->setCredentials($credentials); // where $credentials are your "client_id", "client_secret" and "bearer" in the form of an array with those exact names (from what you got in the earlier steps)

$t->postStatus('API Test - PLZ ignore <3');
```

## Full code overview options etc

```
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


/**
* Post status update
*/

$status = $t->authenticate("vangelier@hotmail.com", "MySecretP@ssW0rd")
            ->postStatus("Text status update");

```
