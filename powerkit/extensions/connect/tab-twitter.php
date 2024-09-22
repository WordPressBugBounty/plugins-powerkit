<?php
/**
 * Connect Tab Twitter
 *
 * @package    Powerkit
 * @subpackage Extensions
 */

// Display information or sign buttons.
if ( powerkit_connect( 'twitter_app_oauth_token' ) ) {
	?>

	<h3><?php esc_html_e( 'Twitter Account', 'powerkit' ); ?></h3>

	<p><span class="tab-badge-success"><?php esc_html_e( '✓ Account', 'powerkit' ); ?> (<a href="https://twitter.com/<?php echo esc_attr( powerkit_connect( 'twitter_app_screen_name' ) ); ?>" target="_blank"><?php echo esc_attr( powerkit_connect( 'twitter_app_screen_name' ) ); ?></a>) <?php esc_html_e( 'successfully connected', 'powerkit' ); ?></span></p>

	<p><?php esc_html_e( 'Your Twitter User ID:', 'powerkit' ); ?> <code><?php echo esc_attr( powerkit_connect( 'twitter_app_screen_name' ) ); ?></code> <?php esc_html_e( 'Please use this ID in settings when requested.', 'powerkit' ); ?></p>

	<form method="post" class="form-logout">
		<?php wp_nonce_field(); ?>

		<input type="hidden" name="logout_account_type" value="twitter">

		<p class="submit">
			<input class="button button-primary" name="logout_account" type="submit" value="<?php esc_html_e( 'Disconnect', 'powerkit' ); ?>" />
		</p>
	</form>
<?php } else { ?>
		<h3><?php esc_html_e( 'Twitter Account', 'powerkit' ); ?></h3>

		<p><?php esc_html_e( 'Connect your Twitter account to display your Twitter feed and the number of followers in Social Links.', 'powerkit' ); ?></p>
	<?php
}

?>

<hr><br>

<p><?php echo sprintf( __( 'You may also change the number of followers manually on <a href="%s" target="_blank">this page</a>.', 'powerkit' ), admin_url( 'options-general.php?page=powerkit_social_links' ) ); ?></p>

<br>

<form class="basic" method="post">
	<h3><?php esc_html_e( 'Manual Settings', 'powerkit' ); ?></h3>

	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="powerkit_connect_twitter_username" class="title">
						<?php esc_html_e( 'Username', 'powerkit' ); ?>
					</label>
				</th>
				<td>
					<input class="regular-text" name="powerkit_connect_twitter_username" id="powerkit_connect_twitter_username" type="text" value="<?php echo esc_attr( get_option( 'powerkit_connect_twitter_username' ) ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="powerkit_connect_twitter_custom_name" class="title">
						<?php esc_html_e( 'Name', 'powerkit' ); ?>
					</label>
				</th>
				<td>
					<input class="regular-text" name="powerkit_connect_twitter_custom_name" id="powerkit_connect_twitter_custom_name" type="text" value="<?php echo esc_attr( get_option( 'powerkit_connect_twitter_custom_name' ) ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="powerkit_connect_twitter_following" class="title">
						<?php esc_html_e( 'Following', 'powerkit' ); ?>
					</label>
				</th>
				<td>
					<input class="regular-text" name="powerkit_connect_twitter_following" id="powerkit_connect_twitter_following" type="number" value="<?php echo esc_attr( get_option( 'powerkit_connect_twitter_following' ) ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="powerkit_connect_twitter_custom_followers" class="title">
						<?php esc_html_e( 'Followers', 'powerkit' ); ?>
					</label>
				</th>
				<td>
					<input class="regular-text" name="powerkit_connect_twitter_custom_followers" id="powerkit_connect_twitter_custom_followers" type="number" value="<?php echo esc_attr( get_option( 'powerkit_connect_twitter_custom_followers' ) ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="powerkit_connect_twitter_custom_avatar" class="title">
						<?php esc_html_e( 'Profile Image', 'powerkit' ); ?>
					</label>
				</th>
				<td>
					<input placeholder="https://example.com/avatar.jpg" class="regular-text" name="powerkit_connect_twitter_custom_avatar" id="powerkit_connect_twitter_custom_avatar" type="text" value="<?php echo esc_attr( get_option( 'powerkit_connect_twitter_custom_avatar' ) ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="powerkit_connect_twitter_custom_avatar_2x" class="title">
						<?php esc_html_e( 'Profile Image (2x)', 'powerkit' ); ?>
					</label>
				</th>
				<td>
					<input placeholder="https://example.com/avatar@2x.jpg" class="regular-text" name="powerkit_connect_twitter_custom_avatar_2x" id="powerkit_connect_twitter_custom_avatar_2x" type="text" value="<?php echo esc_attr( get_option( 'powerkit_connect_twitter_custom_avatar_2x' ) ); ?>" />
				</td>
			</tr>
		</tbody>
	</table>

	<div class="pk-manual-feed-wrap pk-twitter-manual-feed-wrap">
		<h3><?php esc_html_e( 'Manual Feed Settings', 'powerkit' ); ?></h3>

		<div class="pk-manual-feed pk-twitter-manual-feed">
			<?php
			$manual_feed = get_option( 'powerkit_connect_twitter_feed' );

			if ( is_array( $manual_feed ) && ! empty( $manual_feed ) ) {
				foreach ( $manual_feed as $key => $element ) {
					?>
					<div class="pk-element">
						<div class="pk-element-fields">
							<label class="field-text">
								<?php esc_html_e( 'Text', 'powerkit' ); ?>
								<textarea name="powerkit_connect_twitter_feed[<?php echo esc_attr( $key ); ?>][text]" cols="30" rows="2"><?php echo esc_attr( $element['text'] ); ?></textarea>
							</label>
							<label>
								<?php esc_html_e( 'Date', 'powerkit' ); ?>
								<input type="date" name="powerkit_connect_twitter_feed[<?php echo esc_attr( $key ); ?>][date]" value="<?php echo esc_attr( $element['date'] ); ?>">
							</label>
							<label>
								<?php esc_html_e( 'Retweets Count', 'powerkit' ); ?>
								<input type="number" name="powerkit_connect_twitter_feed[<?php echo esc_attr( $key ); ?>][retweets]" value="<?php echo esc_attr( $element['retweets'] ); ?>">
							</label>
							<label>
								<?php esc_html_e( 'Tweet ID', 'powerkit' ); ?>
								<input type="text" name="powerkit_connect_twitter_feed[<?php echo esc_attr( $key ); ?>][tweet_id]" value="<?php echo esc_attr( $element['tweet_id'] ); ?>">
							</label>
							<label>
								<p class="description"><?php esc_html_e( 'Copy the Twitter ID from the share link, for example:', 'powerkit' ); ?> https://twitter.com/codesupplyco/status/<strong>637130509961854976</strong>?s=20</p>
							</label>
						</div>
						<div class="pk-element-actions">
							<span class="dashicons dashicons-sort"></span>

							<a href="#" class="pk-remove-element">
								<?php esc_html_e( 'Delete', 'powerkit' ); ?>
							</a>
						</div>
					</div>
					<?php
				}
			} else {
				?>
				<p class="description pk-msg-empty"><?php esc_html_e( 'The list is empty, you can add new items.', 'powerkit' ); ?></p>
				<?php
			}
			?>
		</div>

		<button class="button pk-add-element"><?php esc_html_e( 'Add New Element', 'powerkit' ); ?></button>
	</div>

	<?php wp_nonce_field(); ?>

	<p class="submit">
		<input class="button button-primary" name="save_twitter_settings" type="submit" value="<?php esc_html_e( 'Save manual settings', 'powerkit' ); ?>" />
	</p>
</form>
