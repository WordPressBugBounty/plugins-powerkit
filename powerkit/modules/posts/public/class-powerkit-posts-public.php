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
class Powerkit_Posts_Public extends Powerkit_Module_Public {
	/**
	 * Initialize
	 */
	public function initialize() {
		add_filter( 'powerkit_pinit_exclude_selectors', array( $this, 'pinit_disable' ) );
		add_action( 'init', array( $this, 'register_templates' ) );
		add_filter( 'powerkit_featured_posts_templates', array( $this, 'list_templates' ) );
	}

	/**
	 * PinIt disable
	 *
	 * @param string $selectors List selectors.
	 */
	public function pinit_disable( $selectors ) {
		$selectors[] = '.pk-block-posts';

		return $selectors;
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/posts-template.php';
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
			'list'     => array(
				'name' => esc_html__( 'Simple List', 'powerkit' ),
				'func' => 'powerkit_featured_posts_default_template',
			),
			'numbered' => array(
				'name' => esc_html__( 'Numbered List', 'powerkit' ),
				'func' => 'powerkit_featured_posts_default_template',
			),
			'large'    => array(
				'name' => esc_html__( 'Large Thumbnails', 'powerkit' ),
				'func' => 'powerkit_featured_posts_default_template',
			),
		);

		return $templates;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'powerkit-widget-posts', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-widget-posts.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-widget-posts', 'rtl', 'replace' );
	}
}
