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
class Powerkit_Headers_Footers_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'wp_head', array( $this, 'header' ) );
		add_action( 'wp_footer', array( $this, 'footer' ) );
	}

	/**
	 * Outputs script / CSS to the frontend header.
	 */
	public function header() {
		$this->output( 'powerkit_insert_header_code' );
	}

	/**
	 * Outputs script / CSS to the frontend footer.
	 */
	public function footer() {
		$this->output( 'powerkit_insert_footer_code' );
	}

	/**
	 * Outputs the given setting, if conditions are met.
	 *
	 * @param string $setting Setting Name.
	 */
	public function output( $setting ) {
		// Ignore admin, feed, robots or trackbacks.
		if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
			return;
		}

		// Get code.
		$code = get_option( $setting );

		// Output.
		echo (string) $code; // XSS.
	}
}
