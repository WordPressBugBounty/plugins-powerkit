<?php
/**
 * The Gutenberg Block.
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Modules/public
 */

/**
 * The initialize block.
 */
class Powerkit_Twitter_Block {

	/**
	 * Initialize
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'block' ) );
		add_filter( 'canvas_register_block_type', array( $this, 'register_block_type' ) );
	}

	/**
	 * Enqueue the block's assets for the editor.
	 */
	public function block() {
		// Styles.
		wp_register_style(
			'powerkit-twitter-block-editor-style',
			plugins_url( 'css/public-powerkit-twitter.css', __FILE__ ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'css/public-powerkit-twitter.css' )
		);

		wp_style_add_data( 'powerkit-twitter-block-editor-style', 'rtl', 'replace' );
	}

	/**
	 * Register block
	 *
	 * @param array $blocks all registered blocks.
	 * @return array
	 */
	public function register_block_type( $blocks ) {
		$blocks[] = array(
			'name'         => 'canvas/twitter',
			'title'        => esc_html__( 'Twitter', 'powerkit' ),
			'description'  => esc_html__( 'The block allows you to display feed from your twitter account.', 'powerkit' ),
			'category'     => 'canvas',
			'keywords'     => array(),
			'icon'         => '
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
					<path d="M22 5.924C21.264 6.25 20.473 6.471 19.643 6.57C20.49 6.062 21.141 5.258 21.447 4.3C20.654 4.77 19.777 5.112 18.841 5.296C18.095 4.498 17.028 4 15.848 4C13.582 4 11.745 5.837 11.745 8.103C11.745 8.425 11.781 8.738 11.851 9.038C8.441 8.868 5.418 7.234 3.394 4.751C3.041 5.358 2.838 6.063 2.838 6.815C2.838 8.239 3.562 9.495 4.663 10.23C3.99 10.208 3.358 10.023 2.803 9.716V9.768C2.803 11.756 4.218 13.415 6.096 13.791C5.752 13.886 5.389 13.936 5.016 13.936C4.751 13.936 4.494 13.91 4.243 13.862C4.765 15.492 6.281 16.679 8.076 16.712C6.672 17.812 4.902 18.469 2.98 18.469C2.648 18.469 2.32 18.449 2 18.412C3.816 19.576 5.973 20.255 8.29 20.255C15.837 20.255 19.965 14.003 19.965 8.58C19.965 8.402 19.961 8.225 19.953 8.05C20.755 7.472 21.45 6.75 22 5.926V5.924Z" />
				</svg>
			',
			'supports'     => array(
				'className'        => true,
				'anchor'           => true,
				'html'             => false,
				'canvasSpacings'   => true,
				'canvasBorder'     => true,
				'canvasResponsive' => true,
			),
			'styles'       => array(),
			'location'     => array(),
			'sections'     => array(
				'general'        => array(
					'title'    => esc_html__( 'Block Settings', 'powerkit' ),
					'priority' => 5,
					'open'     => true,
				),
				'buttonSettings' => array(
					'title'    => esc_html__( 'Button Settings', 'powerkit' ),
					'priority' => 50,
				),
			),
			'layouts'      => array(),
			'fields'       => array_merge(
				array(
					array(
						'key'     => 'showHeader',
						'label'   => esc_html__( 'Display Header', 'powerkit' ),
						'section' => 'general',
						'type'    => 'toggle',
						'default' => true,
					),
					array(
						'key'     => 'showFollowButton',
						'label'   => esc_html__( 'Display Follow Button', 'powerkit' ),
						'section' => 'general',
						'type'    => 'toggle',
						'default' => true,
					),
					array(
						'key'     => 'number',
						'label'   => esc_html__( 'Number of Tweets', 'powerkit' ),
						'section' => 'general',
						'type'    => 'number',
						'min'     => 1,
						'max'     => 12,
						'default' => 4,
					),
				), powerkit_get_gutenberg_button_fields(
					'button',
					'buttonSettings',
					array(
						array(
							'field'    => 'showFollowButton',
							'operator' => '==',
							'value'    => true,
						),
					)
				)
			),
			'template'     => dirname( __FILE__ ) . '/block/render.php',

			// enqueue registered scripts/styles.
			'editor_style' => 'powerkit-twitter-block-editor-style',
		);

		return $blocks;
	}
}

new Powerkit_Twitter_Block();
