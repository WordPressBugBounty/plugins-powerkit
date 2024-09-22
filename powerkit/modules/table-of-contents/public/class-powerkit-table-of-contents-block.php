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
class Powerkit_TOC_Block {

	/**
	 * Initialize
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'block' ) );
		add_filter( 'canvas_register_block_type', array( $this, 'register_block_type' ) );
		add_action( 'rest_api_init', array( $this, 'register_toc_rest' ) );
	}

	/**
	 * Enqueue the block's assets for the editor.
	 */
	public function block() {
		// Scripts.
		wp_register_script(
			'powerkit-toc-block-script',
			plugins_url( 'block/block.js', __FILE__ ),
			array( 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n', 'wp-editor' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'block/block.js' ),
			true
		);

		// Styles.
		wp_register_style(
			'powerkit-toc-block-editor-style',
			plugins_url( 'css/public-powerkit-table-of-contents.css', __FILE__ ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'css/public-powerkit-table-of-contents.css' )
		);

		wp_style_add_data( 'powerkit-toc-block-editor-style', 'rtl', 'replace' );
	}

	/**
	 * Register block
	 *
	 * @param array $blocks all registered blocks.
	 * @return array
	 */
	public function register_block_type( $blocks ) {
		$blocks[] = array(
			'name'          => 'canvas/toc',
			'title'         => esc_html__( 'Table of Contents', 'powerkit' ),
			'description'   => '',
			'category'      => 'canvas',
			'keywords'      => array( 'toc', 'table', 'contents' ),
			'icon'          => '
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
					<path d="M11 7H17V9H11V7ZM11 11H17V13H11V11ZM11 15H17V17H11V15ZM7 7H9V9H7V7ZM7 11H9V13H7V11ZM7 15H9V17H7V15ZM20.1 3H3.9C3.4 3 3 3.4 3 3.9V20.1C3 20.5 3.4 21 3.9 21H20.1C20.5 21 21 20.5 21 20.1V3.9C21 3.4 20.5 3 20.1 3V3ZM19 19H5V5H19V19Z" />
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
					'key'     => 'title',
					'label'   => esc_html__( 'Title', 'powerkit' ),
					'type'    => 'text',
					'default' => 'Table of Contents',
					'section' => 'general',
				),
				array(
					'key'     => 'depth',
					'label'   => esc_html__( 'Depth of Headings', 'powerkit' ),
					'type'    => 'number',
					'min'     => 1,
					'max'     => 6,
					'default' => 2,
					'section' => 'general',
				),
				array(
					'key'     => 'minCount',
					'label'   => esc_html__( 'Minimum number of headings in page content', 'powerkit' ),
					'type'    => 'number',
					'min'     => 1,
					'max'     => 10,
					'default' => 4,
					'section' => 'general',
				),
				array(
					'key'     => 'minCharacters',
					'label'   => esc_html__( 'Minimum number of characters of post content', 'powerkit' ),
					'type'    => 'number',
					'min'     => 0,
					'max'     => 3000,
					'default' => 1000,
					'section' => 'general',
				),
				array(
					'key'     => 'btnHide',
					'label'   => esc_html__( 'Display Button Show\Hide', 'powerkit' ),
					'type'    => 'toggle',
					'default' => false,
					'section' => 'general',
				),
				array(
					'key'             => 'defaultState',
					'label'           => esc_html__( 'Default State', 'powerkit' ),
					'type'            => 'select',
					'default'         => 'expanded',
					'section'         => 'general',
					'choices'         => array(
						'expanded'  => esc_html__( 'Expanded', 'powerkit' ),
						'collapsed' => esc_html__( 'Collapsed', 'powerkit' ),
					),
					'active_callback' => array(
						array(
							'field'    => 'btnHide',
							'operator' => '==',
							'value'    => true,
						),
					),
				),
			),
			'template'      => dirname( __FILE__ ) . '/block/render.php',

			// enqueue registered scripts/styles.
			'editor_script' => 'powerkit-toc-block-script',
			'editor_style'  => 'powerkit-toc-block-editor-style',
		);

		return $blocks;
	}

	/**
	 * Register rest api
	 */
	public function register_toc_rest() {
		register_rest_route(
			'powerkit-toc/v1',
			'/get',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'get_toc_rest' ),
				'permission_callback' => function() {
					return true;
				},
			)
		);
	}

	/**
	 * Callback from rest api
	 *
	 * @param array $request request.
	 */
	public function get_toc_rest( $request ) {
		$args = array(
			'content' => $request['content'],
			'params'  => $request['params'],
		);

		if ( empty( $args['content'] ) ) {
			return new WP_Error( 'empty_content', 'Please, specify `content`', array( 'status' => 404 ) );
		}

		ob_start();

		powerkit_toc_list( $args['params'], $args['content'] );

		$result = ob_get_clean();

		$response = new WP_REST_Response( $result );
		$response->set_status( 200 );

		return $response;
	}
}

new Powerkit_TOC_Block();
