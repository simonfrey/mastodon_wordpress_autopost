<?php
	add_action( 'admin_notices', 'updateLoginInfo' );

	function updateLoginInfo(){
		if(get_option('mastodon_instance_url') == ""){
			echo '<div class="notice notice-error is-dismissible">
		     	 		<p>'.esc_html__('Seems like you are not logged into Mastodon Autopost! Please check the your login!', 'autopost-to-mastodon'). ' - <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=mastodon-settings-page">'.esc_attr__('Settings', 'autopost-to-mastodon').'</a></p>
		    		</div>';
		}
	}