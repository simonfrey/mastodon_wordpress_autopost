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
			if(get_post_meta( get_the_ID(), 'mastodonAutopostNotifiePostSend', true ) == true){
				echo '<div class="notice notice-success is-dismissible">
	       	 		<p>Tooted to Mastodon!</p>
	    		</div>';
	    		update_post_meta( get_the_ID(), 'mastodonAutopostNotifiePostSend', false);
			}
	}	
	

// Set up a post published notification
    function mastodon_post_published_notification( $ID, $post ) {
    	//Only publish on new post or if the setting for publishing updates is set
       if ( $post->post_date == $post->post_modified  || get_option('mastodon_post_on_update') == "1"){
       		//Get Global API Object
	       		global $mastodon_api;
       		
       		//Post Data 
		        $title = $post->post_title;
		        $permalink = get_permalink( $ID );

		    //Mastodon User Settings
		        $email = get_option('mastodon_email');
			    $password = get_option('mastodon_password');
			    $url = get_option('mastodon_server_url');
			
			//Super Basic Posting
				//Login FU
				    $mastodon_api->set_url($url);
					$mastodon_api->create_app('Wordpress Autopost',null,$url,$url);
					$mastodon_api->set_client($mastodon_api->get_client_id(),$mastodon_api->get_client_secret());
					$mastodon_api->login($email,$password);
					$mastodon_api->set_token($mastodon_api->get_access_token(),$mastodon_api->get_token_type());

				//Craft the Post
					$parameters = array();
					$parameters['status'] = $title . "  " . $permalink;

				//Actually send the post
					$mastodon_api->post_statuses($parameters);

			//Notfiy user about toot
				update_post_meta( $ID, 'mastodonAutopostNotifiePostSend', true);
    	}
    }


}
?>