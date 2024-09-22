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
class Powerkit_Inline_Posts_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		// PinIt exclude selectors.
		add_filter( 'powerkit_pinit_exclude_selectors', array( $this, 'pinit_disable' ) );

		// Reset global related variable.
		add_filter( 'the_content', array( $this, 'reset_related' ), 1 );

		// Register Templates.
		add_action( 'init', array( $this, 'register_templates' ) );
		add_filter( 'powerkit_inline_posts_templates', array( $this, 'list_templates' ) );
	}

	/**
	 * Reset global $powerkit_inline_posts.
	 *
	 * @param string $content Content of the current post.
	 */
	public function reset_related( $content ) {
		global $powerkit_inline_posts;

		$powerkit_inline_posts = null;

		return $content;
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/inline-posts-template.php';
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
			'list'   => array(
				'name' => esc_html__( 'List', 'powerkit' ),
				'func' => 'powerkit_inline_posts_default_template',
			),
			'grid'   => array(
				'name' => esc_html__( 'Grid', 'powerkit' ),
				'func' => 'powerkit_inline_posts_default_template',
			),
			'grid-2' => array(
				'name' => esc_html__( 'Grid 2', 'powerkit' ),
				'func' => 'powerkit_inline_posts_default_template',
			),
			'grid-3' => array(
				'name' => esc_html__( 'Grid 3', 'powerkit' ),
				'func' => 'powerkit_inline_posts_default_template',
			),
			'grid-4' => array(
				'name' => esc_html__( 'Grid 4', 'powerkit' ),
				'func' => 'powerkit_inline_posts_default_template',
			),
		);

		return $templates;
	}

	/**
	 * PinIt exclude selectors
	 *
	 * @param string $selectors List selectors.
	 */
	public function pinit_disable( $selectors ) {
		$selectors[] = '.pk-inline-posts-container img';

		return $selectors;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'powerkit-inline-posts', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-inline-posts.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-inline-posts', 'rtl', 'replace' );
	}
}
