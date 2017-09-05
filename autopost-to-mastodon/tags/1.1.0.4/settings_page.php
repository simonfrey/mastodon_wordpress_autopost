<?php
//Wordpress Security function
    defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//Register the settings
 function mastodon_settings_init() {
        // register the settings values to wordpress
            register_setting('mastodon', 'mastodon_server_url');
            register_setting('mastodon', 'mastodon_email');
            register_setting('mastodon', 'mastodon_password');
            register_setting('mastodon', 'mastodon_post_on_update');
     
        // register a new section in the "gnusocial-settings-page" page
            add_settings_section(
                'mastodon_help_func',
                '',
                'mastodon_help_func',
                'mastodon-settings-page'
            );

        // register a new section in the "mastodon-settings-page" page
            add_settings_section(
                'mastodon_server_settings_server_section',
                esc_attr__('Server Settings', 'autopost-to-mastodon'),
                'mastodon_server_settings_description_func',
                'mastodon-settings-page'
            );
     
                // register a new field SERVER URL
                    add_settings_field(
                        'mastodon_server_url_field',
                        esc_attr__('Server URL', 'autopost-to-mastodon'),
                        'mastodon_server_url_func',
                        'mastodon-settings-page',
                        'mastodon_server_settings_server_section'
                    );
             
                // register a new field email
                    add_settings_field(
                        'mastodon_email_field',
                        esc_attr__('Email', 'autopost-to-mastodon'),
                        'mastodon_email_func',
                        'mastodon-settings-page',
                        'mastodon_server_settings_server_section'
                    );
             
                // register a new field PASSWORT
                    add_settings_field(
                        'mastodon_password_field',
                        esc_attr__('Password', 'autopost-to-mastodon'),
                        'mastodon_password_func',
                        'mastodon-settings-page',
                        'mastodon_server_settings_server_section'
                    );

        // register a new section in the "mastodon-settings-page" page
            add_settings_section(
                'mastodon_behavior_section',
                esc_attr__('', 'autopost-to-mastodon'),
                'mastodon_behavior_description_func',
                'mastodon-settings-page'
            );
     
                // register a new field SERVER URL
                    add_settings_field(
                        'mastodon_post_on_update_field',
                        esc_attr__('Also toot on Post Update', 'autopost-to-mastodon'),
                        'mastodon_post_on_update_func',
                        'mastodon-settings-page',
                        'mastodon_behavior_section'
                    );
    }
 
/**
 * Donate
 */
 
    function mastodon_help_func()
    {
        ?>
        <div style="border:2px dashed #000; padding: 1%;">
        <h2><?php esc_html_e('Do you like Mastodon Autopost?','autopost-to-mastodon');?></h2>

       <h4><?php esc_html_e('Yes','autopost-to-mastodon');?>!</h4>
       * <b><?php esc_html_e('Please rate the plugin','autopost-to-mastodon');?>!</b> <?php esc_html_e("For getting the word out, it's important to have good reviews",'autopost-to-mastodon');?>: <a href="https://wordpress.org/plugins/autopost-to-mastodon/" target="_blank"><?php esc_html_e('Rate on Wordpress.org','autopost-to-mastodon');?></a><br>
       * <?php esc_html_e('Consider participating.(e.g. With translating it into another language)','autopost-to-mastodon');?>: <a href="https://github.com/L1am0/mastodon_wordpress_autopost" target="_blank"><?php esc_html_e('Mastodon Autopost is on Github','autopost-to-mastodon');?></a><br>
       * <?php esc_html_e('Want to thank in another way?','autopost-to-mastodon');?>: <a href="http://l1am0.eu/donate.php?p=map" target="_blank"><?php esc_html_e('Buy me a Mate','autopost-to-mastodon');?></a>
       <h4><?php esc_html_e('No','autopost-to-mastodon');?></h4>
       * <?php esc_html_e('Please give me feedback how your experience could be improved','autopost-to-mastodon');?>: <a href="mailto:mastodonautopost@l1am0.eu">mastodonautopost@l1am0.eu</a>
        </div>
        <?php
    }


 
/**
 * Server Settings 
 */
 
// Section Title
    function mastodon_server_settings_description_func()
    {
        esc_html_e('Please provide the account details for the server. The server URL should include http or https and a trailing slash. e.g. http://mastodon.social/', 'autopost-to-mastodon');
    }
 
// Server URL
    function mastodon_server_url_func()
    {
        // get the value of the setting we've registered with register_setting()
        $setting = get_option('mastodon_server_url');
        // output the field
        ?>
        <input type="url" name="mastodon_server_url" value="<?= isset($setting) ? esc_attr($setting) : ''; ?>">
        <?php
    }

 
// email
    function mastodon_email_func()
    {
        // get the value of the setting we've registered with register_setting()
        $setting = get_option('mastodon_email');
        // output the field
        ?>
        <input type="text" name="mastodon_email" value="<?= isset($setting) ? esc_attr($setting) : ''; ?>">
        <?php
    }

 
// Password
    function mastodon_password_func()
    {
        // get the value of the setting we've registered with register_setting()
        $setting = get_option('mastodon_password');
        // output the field
        ?>
        <input type="password" name="mastodon_password" value="<?= isset($setting) ? esc_attr($setting) : ''; ?>">
        <?php
    }




 
/**
 * Behavior Settings 
 */
 
// Section Title
    function mastodon_behavior_description_func()
    {
        esc_html_e('Configure the Plugin Behavior', 'autopost-to-mastodon');
     }
 
// Server URL
    function mastodon_post_on_update_func()
    {
        // get the value of the setting we've registered with register_setting()
        $setting = get_option('mastodon_post_on_update');
        // output the field
        ?>
       
        <input type="checkbox" id="mastodon_post_on_update" name="mastodon_post_on_update" value="1" <?php echo checked( 1, $setting, false ) ?>/>

        <?php
    }


//Add Settings Actions
    add_action('admin_init', 'mastodon_settings_init');




//Add the plugin menu to settings menu
    function mastodon_menu() {
        add_options_page(
            "Mastodon Autopost Settings",
            esc_attr__('Mastodon Autopost Settings', 'autopost-to-mastodon'),
            "administrator",
            "mastodon-settings-page", 
            'mastodon_settings_page'
        );
    }

//Wordpress Standart Settings page
    function mastodon_settings_page() {
         // check user capabilities
         if ( ! current_user_can( 'manage_options' ) ) {
            return;
         }




         
         /* add error/update messages
         
         // check if the user have submitted the settings
         // wordpress will add the "settings-updated" $_GET parameter to the url
         if ( isset( $_GET['settings-updated'] ) ) {
         // add settings saved message with the class of "updated"
         add_settings_error( 'wporg_messages', 'wporg_message', __( 'Settings Saved', 'wporg' ), 'updated' );
         }
         
         // show error/update messages
         settings_errors( 'wporg_messages' );*/
         ?>
         <div class="wrap">
             <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
             <form action="options.php" method="post">
                 <?php
                 // output security fields for the registered setting "wporg"
                    settings_fields( 'mastodon' );
                 // output setting sections and their fields
                 // (sections are registered for "wporg", each field is registered to a specific section)
                    do_settings_sections( 'mastodon-settings-page' );
                 // output save settings button
                    submit_button(esc_attr__('Save Settings', 'autopost-to-mastodon'));
                 ?>
             </form>
         </div>
         <?php
    }    

//Register menu 
    add_action('admin_menu', 'mastodon_menu');

/*
//Shortcut to settings page
    add_filter('plugin_action_links', 'mastodon_autopost_menu_shortcut', 10, 2);

function mastodon_autopost_menu_shortcut($links, $file) {
  

    if (is_admin()) {
        // The "page" query string value must be equal to the slug
        // of the Settings admin page we defined earlier, which in
        // this case equals "myplugin-settings".
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=mastodon-settings-page">'.esc_attr__('Settings', 'autopost-to-mastodon').'</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}

*/


?>