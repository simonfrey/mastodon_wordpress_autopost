<?php
function mastodon_autopost_load_plugin_textdomain() {
	load_plugin_textdomain( 'mastodon-autopost-TD', FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'mastodon_autopost_load_plugin_textdomain' );
?>