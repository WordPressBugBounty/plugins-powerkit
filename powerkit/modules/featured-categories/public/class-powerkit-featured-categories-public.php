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
class Powerkit_Featured_Categories_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		// PinIt exclude selectors.
		add_filter( 'powerkit_pinit_exclude_selectors', array( $this, 'pinit_disable' ) );
	}

	/**
	 * PinIt exclude selectors
	 *
	 * @param string $selectors List selectors.
	 */
	public function pinit_disable( $selectors ) {
		$selectors[] = '.pk-featured-categories img';

		return $selectors;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'powerkit-featured-categories', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-featured-categories.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-featured-categories', 'rtl', 'replace' );
	}
}
