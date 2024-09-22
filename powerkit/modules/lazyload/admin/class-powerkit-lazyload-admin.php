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
class Powerkit_Lazyload_Admin extends Powerkit_Module_Admin {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'admin_init', array( $this, 'register_settings_section' ) );
	}

	/**
	 * Register admin page
	 *
	 * @since 1.0.0
	 */
	public function register_settings_section() {

		add_settings_section( 'powerkit_lazyload_settings', sprintf( '<span id="%s">%s</span>', powerkit_get_page_slug( $this->slug ), esc_html__( 'Lazy Load', 'powerkit' ) ), array( $this, 'powerkit_lazyload_settings_callback' ), 'media' );

		register_setting( 'media', 'powerkit_lazyload_csco_lqip' );
	}

	/**
	 * Section Description.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_lazyload_settings_callback() {
		?>
			<label>
				<input class="regular-text" id="powerkit_lazyload_csco_lqip" name="powerkit_lazyload_csco_lqip" type="checkbox" value="true" <?php checked( (bool) get_option( 'powerkit_lazyload_csco_lqip', false ) ); ?>>
				<?php esc_html_e( 'Displays Low Quality Image Placeholders while the image is loading. Make sure you regenerate thumbnails after saving.', 'powerkit' ); ?>
			</label>
			<br><br>
		<?php
	}
}
