<?php
//Ajax Handler for testing the connection to server
add_action('admin_enqueue_scripts', 'mastodon_autopost_enqueue');
function mastodon_autopost_enqueue($hook) {
    if( 'settings_page_mastodon-settings-page' != $hook) return;

    wp_enqueue_style( 'settings-style', plugins_url( '/css/settingsPage.css', __FILE__ ));

      //Form buttons
    wp_enqueue_script( 'validation-script',
        plugins_url( '/js/formValidation.js', __FILE__ ),
        array('jquery')
    );

    //Ajax
    wp_enqueue_script( 'ajax-script',
        plugins_url( '/js/ajax.js', __FILE__ ),
        array('jquery')
    );

    $connection_nonce = wp_create_nonce('connection_example');
    wp_localize_script('ajax-script', 'my_ajax_obj', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $connection_nonce,
    ));

    wp_localize_script('ajax-script', 'notifications_obj', array(
        'notFound' => esc_html__('Mastodon instance not found! Is the URL correct?', 'autopost-to-mastodon'),
        'typeinpopup' => esc_html__('Please login again and copy the shown code into the popup.', 'autopost-to-mastodon'),
        'uncaught' => esc_html__('Uncaught mastodon error! Please contact the developer.', 'autopost-to-mastodon'),
        'errorDataMsg' => esc_html__('Please include the following data in your email:', 'autopost-to-mastodon'),
        'errorData' => esc_html__('Error Data:', 'autopost-to-mastodon'),
        'ok' => esc_html__('Test Toot successfully tooted to Mastodon!', 'autopost-to-mastodon'),
        'testmsg' => esc_html__('By clicking the "Test Settings" Button the following toot will be tooted to your mastodon profile:', 'autopost-to-mastodon').' "'.esc_html__('This is my first toot with the Autopost to Mastodon Wordpress Plugin', 'autopost-to-mastodon').' https://wordpress.org/plugins/autopost-to-mastodon/"'
    ));
}
 ?>