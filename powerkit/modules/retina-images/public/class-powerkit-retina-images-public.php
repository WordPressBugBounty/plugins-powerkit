<?php
/**
 * The public-facing functionality of the module.
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Modules/public
 */

/**
 * The public-facing functionality of the module.
 */
class Powerkit_Retina_Images_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'init', array( $this, 'register_retina_sizes' ), 20 );
	}

	/**
	 * Register retina sizes
	 */
	public function register_retina_sizes() {
		global $pagenow;

		if ( 'options-media.php' === $pagenow || 'options.php' === $pagenow ) {
			return;
		}

		$images_sizes = powerkit_retina_images_get_register_sizes();
		$filter_sizes = get_option( 'powerkit_retina_images_sizes' );

		// Register new Retina Sizes.
		foreach ( $images_sizes as $_size => $size_data ) {
			if ( in_array( $_size, (array) $filter_sizes, true ) ) {
				continue;
			}

			$new_width  = (int) $size_data['width'] * 2;
			$new_height = (int) $size_data['height'] * 2;

			add_image_size( $_size . '-2x', $new_width, $new_height, $size_data['crop'] );
		}
	}
}
