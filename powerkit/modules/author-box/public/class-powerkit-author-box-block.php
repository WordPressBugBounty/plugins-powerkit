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
class Powerkit_Author_Block {

	/**
	 * Initialize
	 */
	public function __construct() {
		add_filter( 'powerkit_pinit_exclude_selectors', array( $this, 'pinit_disable' ) );
		add_action( 'init', array( $this, 'block' ) );
		add_filter( 'canvas_register_block_type', array( $this, 'register_block_type' ) );
	}

	/**
	 * PinIt disable
	 *
	 * @param string $selectors List selectors.
	 */
	public function pinit_disable( $selectors ) {
		$selectors[] = '.pk-block-author';

		return $selectors;
	}

	/**
	 * Enqueue the block's assets for the editor.
	 */
	public function block() {
		// Styles.
		wp_register_style(
			'powerkit-author-block-editor-style',
			plugins_url( 'css/public-powerkit-author-box.css', __FILE__ ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'css/public-powerkit-author-box.css' )
		);

		wp_style_add_data( 'powerkit-author-block-editor-style', 'rtl', 'replace' );
	}

	/**
	 * Register block
	 *
	 * @param array $blocks all registered blocks.
	 * @return array
	 */
	public function register_block_type( $blocks ) {
		$users         = powerkit_get_users();
		$users_choices = array(
			'current' => esc_html__( 'Current Post’s Author' ),
		);

		foreach ( $users as $user ) {
			$users_choices[ $user->ID ] = $user->display_name;
		}

		$blocks[] = array(
			'name'         => 'canvas/author',
			'title'        => esc_html__( 'Author', 'powerkit' ),
			'description'  => '',
			'category'     => 'canvas',
			'keywords'     => array(),
			'icon'         => '
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
					<path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM7.07 18.28C7.5 17.38 10.12 16.5 12 16.5C13.88 16.5 16.51 17.38 16.93 18.28C15.57 19.36 13.86 20 12 20C10.14 20 8.43 19.36 7.07 18.28ZM18.36 16.83C16.93 15.09 13.46 14.5 12 14.5C10.54 14.5 7.07 15.09 5.64 16.83C4.62 15.49 4 13.82 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 13.82 19.38 15.49 18.36 16.83V16.83ZM12 6C10.06 6 8.5 7.56 8.5 9.5C8.5 11.44 10.06 13 12 13C13.94 13 15.5 11.44 15.5 9.5C15.5 7.56 13.94 6 12 6ZM12 11C11.17 11 10.5 10.33 10.5 9.5C10.5 8.67 11.17 8 12 8C12.83 8 13.5 8.67 13.5 9.5C13.5 10.33 12.83 11 12 11Z" />
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
						'key'     => 'author',
						'label'   => esc_html__( 'Author', 'powerkit' ),
						'section' => 'general',
						'type'    => 'select',
						'choices' => $users_choices,
						'default' => 'current',
					),
					array(
						'key'     => 'bgImage',
						'label'   => esc_html__( 'Background Image', 'powerkit' ),
						'section' => 'general',
						'type'    => 'image',
						'default' => '',
					),
					array(
						'key'     => 'showAvatar',
						'label'   => esc_html__( 'Display Avatar', 'powerkit' ),
						'section' => 'general',
						'type'    => 'toggle',
						'default' => true,
					),
					array(
						'key'     => 'showDescription',
						'label'   => esc_html__( 'Display Description', 'powerkit' ),
						'section' => 'general',
						'type'    => 'toggle',
						'default' => true,
					),
					array(
						'key'             => 'overrideDescription',
						'label'           => esc_html__( 'Override Description', 'powerkit' ),
						'section'         => 'general',
						'type'            => 'textarea',
						'default'         => '',
						'active_callback' => array(
							array(
								'field'    => 'showDescription',
								'operator' => '==',
								'value'    => true,
							),
						),
					),
					array(
						'key'             => 'descriptionLength',
						'label'           => esc_html__( 'Description Length', 'powerkit' ),
						'section'         => 'general',
						'type'            => 'number',
						'step'            => 1,
						'min'             => 0,
						'max'             => 5000,
						'default'         => 100,
						'active_callback' => array(
							array(
								'field'    => 'showDescription',
								'operator' => '==',
								'value'    => true,
							),
						),
					),
					array(
						'key'     => 'showSocialAccounts',
						'label'   => esc_html__( 'Display Social Accounts', 'powerkit' ),
						'section' => 'general',
						'type'    => 'toggle',
						'default' => true,
					),
					array(
						'key'     => 'showArchiveBtn',
						'label'   => esc_html__( 'Display Archive Button', 'powerkit' ),
						'section' => 'general',
						'type'    => 'toggle',
						'default' => false,
					),
				), powerkit_get_gutenberg_button_fields(
					'button',
					'buttonSettings',
					array(
						array(
							'field'    => 'showArchiveBtn',
							'operator' => '==',
							'value'    => true,
						),
					)
				)
			),
			'template'     => dirname( __FILE__ ) . '/block/render.php',

			// enqueue registered scripts/styles.
			'editor_style' => 'powerkit-author-block-editor-style',
		);

		return $blocks;
	}
}

new Powerkit_Author_Block();
