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
class Powerkit_Widget_About_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'powerkit-widget-about', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-widget-about.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-widget-about', 'rtl', 'replace' );
	}

	/**
	 * Register the stylesheets for the admin-facing side of the site.
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'powerkit-widget-about', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-widget-about.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-widget-about', 'rtl', 'replace' );
	}
}
