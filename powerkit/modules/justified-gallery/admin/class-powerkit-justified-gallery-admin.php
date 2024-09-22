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
class Powerkit_Justified_Gallery_Admin extends Powerkit_Module_Admin {

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

		add_settings_section( 'powerkit_justified_gallery_settings', sprintf( '<span id="%s">%s</span>', powerkit_get_page_slug( $this->slug ), esc_html__( 'Justified Gallery', 'powerkit' ) ), array( $this, 'powerkit_justified_gallery_settings_callback' ), 'media' );

		add_settings_field( 'powerkit_justified_gallery_margins', esc_html__( 'Space between images', 'powerkit' ), array( $this, 'powerkit_justified_gallery_margins_callback' ), 'media', 'powerkit_justified_gallery_settings' );
		add_settings_field( 'powerkit_justified_gallery_row_height', esc_html__( 'Row height', 'powerkit' ), array( $this, 'powerkit_justified_gallery_row_height_callback' ), 'media', 'powerkit_justified_gallery_settings' );
		add_settings_field( 'powerkit_justified_gallery_max_row_height', esc_html__( 'Max row height', 'powerkit' ), array( $this, 'powerkit_justified_gallery_max_row_height_callback' ), 'media', 'powerkit_justified_gallery_settings' );
		add_settings_field( 'powerkit_justified_gallery_last_row', esc_html__( 'Last row', 'powerkit' ), array( $this, 'powerkit_justified_gallery_last_row_callback' ), 'media', 'powerkit_justified_gallery_settings' );

		register_setting( 'media', 'powerkit_justified_gallery_margins' );
		register_setting( 'media', 'powerkit_justified_gallery_row_height' );
		register_setting( 'media', 'powerkit_justified_gallery_max_row_height' );
		register_setting( 'media', 'powerkit_justified_gallery_last_row' );
	}

	/**
	 * Section Description.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_justified_gallery_settings_callback() {
		return null;
	}

	/**
	 * Field | Space between images.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_justified_gallery_margins_callback() {
		?>
			<input class="small-text" id="powerkit_justified_gallery_margins" name="powerkit_justified_gallery_margins" type="number" min="0" step="1" value="<?php echo esc_attr( get_option( 'powerkit_justified_gallery_margins', '10' ) ); ?>">
			<span><?php esc_html_e( 'px', 'powerkit' ); ?></span>
		<?php
	}

	/**
	 * Field | Row height.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_justified_gallery_row_height_callback() {
		?>
			<input class="small-text" id="powerkit_justified_gallery_row_height" name="powerkit_justified_gallery_row_height" type="number" min="0" step="1" value="<?php echo esc_attr( get_option( 'powerkit_justified_gallery_row_height', '160' ) ); ?>">
			<span><?php esc_html_e( 'px. The preferred height of rows.', 'powerkit' ); ?></span>
		<?php
	}

	/**
	 * Field | Space Max row height.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_justified_gallery_max_row_height_callback() {
		?>
			<input class="small-text" id="powerkit_justified_gallery_max_row_height" name="powerkit_justified_gallery_max_row_height" type="number" step="1" value="<?php echo esc_attr( get_option( 'powerkit_justified_gallery_max_row_height', '-1' ) ); ?>">
			<span><?php esc_html_e( 'px. Input -1 to remove the limit of the maximum row height.', 'powerkit' ); ?></span>
		<?php
	}

	/**
	 * Field | Last row.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_justified_gallery_last_row_callback() {
		?>
			<label><input id="powerkit_justified_gallery_last_row" name="powerkit_justified_gallery_last_row" type="radio" value="nojustify" <?php checked( 'nojustify', get_option( 'powerkit_justified_gallery_last_row', 'justify' ) ); ?>><?php esc_html_e( 'No Justify', 'powerkit' ); ?>&nbsp;&nbsp;</label>
			<label><input id="powerkit_justified_gallery_last_row" name="powerkit_justified_gallery_last_row" type="radio" value="justify" <?php checked( 'justify', get_option( 'powerkit_justified_gallery_last_row', 'justify' ) ); ?>><?php esc_html_e( 'Justify', 'powerkit' ); ?>&nbsp;&nbsp;</label>
			<label><input id="powerkit_justified_gallery_last_row" name="powerkit_justified_gallery_last_row" type="radio" value="hide" <?php checked( 'hide', get_option( 'powerkit_justified_gallery_last_row', 'justify' ) ); ?>><?php esc_html_e( 'Hide', 'powerkit' ); ?></label>
		<?php
	}
}

