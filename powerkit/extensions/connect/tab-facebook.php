<?php
/**
 * Connect Tab Facebook
 *
 * @package    Powerkit
 * @subpackage Extensions
 */

if ( powerkit_connect( 'facebook_app_access_token' ) ) {
	?>

	<h3><?php esc_html_e( 'Facebook Account', 'powerkit' ); ?></h3>

	<p><span class="tab-badge-success"><?php esc_html_e( '✓ Account successfully connected', 'powerkit' ); ?></span></p>

	<?php
	$accounts = powerkit_connect( 'facebook_app_accounts' );

	if ( $accounts ) {
		$usernames = array();

		foreach ( $accounts as $account ) {
			if ( isset( $account['username'] ) ) {
				$usernames[] = sprintf( '<code>%s</code>', $account['username'] );
			} else {
				$usernames[] = sprintf( '<code>%s</code>', $account['id'] );
			}
		}

		if ( 1 === count( $usernames ) ) {
			?>
			<p><?php esc_html_e( 'Your Facebook page ID:', 'powerkit' ); ?> <?php echo wp_kses_post( implode( ', ', $usernames ) ); ?><?php esc_html_e( '. Please use this ID in settings when requested.', 'powerkit' ); ?></p>
			<?php
		} else {
			?>
			<p><?php esc_html_e( 'Available Facebook page IDs:', 'powerkit' ); ?> <?php echo wp_kses_post( implode( ', ', $usernames ) ); ?><?php esc_html_e( '. Please use one of these IDs in settings when requested.', 'powerkit' ); ?></p>
			<?php

		}
	}
	?>

	<form method="post" class="form-logout">
		<?php wp_nonce_field(); ?>

		<input type="hidden" name="logout_account_type" value="facebook">

		<p class="submit">
			<input class="button button-primary" name="logout_account" type="submit" value="<?php esc_html_e( 'Disconnect', 'powerkit' ); ?>" />
		</p>
	</form>
<?php } else { ?>

		<h3><?php esc_html_e( 'Facebook Account', 'powerkit' ); ?></h3>

		<p><?php esc_html_e( 'Connect your Facebook account to automatically fetch the number of followers of your Facebook page.', 'powerkit' ); ?></p>
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
					<label for="powerkit_connect_facebook_app_id" class="title">
						<?php esc_html_e( 'Facebook App ID', 'powerkit' ); ?>
					</label>
				</th>
				<td>
					<input class="regular-text" name="powerkit_connect_facebook_app_id" id="powerkit_connect_facebook_app_id" type="text" value="<?php echo esc_attr( get_option( 'powerkit_connect_facebook_app_id' ) ); ?>" />
					<p class="description"><?php esc_html_e( 'The Facebook App ID is required if you want to display Facebook comments or Fanpage block on your site.', 'powerkit' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<?php wp_nonce_field(); ?>

	<p class="submit">
		<input class="button button-primary" name="save_facebook_settings" type="submit" value="<?php esc_html_e( 'Save manual settings', 'powerkit' ); ?>" />
	</p>
</form>
