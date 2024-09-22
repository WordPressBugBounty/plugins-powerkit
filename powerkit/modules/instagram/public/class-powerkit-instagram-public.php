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
class Powerkit_Instagram_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'init', array( $this, 'register_templates' ) );
		add_filter( 'powerkit_instagram_templates', array( $this, 'template_default' ) );
		add_filter( 'powerkit_pinit_exclude_selectors', array( $this, 'pinit_disable' ) );
	}

	/**
	 * Register Templates
	 *
	 * @since    1.0.0
	 * @access   private
	 *
	 * @param array $templates List of Templates.
	 */
	public function register_templates( $templates = array() ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/instagram-template.php';
	}

	/**
	 * Filter Register Templates
	 *
	 * @since    1.0.0
	 * @access   private
	 *
	 * @param array $templates List of Templates.
	 */
	public function template_default( $templates = array() ) {
		$templates = array(
			'default' => array(
				'name' => 'Default',
				'func' => 'powerkit_instagram_default_template',
			),
		);

		return $templates;
	}

	/**
	 * PinIt disable
	 *
	 * @param string $selectors List selectors.
	 */
	public function pinit_disable( $selectors ) {
		$selectors[] = '.pk-instagram-image';

		return $selectors;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'powerkit-instagram', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-instagram.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-instagram', 'rtl', 'replace' );
	}
}
