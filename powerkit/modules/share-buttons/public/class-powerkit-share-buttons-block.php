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
class Powerkit_Share_Buttons_Block {

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
			'powerkit-share-buttons-block-editor-style',
			plugins_url( 'css/public-powerkit-share-buttons.css', __FILE__ ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'css/public-powerkit-share-buttons.css' )
		);

		wp_style_add_data( 'powerkit-share-buttons-block-editor-style', 'rtl', 'replace' );
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
		$colors            = apply_filters( 'powerkit_share_buttons_color_schemes', array() );
		$layouts           = apply_filters( 'powerkit_share_buttons_color_layouts', array() );

		// Colors.
		if ( count( (array) $colors ) > 1 ) {
			foreach ( $colors as $name => $scheme ) {
				$styles[] = array(
					'name'  => 'pk-share-buttons-' . $name,
					'label' => $scheme['name'],
				);
			}
		}

		// Layouts.
		if ( count( (array) $layouts ) > 1 ) {
			$layouts_array = array();

			foreach ( $layouts as $name => $layout ) {
				$layouts_array[ $name ] = $layout['name'];
			}

			$additional_fields[] = array(
				'key'     => 'layout',
				'label'   => esc_html__( 'Layout', 'powerkit' ),
				'section' => 'general',
				'type'    => 'select',
				'default' => 'default',
				'choices' => $layouts_array,
			);
		}

		$accounts         = apply_filters( 'powerkit_share_buttons_accounts', array(), null, null );
		$accounts_choices = array();

		foreach ( $accounts as $key => $account ) {
			$accounts_choices[ $key ] = esc_html( $account['name'] );
		}

		$blocks[] = array(
			'name'         => 'canvas/share-buttons',
			'title'        => esc_html__( 'Share Buttons', 'powerkit' ),
			'category'     => 'canvas',
			'keywords'     => array(),
			'icon'         => '<svg width="22" height="24" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><path fill="none" d="M-1 0h24v24H-1z"/><path d="M17.667 16.4c1.84 0 3.333 1.477 3.333 3.3 0 1.823-1.492 3.3-3.333 3.3-1.841 0-3.334-1.477-3.334-3.3 0-1.823 1.493-3.3 3.334-3.3zM4.333 8.7c1.841 0 3.334 1.477 3.334 3.3 0 1.823-1.493 3.3-3.334 3.3C2.493 15.3 1 13.823 1 12c0-1.823 1.492-3.3 3.333-3.3zM17.667 1C19.507 1 21 2.477 21 4.3c0 1.823-1.492 3.3-3.333 3.3-1.841 0-3.334-1.477-3.334-3.3 0-1.823 1.493-3.3 3.334-3.3zM7.21 13.661l7.589 4.378m-.011-12.078L7.21 10.339" stroke="#2D2D2D" stroke-width="1.5"/></g></svg>',
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

			'sections'      => array(
				'general'        => array(
					'title'    => esc_html__( 'Block Settings', 'powerkit' ),
					'priority' => 5,
					'open'     => true,
				),
			),
			'layouts'      => array(),
			'fields'       => array_merge(
				$additional_fields, array(
					array(
						'key'      => 'accounts',
						'label'    => esc_html__( 'Accounts', 'powerkit' ),
						'section'  => 'general',
						'type'     => 'react-select',
						'multiple' => true,
						'choices'  => $accounts_choices,
						'default'  => array(
							'facebook',
							'twitter',
							'pinterest',
						),
						'items'    => array(
							'type' => 'string',
						),
					),
					array(
						'key'     => 'showTotal',
						'label'   => esc_html__( 'Display Total Shares', 'powerkit' ),
						'section' => 'general',
						'type'    => 'toggle',
						'default' => false,
					),
					array(
						'key'     => 'showIcons',
						'label'   => esc_html__( 'Display Icons', 'powerkit' ),
						'section' => 'general',
						'type'    => 'toggle',
						'default' => true,
					),
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
				)
			),
			'template'     => dirname( __FILE__ ) . '/block/render.php',
			// enqueue registered scripts/styles.
			'editor_style' => 'powerkit-share-buttons-block-editor-style',
		);

		return $blocks;
	}
}

new Powerkit_Share_Buttons_Block();
