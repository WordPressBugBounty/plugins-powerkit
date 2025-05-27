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
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_powerkit_refresh_kit_forms', array( $this, 'ajax_refresh_kit_forms' ) );
		add_action( 'wp_ajax_powerkit_refresh_mailchimp_lists', array( $this, 'ajax_refresh_mailchimp_lists' ) );
		add_action( 'wp_ajax_powerkit_refresh_mailerlite_groups', array( $this, 'ajax_refresh_mailerlite_groups' ) );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param string $hook The current admin page.
	 */
	public function admin_enqueue_scripts( $hook ) {
		// Only load on opt-in forms settings page.
		if ( 'settings_page_powerkit_opt_in_forms' !== $hook ) {
			return;
		}

		wp_enqueue_script(
			'powerkit-opt-in-forms-admin',
			plugin_dir_url( __FILE__ ) . 'js/admin-powerkit-opt-in-forms.js',
			array( 'jquery' ),
			powerkit_get_setting( 'version' ),
			true
		);

		// Localize the script with a nonce.
		wp_localize_script(
			'powerkit-opt-in-forms-admin',
			'powerkit_opt_in_forms',
			array(
				'nonce' => wp_create_nonce(),
			)
		);
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

		// Get selected service.
		$selected_service = get_option( 'powerkit_subscription_service', 'mailchimp' );
		?>

			<div class="wrap pk-wrap">
				<h1><?php esc_html_e( 'Opt-in Forms', 'powerkit' ); ?></h1>

				<div class="pk-settings">
					<form method="post">

						<!-- Service Selection -->
						<table class="form-table">
							<tbody>
								<tr>
									<th scope="row"><label for="powerkit_subscription_service"><?php esc_html_e( 'Subscription Service', 'powerkit' ); ?></label></th>
									<td>
										<select class="regular-text" name="powerkit_subscription_service" id="powerkit_subscription_service">
											<option value="mailchimp" <?php selected( 'mailchimp', $selected_service ); ?>><?php esc_html_e( 'MailChimp', 'powerkit' ); ?></option>
											<option value="kit" <?php selected( 'kit', $selected_service ); ?>><?php esc_html_e( 'Kit.com', 'powerkit' ); ?></option>
											<option value="mailerlite" <?php selected( 'mailerlite', $selected_service ); ?>><?php esc_html_e( 'MailerLite', 'powerkit' ); ?></option>
											<option value="custom" <?php selected( 'custom', $selected_service ); ?>><?php esc_html_e( 'Custom', 'powerkit' ); ?></option>
										</select>
									</td>
								</tr>
							</tbody>
						</table>

						<!-- MailChimp Settings -->
						<div id="mailchimp-settings" class="service-settings" <?php echo 'mailchimp' !== $selected_service ? 'style="display: none;"' : ''; ?>>
							<h3><?php esc_html_e( 'MailChimp', 'powerkit' ); ?></h3>

							<table class="form-table">
								<tbody>
									<!-- API Key -->
									<tr>
										<th scope="row"><label for="powerkit_mailchimp_token"><?php esc_html_e( 'API Key', 'powerkit' ); ?></label></th>
										<td>
											<input class="regular-text" id="powerkit_mailchimp_token" name="powerkit_mailchimp_token" type="text" value="<?php echo esc_attr( get_option( 'powerkit_mailchimp_token' ) ); ?>">
											<button type="button" id="refresh-mailchimp-lists" class="button button-secondary"><?php esc_html_e( 'Refresh Lists', 'powerkit' ); ?></button>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<ol>
												<li><?php esc_html_e( 'Log in to your', 'powerkit' ); ?> <?php printf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( 'https://mailchimp.com' ), esc_html__( 'MailChimp account', 'powerkit' ) ); ?>.</li>
												<li><?php esc_html_e( 'Click your profile name to expand the Account Panel, and choose Account.', 'powerkit' ); ?></li>
												<li><?php esc_html_e( 'Click the Extras drop-down menu and choose API keys.', 'powerkit' ); ?></li>
												<li><?php esc_html_e( 'Copy an existing API key or click the Create A Key button.', 'powerkit' ); ?></li>
												<li><?php esc_html_e( 'Click "Refresh Lists" button after entering your API key to load your audiences.', 'powerkit' ); ?></li>
											</ol>
										</td>
									</tr>
									<!-- Lists -->
									<tr>
										<th scope="row"><label for="powerkit_mailchimp_list"><?php esc_html_e( 'Default Audience', 'powerkit' ); ?></label></th>
										<td>
											<select class="regular-text" name="powerkit_mailchimp_list" id="powerkit_mailchimp_list">
												<option value=""><?php esc_html_e( '— Select an Audience —', 'powerkit' ); ?></option>
												<?php
												$saved_list_id = get_option( 'powerkit_mailchimp_list' );
												$token         = get_option( 'powerkit_mailchimp_token' );

												if ( $token ) {
													$data = powerkit_mailchimp_request(
														'GET', 'lists', array(
															'sort_field' => 'date_created',
															'sort_dir' => 'DESC',
															'count'    => 1000,
														)
													);

													if ( is_array( $data ) && isset( $data['lists'] ) && $data['lists'] ) {
														foreach ( $data['lists'] as $item ) {
															printf(
																'<option value="%1$s" %2$s>%3$s</option>',
																esc_attr( $item['id'] ),
																selected( $saved_list_id, $item['id'], false ),
																esc_html( $item['name'] . ' (' . $item['id'] . ')' )
															);
														}
													}
												}
												?>
											</select>
											<p class="description notification-area"></p>
										</td>
									</tr>
									<!-- Enable double opt-in -->
									<tr>
										<th scope="row"><label for="powerkit_mailchimp_double_optin"><?php esc_html_e( 'Enable Double opt-in', 'powerkit' ); ?></label></th>
										<td><input class="regular-text" id="powerkit_mailchimp_double_optin" name="powerkit_mailchimp_double_optin" type="checkbox" value="true" <?php checked( (bool) get_option( 'powerkit_mailchimp_double_optin', false ) ); ?>></td>
									</tr>
								</tbody>
							</table>
						</div>

						<!-- Kit.com Settings -->
						<div id="kit-settings" class="service-settings" <?php echo 'kit' !== $selected_service ? 'style="display: none;"' : ''; ?>>
							<h3><?php esc_html_e( 'Kit.com', 'powerkit' ); ?></h3>

							<table class="form-table">
								<tbody>
									<!-- API Key -->
									<tr>
										<th scope="row"><label for="powerkit_kit_token"><?php esc_html_e( 'API Key', 'powerkit' ); ?></label></th>
										<td>
											<input class="regular-text" id="powerkit_kit_token" name="powerkit_kit_token" type="text" value="<?php echo esc_attr( get_option( 'powerkit_kit_token' ) ); ?>">
											<button type="button" id="refresh-kit-forms" class="button button-secondary"><?php esc_html_e( 'Refresh Forms', 'powerkit' ); ?></button>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<ol>
												<li><?php esc_html_e( 'Log in to your', 'powerkit' ); ?> <?php printf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( 'https://kit.com' ), esc_html__( 'Kit.com account', 'powerkit' ) ); ?>.</li>
												<li><?php esc_html_e( 'Click on your profile icon and go to Settings.', 'powerkit' ); ?></li>
												<li><?php esc_html_e( 'Navigate to the Developer section.', 'powerkit' ); ?></li>
												<li><?php esc_html_e( 'Copy the V3 Public API Key.', 'powerkit' ); ?></li>
											</ol>
										</td>
									</tr>
									<tr>
										<th scope="row"><label for="powerkit_kit_form"><?php esc_html_e( 'Default Form', 'powerkit' ); ?></label></th>
										<td>
											<select class="regular-text" id="powerkit_kit_form" name="powerkit_kit_form">
												<option value=""><?php esc_html_e( '— Select a Form —', 'powerkit' ); ?></option>
												<?php
												$saved_form_id = get_option( 'powerkit_kit_form' );
												$forms         = function_exists( 'powerkit_kit_get_forms' ) ? powerkit_kit_get_forms() : array();

												if ( ! empty( $forms ) ) {
													foreach ( $forms as $form ) {
														printf(
															'<option value="%1$s" %2$s>%3$s</option>',
															esc_attr( $form['id'] ),
															selected( $saved_form_id, $form['id'], false ),
															esc_html( $form['name'] . ' (' . $form['id'] . ')' )
														);
													}
												}
												?>
											</select>
											<p class="description"><?php esc_html_e( 'Select a form from your Kit.com account. If you don\'t see your forms, click "Refresh Forms" after entering your API key.', 'powerkit' ); ?></p>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<!-- MailerLite Settings -->
						<div id="mailerlite-settings" class="service-settings" <?php echo 'mailerlite' !== $selected_service ? 'style="display: none;"' : ''; ?>>
							<h3><?php esc_html_e( 'MailerLite', 'powerkit' ); ?></h3>

							<table class="form-table">
								<tbody>
									<!-- API Key -->
									<tr>
										<th scope="row"><label for="powerkit_mailerlite_token"><?php esc_html_e( 'API Key', 'powerkit' ); ?></label></th>
										<td>
											<textarea class="regular-text" id="powerkit_mailerlite_token" name="powerkit_mailerlite_token" rows="5"><?php echo esc_textarea( get_option( 'powerkit_mailerlite_token' ) ); ?></textarea>
											<p>
												<button type="button" id="refresh-mailerlite-groups" class="button button-secondary"><?php esc_html_e( 'Refresh Groups', 'powerkit' ); ?></button>
											</p>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<ol>
												<li><?php esc_html_e( 'Log in to your', 'powerkit' ); ?> <?php printf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( 'https://dashboard.mailerlite.com/integrations/api' ), esc_html__( 'MailerLite account', 'powerkit' ) ); ?>.</li>
												<li><?php esc_html_e( 'Go to Integrations → API from the main menu.', 'powerkit' ); ?></li>
												<li><?php esc_html_e( 'Copy your API key or generate a new one if needed.', 'powerkit' ); ?></li>
												<li><?php esc_html_e( 'Click "Refresh Groups" button after entering your API key to load your groups.', 'powerkit' ); ?></li>
											</ol>
										</td>
									</tr>
									<!-- Default Group -->
									<tr>
										<th scope="row"><label for="powerkit_mailerlite_group"><?php esc_html_e( 'Default Group', 'powerkit' ); ?></label></th>
										<td>
											<select class="regular-text" id="powerkit_mailerlite_group" name="powerkit_mailerlite_group">
												<option value=""><?php esc_html_e( '— Select a Form —', 'powerkit' ); ?></option>
												<?php
												$saved_group_id = get_option( 'powerkit_mailerlite_group' );
												$groups         = function_exists( 'powerkit_mailerlite_get_groups' ) ? powerkit_mailerlite_get_groups() : array();

												if ( ! empty( $groups ) ) {
													foreach ( $groups as $group ) {
														printf(
															'<option value="%1$s" %2$s>%3$s</option>',
															esc_attr( $group['id'] ),
															selected( (string) $saved_group_id, (string) $group['id'], false ),
															esc_html( $group['name'] . ' (' . $group['id'] . ')' )
														);
													}
												}
												?>
											</select>
											<p class="description notification-area"></p>
											<p class="description"><?php esc_html_e( 'Select a group from your MailerLite account. If you don\'t see your groups, click "Refresh Groups" after entering your API key.', 'powerkit' ); ?></p>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<!-- Custom Form Settings -->
						<div id="custom-settings" class="service-settings" <?php echo 'custom' !== $selected_service ? 'style="display: none;"' : ''; ?>>
							<h3><?php esc_html_e( 'Custom Form', 'powerkit' ); ?></h3>

							<table class="form-table">
								<tbody>
									<!-- Form Action URL -->
									<tr>
										<th scope="row"><label for="powerkit_custom_form_action"><?php esc_html_e( 'Form Action URL', 'powerkit' ); ?></label></th>
										<td><input class="regular-text" id="powerkit_custom_form_action" name="powerkit_custom_form_action" type="text" value="<?php echo esc_attr( get_option( 'powerkit_custom_form_action' ) ); ?>"></td>
									</tr>
									<!-- Email Field Name -->
									<tr>
										<th scope="row"><label for="powerkit_custom_email_name"><?php esc_html_e( 'Email Field Name', 'powerkit' ); ?></label></th>
										<td><input class="regular-text" id="powerkit_custom_email_name" name="powerkit_custom_email_name" type="text" value="<?php echo esc_attr( get_option( 'powerkit_custom_email_name', 'email' ) ); ?>"></td>
									</tr>
									<!-- Name Field Name -->
									<tr>
										<th scope="row"><label for="powerkit_custom_name_field"><?php esc_html_e( 'Name Field Name', 'powerkit' ); ?></label></th>
										<td><input class="regular-text" id="powerkit_custom_name_field" name="powerkit_custom_name_field" type="text" value="<?php echo esc_attr( get_option( 'powerkit_custom_name_field', 'name' ) ); ?>"></td>
									</tr>
									<!-- Hidden Fields -->
									<tr>
										<th scope="row"><label for="powerkit_custom_hidden_fields"><?php esc_html_e( 'Hidden Fields', 'powerkit' ); ?></label></th>
										<td>
											<textarea class="regular-text" id="powerkit_custom_hidden_fields" name="powerkit_custom_hidden_fields" rows="5"><?php echo esc_html( get_option( 'powerkit_custom_hidden_fields', '' ) ); ?></textarea>
											<p class="description"><?php esc_html_e( 'Enter hidden fields in format: field_name=value (one per line)', 'powerkit' ); ?></p>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<!-- Common Settings -->
						<h3><?php esc_html_e( 'Common Settings', 'powerkit' ); ?></h3>

						<table class="form-table">
							<tbody>
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
			// Subscription service.
			if ( isset( $_POST['powerkit_subscription_service'] ) ) { // Input var ok.
				update_option( 'powerkit_subscription_service', sanitize_text_field( wp_unslash( $_POST['powerkit_subscription_service'] ) ) ); // Input var ok.
			}

			// MailChimp settings.
			if ( isset( $_POST['powerkit_mailchimp_token'] ) ) { // Input var ok.
				update_option( 'powerkit_mailchimp_token', sanitize_text_field( wp_unslash( $_POST['powerkit_mailchimp_token'] ) ) ); // Input var ok.
			}
			if ( isset( $_POST['powerkit_mailchimp_list'] ) ) { // Input var ok.
				update_option( 'powerkit_mailchimp_list', (string) sanitize_text_field( wp_unslash( $_POST['powerkit_mailchimp_list'] ) ) ); // Input var ok.
			}
			if ( isset( $_POST['powerkit_mailchimp_double_optin'] ) ) { // Input var ok.
				update_option( 'powerkit_mailchimp_double_optin', true );
			} else {
				update_option( 'powerkit_mailchimp_double_optin', false );
			}

			// Kit.com settings.
			if ( isset( $_POST['powerkit_kit_token'] ) ) { // Input var ok.
				update_option( 'powerkit_kit_token', sanitize_text_field( wp_unslash( $_POST['powerkit_kit_token'] ) ) ); // Input var ok.
			}
			if ( isset( $_POST['powerkit_kit_form'] ) ) { // Input var ok.
				update_option( 'powerkit_kit_form', (string) sanitize_text_field( wp_unslash( $_POST['powerkit_kit_form'] ) ) ); // Input var ok.
			}

			// MailerLite settings.
			if ( isset( $_POST['powerkit_mailerlite_token'] ) ) { // Input var ok.
				update_option( 'powerkit_mailerlite_token', sanitize_text_field( wp_unslash( $_POST['powerkit_mailerlite_token'] ) ) ); // Input var ok.
			}
			if ( isset( $_POST['powerkit_mailerlite_group'] ) ) { // Input var ok.
				update_option( 'powerkit_mailerlite_group', (string) sanitize_text_field( wp_unslash( $_POST['powerkit_mailerlite_group'] ) ) ); // Input var ok.
			}

			// Custom form settings.
			if ( isset( $_POST['powerkit_custom_form_action'] ) ) { // Input var ok.
				update_option( 'powerkit_custom_form_action', esc_url_raw( wp_unslash( $_POST['powerkit_custom_form_action'] ) ) ); // Input var ok.
			}
			if ( isset( $_POST['powerkit_custom_email_name'] ) ) { // Input var ok.
				update_option( 'powerkit_custom_email_name', sanitize_text_field( wp_unslash( $_POST['powerkit_custom_email_name'] ) ) ); // Input var ok.
			}
			if ( isset( $_POST['powerkit_custom_name_field'] ) ) { // Input var ok.
				update_option( 'powerkit_custom_name_field', sanitize_text_field( wp_unslash( $_POST['powerkit_custom_name_field'] ) ) ); // Input var ok.
			}
			if ( isset( $_POST['powerkit_custom_hidden_fields'] ) ) { // Input var ok.
				update_option( 'powerkit_custom_hidden_fields', sanitize_textarea_field( wp_unslash( $_POST['powerkit_custom_hidden_fields'] ) ) ); // Input var ok.
			}

			// Privacy setting.
			if ( isset( $_POST['powerkit_mailchimp_privacy'] ) ) { // Input var ok.
				update_option( 'powerkit_mailchimp_privacy', wp_kses( wp_unslash( $_POST['powerkit_mailchimp_privacy'] ), 'post' ) ); // Input var ok. sanitization ok.
			}

			printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Settings saved.', 'powerkit' ) );
		}
	}

	/**
	 * AJAX handler to refresh Kit.com forms
	 */
	public function ajax_refresh_kit_forms() {
		// Check nonce.
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) ) ) {
			wp_send_json_error( 'Invalid security token' );
		}

		// Check for API key.
		if ( ! isset( $_POST['api_key'] ) || empty( $_POST['api_key'] ) ) {
			wp_send_json_error( 'API key is required' );
		}

		$api_key = sanitize_text_field( wp_unslash( $_POST['api_key'] ) );

		// Temporarily update the API key option.
		$original_key = get_option( 'powerkit_kit_token' );
		update_option( 'powerkit_kit_token', $api_key );

		// Get forms using the helper function.
		$forms = function_exists( 'powerkit_kit_get_forms' ) ? powerkit_kit_get_forms() : array();

		// Restore original key if this was just a test.
		if ( $api_key !== $original_key ) {
			update_option( 'powerkit_kit_token', $original_key );
		}

		wp_send_json_success( $forms );
	}

	/**
	 * AJAX handler to refresh MailChimp lists
	 */
	public function ajax_refresh_mailchimp_lists() {
		// Check nonce.
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) ) ) {
			wp_send_json_error( 'Invalid security token' );
		}

		// Check for API key.
		if ( ! isset( $_POST['api_key'] ) || empty( $_POST['api_key'] ) ) {
			wp_send_json_error( 'API key is required' );
		}

		$api_key = sanitize_text_field( wp_unslash( $_POST['api_key'] ) );

		// Temporarily update the API key option.
		$original_key = get_option( 'powerkit_mailchimp_token' );
		update_option( 'powerkit_mailchimp_token', $api_key );

		// Get lists using the MailChimp API.
		$lists = array();

		$data = powerkit_mailchimp_request(
			'GET', 'lists', array(
				'sort_field' => 'date_created',
				'sort_dir'   => 'DESC',
				'count'      => 1000,
			)
		);

		if ( is_array( $data ) && isset( $data['lists'] ) && $data['lists'] ) {
			foreach ( $data['lists'] as $item ) {
				if ( isset( $item['id'] ) && isset( $item['name'] ) ) {
					$lists[] = array(
						'id'   => $item['id'],
						'name' => $item['name'],
					);
				}
			}
		} elseif ( is_array( $data ) && isset( $data['type'] ) && isset( $data['title'] ) ) {
			// API returned an error.
			$error_message = $data['type'] . ': ' . $data['title'];
			if ( isset( $data['detail'] ) ) {
				$error_message .= ' - ' . $data['detail'];
			}
			wp_send_json_error( $error_message );
		} else {
			wp_send_json_error( 'Unable to fetch MailChimp lists. Check your API key.' );
		}

		// Restore original key if this was just a test.
		if ( $api_key !== $original_key ) {
			update_option( 'powerkit_mailchimp_token', $original_key );
		}

		wp_send_json_success( $lists );
	}

	/**
	 * AJAX handler to refresh MailerLite groups
	 */
	public function ajax_refresh_mailerlite_groups() {
		// Check nonce.
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) ) ) {
			wp_send_json_error( 'Invalid security token' );
		}

		// Check for API key.
		if ( ! isset( $_POST['api_key'] ) || empty( $_POST['api_key'] ) ) {
			wp_send_json_error( 'API key is required' );
		}

		$api_key = sanitize_text_field( wp_unslash( $_POST['api_key'] ) );

		// Temporarily update the API key option.
		$original_key = get_option( 'powerkit_mailerlite_token' );
		update_option( 'powerkit_mailerlite_token', $api_key );

		// Get groups using the helper function.
		$groups = function_exists( 'powerkit_mailerlite_get_groups' ) ? powerkit_mailerlite_get_groups() : array();

		// If the function is not available or no groups were found, try the direct API request.
		if ( empty( $groups ) ) {
			// Make API request to MailerLite.
			$response = wp_remote_get( 'https://api.mailerlite.com/api/v2/groups', array(
				'headers' => array(
					'X-MailerLite-ApiKey' => $api_key,
					'Content-Type'        => 'application/json',
				),
			) );

			if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
				$body = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( is_array( $body ) && ! empty( $body ) ) {
					foreach ( $body as $item ) {
						if ( isset( $item['id'] ) && isset( $item['name'] ) ) {
							$groups[] = array(
								'id'   => (string) $item['id'],
								'name' => $item['name'],
							);
						}
					}
				}
			} else {
				$error_message = 'Unable to fetch MailerLite groups. Check your API key.';

				if ( is_wp_error( $response ) ) {
					$error_message = $response->get_error_message();
				}

				wp_send_json_error( $error_message );
			}
		}

		// Restore original key if this was just a test.
		if ( $api_key !== $original_key ) {
			update_option( 'powerkit_mailerlite_token', $original_key );
		}

		wp_send_json_success( $groups );
	}
}
