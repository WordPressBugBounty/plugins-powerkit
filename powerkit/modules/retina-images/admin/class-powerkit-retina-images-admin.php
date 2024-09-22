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
class Powerkit_Retina_Images_Admin extends Powerkit_Module_Admin {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'admin_init', array( $this, 'register_settings_section' ) );
		add_action( 'pre_update_option_powerkit_retina_images_sizes', array( $this, 'powerkit_retina_images_sizes_filter' ) );
	}

	/**
	 * Register admin page
	 *
	 * @since 1.0.0
	 */
	public function register_settings_section() {

		add_settings_section( 'powerkit_retina_images_settings', sprintf( '<span id="%s">%s</span>', powerkit_get_page_slug( $this->slug ), esc_html__( 'Retina Images', 'powerkit' ) ), array( $this, 'powerkit_retina_images_settings_callback' ), 'media' );

		add_settings_field( 'powerkit_retina_images_sizes', esc_html__( 'Sizes', 'powerkit' ), array( $this, 'powerkit_retina_images_sizes_callback' ), 'media', 'powerkit_retina_images_settings' );

		register_setting( 'media', 'powerkit_retina_images_sizes' );
	}

	/**
	 * Section Description.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_retina_images_settings_callback() {
		return null;
	}

	/**
	 * Filer Save | Sizes.
	 *
	 * @since 1.0.0

	 * @param mixed $value The new option value.
	 */
	public function powerkit_retina_images_sizes_filter( $value ) {

		$images_sizes = powerkit_retina_images_get_register_sizes();
		$save_sizes   = array();

		foreach ( $images_sizes as $_size => $size_data ) {
			if ( ! in_array( $_size, (array) $value, true ) ) {
				$save_sizes[] = $_size;
			}
		}

		return $save_sizes;
	}

	/**
	 * Field | Sizes.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_retina_images_sizes_callback() {
		?>
			<?php
			$images_sizes = powerkit_retina_images_get_register_sizes();
			$filter_sizes = get_option( 'powerkit_retina_images_sizes' );

			// Loop Sizes.
			foreach ( $images_sizes as $_size => $size_data ) {
				if ( in_array( $_size, (array) $filter_sizes, true ) ) {
					$checked = false;
				} else {
					$checked = true;
				}
				?>
					<p><label><input class="regular-text" id="powerkit_retina_images_sizes" name="powerkit_retina_images_sizes[]" type="checkbox" value="<?php echo esc_attr( $_size ); ?>" <?php checked( $checked ); ?>> <?php echo esc_html( $_size ); ?></label></p>
				<?php
			}
			?>
		<?php
	}
}
