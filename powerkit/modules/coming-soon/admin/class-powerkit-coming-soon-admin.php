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
class Powerkit_Coming_Soon_Admin extends Powerkit_Module_Admin {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'init', array( $this, 'activation' ) );
		add_action( 'admin_menu', array( $this, 'register_options_page' ) );
		add_action( 'admin_notices', array( $this, 'notices' ) );
		add_action( 'display_post_states', array( $this, 'post_state' ), 10, 2 );
	}

	/**
	 * Activation.
	 */
	public function activation() {
		$activate = get_option( 'powerkit_coming_soon_init', false );

		if ( ! $activate ) {
			$page_id = wp_insert_post(  wp_slash( array(
				'post_title'   => esc_html__( 'Coming Soon', 'powerkit' ),
				'post_content' => powerkit_coming_soon_default_content(),
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_author'  => 1,
			) ) );

			update_option( 'powerkit_coming_soon_init', true );

			update_option( 'powerkit_coming_soon_page', $page_id );
		}
	}

	/**
	 * Prints admin screen notices.
	 */
	public function notices() {
		$screen = get_current_screen();

		if ( false !== strpos( $screen->base, $this->slug ) ) {
			return;
		}

		// Check Status.
		if ( ! powerkit_coming_soon_status() ) {
			return;
		}

		// Check Notice.
		if ( 'yes' === get_option( 'powerkit_coming_soon_notice', 'yes' ) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p>
					<?php
					// translators: Link deactivate.
					echo wp_kses( sprintf( __( 'The Coming Soon is active. Please don\'t forget to <a href="%1$s">deactivate</a> as soon as you are done.', 'powerkit' ), powerkit_get_page_url( $this->slug ) ), 'post' );
					?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Set display state for page of Coming Soon.
	 *
	 * @param string $states An array of post display states.
	 * @param object $post   The current post object.
	 */
	public function post_state( $states, $post ) {
		$page_id = get_option( 'powerkit_coming_soon_page' );

		if ( isset( $post->ID ) && intval( $post->ID ) === intval( $page_id ) ) {
			$states[] = esc_html__( 'Coming Soon Page', 'powerkit' );
		}

		return $states;
	}

	/**
	 * Register admin page
	 *
	 * @since 1.0.0
	 */
	public function register_options_page() {
		add_options_page( esc_html__( 'Coming Soon', 'powerkit' ), esc_html__( 'Coming Soon', 'powerkit' ), 'manage_options', powerkit_get_page_slug( $this->slug ), array( $this, 'build_options_page' ) );
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
				<h1><?php esc_html_e( 'Coming Soon', 'powerkit' ); ?></h1>

				<div class="pk-settings">
					<form method="post">
						<table class="form-table">
							<tbody>
								<!-- Status -->
								<tr>
									<th scope="row"><label for="powerkit_coming_soon_status"><?php esc_html_e( 'Status', 'powerkit' ); ?></label></th>
									<td>
										<label><input class="regular-text" id="powerkit_coming_soon_status" name="powerkit_coming_soon_status" type="radio" value="yes" <?php checked( true, (bool) get_option( 'powerkit_coming_soon_status', false ) ); ?>> <?php esc_html_e( 'Activated', 'powerkit' ); ?></label>
										<br>
										<label><input value="no" class="regular-text" id="powerkit_coming_soon_status" name="powerkit_coming_soon_status" type="radio" <?php checked( false, (bool) get_option( 'powerkit_coming_soon_status', false ) ); ?>> <?php esc_html_e( 'Deactivated', 'powerkit' ); ?></label>
									</td>
								</tr>

								<!-- Content from Page -->
								<tr>
									<?php
									$pages = new WP_Query();

									$pages = $pages->query( array(
										'posts_per_page' => -1,
										'post_type'      => 'page',
									) );

									if ( $pages ) {
										?>
										<th scope="row"><label for="powerkit_coming_soon_page"><?php esc_html_e( 'Content from Page', 'powerkit' ); ?></label></th>
										<td>
											<select class="regular-text" name="powerkit_coming_soon_page" id="powerkit_coming_soon_page">
												<option value=""><?php esc_html_e( '- not selected -', 'powerkit' ); ?></option>
												<?php foreach ( $pages as $page ) : ?>
													<option <?php selected( $page->ID, get_option( 'powerkit_coming_soon_page' ) ); ?> value="<?php echo esc_attr( $page->ID ); ?>"><?php echo esc_html( $page->post_title ); ?></option>
												<?php endforeach; ?>
											</select>
										</td>
									<?php } else { ?>
										<td colspan="2">
											<code><?php esc_html_e( 'Pages no found.', 'powerkit' ); ?></code>
										</td>
									<?php } ?>

									<!-- Notice -->
									<tr>
										<th scope="row"><label for="powerkit_coming_soon_notice"><?php esc_html_e( 'Notice', 'powerkit' ); ?></label></th>
										<td>
											<select class="regular-text" id="powerkit_coming_soon_notice" name="powerkit_coming_soon_notice">
												<option value="yes" <?php selected( 'yes', get_option( 'powerkit_coming_soon_notice', 'yes' ) ); ?>><?php esc_html_e( 'Yes', 'powerkit' ); ?></option>
												<option value="no" <?php selected( 'no', get_option( 'powerkit_coming_soon_notice', 'yes' ) ); ?>><?php esc_html_e( 'No', 'powerkit' ); ?></option>
											</select>
											<p class="description"><?php esc_html_e( 'Do you want to see notices when maintenance mode is activated?', 'powerkit' ); ?></p>
										</td>
									</tr>

									<!-- HTTP status code -->
									<tr>
										<th scope="row"><label for="powerkit_coming_soon_httpcode"><?php esc_html_e( 'HTTP status code', 'powerkit' ); ?></label></th>
										<td><input class="regular-text" id="powerkit_coming_soon_httpcode" name="powerkit_coming_soon_httpcode" type="text" value="<?php echo esc_attr( get_option( 'powerkit_coming_soon_httpcode', 404 ) ); ?>"></td>
									</tr>
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
			if ( isset( $_POST['powerkit_coming_soon_status'] ) && 'yes' === $_POST['powerkit_coming_soon_status'] ) { // Input var ok; sanitization ok.
				update_option( 'powerkit_coming_soon_status', true );
			} else {
				update_option( 'powerkit_coming_soon_status', false );
			}
			if ( isset( $_POST['powerkit_coming_soon_page'] ) ) { // Input var ok.
				update_option( 'powerkit_coming_soon_page', sanitize_text_field( wp_unslash( $_POST['powerkit_coming_soon_page'] ) ) ); // Input var ok.
			}
			if ( isset( $_POST['powerkit_coming_soon_notice'] ) ) { // Input var ok.
				update_option( 'powerkit_coming_soon_notice', sanitize_text_field( wp_unslash( $_POST['powerkit_coming_soon_notice'] ) ) ); // Input var ok.
			}
			if ( isset( $_POST['powerkit_coming_soon_httpcode'] ) ) { // Input var ok.
				update_option( 'powerkit_coming_soon_httpcode', sanitize_text_field( wp_unslash( $_POST['powerkit_coming_soon_httpcode'] ) ) ); // Input var ok.
			}
			printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Settings saved.', 'powerkit' ) );
		}
	}
}
