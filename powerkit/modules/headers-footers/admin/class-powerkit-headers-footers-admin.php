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
class Powerkit_Headers_Footers_Admin extends Powerkit_Module_Admin {

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
		add_options_page( esc_html__( 'Insert Headers & Footers', 'powerkit' ), esc_html__( 'Insert Headers & Footers', 'powerkit' ), 'manage_options', powerkit_get_page_slug( $this->slug ), array( $this, 'build_options_page' ) );
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
				<h1><?php esc_html_e( 'Insert Headers & Footers', 'powerkit' ); ?></h1>

				<div class="pk-settings">
					<form method="post">
						<table class="form-table">
							<tbody>
								<!-- Scripts in Header -->
								<tr>
									<th scope="row">
										<label for="powerkit_insert_header_code">
											<?php esc_html_e( 'Scripts in Header', 'powerkit' ); ?>
											<p class="description"><?php esc_html_e( 'These scripts will be printed in the &lt;head&gt; section.', 'powerkit' ); ?></p>
										</label>
									</th>
									<td><textarea style="width:100%" id="powerkit_insert_header_code" name="powerkit_insert_header_code" rows="8"><?php echo (string) get_option( 'powerkit_insert_header_code' ); // XSS. ?></textarea></td>
								</tr>

								<!-- Scripts in Footer -->
								<tr>
									<th scope="row">
										<label for="powerkit_insert_footer_code">
											<?php esc_html_e( 'Scripts in Footer', 'powerkit' ); ?>
											<p class="description"><?php esc_html_e( 'These scripts will be printed above the &lt;/body&gt; tag.', 'powerkit' ); ?></p>
										</label>
									</th>
									<td><textarea style="width:100%" id="powerkit_insert_footer_code" name="powerkit_insert_footer_code" rows="8"><?php echo (string) get_option( 'powerkit_insert_footer_code' ); // XSS. ?></textarea></td>
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

			if ( isset( $_POST['powerkit_insert_header_code'] ) ) { // Input var ok.
				update_option( 'powerkit_insert_header_code', wp_unslash( $_POST['powerkit_insert_header_code'] ) ); // Input var ok. sanitization ok.
			}
			if ( isset( $_POST['powerkit_insert_footer_code'] ) ) { // Input var ok.
				update_option( 'powerkit_insert_footer_code', wp_unslash( $_POST['powerkit_insert_footer_code'] ) ); // Input var ok. sanitization ok.
			}
			printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Settings saved.', 'powerkit' ) );
		}
	}
}
