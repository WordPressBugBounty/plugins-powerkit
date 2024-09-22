<?php
/**
 * The Featured Categories Block.
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
class Powerkit_Featured_Categories_Block {

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
			'powerkit-featured-categories-block-editor-style',
			plugins_url( 'css/public-powerkit-featured-categories.css', __FILE__ ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'css/public-powerkit-featured-categories.css' )
		);

		wp_style_add_data( 'powerkit-featured-categories-block-editor-style', 'rtl', 'replace' );
	}

	/**
	 * Register block
	 *
	 * @param array $blocks all registered blocks.
	 * @return array
	 */
	public function register_block_type( $blocks ) {

		$blocks[] = array(
			'name'         => 'canvas/featured-categories',
			'title'        => esc_html__( 'Featured Categories', 'powerkit' ),
			'description'  => '',
			'category'     => 'canvas',
			'keywords'     => array(),
			'icon'         => '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><path fill="#000" d="M3 5h6v6H3z"/><path stroke="#000" stroke-width="2" stroke-linejoin="round" d="M11 6h10M11 10h10"/><g><path fill="#000" d="M3 13h6v6H3z"/><path stroke="#000" stroke-width="2" stroke-linejoin="round" d="M11 14h10M11 18h10"/></g></g></svg>',
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
				'general' => array(
					'title'    => esc_html__( 'Block Settings', 'powerkit' ),
					'priority' => 5,
					'open'     => true,
				),
				'color'   => array(
					'title'    => esc_html__( 'Color Settings', 'powerkit' ),
					'priority' => 60,
				),
			),
			'layouts'      => powerkit_featured_categories_locations(),
			'fields'       => array(
				array(
					'key'     => 'filter_ids',
					'label'   => esc_html__( 'Filter by Categories', 'powerkit' ),
					'section' => 'general',
					'type'    => 'categories-selector',
					'default' => '',
				),
				array(
					'key'      => 'orderby',
					'label'    => esc_html__( 'Order By', 'powerkit' ),
					'section'  => 'general',
					'type'     => 'select',
					'multiple' => false,
					'choices'  => array(
						'name'    => esc_html__( 'Name', 'powerkit' ),
						'count'   => esc_html__( 'Posts count', 'powerkit' ),
						'include' => esc_html__( 'Filter include', 'powerkit' ),
						'id'      => esc_html__( 'ID', 'powerkit' ),
					),
					'default'  => 'tile',
				),
				array(
					'key'      => 'order',
					'label'    => esc_html__( 'Order', 'powerkit' ),
					'section'  => 'general',
					'type'     => 'select',
					'multiple' => false,
					'choices'  => array(
						'ASC'  => esc_html__( 'ASC', 'powerkit' ),
						'DESC' => esc_html__( 'DESC', 'powerkit' ),
					),
					'default'  => 'ASC',
				),
				array(
					'key'     => 'maximum',
					'label'   => esc_html__( 'Maximum count', 'powerkit' ),
					'section' => 'general',
					'type'    => 'number',
					'default' => 0,
					'step'    => 1,
					'min'     => 0,
					'max'     => 1000,
				),
				array(
					'key'     => 'number',
					'label'   => esc_html__( 'Display number posts', 'powerkit' ),
					'section' => 'general',
					'type'    => 'toggle',
					'default' => true,
				),
				array(
					'key'     => 'bgOverlay',
					'label'   => esc_html__( 'Background Overlay', 'powerkit' ),
					'section' => 'color',
					'type'    => 'color',
					'output'  => array(
						array(
							'element'  => '$ .pk-featured-item .pk-featured-content:before',
							'property' => 'background-color',
							'suffix'   => '!important',
						),
					),
				),
				array(
					'key'     => 'bgOpacityOverlay',
					'label'   => esc_html__( 'Background Overlay Opacity', 'powerkit' ),
					'section' => 'color',
					'type'    => 'number',
					'step'    => 0.1,
					'min'     => 0,
					'max'     => 1,
					'default' => 0.3,
					'output'  => array(
						array(
							'element'  => '$ .pk-featured-item .pk-featured-content:before',
							'property' => 'opacity',
							'suffix'   => '!important',
						),
					),
				),
			),
			'template'     => dirname( __FILE__ ) . '/block/tiles.php',
			// enqueue registered scripts/styles.
			'editor_style' => 'powerkit-featured-categories-block-editor-style',
		);

		return $blocks;
	}
}

new Powerkit_Featured_Categories_Block();
