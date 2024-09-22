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
class Powerkit_Author_Box_Public extends Powerkit_Module_Public {
	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'init', array( $this, 'register_templates' ) );
		add_filter( 'powerkit_widget_author_templates', array( $this, 'list_templates' ) );
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/widget-author-template.php';
	}

	/**
	 * Filter Register Templates
	 *
	 * @since    1.0.0
	 * @access   private
	 *
	 * @param array $templates List of Templates.
	 */
	public function list_templates( $templates = array() ) {
		$templates = array(
			'default' => array(
				'name' => esc_html__( 'Default', 'powerkit' ),
				'func' => 'powerkit_widget_author_default_template',
			),
		);

		return $templates;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'powerkit-author-box', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-author-box.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-author-box', 'rtl', 'replace' );
	}
}
