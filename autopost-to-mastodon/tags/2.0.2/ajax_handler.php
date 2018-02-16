<?php
add_action('wp_ajax_get_bearer', 'mastodon_autopost_get_bearer_ajax_handler');
function mastodon_autopost_get_bearer_ajax_handler() {
    check_ajax_referer('connection_example');

    //Testsend
                global $mastodon_api;


            //Mastodon User Settings
                $token = $_POST['token'];


                $recoveredData = get_option('mastodon_creds');
                $url = get_option('mastodon_url');

                // unserializing to get actual array
                $recoveredArray = (array) json_decode($recoveredData);
                


                $mastodon_api->setMastodonDomain($url); // Set the mastodon domain, you can remove this line if you're using mastodon.social as it's the default

                $mastodon_api->setCredentials($recoveredArray);

            //Get bearer
                $bearer = $mastodon_api->getAccessToken($token);



                if($bearer){
                    $recoveredArray['bearer'] = $bearer;
                    $serializedData = json_encode($recoveredArray);
                    update_option( 'mastodon_creds', $serializedData );

                    echo '{"status":"0", "action":"getBearer"}';
                }else{
                    echo '{"status":"-1", "action":"getBearer", "mastodonResponse": '.json_encode($bearer).', "phpVersion": "'.phpversion().'", "wordpressVersion":"'.get_bloginfo('version').'", "wordpressLanguage":"'.get_bloginfo('language').'"}';
                }

    wp_die(); // all ajax handlers should die when finished
}

add_action('wp_ajax_test_connection', 'mastodon_autopost_ajax_handler');
function mastodon_autopost_ajax_handler() {
    check_ajax_referer('connection_example');

    //Testsend
                global $mastodon_api;

                $url = get_option('mastodon_url');
;
                $recoveredData = get_option('mastodon_creds');
;
                // unserializing to get actual array
                $recoveredArray = (array) json_decode($recoveredData);
                


                $mastodon_api->setMastodonDomain($url); // Set the mastodon domain, you can remove this line if you're using mastodon.social as it's the default

                $mastodon_api->setCredentials($recoveredArray);

                //Post Data 
                $title = esc_html__('This is my first toot with the Autopost to Mastodon Wordpress Plugin', 'autopost-to-mastodon');
                $permalink = "https://wordpress.org/plugins/autopost-to-mastodon/";

            
            //Testpost
                $response = $mastodon_api->postStatus($title . "  " . $permalink);

    
                if(isset($response['id'])){
                    echo '{"status":"0", "action":"testConnection", "serverURL": "'.$response['url'].'"}';
                }else{
                    echo '{"status":"-1", "action":"testConnection", "mastodonResponse": '.json_encode($response).', "phpVersion": "'.phpversion().'", "wordpressVersion":"'.get_bloginfo('version').'", "wordpressLanguage":"'.get_bloginfo('language').'"}';
                }

                  
    wp_die(); // all ajax handlers should die when finished
}

add_action('wp_ajax_user_auth', 'mastodon_autopost_userAuth_ajax_handler');
function mastodon_autopost_userAuth_ajax_handler() {
    check_ajax_referer('connection_example');

    //Testsend
                global $mastodon_api;

                $url = $_POST['serverURL'];
            
            //Set the send url
            $mastodon_api->setMastodonDomain($url);

            //Create mastodon app
            $token_info = $mastodon_api->createApp("Wordpress Mastodon Autopost", "https://wordpress.org/plugins/autopost-to-mastodon/");
            
            $serializedData = json_encode($token_info);
            // save the special tokens to a file, so you don't lose them
            update_option( 'mastodon_creds', $serializedData );
            update_option( 'mastodon_url', $url );

            $mastodon_api->setCredentials($token_info);

            
            //Get the auth url
            $auth_url = $mastodon_api ->getAuthUrl();

            
            echo '{"status":"0", "action":"userAuth", "authUrl": "'.$auth_url.'", "phpVersion": "'.phpversion().'", "wordpressVersion":"'.get_bloginfo('version').'", "wordpressLanguage":"'.get_bloginfo('language').'"}';
              
    wp_die(); // all ajax handlers should die when finished
}
?>