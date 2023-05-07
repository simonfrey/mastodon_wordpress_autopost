<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

delete_option( 'autopostToMastodon-client-id' );
delete_option( 'autopostToMastodon-client-secret' );
delete_option( 'autopostToMastodon-token' );
delete_option( 'autopostToMastodon-instance' );
delete_option( 'autopostToMastodon-message' );
delete_option( 'autopostToMastodon-mode' );
delete_option( 'autopostToMastodon-toot-size' );
delete_option( 'autopostToMastodon-notice' );
// get all post types
$args = array(
   'public'   => true,
);
$output = 'names';
$operator = 'and';
$post_types = get_post_types( $args, $output, $operator );
// delete configs for all post_types
foreach ( $post_types  as $post_type ) {
    delete_option("autopostToMastodon-post_types-$post_type" );
}
