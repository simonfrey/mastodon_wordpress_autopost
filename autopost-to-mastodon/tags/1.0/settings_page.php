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
     
        // register a new section in the "mastodon-settings-page" page
            add_settings_section(
                'mastodon_server_settings_server_section',
                'Server Settings',
                mastodon_server_settings_description_func,
                'mastodon-settings-page'
            );
     
                // register a new field SERVER URL
                    add_settings_field(
                        'mastodon_server_url_field',
                        'Server URL',
                        mastodon_server_url_func,
                        'mastodon-settings-page',
                        'mastodon_server_settings_server_section'
                    );
             
                // register a new field email
                    add_settings_field(
                        'mastodon_email_field',
                        'Email',
                        mastodon_email_func,
                        'mastodon-settings-page',
                        'mastodon_server_settings_server_section'
                    );
             
                // register a new field PASSWORT
                    add_settings_field(
                        'mastodon_password_field',
                        'Password',
                        mastodon_password_func,
                        'mastodon-settings-page',
                        'mastodon_server_settings_server_section'
                    );

        // register a new section in the "mastodon-settings-page" page
            add_settings_section(
                'mastodon_behavior_section',
                'Plugin Behavior',
                mastodon_behavior_description_func,
                'mastodon-settings-page'
            );
     
                // register a new field SERVER URL
                    add_settings_field(
                        'mastodon_post_on_update_field',
                        'Also post on Post Update',
                        mastodon_post_on_update_func,
                        'mastodon-settings-page',
                        'mastodon_behavior_section'
                    );
    }
 
 
/**
 * Server Settings 
 */
 
// Section Title
    function mastodon_server_settings_description_func()
    {
         echo "Please provide the account details for the server. The server URL should include http or https and a trailing slash. e.g. http://www.smalltexts.com/ ";           
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
         echo "Configure the Plugin Behavior" .get_option('mastodon_post_on_update');           
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
            "Mastodon Autopost Settings",
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
                    submit_button( 'Save Settings' );
                 ?>
             </form>
         </div>
         <?php
    }    

//Register menu 
    add_action('admin_menu', 'mastodon_menu');


   



?>