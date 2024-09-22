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
class Powerkit_Slider_Gallery_Admin extends Powerkit_Module_Admin {

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

		add_settings_section( 'powerkit_slider_gallery_settings', sprintf( '<span id="%s">%s</span>', powerkit_get_page_slug( $this->slug ), esc_html__( 'Slider Gallery', 'powerkit' ) ), array( $this, 'powerkit_slider_gallery_settings_callback' ), 'media' );

		add_settings_field( 'powerkit_slider_gallery_page_dots', esc_html__( 'Display page dots', 'powerkit' ), array( $this, 'powerkit_slider_gallery_page_dots_callback' ), 'media', 'powerkit_slider_gallery_settings' );
		add_settings_field( 'powerkit_slider_gallery_page_info', esc_html__( 'Display page info', 'powerkit' ), array( $this, 'powerkit_slider_gallery_page_info_callback' ), 'media', 'powerkit_slider_gallery_settings' );
		add_settings_field( 'powerkit_slider_gallery_nav', esc_html__( 'Display buttons to click to previous & next slide', 'powerkit' ), array( $this, 'powerkit_slider_gallery_nav_callback' ), 'media', 'powerkit_slider_gallery_settings' );

		register_setting( 'media', 'powerkit_slider_gallery_page_dots' );
		register_setting( 'media', 'powerkit_slider_gallery_page_info' );
		register_setting( 'media', 'powerkit_slider_gallery_nav' );
	}

	/**
	 * Section Description.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_slider_gallery_settings_callback() {
		return null;
	}

	/**
	 * Field | Display page dots.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_slider_gallery_page_dots_callback() {
		?>
			<input class="regular-text" id="powerkit_slider_gallery_page_dots" name="powerkit_slider_gallery_page_dots" type="checkbox" value="true" <?php checked( (bool) get_option( 'powerkit_slider_gallery_page_dots', true ) ); ?>>
		<?php
	}

	/**
	 * Field | Display page info.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_slider_gallery_page_info_callback() {
		?>
			<input class="regular-text" id="powerkit_slider_gallery_page_info" name="powerkit_slider_gallery_page_info" type="checkbox" value="true" <?php checked( (bool) get_option( 'powerkit_slider_gallery_page_info', true ) ); ?>>
		<?php
	}

	/**
	 * Field | Display buttons to click to previous & next slide.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_slider_gallery_nav_callback() {
		?>
			<input class="regular-text" id="powerkit_slider_gallery_nav" name="powerkit_slider_gallery_nav" type="checkbox" value="true" <?php checked( (bool) get_option( 'powerkit_slider_gallery_nav', true ) ); ?>>
		<?php
	}
}

