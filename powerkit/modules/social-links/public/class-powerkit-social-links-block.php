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
class Powerkit_Social_Links_Block {

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
			'powerkit-social-links-block-editor-style',
			plugins_url( 'css/public-powerkit-social-links.css', __FILE__ ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'css/public-powerkit-social-links.css' )
		);

		wp_style_add_data( 'powerkit-social-links-block-editor-style', 'rtl', 'replace' );
	}

	/**
	 * Register block
	 *
	 * @param array $blocks all registered blocks.
	 * @return array
	 */
	public function register_block_type( $blocks ) {
		$additional_fields = array();
		$styles            = array();
		$colors            = apply_filters( 'powerkit_social_links_color_schemes', array() );
		$templates         = powerkit_social_links_get_templates();

		// Colors.
		if ( count( (array) $colors ) > 1 ) {
			foreach ( $colors as $name => $scheme ) {
				$styles[] = array(
					'name'  => 'pk-social-links-' . $name,
					'label' => $scheme['name'],
				);
			}
		}

		// Templates.
		if ( count( (array) $templates ) > 1 ) {
			$additional_fields[] = array(
				'key'     => 'template',
				'label'   => esc_html__( 'Template', 'powerkit' ),
				'section' => 'general',
				'type'    => 'select',
				'default' => 'inline',
				'choices' => $templates,
			);

			$additional_fields[] = array(
				'key'             => 'aligning',
				'label'           => esc_html__( 'Aligning Items', 'powerkit' ),
				'section'         => 'general',
				'type'            => 'select',
				'default'         => 'default',
				'choices'         => array(
					'default' => esc_html__( 'Default', 'powerkit' ),
					'left'    => esc_html__( 'Left', 'powerkit' ),
					'center'  => esc_html__( 'Center', 'powerkit' ),
					'right'   => esc_html__( 'Right', 'powerkit' ),
				),
				'active_callback' => array(
					array(
						'field'    => 'template',
						'operator' => '==',
						'value'    => 'inline',
					),
				),
			);
		}

		$blocks[] = array(
			'name'         => 'canvas/social-links',
			'title'        => esc_html__( 'Social Links', 'powerkit' ),
			'category'     => 'canvas',
			'keywords'     => array(),
			'icon'         => '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><path fill="none" d="M0 0h24v24H0z"/><g stroke="#2D2D2D" stroke-width="1.5"><path d="M10 12.83a5.45 5.45 0 003.97 2.156 5.46 5.46 0 004.237-1.571l3.265-3.246a5.387 5.387 0 00-.066-7.584 5.465 5.465 0 00-7.63-.066l-1.871 1.85"/><path d="M14 11.17a5.45 5.45 0 00-3.97-2.156 5.46 5.46 0 00-4.237 1.571l-3.265 3.246a5.387 5.387 0 00.066 7.584 5.465 5.465 0 007.63.066l1.86-1.85"/></g></g></svg>',
			'supports'     => array(
				'className'        => true,
				'anchor'           => true,
				'html'             => false,
				'canvasSpacings'   => true,
				'canvasBorder'     => true,
				'canvasResponsive' => true,
			),
			'styles'       => $styles,
			'location'     => array(),

			'sections'     => array(
				'general' => array(
					'title'    => esc_html__( 'Block Settings', 'powerkit' ),
					'priority' => 5,
					'open'     => true,
				),
			),
			'layouts'      => array(),
			'fields'       => array_merge(
				$additional_fields,
				array(
					array(
						'key'     => 'showLabels',
						'label'   => esc_html__( 'Display Labels', 'powerkit' ),
						'section' => 'general',
						'type'    => 'toggle',
						'default' => true,
					),
					array(
						'key'     => 'showTitles',
						'label'   => esc_html__( 'Display Titles', 'powerkit' ),
						'section' => 'general',
						'type'    => 'toggle',
						'default' => true,
					),
					array(
						'key'     => 'showCounts',
						'label'   => esc_html__( 'Display Counts', 'powerkit' ),
						'section' => 'general',
						'type'    => 'toggle',
						'default' => true,
					),
					array(
						'key'     => 'count',
						'label'   => esc_html__( 'Maximum number of social links', 'powerkit' ),
						'help'    => esc_html__( 'Input -1 to remove the maximum limit of the social links.', 'powerkit' ),
						'section' => 'general',
						'type'    => 'number',
						'min'     => -1,
						'max'     => 50,
						'default' => -1,
					),
				)
			),
			'template'     => dirname( __FILE__ ) . '/block/render.php',

			// enqueue registered scripts/styles.
			'editor_style' => 'powerkit-social-links-block-editor-style',
		);

		return $blocks;
	}
}

new Powerkit_Social_Links_Block();
