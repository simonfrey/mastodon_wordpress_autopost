<?php 
define("ACCOUNT_CONNECTED",isset($account) && $account !== null);
define("ADVANCED_VIEW",false);
?>


<div class="wrap">
	<h1><?php esc_html_e( 'Mastodon Autopost Configuration', 'autopost-to-mastodon' ); ?></h1>

	
	<br>
	
	<a href="https://github.com/simonfrey/mastodon_wordpress_autopost" target="_blank" class="github-icon" target="_blank">
		<svg aria-hidden="true" class="octicon octicon-mark-github" height="32" version="1.1" viewBox="0 0 16 16" width="32"><path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0 0 16 8c0-4.42-3.58-8-8-8z"></path></svg>
	</a>
	
	<a href="https://paypal.me/51edpo" target="_blank"><img src="<?php echo plugins_url( 'img/paypal.png', __FILE__ );?>" style="height:30px;"></a>
<br>
<br>
	<?php if(ACCOUNT_CONNECTED): ?>
			<input type="button" class="button active tab-button" value="<?php esc_attr_e( 'Simple configuration', 'autopost-to-mastodon' ); ?>" id="hide_advanced_configuration">
			<input type="button" class="button tab-button" value="<?php esc_attr_e( 'Advanced configuration', 'autopost-to-mastodon' ); ?>" id="show_advanced_configuration">
	<?php endif ?>
	<form method="POST">
		<?php wp_nonce_field( 'autopostToMastodon-configuration' ); ?>
		<table class="form-table">
			<tbody>
				<tr style="display:<?php echo !ACCOUNT_CONNECTED ? "block":"none"?>">
					<th scope="row">
						<label for="instance"><?php esc_html_e( 'Instance', 'autopost-to-mastodon' ); ?></label>
					</th>
					<td>
						<input type="text" id="instance" name="instance" size="80" value="<?php esc_attr_e( $instance ); ?>" list="mInstances">
					</td>
					<td>
						<input class="button button-primary" type="submit" value="<?php esc_attr_e( 'Connect to Mastodon', 'autopost-to-mastodon' ); ?>" name="save" id="save">
					</td>
				</tr>
				<tr style="display:<?php echo ACCOUNT_CONNECTED ? "block" : "none"?>">
					<th scope="row">
						<label><?php esc_html_e( 'Status', 'autopost-to-mastodon' ); ?></label>
					</th>
					<td>
						<div class="account">
						<?php if(ACCOUNT_CONNECTED): ?>
								<a href="<?php echo $account->url ?>" target="_blank"><img class="m-avatar" src="<?php echo $account->avatar ?>"></a>
						<?php endif ?>
							<div class="details">
								<?php if(ACCOUNT_CONNECTED): ?>
									<div class="connected"><?php esc_html_e( 'Connected as', 'autopost-to-mastodon' ); ?>&nbsp;<?php echo $account->username ?></div>
									<a class="link" href="<?php echo $account->url ?>" target="_blank"><?php echo $account->url ?></a>

									<p><a href="<?php echo $_SERVER['REQUEST_URI'] . '&disconnect' ?>" class="button"><?php esc_html_e( 'Disconnect', 'autopost-to-mastodon' ); ?></a>
									<a href="<?php echo $_SERVER['REQUEST_URI'] . '&testToot' ?>" class="button"><?php esc_html_e( 'Send test toot', 'autopost-to-mastodon' ); ?></a></p>
								<?php else: ?>
									<div class="disconnected"><?php esc_html_e( 'Disconnected', 'autopost-to-mastodon' ); ?></div>
								<?php endif ?>
							</div>
							<div class="separator"></div>
						</div>
					</td>
				</tr>
				<tr class="advanced_setting">
					<th scope="row">
						<label for="content_warning"><?php esc_html_e( 'Default Content Warning', 'autopost-to-mastodon' ); ?></label>
					</th>
					<td>
						<input type="text" id="content_warning" name="content_warning" style="width:300px" value="<?php esc_attr_e( $content_warning ); ?>">
					</td>
				</tr>
				<tr style="display:<?php echo ACCOUNT_CONNECTED ? "block" : "none"?>">
					<th scope="row">
						<label for="message"><?php esc_html_e( 'Message', 'autopost-to-mastodon' ); ?></label>
					</th>
					<td class="advanced_setting">
						<textarea  rows="10" cols="80" name="message" id="message"><?php esc_html_e( stripslashes( $message ) ); ?></textarea>
						<p class="description"><i><?php esc_html_e( 'You can use these metas in the message', 'autopost-to-mastodon' ); ?></i>
							: [title], [excerpt], [permalink] <?php esc_html_e( 'and', 'autopost-to-mastodon' ); ?> [tags]</p>
					</td>
					<td class="not_advanced_setting messageRadioButtons">
							<label>
                                <b>title</b><br>
                                <a href="">permalink</a><br><br><br>

                                <input type="radio" name="message_template" value="[title]&#10;&#10;[permalink]">
                            </label>
							<label>
                                <b>title</b><br>
                                <a href="">permalink</a><br>#tags<br><br>
                                <input type="radio" name="message_template" value="[title]&#10;&#10;[permalink]&#10;&#10;[tags]">
                            </label>
							<label>
                                <b>title</b><br>
                                <i>Here comes the excerpt...</i><br><a href="">permalink</a><br>
                                #tags<br>
                                <input type="radio" name="message_template" value="[title]&#10;&#10;[excerpt]&#10;&#10;[permalink]&#10;&#10;[tags]">
                            </label>
					</td>
				</tr>
				<tr style="display:<?php echo ACCOUNT_CONNECTED ? "block" : "none"?>">
					<th scope="row">
						<label for="mode"><?php esc_html_e( 'Toot mode', 'autopost-to-mastodon' ); ?></label>
					</th>
					<td class="scopeRadioButtons">
							<label><input type="radio" name="mode" <?php if ( 'public' === $mode ): ?>checked<?php endif; ?> value="public"><img src="<?php echo plugins_url( 'img/post/public.svg', __FILE__ );?>" class="modeIcon"> <?php esc_html_e( 'Public', 'autopost-to-mastodon' ); ?></label>
							<label><input type="radio" name="mode" <?php if ( 'unlisted' === $mode ): ?>checked<?php endif; ?> value="unlisted"><img src="<?php echo plugins_url( 'img/post/unlisted.svg', __FILE__ );?>" class="modeIcon"> <?php esc_html_e( 'Unlisted', 'autopost-to-mastodon' ); ?></label>
							<label><input type="radio" name="mode" <?php if ( 'private' === $mode ): ?>checked<?php endif; ?> value="private"><img src="<?php echo plugins_url( 'img/post/private.svg', __FILE__ );?>" class="modeIcon"> <?php esc_html_e( 'Private', 'autopost-to-mastodon' ); ?></label>
							<label><input type="radio" name="mode" <?php if ( 'direct' === $mode ): ?>checked<?php endif; ?> value="direct"><img src="<?php echo plugins_url( 'img/post/direct.svg', __FILE__ );?>" class="modeIcon"> <?php esc_html_e( 'Direct', 'autopost-to-mastodon' ); ?></label>
					</td>
				</tr>
				<tr class="advanced_setting">
					<th scope="row">
						<label for="size"><?php esc_html_e( 'Toot size', 'autopost-to-mastodon' ); ?></label>
					</th>
					<td>
						<input name="size" id="size" type="number" min="100" max="500" value="<?php esc_attr_e( $toot_size ); ?>"> <?php esc_html_e( 'characters', 'autopost-to-mastodon' ); ?>
					</td>
				</tr>

				<tr style="display:<?php echo ACCOUNT_CONNECTED ? "block" : "none"?>">
					<th scope="row">
						<label for="cats_as_tags"><?php esc_html_e( 'Use categories as tags', 'autopost-to-mastodon' ); ?></label>
					</th>
					<td>
						<input type="checkbox" id="cats_as_tags" name="cats_as_tags" value="on"  <?php echo ( $cats_as_tags  == 'on')?'checked':''; ?>>
					</td>
				</tr>

				<tr style="display:<?php echo ACCOUNT_CONNECTED ? "block" : "none"?>">
					<th scope="row">
						<label for="autopost_standard"><?php esc_html_e( 'Autopost new posts', 'autopost-to-mastodon' ); ?></label>
					</th>
					<td>
						<input type="checkbox" id="autopost_standard" name="autopost_standard" value="on"  <?php echo ( $autopost  == 'on')?'checked':''; ?>>
					</td>
                </tr>
                <tr style="display:<?php echo ACCOUNT_CONNECTED ? "block" : "none"?>">
					<th scope="row">
						<label for="post_types"><?php esc_html_e( 'Choose active post types', 'autopost-to-mastodon' ); ?></label>
                    </th>
                    <td>
                        <fieldset id="post_types">
<?php
    // get all post types
    $args = array(
       'public'   => true,
       // '_builtin' => false,
    );
    $output = 'objects';
    $operator = 'and';
    $wp_post_types = get_post_types( $args, $output, $operator );
    foreach ( $wp_post_types  as $post_type ) {
        $checked = ($post_types[$post_type->name] == 'on')?'checked':'';
        echo "<label for=\"" . $post_type->name . "\"><input type=\"checkbox\" id=\"" . $post_type->name . "\" name=\"" . $post_type->name . "\"" . $checked . " />" . $post_type->label . "</label></br>";
    }
?>
                        </fieldset>
                    </td>
                </tr>
			</tbody>
		</table>

		<?php if(ACCOUNT_CONNECTED): ?>
			<input class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save configuration', 'autopost-to-mastodon' ); ?>" name="save" id="save">
		<?php endif ?>

	</form>

<?php
	require("instanceList.php")
?>
</div>
