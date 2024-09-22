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
class Powerkit_Pinterest_Admin extends Powerkit_Module_Admin {

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

		add_settings_section( 'powerkit_pinit_settings', sprintf( '<span id="%s">%s</span>', powerkit_get_page_slug( $this->slug ), esc_html__( 'Pin It Button', 'powerkit' ) ), array( $this, 'powerkit_pinit_settings_callback' ), 'media' );

		add_settings_field( 'powerkit_pinit_image_selectors', esc_html__( 'Image selectors', 'powerkit' ), array( $this, 'powerkit_pinit_image_selectors_callback' ), 'media', 'powerkit_pinit_settings' );
		add_settings_field( 'powerkit_pinit_exclude_selectors', esc_html__( 'Exclude selectors', 'powerkit' ), array( $this, 'powerkit_pinit_exclude_selectors_callback' ), 'media', 'powerkit_pinit_settings' );
		add_settings_field( 'powerkit_pinit_only_hover', esc_html__( 'Display on mouse hover only', 'powerkit' ), array( $this, 'powerkit_pinit_only_hover_callback' ), 'media', 'powerkit_pinit_settings' );

		register_setting( 'media', 'powerkit_pinit_image_selectors' );
		register_setting( 'media', 'powerkit_pinit_exclude_selectors' );
		register_setting( 'media', 'powerkit_pinit_only_hover' );
	}

	/**
	 * Section Description.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_pinit_settings_callback() {
		return null;
	}

	/**
	 * Field | Image selectors.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_pinit_image_selectors_callback() {
		?>
			<textarea class="regular-text" id="powerkit_pinit_image_selectors" name="powerkit_pinit_image_selectors" rows="8"><?php echo esc_attr( get_option( 'powerkit_pinit_image_selectors', '.entry-content img' ) ); ?></textarea>
			<p class="description"><?php esc_html_e( 'Only images with these CSS classes will show the "Pin it" button. The new conditions add a new row.', 'powerkit' ); ?></p>
		<?php
	}

	/**
	 * Field | Exclude selectors.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_pinit_exclude_selectors_callback() {
		?>
			<textarea class="regular-text" id="powerkit_pinit_exclude_selectors" name="powerkit_pinit_exclude_selectors" rows="8"><?php echo esc_attr( get_option( 'powerkit_pinit_exclude_selectors', '' ) ); ?></textarea>
			<p class="description"><?php esc_html_e( 'Images with these CSS classes won\'t show the "Pin it" button. The new conditions add a new row.', 'powerkit' ); ?></p>
		<?php
	}

	/**
	 * Field | Display on mouse hover only.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_pinit_only_hover_callback() {
		?>
			<input class="regular-text" id="powerkit_pinit_only_hover" name="powerkit_pinit_only_hover" type="checkbox" value="true" <?php checked( (bool) get_option( 'powerkit_pinit_only_hover', true ) ); ?>>
		<?php
	}
}
