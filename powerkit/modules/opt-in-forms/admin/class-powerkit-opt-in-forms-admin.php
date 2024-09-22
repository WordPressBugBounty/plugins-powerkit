<?php
/**
 * The admin-specific functionality of the module.
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Modules/Admin
 */

/**
 * The admin-specific functionality of the module.
 */
class Powerkit_Opt_In_Forms_Admin extends Powerkit_Module_Admin {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'admin_menu', array( $this, 'register_options_page' ) );
	}

	/**
	 * Register admin page
	 *
	 * @since 1.0.0
	 */
	public function register_options_page() {
		add_options_page( esc_html__( 'Opt-in Forms', 'powerkit' ), esc_html__( 'Opt-in Forms', 'powerkit' ), 'manage_options', powerkit_get_page_slug( $this->slug ), array( $this, 'build_options_page' ) );
	}

	/**
	 * Build admin page
	 *
	 * @since 1.0.0
	 */
	public function build_options_page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient rights to view this page.', 'powerkit' ) );
		}

		$this->save_options_page();
		?>

			<div class="wrap pk-wrap">
				<h1><?php esc_html_e( 'Opt-in Forms', 'powerkit' ); ?></h1>

				<div class="pk-settings">
					<form method="post">
						<h3><?php esc_html_e( 'MailChimp', 'powerkit' ); ?></h3>

						<table class="form-table">
							<tbody>
								<!-- API Key -->
								<tr>
									<th scope="row"><label for="powerkit_mailchimp_token"><?php esc_html_e( 'API Key', 'powerkit' ); ?></label></th>
									<td><input class="regular-text" id="powerkit_mailchimp_token" name="powerkit_mailchimp_token" type="text" value="<?php echo esc_attr( get_option( 'powerkit_mailchimp_token' ) ); ?>"></td>
								</tr>
								<tr>
									<td colspan="2">
										<ol>
											<li><?php esc_html_e( 'Log in to your', 'powerkit' ); ?> <?php echo sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( 'https://mailchimp.com' ), esc_html__( 'MailChimp account', 'powerkit' ) ); ?>.</li>
											<li><?php esc_html_e( 'Click your profile name to expand the Account Panel, and choose Account.', 'powerkit' ); ?></li>
											<li><?php esc_html_e( 'Click the Extras drop-down menu and choose API keys.', 'powerkit' ); ?></li>
											<li><?php esc_html_e( 'Copy an existing API key or click the Create A Key button.', 'powerkit' ); ?></li>
											<li><?php esc_html_e( 'Name your key descriptively, so you know what application uses that key.', 'powerkit' ); ?></li>
										</ol>
									</td>
								</tr>
								<!-- Lists -->
								<tr>
									<?php
									$token = get_option( 'powerkit_mailchimp_token' );

									if ( $token ) {

										$response = null;

										$data = powerkit_mailchimp_request(
											'GET', 'lists', array(
												'sort_field' => 'date_created',
												'sort_dir' => 'DESC',
												'count'    => 1000,
											)
										);

										if ( is_array( $data ) ) {
											$response .= isset( $data['type'] ) ? $data['type'] : '';
											$response .= isset( $data['title'] ) ? ':' . $data['title'] : '';
										}

										if ( is_array( $data ) && isset( $data['lists'] ) && $data['lists'] ) {
											?>
											<th scope="row"><label for="powerkit_mailchimp_list"><?php esc_html_e( 'Default Audience', 'powerkit' ); ?></label></th>
											<td>
												<select class="regular-text" name="powerkit_mailchimp_list" id="powerkit_mailchimp_list">
													<option value=""><?php esc_html_e( '- not selected -', 'powerkit' ); ?></option>
													<?php foreach ( $data['lists'] as $item ) : ?>
														<option <?php selected( $item['id'], get_option( 'powerkit_mailchimp_list' ) ); ?> value="<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['name'] ); ?></option>
													<?php endforeach; ?>
												</select>
											</td>
										<?php } else { ?>
											<td colspan="2">
												<code><?php printf( '[%s] %s', esc_html( $response ), esc_html__( 'Invalid API Key or MailChimp access error!', 'powerkit' ) ); ?></code>
											</td>
										<?php } ?>
									<?php } ?>
								</tr>
								<!-- Enable double opt-in -->
								<tr>
									<th scope="row"><label for="powerkit_mailchimp_double_optin"><?php esc_html_e( 'Enable Double opt-in', 'powerkit' ); ?></label></th>
									<td><input class="regular-text" id="powerkit_mailchimp_double_optin" name="powerkit_mailchimp_double_optin" type="checkbox" value="true" <?php checked( (bool) get_option( 'powerkit_mailchimp_double_optin', false ) ); ?>></td>
								</tr>
								<!-- Data Privacy Checkbox Label -->
								<tr>
									<th scope="row">
										<label for="powerkit_mailchimp_privacy">
											<?php esc_html_e( 'Data Privacy Checkbox Label', 'powerkit' ); ?>
											<p class="description"><?php esc_html_e( 'Enter the contents that should display as a label for the data privacy checkbox. Leave blank to disable.', 'powerkit' ); ?></p>
										</label>
									</th>
									<td><textarea class="regular-text" id="powerkit_mailchimp_privacy" name="powerkit_mailchimp_privacy" rows="5"><?php echo esc_html( powerkit_mailchimp_get_privacy_text() ); ?></textarea></td>
								</tr>
							</tbody>
						</table>

						<?php wp_nonce_field(); ?>

						<p class="submit"><input class="button button-primary" name="save_settings" type="submit" value="<?php esc_html_e( 'Save changes', 'powerkit' ); ?>" /></p>
					</form>
				</div>
			</div>
		<?php
	}


	/**
	 * Settings save
	 *
	 * @since 1.0.0
	 */
	protected function save_options_page() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'] ) ) { // Input var ok; sanitization ok.
			return;
		}

		if ( isset( $_POST['save_settings'] ) ) { // Input var ok.

			if ( isset( $_POST['powerkit_mailchimp_token'] ) ) { // Input var ok.
				update_option( 'powerkit_mailchimp_token', sanitize_text_field( wp_unslash( $_POST['powerkit_mailchimp_token'] ) ) ); // Input var ok.
			}
			if ( isset( $_POST['powerkit_mailchimp_list'] ) ) { // Input var ok.
				update_option( 'powerkit_mailchimp_list', sanitize_text_field( wp_unslash( $_POST['powerkit_mailchimp_list'] ) ) ); // Input var ok.
			}
			if ( isset( $_POST['powerkit_mailchimp_double_optin'] ) ) { // Input var ok.
				update_option( 'powerkit_mailchimp_double_optin', true );
			} else {
				update_option( 'powerkit_mailchimp_double_optin', false );
			}
			if ( isset( $_POST['powerkit_mailchimp_privacy'] ) ) { // Input var ok.
				update_option( 'powerkit_mailchimp_privacy', wp_kses( wp_unslash( $_POST['powerkit_mailchimp_privacy'] ), 'post' ) ); // Input var ok. sanitization ok.
			}
			printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Settings saved.', 'powerkit' ) );
		}
	}
}
