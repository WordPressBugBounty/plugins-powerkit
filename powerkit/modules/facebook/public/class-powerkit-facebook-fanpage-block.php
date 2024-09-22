<?php
/**
 * Facebook Fanpage Block.
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Modules/public
 */

/**
 * Initialize Facebook Fanpage block.
 */
class Powerkit_Block_Facebook_Fanpage {

	/**
	 * Initialize
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'canvas_register_block_type', array( $this, 'register_block_type' ) );
	}

	/**
	 * Enqueue the block's assets for the editor.
	 */
	public function init() {
		// Editor Scripts.
		wp_register_script(
			'powerkit-block-facebook-fanpage-editor-script',
			plugins_url( 'block/block.js', __FILE__ ),
			array( 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n', 'wp-editor', 'lodash', 'jquery' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'block/block.js' ),
			true
		);
	}

	/**
	 * Register block
	 *
	 * @param array $blocks all registered blocks.
	 * @return array
	 */
	public function register_block_type( $blocks ) {
		$blocks[] = array(
			'name'          => 'canvas/facebook-fanpage',
			'title'         => esc_html__( 'Facebook Fanpage', 'powerkit' ),
			'description'   => '',
			'category'      => 'canvas',
			'keywords'      => array( 'facebook', 'fanpage' ),
			'icon'          => '
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
					<path d="M20 3H4C3.4 3 3 3.4 3 4V20C3 20.5 3.4 21 4 21H12.6V14H10.3V11.3H12.6V9.3C12.6 7 14 5.7 16.1 5.7C17.1 5.7 17.9 5.8 18.2 5.8V8.2H16.8C15.7 8.2 15.5 8.7 15.5 9.5V11.2H18.2L17.8 14H15.5V21H20C20.5 21 21 20.6 21 20V4C21 3.4 20.6 3 20 3Z" />
				</svg>
			',
			'supports'      => array(
				'className'        => true,
				'anchor'           => true,
				'html'             => false,
				'canvasSpacings'   => true,
				'canvasBorder'     => true,
				'canvasResponsive' => true,
			),
			'styles'        => array(),
			'location'      => array(),
			'sections'      => array(
				'general' => array(
					'title'    => esc_html__( 'Block Settings', 'powerkit' ),
					'priority' => 5,
					'open'     => true,
				),
			),
			'layouts'       => array(),

			// Set fields just for add block attributes.
			// Editor render for this block is custom JSX
			// so we don't need to render fields automatically.
			'fields'        => array(
				array(
					'key'     => 'href',
					'label'   => esc_html__( 'Facebook Fanpage URL', 'powerkit' ),
					'type'    => 'text',
					'section' => 'general',
					'default' => '',
				),
				array(
					'key'     => 'showCover',
					'label'   => esc_html__( 'Display Cover', 'powerkit' ),
					'type'    => 'toggle',
					'section' => 'general',
					'default' => true,
				),
				array(
					'key'     => 'showFacepile',
					'label'   => esc_html__( 'Display Facepile', 'powerkit' ),
					'type'    => 'toggle',
					'section' => 'general',
					'default' => false,
				),
				array(
					'key'     => 'showPosts',
					'label'   => esc_html__( 'Display Posts', 'powerkit' ),
					'type'    => 'toggle',
					'section' => 'general',
					'default' => false,
				),
				array(
					'key'     => 'smallHeader',
					'label'   => esc_html__( 'Small Header', 'powerkit' ),
					'type'    => 'toggle',
					'section' => 'general',
					'default' => false,
				),
			),
			'template'      => dirname( __FILE__ ) . '/block/render.php',
			// enqueue registered scripts/styles.
			'editor_script' => 'powerkit-block-facebook-fanpage-editor-script',
		);

		return $blocks;
	}
}

new Powerkit_Block_Facebook_Fanpage();
