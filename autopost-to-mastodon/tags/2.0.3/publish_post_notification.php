<?php
//Wordpress Security function
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class mastodon_post_handler{

	//Setup the api_handöer
		function __construct() {
	   		// //Add wordpress hook
		   	 	add_action( 'publish_post', array(&$this, 'mastodon_post_published_notification'), 10, 2 );
		   	 	add_action( 'publish_page', array(&$this, 'mastodon_post_published_notification'), 10, 2 );
		   		add_action( 'admin_notices', array(&$this, 'post_send') );

	   	} 
	//Form the success message
		function post_send() {
			switch (get_post_meta( get_the_ID(), 'mastodonAutopostPostNotification', true )) {
				/*case 404:
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
				break;*/
				case 999:

					$mResponse = get_post_meta( get_the_ID(), 'mastodonAutopostMastdonResponse', true );
                    $mData =  '{"status":"-1", "action":"postStatus", "mastodonResponse": '.json_encode($mResponse).', "phpVersion": "'.phpversion().'", "wordpressVersion":"'.get_bloginfo('version').'", "wordpressLanguage":"'.get_bloginfo('language').'"}';

					echo "<div class='notice notice-error is-dismissible'>
		       	 		<p>".esc_html__("Uncaught mastodon error! Please contact the developer.", "autopost-to-mastodon")." 'mastodonautopost@l1am0.eu'<br>".esc_html__("Please include the following data in your email:", "autopost-to-mastodon")."<br>".esc_html__("Error Data:", "autopost-to-mastodon")." <input value='".$mData."' onclick='this.select();'></p></div>";

		       	 		/*. ' - <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=mastodon-settings-page">'.esc_attr__('Settings', 'autopost-to-mastodon').'</a></p>
		    		</div>';*/
		    		update_post_meta( get_the_ID(), 'mastodonAutopostPostNotification', 0);
				break;
				case 200:
					echo '<div class="notice notice-success is-dismissible">
		       	 		<p>'.esc_html__('Tooted to Mastodon!', 'autopost-to-mastodon'). ' <a href="'.get_post_meta( get_the_ID(), 'mastodonAutopostLastSuccessfullPostURL', true ).'" target="_blank">'.esc_html__('Open toot', 'autopost-to-mastodon'). '.</a></p>
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
		        $content = $post->post_content;

		    //Behavior
		        $visibility = get_option('mastodon_post_visibility');
				$postFormat = get_option('mastodon_post_format');

		        //mastodon login fu
                $url = get_option('mastodon_url');;
                $recoveredData = get_option('mastodon_creds');
                // unserializing to get actual array
                $recoveredArray = (array) json_decode($recoveredData);
             	$mastodon_api->setMastodonDomain($url); // Set the mastodon domain, you can remove this line if you're using mastodon.social as it's the default
                $mastodon_api->setCredentials($recoveredArray);


				//Craft the Post
				//Depending of the set post format in settings
					switch ($postFormat) {
						case 1:
							//Title Link and Image
								$titleLen = strlen($title);
								$permaLinkLen = strlen($permalink);
								$hashtagsLen = strlen($hashtags);
								$contetMaxLen = 500 - 7 - $titleLen - $permaLinkLen - $hashtagsLen;

								$shortContent = substr($content,0,$contetMaxLen);

						        $postContentLong = $title . "\n". $shortContent." ...\n". $permalink."\n" . $hashtags;
								$postContent = substr($postContentLong,0,500);
							break;
						
						default:
							//Only title and link
								$postContentLong = $title . "\n" . $permalink . "\n" . $hashtags;
								$postContent = substr($postContentLong,0,500);
								break;
					}

				//Actually send the post
					$postResp = $mastodon_api->postStatus($postContent, $visibility);

				if(get_option('mastodon_post_on_update') == "1"){
					update_post_meta( $ID, 'mastodonAutopostPublishedNoRetoot', false);
				}else{
					update_post_meta( $ID, 'mastodonAutopostPublishedNoRetoot', true);
				}

				if(isset($postResp[id])){
							update_post_meta( $ID, 'mastodonAutopostPostNotification', 200);
							update_post_meta( $ID, 'mastodonAutopostLastSuccessfullPostURL', $postResp['url']);
				}else{
							update_post_meta( $ID, 'mastodonAutopostPostNotification', 999);
							update_post_meta( $ID, 'mastodonAutopostMastdonResponse', $postResp);
				}
					
    	}
    }


}
?>