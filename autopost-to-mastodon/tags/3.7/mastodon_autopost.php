<?php
/**
 * Plugin Name: Mastodon Autopost
 * Plugin URI: https://github.com/simonfrey/mastodon_wordpress_autopost
 * Description: A Wordpress Plugin that automatically posts your new articles to Mastodon
 * Version: 3.7
 * Author: L1am0
 * Author URI: https://www.simon-frey.com
 * License: GPL2
 * Text Domain: autopost-to-mastodon
 * Domain Path: /languages
 */

require_once 'client.php';

class autopostToMastodon
{
    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_menu', array($this, 'configuration_page'));
        add_action('save_post', array($this, 'toot_post'));
        add_action('admin_notices', array($this, 'admin_notices'));
        add_action('add_meta_boxes', array($this, 'add_metabox'));
        add_action('publish_future_post', array($this, 'toot_scheduled_post'));
        add_action('wp_ajax_get_toot_preview', array($this, 'get_toot_preview_ajax_handler'));

    }

    /**
     * Init
     *
     * Plugin initialization
     *
     * @return void
     */
    public function init()
    {
        $plugin_dir = basename(dirname(__FILE__));
        load_plugin_textdomain('autopost-to-mastodon', false, $plugin_dir . '/languages');

        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            $client_id = get_option('autopostToMastodon-client-id');
            $client_secret = get_option('autopostToMastodon-client-secret');

            if (!empty($code) && !empty($client_id) && !empty($client_secret)) {
                echo __('Authentification, please wait', 'autopost-to-mastodon') . '...';

                update_option('autopostToMastodon-token', 'nada');

                $instance = get_option('autopostToMastodon-instance');
                $client = new Client($instance);
                $token = $client->get_bearer_token($client_id, $client_secret, $code, get_admin_url());

                if (isset($token->error)) {
                    print_r($token);
                    //TODO: Propper error message
                    update_option(
                        'autopostToMastodon-notice',
                        serialize(
                            array(
                                'message' => '<strong>Mastodon Autopost</strong> : ' . __("Can't log you in.", 'autopost-to-mastodon') .
                                '<p><strong>' . __('Instance message', 'autopost-to-mastodon') . '</strong> : ' . $token->error_description . '</p>',
                                'class' => 'error',
                            )
                        )
                    );
                    unset($token);
                    update_option('autopostToMastodon-token', '');
                } else {
                    update_option('autopostToMastodon-client-id', '');
                    update_option('autopostToMastodon-client-secret', '');
                    update_option('autopostToMastodon-token', $token->access_token);
                }
                $redirect_url = get_admin_url() . 'options-general.php?page=autopost-to-mastodon';
            } else {
                //Probably hack or bad refresh, redirect to homepage
                $redirect_url = home_url();
            }

            wp_redirect($redirect_url);
            exit;
        }

        $token = get_option('autopostToMastodon-token');
        if (empty($token)) {
            update_option(
                'autopostToMastodon-notice',
                serialize(
                    array(
                        'message' => '<strong>Mastodon Autopost</strong> : ' . __('Please login to your mastodon account!', 'autopost-to-mastodon') . '<a href="' . get_admin_url() . 'options-general.php?page=autopost-to-mastodon"> ' . __('Go to Mastodon Autopost Settings', 'autopost-to-mastodon') . '</a>',
                        'class' => 'error',
                    )
                )
            );
        }

    }

    /**
     * Enqueue_scripts
     *
     * @return void
     */
    public function enqueue_scripts($hook)
    {

        global $pagenow;

        $infos = get_plugin_data(__FILE__);
        if ($pagenow == "options-general.php") {
            //We might be on settings page <-- Do you know a bette solution to get if we are in our own settings page?
            $plugin_url = plugin_dir_url(__FILE__);
            wp_enqueue_script('settings_page', $plugin_url . 'js/settings_page.js', array('jquery'), $infos['Version'], true);

        }
    }

    /**
     * Configuration_page
     *
     * Add the configuration page menu
     *
     * @return void
     */
    public function configuration_page()
    {
        add_options_page(
            'Mastodon Autopost',
            'Mastodon Autopost',
            'manage_options',
            'autopost-to-mastodon',
            array($this, 'show_configuration_page')
        );
    }

    /**
     * Show_configuration_page
     *
     * Content of the configuration page
     *
     * @throws Exception The exception.
     * @return void
     */
    public function show_configuration_page()
    {

        wp_enqueue_style('autopostToMastodon-configuration', plugin_dir_url(__FILE__) . 'style.css');

        if (isset($_GET['disconnect'])) {
            update_option('autopostToMastodon-token', '');
        } elseif (isset($_GET['testToot'])) {
            $this->sendTestToot();
        }

        $token = get_option('autopostToMastodon-token');

        if (isset($_POST['save'])) {

            $is_valid_nonce = wp_verify_nonce($_POST['_wpnonce'], 'autopostToMastodon-configuration');

            if ($is_valid_nonce) {
                $instance = esc_url($_POST['instance']);
                if (strpos($instance, 'http') !== 0) {
                    $instance = "https://"+$instance;
                }
                $message = stripslashes($_POST['message']);
                $content_warning = $_POST['content_warning'];

                $client = new Client($instance);
                $redirect_url = get_admin_url();
                $auth_url = $client->register_app($redirect_url);

                if (strpos($auth_url, 'ERROR') === 0) {
                    update_option(
                        'autopostToMastodon-notice',
                        serialize(
                            array(
                                'message' => '<strong>Mastodon Autopost</strong> : ' . __('The given instance url belongs to no valid mastodon instance !', 'autopost-to-mastodon'). " ".$auth_url,
                                'class' => 'error',
                            )
                        )
                    );

                } else {

                    if (empty($instance)) {
                        update_option(
                            'autopostToMastodon-notice',
                            serialize(
                                array(
                                    'message' => '<strong>Mastodon Autopost</strong> : ' . __('Thank you to set your Mastodon instance before connect !', 'autopost-to-mastodon'),
                                    'class' => 'error',
                                )
                            )
                        );
                    } else {
                        update_option('autopostToMastodon-client-id', $client->get_client_id());
                        update_option('autopostToMastodon-client-secret', $client->get_client_secret());
                        update_option('autopostToMastodon-instance', $instance);

                        update_option('autopostToMastodon-message', sanitize_textarea_field($message));
                        update_option('autopostToMastodon-mode', sanitize_text_field($_POST['mode']));
                        update_option('autopostToMastodon-toot-size', (int) $_POST['size']);

                        if (isset($_POST['autopost_standard'])) {
                            update_option('autopostToMastodon-postOnStandard', 'on');
                        } else {
                            update_option('autopostToMastodon-postOnStandard', 'off');
                        }


                        if (isset($_POST['cats_as_tags'])) {
                            update_option('autopostToMastodon-catsAsTags', 'on');
                        } else {
                            update_option('autopostToMastodon-catsAsTags', 'off');
                        }

                        // get all post types
                        $args = array(
                           'public'   => true,
                        );
                        $output = 'names';
                        $operator = 'and';
                        $post_types = get_post_types( $args, $output, $operator );
                        // check post for content type configs
                        foreach ( $post_types  as $post_type ) {
                            if (isset($_POST[$post_type]) ? $_POST[$post_type] : "off" == "on") {
                                update_option("autopostToMastodon-post_types-$post_type", 'on');
                            }
                            else {
                                update_option("autopostToMastodon-post_types-$post_type", 'off');
                            }
                        }

                        update_option('autopostToMastodon-content-warning', sanitize_textarea_field($content_warning));

                        $account = $client->verify_credentials($token);

                        if (isset($account->error)) {
                            echo '<meta http-equiv="refresh" content="0; url=' . $auth_url . '" />';
                            echo __('Redirect to ', 'autopost-to-mastodon') . $instance;
                            exit;
                        }

                        //Inform user that save was successfull
                        update_option(
                            'autopostToMastodon-notice',
                            serialize(
                                array(
                                    'message' => '<strong>Mastodon Autopost</strong> : ' . __('Configuration successfully saved !', 'autopost-to-mastodon'),
                                    'class' => 'success',
                                )
                            )
                        );

                    }
                }

                $this->admin_notices();
            }
        }

        $instance = get_option('autopostToMastodon-instance');

        if (!empty($token)) {
            $client = new Client($instance);
            $account = $client->verify_credentials($token);
        }

        $message = get_option('autopostToMastodon-message', "[title]\n[excerpt]\n[permalink]\n[tags]");
        $mode = get_option('autopostToMastodon-mode', 'public');
        $toot_size = get_option('autopostToMastodon-toot-size', 500);
        $content_warning = get_option('autopostToMastodon-content-warning', '');
        $autopost = get_option('autopostToMastodon-postOnStandard', 'on');
        $cats_as_tags = get_option('autopostToMastodon-catsAsTags', 'on');
        $post_types = [];

        // get all post types
        $args = array(
           'public'   => true,
        );
        $output = 'names';
        $operator = 'and';
        $wp_post_types = get_post_types( $args, $output, $operator );

        // add form context data for post type options
        foreach ( $wp_post_types  as $post_type ) {
            $post_types[$post_type] = get_option("autopostToMastodon-post_types-$post_type", 'on');
        }

        include 'form.tpl.php';
    }

    /**
     * Toot_post
     * Post the toot
     *
     * @param int $id The post ID.
     * @return void
     */
    public function toot_post($id)
    {

        $post = get_post($id);

        $thumb_url = get_the_post_thumbnail_url($id, 'medium_large'); //Don't change the resolution !

        $toot_size = (int) get_option('autopostToMastodon-toot-size', 500);

        $toot_on_mastodon_option = false;
        $cw_content = (string) get_option('autopostToMastodon-content-warning', '');

        // check for cw-tags:
        //  CW
        //  CN
        //  CW: $reason
        //  CN: $reason
        // are recognized and added to $cw_content
        $post_tags = get_the_tags($id);
        if ($post_tags) {
            foreach ($post_tags as $tag) {
                if(preg_match('/^(?:CW|CN)[: ].+/', $tag->name)) {
                    $cw_content = $tag->name." ".$cw_content;
                } else if($tag->name == "CN") {
                    $cw_content = "CN (Content Notice) ".$cw_content;
                } else if($tag->name == "CW") {
                    $cw_content = "CW (Content Warning) ".$cw_content;
                }
            }
        }

        $toot_on_mastodon_option = isset($_POST['toot_on_mastodon']);

        if ($toot_on_mastodon_option) {
            update_post_meta($id, 'autopostToMastodon-post-status', 'on');
        } else {
            if (get_post_meta($id, 'autopostToMastodon-post-status', true) == 'on') {
                update_post_meta($id, 'autopostToMastodon-post-status', 'off');
            }
        }


        // Only toot once to prevent bans of people on instances that forbid this
        if (get_post_meta($id, 'autopostToMastodonshare-lastSuccessfullTootURL', true) != "") {
            return;
        }

        if ($toot_on_mastodon_option) {
            $message = $this->getTootFromTemplate($id);

            if (!empty($message)) {

                //Save the toot, for scheduling
                if ($post->post_status == 'future') {
                    update_post_meta($id, 'autopostToMastodon-toot', $message);

                    if ($thumb_url) {

                        $thumb_path = str_replace(get_site_url(), get_home_path(), $thumb_url);
                        update_post_meta($id, 'autopostToMastodon-toot-thumbnail', $thumb_path);
                    }

                    update_option(
                        'autopostToMastodon-notice',
                        serialize(
                            array(
                                'message' => '<strong>Mastodon Autopost</strong> : ' . __('Toot saved for schedule !', 'autopost-to-mastodon'),
                                'class' => 'info',
                            )
                        )
                    );
                } else if ($post->post_status == 'publish') {

                    $instance = get_option('autopostToMastodon-instance');
                    $access_token = get_option('autopostToMastodon-token');
                    $mode = get_option('autopostToMastodon-mode', 'public');

                    $client = new Client($instance, $access_token);

                    if ($thumb_url) {

                        $thumb_path = str_replace(get_site_url(), get_home_path(), $thumb_url);
                        // read alt-text from thumbnail and use it as image description for mastodon
                        $thumb = get_the_post_thumbnail($id);
                        $thumb_alt = preg_replace('/^.*?alt="(.*?)".*$/', '$1', $thumb);

                        $attachment = $client->create_attachment($thumb_path);

                        if (is_object($attachment)) {
                            $media = $attachment->id;
                        }
                    }

                    $toot = $client->postStatus($message, $mode, $media, $cw_content);

                    update_post_meta($id, 'autopostToMastodon-post-status', 'off');

                    add_action('admin_notices', 'autopostToMastodon_notice_toot_success');
                    if (isset($toot->errors)) {
                        update_option(
                            'autopostToMastodon-notice',
                            serialize(
                                array(
                                    'message' => '<strong>Mastodon Autopost</strong> : ' . __('Sorry, can\'t send toot !', 'autopost-to-mastodon') .
                                    '<p><strong>' . __('Instance message', 'autopost-to-mastodon') . '</strong> : ' . json_encode($toot->errors) . '</p>',
                                    'class' => 'error',
                                )
                            )
                        );
                    } else {
                        update_option(
                            'autopostToMastodon-notice',
                            serialize(
                                array(
                                    'message' => '<strong>Mastodon Autopost</strong> : ' . __('Toot successfully sent !', 'autopost-to-mastodon') . ' <a href="' . $toot->url . '" target="_blank">' . __('View Toot', 'autopost-to-mastodon') . '</a>',
                                    'class' => 'success',
                                )
                            )
                        );
                        //Save the toot url for syndication
                        update_post_meta($id, 'autopostToMastodonshare-lastSuccessfullTootURL', $toot->url);
                    }
                }

            }
        }
    }

    /**
     * Toot_scheduled_post
     * @param  integer $post_id
     */
    public function toot_scheduled_post($post_id)
    {
        $instance = get_option('autopostToMastodon-instance');
        $access_token = get_option('autopostToMastodon-token');
        $mode = get_option('autopostToMastodon-mode', 'public');

        $message = $this->getTootFromTemplate($post_id);
        if (!empty($message)) {

            $thumb_url = get_the_post_thumbnail_url($post_id);
            $thumb_path = get_post_meta($post_id, 'autopostToMastodon-toot-thumbnail', true);

            $client = new Client($instance, $access_token);

            if ($thumb_url && $thumb_path) {

                // read alt-text from thumbnail and use it as image description for mastodon
                $thumb = get_the_post_thumbnail($post_id, 'medium_large');
                $thumb_alt = preg_replace('/^.*?alt="(.*?)".*$/', '$1', $thumb);

                $attachment = $client->create_attachment($thumb_path);

                if (is_object($attachment)) {
                    $media = $attachment->id;
                }
            }

            $toot = $client->postStatus($message, $mode, $media);
        }
    }

    /**
     * Admin_notices
     * Show the notice (error or info)
     *
     * @return void
     */
    public function admin_notices()
    {

        $notice = unserialize(get_option('autopostToMastodon-notice'));

        if (is_array($notice)) {
            echo '<div class="notice notice-' . sanitize_html_class($notice['class']) . ' is-dismissible"><p>' . $notice['message'] . '</p></div>';
            update_option('autopostToMastodon-notice', null);
        }
    }

    /**
     * Add_metabox
     *
     * @return void
     */
    public function add_metabox()
    {
        $active_post_types = [];
        // get all post types
        $args = array(
           'public'   => true,
        );
        $output = 'names';
        $operator = 'and';
        $post_types = get_post_types( $args, $output, $operator );

        // add form context data for post type options
        foreach ( $post_types  as $post_type ) {
            if (get_option("autopostToMastodon-post_types-$post_type", 'on') == 'on') {
                array_push($active_post_types, $post_type);
            }
        }

        // empty array activates everywhere -> check
        if (!empty($active_post_types)) {
            add_meta_box(
                'autopostToMastodon_metabox',
                'Mastodon Autopost',
                array($this, 'metabox'),
                $active_post_types,
                'side',
                'high'
            );
        }
    }

    /**
     * Metabox
     *
     * @param WP_Post $post the current post.
     * @return void
     */
    public function metabox($post)
    {
        if ($post->post_title == '' && $post->post_content == '') {
            $status = ('on' == (string) get_option('autopostToMastodon-postOnStandard', true));
        } else {
            $status = ('on' == (string) get_post_meta($post->ID, 'autopostToMastodon-post-status', 'off'));
        }

        $checked = ($status) ? 'checked' : '';

        echo '<div style="margin: 20px 0;"><input ' . $checked . ' type="checkbox" name="toot_on_mastodon" id="toot_on_mastodon" value="on">' .
        '<label for="toot_on_mastodon">' . __('Toot on Mastodon', 'autopost-to-mastodon') . '</label></div>';

    }

    public function get_toot_preview_ajax_handler()
    {
        check_ajax_referer('mastodonNonce');

        $return = array(
            'message' => $this->getTootFromTemplate($_POST['post_ID']),
        );

        wp_send_json($return);
    }

    private function fixHashTag($tag)
    {
        $tag = html_entity_decode($tag, ENT_COMPAT, 'UTF-8');
        if (preg_match('/\s/', $tag)) {
           $tag = ucwords($tag);
        }
        $tag = preg_replace('/[^[:alnum:]_]/', '', $tag);
        return '#' . $tag;
    }
        
    private function getTootFromTemplate($id)
    {

        $post = get_post($id);
        $toot_size = (int) get_option('autopostToMastodon-toot-size', 500);

        $message_template = get_option('autopostToMastodon-message', "[title]\n[excerpt]\n[permalink]\n[tags]");

        //Replace title
        $post_title = html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8');
        $message_template = str_replace("[title]", $post_title, $message_template);

        //Replace permalink
        $post_permalink = get_the_permalink($id);
        $message_template = str_replace("[permalink]", $post_permalink, $message_template);

        //Replace tags
        $post_tags_content = '';
        $cats_as_tags = get_option('autopostToMastodon-catsAsTags', 'off');
        if ($cats_as_tags == 'on') {
            $post_cats = get_the_category($id);
            if (sizeof($post_cats) > 0 && $post_cats) {
                foreach ($post_cats as $cat) {
                    $post_tags_content = $post_tags_content . $this->fixHashTag($cat->name) . ' ';
                }
            }
        }

        $post_tags = get_the_tags($id);
        if ($post_tags) {
            foreach ($post_tags as $tag) {
                $post_tags_content = $post_tags_content . $this->fixHashTag($tag->name) . ' ';
            }
            $post_tags_content = trim($post_tags_content);
        }
        $message_template = str_replace("[tags]", $post_tags_content, $message_template);

        //Replace excerpt
        //Replace with the excerpt of the post
        $post_optional_excerpt = $post->post_excerpt;
        if (strlen($post_optional_excerpt) > 0) {
            $post_content_long = $post_optional_excerpt;
        } else {
            $post_content_long = $post->post_content;
        }
        if ($wp_version[0] == "5") {
            $post_content_long = excerpt_remove_blocks($post_content_long);
        }
        $post_content_long = strip_shortcodes($post_content_long);
        $post_content_long = html_entity_decode($post_content_long, ENT_COMPAT, 'UTF-8');
        $post_content_long = wp_strip_all_tags($post_content_long);
        //$post_content_long = str_replace("...", "",$post_content_long);

        $excerpt_len = $toot_size - strlen($message_template) + 9 - 5;

        mb_internal_encoding("UTF-8");
      
        $post_excerpt = mb_substr($post_content_long, 0, $excerpt_len);

        $message_template = str_replace("[excerpt]", $post_excerpt, $message_template);

        return mb_substr($message_template, 0, $toot_size);
    }

    private function sendTestToot()
    {
        $instance = get_option('autopostToMastodon-instance');
        $access_token = get_option('autopostToMastodon-token');
        $mode = 'public';

        $client = new Client($instance, $access_token);
        //TODO: Add propper message
        $message = __("This is my first post with Mastodon Autopost plugin for wordpress", 'autopost-to-mastodon') . " - https://wordpress.org/plugins/autopost-to-mastodon/";
        $media = null;
        $toot = $client->postStatus($message, $mode, $media);

        if (isset($toot->error)) {
            update_option(
                'autopostToMastodon-notice',
                serialize(
                    array(
                        'message' => '<strong>Mastodon Autopost</strong> : ' . __('Sorry, can\'t send toot !', 'autopost-to-mastodon') .
                        '<p><strong>' . __('Instance message', 'autopost-to-mastodon') . '</strong> : ' . $toot->error . '</p>',
                        'class' => 'error',
                    )
                )
            );
        } else {
            update_option(
                'autopostToMastodon-notice',
                serialize(
                    array(
                        'message' => '<strong>Mastodon Autopost</strong> : ' . __('Toot successfully sent !', 'autopost-to-mastodon') . ' <a href="' . $toot->url . '" target="_blank">' . __('View Toot', 'autopost-to-mastodon') . '</a>',
                        'class' => 'success',
                    )
                )
            );
        }
        $this->admin_notices();
    }
}

$autopostToMastodon = new autopostToMastodon();
