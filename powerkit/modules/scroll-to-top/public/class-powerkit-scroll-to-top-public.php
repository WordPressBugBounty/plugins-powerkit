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
class Powerkit_Scroll_To_Top_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );
	}

	/**
	 * Add html markup for the button.
	 */
	public function wp_footer() {
		ob_start();
		?>
			<a href="#top" class="pk-scroll-to-top">
				<i class="pk-icon pk-icon-up"></i>
			</a>
		<?php
		echo apply_filters( 'powerkit_scroll_to_top_template', ob_get_clean() ); // XSS.
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		// Styles.
		wp_enqueue_style( 'powerkit-scroll-to-top', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-scroll-to-top.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Scripts.
		wp_enqueue_script( 'powerkit-scroll-to-top', plugin_dir_url( __FILE__ ) . 'js/public-powerkit-scroll-to-top.js', array( 'jquery' ), powerkit_get_setting( 'version' ), true );
	}
}
