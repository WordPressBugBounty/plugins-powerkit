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
class Powerkit_Post_Views_Admin extends Powerkit_Module_Admin {

	/**
	 * Initialize
	 */
	public function initialize() {

		add_filter( 'init', function() {
			$post_types = get_post_types( array(
				'publicly_queryable' => 1,
				'_builtin'           => false,
			) );

			// Merge post types.
			$post_types = array_merge( array(
				'post' => 'post',
			), $post_types );

			foreach ( $post_types as $post_type ) {
				add_filter( "manage_{$post_type}_posts_columns", array( $this, 'column_views' ) );
				add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'custom_column_views' ), 6, 2 );
			}
		} );

		add_action( 'admin_menu', array( $this, 'register_options_page' ) );
		add_action( 'admin_head', array( $this, 'column_style' ) );
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
	}

	/**
	 * Register admin page
	 */
	public function register_options_page() {
		add_options_page( esc_html__( 'Post Views', 'powerkit' ), esc_html__( 'Post Views', 'powerkit' ), 'manage_options', powerkit_get_page_slug( $this->slug ), array( $this, 'build_options_page' ) );
	}

	/**
	 * Build admin page
	 */
	public function build_options_page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient rights to view this page.', 'powerkit' ) );
		}

		$this->process_options_page();

		$options = powerkit_post_views_options();
		?>
			<div class="wrap pk-wrap">
				<h1><?php esc_html_e( 'Post Views Settings', 'powerkit' ); ?></h1>

				<div class="pk-settings">
					<form method="post" action="<?php echo esc_url( powerkit_get_page_url( $this->slug ) ); ?>">
						<table class="form-table">
							<tbody>
								<?php
								if ( empty( $options['token'] ) ) {

									if ( empty( $options['clientid'] ) || empty( $options['psecret'] ) ) {
										?>

										<p><?php echo wp_kses( __( 'In order to connect to your Google Analytics Account, you need to create a new project in the <a href="https://console.developers.google.com/project" target="_blank">Google API Console</a> and activate the Analytics API in "APIs &amp; Services"', 'powerkit' ), 'post' ); ?></p>

										<ol>
											<li><?php echo wp_kses( __( 'Go to "APIs &amp; Services".', 'powerkit' ), 'post' ); ?></li>
											<li><?php echo wp_kses( __( 'On the tab "OAuth consent screen" register the application, select "User Type" (External) and click "Create".', 'powerkit' ), 'post' ); ?></li>
											<li><?php echo wp_kses( __( 'On the tab "OAuth consent screen" enter "Application name" and add your domain.', 'powerkit' ), 'post' ); ?></li>
											<li><?php echo wp_kses( sprintf( __( 'Then, create an OAuth Client ID in "APIs  &amp; Services > Credentials" (Select "Web application", enter this URL %s for the "Authorized redirect URIs" field). ', 'powerkit' ), '<code>' . powerkit_get_page_url( $this->slug ) . '</code>' ), 'post' ); ?></li>
											<li><?php echo wp_kses( __( 'Enter your access below and connect to Google Analytics (if you have received a notice - "This app is not verified", then you can continue by clicking on "Advanced" link and follow the instructions).', 'powerkit' ), 'post' ); ?></li>
										</ol>

										<p><?php echo wp_kses( __( 'Make sure to activate the following Google APIs:', 'powerkit' ), 'post' ); ?></p>

										<ol>
											<li><?php echo wp_kses( __( 'Google Analytics API', 'powerkit' ), 'post' ); ?></li>
											<li><?php echo wp_kses( __( 'Analytics Reporting API', 'powerkit' ), 'post' ); ?></li>
											<li><?php echo wp_kses( __( 'Google Analytics Data API', 'powerkit' ), 'post' ); ?></li>
										</ol>

										<!-- Client ID -->
										<tr>
											<th scope="row"><label for="powerkit_post_views_clientid"><?php esc_html_e( 'Client ID', 'powerkit' ); ?></label></th>
											<td><input class="regular-text" id="powerkit_post_views_clientid" name="powerkit_post_views_clientid" type="text" value="<?php echo esc_attr( $options['clientid'] ); ?>"></td>
										</tr>
										<!-- Client secret -->
										<tr>
											<th scope="row"><label for="powerkit_post_views_psecret"><?php esc_html_e( 'Client secret', 'powerkit' ); ?></label></th>
											<td><input class="regular-text" id="powerkit_post_views_psecret" name="powerkit_post_views_psecret" type="text" value="<?php echo esc_attr( $options['psecret'] ); ?>"></td>
										</tr>
										<?php
									} else {
										$googleapis_auth = add_query_arg( array(
											'client_id'     => $options['clientid'],
											'redirect_uri'  => powerkit_get_page_url( $this->slug ),
											'scope'         => 'https://www.googleapis.com/auth/analytics.readonly+https://www.googleapis.com/auth/userinfo.email+https://www.googleapis.com/auth/userinfo.profile&response_type=code&access_type=offline&state=init&approval_prompt=force'
										), 'https://accounts.google.com/o/oauth2/v2/auth' );
										?>
											<tr>
												<td colspan="2">
													<p><a class="button" href="<?php echo esc_url( $googleapis_auth ); ?>"><?php esc_html_e( 'Connect to Google Analytics', 'powerkit' ); ?> &raquo;</a></p>

													<p><a class="button" href="<?php echo esc_url( wp_nonce_url( powerkit_get_page_url( $this->slug . '&state=clear-api' ) ) ); ?>"><?php esc_html_e( 'Clear the API keys', 'powerkit' ); ?> &raquo;</a></p>
												</td>
											</tr>

											<tr><td colspan="2"><hr></td></tr>
										<?php
									}
								} else {
									?>
										<p><?php esc_html_e( 'You are connected to Google Analytics with the e-mail address', 'powerkit' ); ?> <?php echo esc_html( $options['gmail'] ); ?></p>

										<p><a href="<?php echo esc_url( wp_nonce_url( powerkit_get_page_url( $this->slug . '&state=disconnect' ) ) ); ?>"><?php esc_html_e( 'Disconnect from Google Analytics', 'powerkit' ); ?> &raquo;</a></p>

										<p><a href="<?php echo esc_url( wp_nonce_url( powerkit_get_page_url( $this->slug . '&state=reset-cache' ) ) ); ?>"><?php esc_html_e( 'Empty page views cache', 'powerkit' ); ?> &raquo;</a></p>

										<!-- GA4 Property ID -->
										<tr>

											<th scope="row"><label for="powerkit_post_views_property_id"><?php esc_html_e( 'GA4 Property ID', 'powerkit' ); ?></label></th>
											<td>
												<input class="regular-text" id="powerkit_post_views_property_id" name="powerkit_post_views_property_id" type="text" value="<?php echo esc_attr( $options['property_id'] ); ?>">

												<p><?php echo wp_kses( __( 'To determine a Google Analytics 4 property Id:', 'powerkit' ), 'post' ); ?></li></p>

												<p><?php echo wp_kses( __( 'Visit Google Analytics at', 'powerkit' ), 'post' ); ?> <a target="_blank" href="https://analytics.google.com/">https://analytics.google.com/</a></p>

												<ol>
													<li><?php echo wp_kses( __( 'Select Admin.', 'powerkit' ), 'post' ); ?></li>
													<li><?php echo wp_kses( __( 'Select the Property.', 'powerkit' ), 'post' ); ?></li>
													<li><?php echo wp_kses( __( 'Select Property Settings.', 'powerkit' ), 'post' ); ?></li>
													<li><?php echo wp_kses( __( 'If the Property Settings shows a numeric "PROPERTY ID" such as "123...", this is the numeric Id of your Google Analytics 4 property.', 'powerkit' ), 'post' ); ?></li>
												</ol>
											</td>
										</tr>
										<!-- Start date for the analytics -->
										<tr>
											<th scope="row"><label for="powerkit_post_views_startdate"><?php esc_html_e( 'Start date for the analytics', 'powerkit' ); ?></label></th>
											<td><input class="regular-text" id="powerkit_post_views_startdate" name="powerkit_post_views_startdate" type="date" value="<?php echo esc_attr( $options['startdate'] ); ?>"></td>
										</tr>
										<!-- Default value when a count cannot be fetched -->
										<tr>
											<th scope="row"><label for="powerkit_post_views_defaultval"><?php esc_html_e( 'Default value when a count cannot be fetched', 'powerkit' ); ?></label></th>
											<td><input class="regular-text" id="powerkit_post_views_defaultval" name="powerkit_post_views_defaultval" type="text" value="<?php echo esc_attr( $options['defaultval'] ); ?>"></td>
										</tr>
										<!-- Display the Views column in Posts list -->
										<tr>
											<th scope="row"><label for="powerkit_post_views_column"><?php esc_html_e( 'Display the Views column in Posts list', 'powerkit' ); ?></label></th>
											<td><input class="regular-text" id="powerkit_post_views_column" name="powerkit_post_views_column" type="checkbox" value="true" <?php checked( (bool) $options['column'] ); ?>></td>
										</tr>
										<!-- Search pageviews slugs with trailing slash -->
										<tr>
											<th scope="row"><label for="powerkit_post_views_trailing"><?php esc_html_e( 'Search pageviews slugs with trailing slash', 'powerkit' ); ?></label></th>
											<td><input class="regular-text" id="powerkit_post_views_trailing" name="powerkit_post_views_trailing" type="checkbox" value="true" <?php checked( (bool) $options['trailing'] ); ?>></td>
										</tr>
									<?php
								}
								?>
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
	 * Process options page
	 */
	public function process_options_page() {

		global $wpdb;

		$options = powerkit_post_views_options();

		/** Save settings */
		/** ------------------- */

		if ( isset( $_POST['save_settings'] ) ) { // Input var ok; sanitization ok.

			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'] ) ) { // Input var ok; sanitization ok.
				return;
			}

			if ( isset( $_POST['powerkit_post_views_clientid'] ) ) { // Input var ok; sanitization ok.
				$options['clientid'] = sanitize_text_field( $_POST['powerkit_post_views_clientid'] ); // Input var ok; sanitization ok.
			}

			if ( isset( $_POST['powerkit_post_views_psecret'] ) ) { // Input var ok; sanitization ok.
				$options['psecret'] = sanitize_text_field( $_POST['powerkit_post_views_psecret'] ); // Input var ok; sanitization ok.
			}

			if ( isset( $_POST['powerkit_post_views_property_id'] ) ) { // Input var ok; sanitization ok.
				$options['property_id'] = sanitize_text_field( $_POST['powerkit_post_views_property_id'] ); // Input var ok; sanitization ok.
			}

			if ( isset( $_POST['powerkit_post_views_startdate'] ) ) { // Input var ok; sanitization ok.
				$options['startdate'] = sanitize_text_field( $_POST['powerkit_post_views_startdate'] ); // Input var ok; sanitization ok.
			}

			if ( isset( $_POST['powerkit_post_views_defaultval'] ) ) { // Input var ok; sanitization ok.
				$options['defaultval'] = sanitize_text_field( $_POST['powerkit_post_views_defaultval'] ); // Input var ok; sanitization ok.
			}

			$options['column']   = ( isset( $_POST['powerkit_post_views_column'] ) ); // Input var ok; sanitization ok.
			$options['trailing'] = ( isset( $_POST['powerkit_post_views_trailing'] ) ); // Input var ok; sanitization ok.

			do_action( 'powerkit_post_views_save_options', $options );

			update_option( 'powerkit_post_views_options', $options );

			printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Settings saved.', 'powerkit' ) );
		}

		/** Actions */
		/** ------------------- */

		if ( isset( $_GET['state'] ) && 'init' === $_GET['state'] ) { // Input var ok; sanitization ok.

			$request = new WP_Http();

			$result = $request->request( 'https://accounts.google.com/o/oauth2/token', array(
				'method' => 'POST',
				'body'   => array(
					'code'          => sanitize_text_field( isset( $_GET['code'] ) ? $_GET['code'] : null ), // Input var ok; sanitization ok.
					'client_id'     => $options['clientid'],
					'client_secret' => $options['psecret'],
					'redirect_uri'  => powerkit_get_page_url( $this->slug ),
					'grant_type'    => 'authorization_code',
				),
			) );

			if ( ! is_array( $result ) || ! isset( $result['response']['code'] ) && 200 !== $result['response']['code'] ) {
				?>
				<div id="message" class="error">
					<p>
						<?php esc_html_e( 'There was something wrong with Google!', 'powerkit' ); ?>
					</p>
				</div>
				<?php
			}

			$tjson = json_decode( $result['body'] );

			$options['token']         = $tjson->access_token;
			$options['token_refresh'] = $tjson->refresh_token;
			$options['expires']       = time() + $tjson->expires_in;

			update_option( 'powerkit_post_views_options', $options );

			$ijson = powerkit_post_views_api_call( 'https://www.googleapis.com/oauth2/v3/userinfo', array() );

			$options['gid']   = $ijson->sub;
			$options['gmail'] = $ijson->email;

			update_option( 'powerkit_post_views_options', $options );

			if ( ! empty( $options['token'] ) && ! empty( $options['gmail'] ) ) {
				?>
					<script>window.location = '<?php echo esc_url( powerkit_get_page_url( $this->slug ) ); ?> ';</script>
				<?php
				exit;
			}
		} elseif ( isset( $_GET['state'] ) && 'disconnect' === $_GET['state'] ) { // Input var ok; sanitization ok.

			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'] ) ) { // Input var ok; sanitization ok.
				return;
			}

			$options['error']         = null;
			$options['gid']           = null;
			$options['gmail']         = null;
			$options['token']         = null;
			$options['token_refresh'] = null;
			$options['expires']       = null;
			$options['defaultval']    = 0;

			update_option( 'powerkit_post_views_options', $options );

		} elseif ( isset( $_GET['state'] ) && 'clear-api' === $_GET['state'] ) { // Input var ok; sanitization ok.

			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'] ) ) { // Input var ok; sanitization ok.
				return;
			}

			$options['error']    = null;
			$options['clientid'] = null;
			$options['psecret']  = null;

			update_option( 'powerkit_post_views_options', $options );

			printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'API Keys removed.', 'powerkit' ) );

		} elseif ( isset( $_GET['state'] ) && 'refresh-token' === $_GET['state'] ) { // Input var ok; sanitization ok.

			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'] ) ) { // Input var ok; sanitization ok.
				return;
			}

			powerkit_post_views_refresh_token();

			printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Token refreshed successfully.', 'powerkit' ) );

		} elseif ( isset( $_GET['state'] ) && 'reset-cache' === $_GET['state'] ) { // Input var ok; sanitization ok.

			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'] ) ) { // Input var ok; sanitization ok.
				return;
			}

			$wpdb->query( "UPDATE {$wpdb->prefix}pk_post_views SET period = 0" ); // db call ok; no-cache ok.

			printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Cache flushed successfully.', 'powerkit' ) );
		}
	}

	/**
	 * Filters the columns displayed in the Posts list table.
	 *
	 * @param array $post_columns An associative array of column headings.
	 */
	public function column_views( $post_columns ) {

		$options = powerkit_post_views_options();

		if ( ! empty( $options['token'] ) && $options['column'] ) {

			$post_columns['pk_post_views'] = esc_html__( 'Views', 'powerkit' );

		}

		return $post_columns;
	}

	/**
	 * Fires in each custom column in the Posts list table.
	 *
	 * @param string $column_name The name of the column to display.
	 * @param int    $post_id     The current post ID.
	 */
	public function custom_column_views( $column_name, $post_id ) {

		if ( 'pk_post_views' === $column_name ) {

			echo powerkit_get_post_views( $post_id, true ); // XSS.
		}
	}

	/**
	 * Add column style.
	 */
	public function column_style() {
		echo '<style>.column-pk_post_views { width: 120px; }</style>';
	}

	/**
	 * Output notice.
	 */
	public function admin_notice() {

		$options = powerkit_post_views_options();

		if ( current_user_can( 'manage_options' ) ) {

			if ( isset( $options['token'] ) && empty( $options['token'] ) ) {

				echo '<div class="error"><p>' . esc_html__( 'Google Post Views Warning: You have to (re)connect the plugin to your Google account.' ) . '<br><a href="' . esc_url( powerkit_get_page_url( $this->slug ) ) . '">' . esc_html__( 'Update settings', 'powerkit' ) . ' &rarr;</a></p></div>';

			} elseif ( isset( $options['error'] ) && ! empty( $options['error'] ) ) {

				echo '<div class="error"><p>' . esc_html__( 'Google Post Views Error: ', 'powerkit' ) . wp_kses( $options['error'], 'post' ) . '<br><a href="' . esc_url( powerkit_get_page_url( $this->slug ) ) . '">' . esc_html__( 'Update settings', 'powerkit' ) . ' &rarr;</a></p></div>';
			}
		}
	}
}
