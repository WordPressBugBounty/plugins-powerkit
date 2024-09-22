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
class Powerkit_Lightbox_Admin extends Powerkit_Module_Admin {

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

		add_settings_section( 'powerkit_lightbox_settings', sprintf( '<span id="%s">%s</span>', powerkit_get_page_slug( $this->slug ), esc_html__( 'Lightbox', 'powerkit' ) ), array( $this, 'powerkit_lightbox_settings_callback' ), 'media' );

		add_settings_field( 'powerkit_lightbox_single_image_selectors', esc_html__( 'Single image selectors', 'powerkit' ), array( $this, 'powerkit_lightbox_single_image_selectors_callback' ), 'media', 'powerkit_lightbox_settings' );
		add_settings_field( 'powerkit_lightbox_gallery_selectors', esc_html__( 'Gallery selectors', 'powerkit' ), array( $this, 'powerkit_lightbox_gallery_selectors_callback' ), 'media', 'powerkit_lightbox_settings' );
		add_settings_field( 'powerkit_lightbox_exclude_selectors', esc_html__( 'Exclude selectors', 'powerkit' ), array( $this, 'powerkit_lightbox_exclude_selectors_callback' ), 'media', 'powerkit_lightbox_settings' );
		add_settings_field( 'powerkit_lightbox_zoom_icon', esc_html__( 'Display Lightbox zoom icon', 'powerkit' ), array( $this, 'powerkit_lightbox_zoom_icon_callback' ), 'media', 'powerkit_lightbox_settings' );

		register_setting( 'media', 'powerkit_lightbox_single_image_selectors' );
		register_setting( 'media', 'powerkit_lightbox_gallery_selectors' );
		register_setting( 'media', 'powerkit_lightbox_exclude_selectors' );
		register_setting( 'media', 'powerkit_lightbox_zoom_icon' );
	}

	/**
	 * Section Description.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_lightbox_settings_callback() {
		return null;
	}

	/**
	 * Field | Single Image selectors.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_lightbox_single_image_selectors_callback() {
		?>
			<textarea class="regular-text" id="powerkit_lightbox_single_image_selectors" name="powerkit_lightbox_single_image_selectors" rows="8"><?php echo esc_attr( get_option( 'powerkit_lightbox_single_image_selectors', '.entry-content img' ) ); ?></textarea>
			<p class="description"><?php esc_html_e( 'Lightbox will be enabled for images with these CSS classes only. Start every class with a new line.', 'powerkit' ); ?></p>
		<?php
	}

	/**
	 * Field | Gallery Image selectors.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_lightbox_gallery_selectors_callback() {
		?>
			<textarea class="regular-text" id="powerkit_lightbox_gallery_selectors" name="powerkit_lightbox_gallery_selectors" rows="8"><?php echo esc_attr( get_option( 'powerkit_lightbox_gallery_selectors', '.wp-block-gallery, .gallery' ) ); ?></textarea>
			<p class="description"><?php esc_html_e( 'Lightbox will be enabled for images inside containers with these CSS classes. Start every class with a new line.', 'powerkit' ); ?></p>
		<?php
	}

	/**
	 * Field | Exclude selectors.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_lightbox_exclude_selectors_callback() {
		?>
			<textarea class="regular-text" id="powerkit_lightbox_exclude_selectors" name="powerkit_lightbox_exclude_selectors" rows="8"><?php echo esc_attr( get_option( 'powerkit_lightbox_exclude_selectors', '' ) ); ?></textarea>
			<p class="description"><?php esc_html_e( 'Lightbox will be disabled for images with these CSS classes. Start every class with a new line.', 'powerkit' ); ?></p>
		<?php
	}

	/**
	 * Field | Display on mouse hover only.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_lightbox_zoom_icon_callback() {
		?>
			<input class="regular-text" id="powerkit_lightbox_zoom_icon" name="powerkit_lightbox_zoom_icon" type="checkbox" value="true" <?php checked( (bool) get_option( 'powerkit_lightbox_zoom_icon', true ) ); ?>>
		<?php
	}
}
