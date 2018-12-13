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