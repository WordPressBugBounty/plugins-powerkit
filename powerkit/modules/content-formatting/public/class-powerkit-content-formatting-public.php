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
class Powerkit_Content_Formatting_Public extends Powerkit_Module_Public {
	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 6 );
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 6 );
	}

	/**
	 * This function will register scripts and styles for admin dashboard.
	 *
	 * @param string $page Current page.
	 */
	public function admin_enqueue_scripts( $page ) {
		wp_enqueue_style( 'powerkit-content-formatting', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-content-formatting.css' ), array(), powerkit_get_setting( 'version' ) );
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	public function after_setup_theme() {
		add_editor_style( powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-content-formatting.css' ) );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'powerkit-content-formatting', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-content-formatting.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-content-formatting', 'rtl', 'replace' );
	}
}
