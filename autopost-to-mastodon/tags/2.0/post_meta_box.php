<?php
// register the meta box
add_action( 'add_meta_boxes', 'mastodon_autopost_metabox' );
function mastodon_autopost_metabox() {
    add_meta_box(
        'mastodon_autopost_metabox',          // this is HTML id of the box on edit screen
        esc_attr__('Mastodon Autopost', 'autopost-to-mastodon'),    // title of the box
        'mastodon_autopost_metabox_content',   // function to be called to display the checkboxes, see the function below
        'post',        // on which edit screen the box should appear
        'normal',      // part of page where the box should appear
        'default'      // priority of the box
    );

    add_meta_box(
        'mastodon_autopost_metabox',          // this is HTML id of the box on edit screen
        esc_attr__('Mastodon Autopost', 'autopost-to-mastodon'),    // title of the box
        'mastodon_autopost_metabox_content',   // function to be called to display the checkboxes, see the function below
        'page',        // on which edit screen the box should appear
        'normal',      // part of page where the box should appear
        'default'      // priority of the box
    );
}

// display the metabox
function mastodon_autopost_metabox_content() {
    // nonce field for security check, you can have the same
    wp_nonce_field( plugin_basename( __FILE__ ), 'mastodon_autopost_nonce' );

    $checked = '';
    if(get_post_meta( get_the_ID(), 'autopost_this_post', true) == null || get_post_meta( get_the_ID(), 'autopost_this_post', true)){
        if(get_post_meta(get_the_ID(), 'mastodonAutopostPublishedNoRetoot', true)){
            update_post_meta( get_the_ID(), 'autopost_this_post', 0 );
            get_post_meta(get_the_ID(), 'mastodonAutopostPublishedNoRetoot', false);
        }else{
            $checked = 'checked';
        }
    }
    echo '<label><input type="checkbox" name="autopost_this_post" id="autopost_this_post" value="1" '.$checked.'/>'. esc_attr__('Post to Mastodon', 'autopost-to-mastodon').'</label>';
}

// save data from checkboxes
function mastodon_autopost_metabox_field_data() {

    // check if this isn't an auto save
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
       return;

    // security check
    if (!isset($_POST['mastodon_autopost_nonce']) || !wp_verify_nonce( $_POST['mastodon_autopost_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    // further checks if you like, 
    // for example particular user, role or maybe post type in case of custom post types

    // now store data in custom fields based on checkboxes selected
    if (isset( $_POST['autopost_this_post'] ) && $_POST['autopost_this_post'] == 1)
        update_post_meta( get_the_ID(), 'autopost_this_post', 1 );
    else
        update_post_meta( get_the_ID(), 'autopost_this_post', 0 );
   
}

//save data hooks - publish_post(9) to get executed before sending the toot
    add_action( 'save_post', 'mastodon_autopost_metabox_field_data',9 );
    add_action( 'publish_post', 'mastodon_autopost_metabox_field_data',9 );
    add_action( 'publish_page', 'mastodon_autopost_metabox_field_data',9 );


?>