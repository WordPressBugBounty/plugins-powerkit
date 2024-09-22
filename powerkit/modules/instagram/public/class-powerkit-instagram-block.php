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
class Powerkit_Instagram_Block {

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
			'powerkit-instagram-block-editor-style',
			plugins_url( 'css/public-powerkit-instagram.css', __FILE__ ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'css/public-powerkit-instagram.css' )
		);

		wp_style_add_data( 'powerkit-instagram-block-editor-style', 'rtl', 'replace' );
	}

	/**
	 * Register block
	 *
	 * @param array $blocks all registered blocks.
	 * @return array
	 */
	public function register_block_type( $blocks ) {
		$blocks[] = array(
			'name'         => 'canvas/instagram',
			'title'        => esc_html__( 'Instagram', 'powerkit' ),
			'description'  => esc_html__( 'The block allows you to display images from your instagram account.', 'powerkit' ),
			'category'     => 'canvas',
			'keywords'     => array(),
			'icon'         => '
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
					<path d="M12 4.622c2.403 0 2.688.01 3.637.052.877.04 1.354.187 1.67.31.42.163.72.358 1.036.673.315.315.51.615.673 1.035.123.317.27.794.31 1.67.043.95.052 1.235.052 3.638s-.01 2.688-.052 3.637c-.04.877-.187 1.354-.31 1.67-.163.42-.358.72-.673 1.036-.315.315-.615.51-1.035.673-.317.123-.794.27-1.67.31-.95.043-1.234.052-3.638.052s-2.688-.01-3.637-.052c-.877-.04-1.354-.187-1.67-.31-.42-.163-.72-.358-1.036-.673-.315-.315-.51-.615-.673-1.035-.123-.317-.27-.794-.31-1.67-.043-.95-.052-1.235-.052-3.638s.01-2.688.052-3.637c.04-.877.187-1.354.31-1.67.163-.42.358-.72.673-1.036.315-.315.615-.51 1.035-.673.317-.123.794-.27 1.67-.31.95-.043 1.235-.052 3.638-.052M12 3c-2.444 0-2.75.01-3.71.054s-1.613.196-2.185.418c-.592.23-1.094.538-1.594 1.04-.5.5-.807 1-1.037 1.593-.223.572-.375 1.226-.42 2.184C3.01 9.25 3 9.555 3 12s.01 2.75.054 3.71.196 1.613.418 2.186c.23.592.538 1.094 1.038 1.594s1.002.808 1.594 1.038c.572.222 1.227.375 2.185.418.96.044 1.266.054 3.71.054s2.75-.01 3.71-.054 1.613-.196 2.186-.418c.592-.23 1.094-.538 1.594-1.038s.808-1.002 1.038-1.594c.222-.572.375-1.227.418-2.185.044-.96.054-1.266.054-3.71s-.01-2.75-.054-3.71-.196-1.613-.418-2.186c-.23-.592-.538-1.094-1.038-1.594s-1.002-.808-1.594-1.038c-.572-.222-1.227-.375-2.185-.418C14.75 3.01 14.445 3 12 3zm0 4.378c-2.552 0-4.622 2.07-4.622 4.622s2.07 4.622 4.622 4.622 4.622-2.07 4.622-4.622S14.552 7.378 12 7.378zM12 15c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3zm4.804-8.884c-.596 0-1.08.484-1.08 1.08s.484 1.08 1.08 1.08c.596 0 1.08-.484 1.08-1.08s-.483-1.08-1.08-1.08z" />
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
						'label'   => esc_html__( 'Number of Images', 'powerkit' ),
						'section' => 'general',
						'type'    => 'number',
						'min'     => 1,
						'max'     => 999,
						'default' => 4,
					),
					array(
						'key'     => 'columns',
						'label'   => esc_html__( 'Number of Columns', 'powerkit' ),
						'section' => 'general',
						'type'    => 'number',
						'min'     => 1,
						'max'     => 7,
						'default' => 3,
					),
					array(
						'key'     => 'size',
						'label'   => esc_html__( 'Photo Size', 'powerkit' ),
						'section' => 'general',
						'type'    => 'select',
						'choices' => array(
							'thumbnail' => esc_html__( 'Thumbnail', 'powerkit' ),
							'small'     => esc_html__( 'Small', 'powerkit' ),
							'large'     => esc_html__( 'Large', 'powerkit' ),
						),
						'default' => 'thumbnail',
					),
					array(
						'key'     => 'target',
						'label'   => esc_html__( 'Open Links In', 'powerkit' ),
						'section' => 'general',
						'type'    => 'select',
						'choices' => array(
							'_blank' => esc_html__( 'New window (_blank)', 'powerkit' ),
							'_self'  => esc_html__( 'Current window (_self)', 'powerkit' ),
						),
						'default' => '_blank',
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
			'editor_style' => 'powerkit-instagram-block-editor-style',
		);

		return $blocks;
	}
}

new Powerkit_Instagram_Block();
