<?php
//Wordpress Security function
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class mastodon_post_handler{

	//Setup the api_handöer
		function __construct() {
	   		// //Add wordpress hook
		   	 	add_action( 'publish_post', array(&$this, 'mastodon_post_published_notification'), 10, 2 );
		   		add_action( 'admin_notices', array(&$this, 'post_send') );

	   	} 
	//Form the success message
		function post_send() {
			switch (get_post_meta( get_the_ID(), 'mastodonAutopostPostNotification', true )) {
				case 404:
					echo '<div class="notice notice-error is-dismissible">
		       	 		<p>'.esc_html__('Mastodon instance not found! Is the URL correct?', 'autopost-to-mastodon'). ' - <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=mastodon-settings-page">'.esc_attr__('Settings', 'autopost-to-mastodon').'</a></p>
		    		</div>';
		    		update_post_meta( get_the_ID(), 'mastodonAutopostPostNotification', 0);
				break;
				case 401:
					echo '<div class="notice notice-error is-dismissible">
		       	 		<p>'.esc_html__('Could not access mastodon profile! Are email and password correct?', 'autopost-to-mastodon'). ' - <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=mastodon-settings-page">'.esc_attr__('Settings', 'autopost-to-mastodon').'</a></p>
		    		</div>';
		    		update_post_meta( get_the_ID(), 'mastodonAutopostPostNotification', 0);
				break;
				case 999:
					echo '<div class="notice notice-error is-dismissible">
		       	 		<p>'.esc_html__('Uncaught mastodon error! Please contact the developer.', 'autopost-to-mastodon'). ' - <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=mastodon-settings-page">'.esc_attr__('Settings', 'autopost-to-mastodon').'</a></p>
		    		</div>';
		    		update_post_meta( get_the_ID(), 'mastodonAutopostPostNotification', 0);
				break;
				case 200:
					echo '<div class="notice notice-success is-dismissible">
		       	 		<p>'.esc_html__('Tooted to Mastodon!', 'autopost-to-mastodon'). '</p>
		    		</div>';
		    		update_post_meta( get_the_ID(), 'mastodonAutopostPostNotification', 0);
				break;
			}
	}	
	

// Set up a post published notification
    function mastodon_post_published_notification( $ID, $post ) {
    	//Only publish on new post or if the setting for publishing updates is set
       //if ((get_post_meta( $ID, 'autopost_this_post', true) == null || get_post_meta( $ID, 'autopost_this_post', true)) && ($post->post_date == $post->post_modified  || get_option('mastodon_post_on_update') == "1")){
       if(get_post_meta( $ID, 'autopost_this_post', true) ||  (get_post_meta( $ID, 'autopost_this_post', true) == null && ($post->post_date == $post->post_modified  || get_option('mastodon_post_on_update') == "1"))){
            		//Get Global API Object
	       		global $mastodon_api;
       		
       		//Post Data 
		        $title = $post->post_title;
		        $hashtags = get_option('mastodon_post_hashtags');
		        $permalink = get_permalink( $ID );

		    //Mastodon User Settings
		        $email = get_option('mastodon_email');
			    $password = get_option('mastodon_password');
			    $url = get_option('mastodon_server_url');
			
			//Super Basic Posting
				//Login FU
				    $mastodon_api->set_url($url);
					$urlResp = $mastodon_api->create_app('Wordpress Mastodon Autopost',null,$url,$url);
					$mastodon_api->set_client($mastodon_api->get_client_id(),$mastodon_api->get_client_secret());
					$loginResp = $mastodon_api->login($email,$password);
					$mastodon_api->set_token($mastodon_api->get_access_token(),$mastodon_api->get_token_type());

				//Craft the Post
				//Depending of the set post format in settings
					$post_format = 0;
					$parameters = array();

					switch ($post_format) {
						case 1:
							//Title Link and Image
								if ( has_post_thumbnail($ID) ){
       								//Get Image
       									$imageURL = wp_get_attachment_image_url( get_post_thumbnail_id($ID), 'large' ); 
        							
         							//Upload image 
        								$imageData = $mastodon_api->media($imageURL);
 
									$var_info = print_r($imageData, true);

        							//Set image data to post
        								$media_ids = array($imageData['id']);
        								$parameters['media_ids'] = $media_ids;

        								$parameters['status'] = $var_info . $title . " " . $permalink;

        						}

							break;
						
						default:
							//Only title and link
								$postContent = $title . "  " . $permalink . " " . $hashtags;
								$parameters['status'] = substr($postContent,0,500);
								break;
					}

				//Actually send the post
					$postResp = $mastodon_api->post_statuses($parameters);

				if(get_option('mastodon_post_on_update') == "1"){
					update_post_meta( $ID, 'mastodonAutopostPublishedNoRetoot', false);
				}else{
					update_post_meta( $ID, 'mastodonAutopostPublishedNoRetoot', true);
				}

				switch ($postResp['response']) {
						case null:
							//HTTPS(404) -> not found							
							update_post_meta( $ID, 'mastodonAutopostPostNotification', 404);
						break;
						case "404":
							//not found -> URL wrong
							update_post_meta( $ID, 'mastodonAutopostPostNotification', 404);

						break;
						case "401":
							//token wrong -> Username or password wrong
							update_post_meta( $ID, 'mastodonAutopostPostNotification', 401);

						break;
						case "200":
							//success
							update_post_meta( $ID, 'mastodonAutopostPostNotification', 200);
						break;
						default:
							//Uncaught error
							update_post_meta( $ID, 'mastodonAutopostPostNotification', 999);
						break;
				}

    	}
    }


}
?>