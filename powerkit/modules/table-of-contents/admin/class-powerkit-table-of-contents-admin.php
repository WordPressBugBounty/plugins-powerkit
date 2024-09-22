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
class Powerkit_Table_Of_Contents_Admin extends Powerkit_Module_Admin {

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
		add_options_page( esc_html__( 'Table of Contents', 'powerkit' ), esc_html__( 'Table of Contents', 'powerkit' ), 'manage_options', powerkit_get_page_slug( $this->slug ), array( $this, 'build_options_page' ) );
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
				<h1><?php esc_html_e( 'Table of Contents', 'powerkit' ); ?></h1>

				<div class="pk-settings">
					<form method="post">
						<table class="form-table">
							<tbody>
								<!-- Enable Table of Contents in all posts automatically -->
								<tr>
									<th scope="row"><label for="powerkit_toc_enable_automatically"><?php esc_html_e( 'Enable Table of Contents in all posts automatically', 'powerkit' ); ?></label></th>
									<td><input class="regular-text" id="powerkit_toc_enable_automatically" name="powerkit_toc_enable_automatically" type="checkbox" value="true" <?php checked( (bool) get_option( 'powerkit_toc_enable_automatically', false ) ); ?>></td>
								</tr>
								<!-- Title -->
								<tr>
									<th scope="row"><label for="powerkit_toc_title"><?php esc_html_e( 'Title', 'powerkit' ); ?></label></th>
									<td><input class="regular-text" id="powerkit_toc_title" name="powerkit_toc_title" type="text" value="<?php echo esc_attr( get_option( 'powerkit_toc_title', esc_html__( 'Table of Contents', 'powerkit' ) ) ); ?>"></td>
								</tr>
								<!-- Exclude Posts -->
								<tr>
									<th scope="row">
										<label for="powerkit_toc_exclude"><?php esc_html_e( 'Exclude Posts', 'powerkit' ); ?></label>
										<p class="description"><?php esc_html_e( 'Enter a comma-separated list of post IDs where you’d like to avoid Table of Contents to appear.', 'powerkit' ); ?></p>
									</th>
									<td><input class="regular-text" id="powerkit_toc_exclude" name="powerkit_toc_exclude" type="text" value="<?php echo esc_attr( get_option( 'powerkit_toc_exclude' ) ); ?>"></td>
								</tr>
								<!-- Depth of headings -->
								<tr>
									<th scope="row"><label for="powerkit_toc_depth"><?php esc_html_e( 'Depth of headings', 'powerkit' ); ?></label></th>
									<td><input class="regular-text" id="powerkit_toc_depth" name="powerkit_toc_depth" type="number" value="<?php echo esc_attr( get_option( 'powerkit_toc_depth', 2 ) ); ?>"></td>
								</tr>
								<!-- Minimum number of headings in page content -->
								<tr>
									<th scope="row"><label for="powerkit_toc_min_count"><?php esc_html_e( 'Minimum number of headings in page content', 'powerkit' ); ?></label></th>
									<td><input class="regular-text" id="powerkit_toc_min_count" name="powerkit_toc_min_count" type="number" value="<?php echo esc_attr( get_option( 'powerkit_toc_min_count', 4 ) ); ?>"></td>
								</tr>
								<!-- Minimum number of characters of post content -->
								<tr>
									<th scope="row"><label for="powerkit_toc_min_characters"><?php esc_html_e( 'Minimum number of characters of post content', 'powerkit' ); ?></label></th>
									<td><input class="regular-text" id="powerkit_toc_min_characters" name="powerkit_toc_min_characters" type="number" value="<?php echo esc_attr( get_option( 'powerkit_toc_min_characters', 1000 ) ); ?>"></td>
								</tr>
								<!-- Display Button Show\Hide -->
								<tr>
									<th scope="row"><label for="powerkit_toc_btn_hide"><?php esc_html_e( 'Display Button Show\Hide', 'powerkit' ); ?></label></th>
									<td><input class="regular-text" id="powerkit_toc_btn_hide" name="powerkit_toc_btn_hide" type="checkbox" value="true" <?php checked( (bool) get_option( 'powerkit_toc_btn_hide', true ) ); ?>></td>
								</tr>
								<!-- Default State -->
								<tr>
									<th scope="row"><label for="powerkit_toc_default_state"><?php esc_html_e( 'Default State', 'powerkit' ); ?></label></th>
									<td>
										<select name="powerkit_toc_default_state" id="powerkit_toc_default_state">
											<option value="<?php echo esc_attr( 'expanded' ); ?>" <?php selected( get_option( 'powerkit_toc_default_state', 'expanded' ), 'expanded' ); ?>><?php esc_html_e( 'Expanded', 'powerkit' ); ?></option>
											<option value="<?php echo esc_attr( 'collapsed' ); ?>" <?php selected( get_option( 'powerkit_toc_default_state', 'expanded' ), 'collapsed' ); ?>><?php esc_html_e( 'Collapsed', 'powerkit' ); ?></option>
										</select>
									</td>
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
			if ( isset( $_POST['powerkit_toc_enable_automatically'] ) ) { // Input var ok.
				update_option( 'powerkit_toc_enable_automatically', true );
			} else {
				update_option( 'powerkit_toc_enable_automatically', false );
			}
			if ( isset( $_POST['powerkit_toc_title'] ) ) { // Input var ok.
				update_option( 'powerkit_toc_title', sanitize_text_field( $_POST['powerkit_toc_title'] ) ); // Input var ok; sanitization ok.
			}
			if ( isset( $_POST['powerkit_toc_exclude'] ) ) { // Input var ok.
				update_option( 'powerkit_toc_exclude', sanitize_text_field( $_POST['powerkit_toc_exclude'] ) ); // Input var ok; sanitization ok.
			}
			if ( isset( $_POST['powerkit_toc_depth'] ) ) { // Input var ok.
				update_option( 'powerkit_toc_depth', (int) sanitize_text_field( $_POST['powerkit_toc_depth'] ) ); // Input var ok; sanitization ok.
			}
			if ( isset( $_POST['powerkit_toc_min_count'] ) ) { // Input var ok.
				update_option( 'powerkit_toc_min_count', (int) sanitize_text_field( $_POST['powerkit_toc_min_count'] ) ); // Input var ok; sanitization ok.
			}
			if ( isset( $_POST['powerkit_toc_min_characters'] ) ) { // Input var ok.
				update_option( 'powerkit_toc_min_characters', (int) sanitize_text_field( $_POST['powerkit_toc_min_characters'] ) ); // Input var ok; sanitization ok.
			}
			if ( isset( $_POST['powerkit_toc_btn_hide'] ) ) { // Input var ok.
				update_option( 'powerkit_toc_btn_hide', true );
			} else {
				update_option( 'powerkit_toc_btn_hide', false );
			}
			if ( isset( $_POST['powerkit_toc_default_state'] ) ) { // Input var ok.
				update_option( 'powerkit_toc_default_state', sanitize_text_field( $_POST['powerkit_toc_default_state'] ) ); // Input var ok; sanitization ok.
			}

			printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Settings saved.', 'powerkit' ) );
		}
	}
}
