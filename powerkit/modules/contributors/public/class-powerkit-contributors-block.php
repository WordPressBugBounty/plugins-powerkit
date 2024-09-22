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
class Powerkit_Contributors_Block {

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
			'powerkit-contributors-block-editor-style',
			plugins_url( 'css/public-powerkit-contributors.css', __FILE__ ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'css/public-powerkit-contributors.css' )
		);

		wp_style_add_data( 'powerkit-contributors-block-editor-style', 'rtl', 'replace' );
	}

	/**
	 * Register block
	 *
	 * @param array $blocks all registered blocks.
	 * @return array
	 */
	public function register_block_type( $blocks ) {
		$users         = powerkit_get_users();
		$users_choices = array();

		foreach ( $users as $user ) {
			$users_choices[ $user->ID ] = $user->display_name;
		}

		$blocks[] = array(
			'name'         => 'canvas/contributors',
			'title'        => esc_html__( 'Contributors', 'powerkit' ),
			'description'  => '',
			'category'     => 'canvas',
			'keywords'     => array(),
			'icon'         => '
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
					<path d="M12.51 9.99C12.51 8.34 11.16 6.99 9.51 6.99C7.86 6.99 6.51 8.34 6.51 9.99C6.51 11.64 7.86 12.99 9.51 12.99C11.16 12.99 12.51 11.64 12.51 9.99ZM9.51 10.99C8.96 10.99 8.51 10.54 8.51 9.99C8.51 9.44 8.96 8.99 9.51 8.99C10.06 8.99 10.51 9.44 10.51 9.99C10.51 10.54 10.06 10.99 9.51 10.99ZM16.01 12.99C17.12 12.99 18.01 12.1 18.01 10.99C18.01 9.88 17.12 8.99 16.01 8.99C14.9 8.99 14 9.88 14.01 10.99C14.01 12.1 14.9 12.99 16.01 12.99ZM12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2V2ZM5.85 17.11C6.53 16.57 8.12 16 9.51 16C9.58 16 9.66 16.01 9.74 16.01C9.98 15.37 10.41 14.72 11.04 14.15C10.48 14.05 9.95 13.99 9.51 13.99C8.21 13.99 6.12 14.44 4.78 15.42C4.28 14.38 4 13.22 4 11.99C4 7.58 7.59 3.99 12 3.99C16.41 3.99 20 7.58 20 11.99C20 13.19 19.73 14.33 19.25 15.36C18.25 14.77 16.89 14.49 16.01 14.49C14.49 14.49 11.51 15.3 11.51 17.19V19.97C9.24 19.84 7.22 18.76 5.85 17.11V17.11Z" />
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
				'general' => array(
					'title'    => esc_html__( 'Block Settings', 'powerkit' ),
					'priority' => 5,
					'open'     => true,
				),
			),
			'layouts'      => array(),
			'fields'       => array(
				array(
					'key'      => 'contributors',
					'label'    => esc_html__( 'Contributors', 'powerkit' ),
					'section'  => 'general',
					'type'     => 'react-select',
					'choices'  => $users_choices,
					'multiple' => true,
					'default'  => array(),
					'items'    => array(
						'type' => 'integer',
					),
				),
				array(
					'key'     => 'showAvatar',
					'label'   => esc_html__( 'Display Avatar', 'powerkit' ),
					'section' => 'general',
					'type'    => 'toggle',
					'default' => true,
				),
				array(
					'key'     => 'showSocialAccounts',
					'label'   => esc_html__( 'Display Social Links', 'powerkit' ),
					'section' => 'general',
					'type'    => 'toggle',
					'default' => true,
				),
				array(
					'key'     => 'showBio',
					'label'   => esc_html__( 'Display Bio', 'powerkit' ),
					'section' => 'general',
					'type'    => 'toggle',
					'default' => true,
				),
				array(
					'key'     => 'showRecentPosts',
					'label'   => esc_html__( 'Display Recent Posts', 'powerkit' ),
					'section' => 'general',
					'type'    => 'toggle',
					'default' => false,
				),
				array(
					'key'             => 'countRecentPosts',
					'label'           => esc_html__( 'Number of Recent Posts', 'powerkit' ),
					'section'         => 'general',
					'type'            => 'number',
					'step'            => 1,
					'min'             => 0,
					'max'             => 1000,
					'default'         => 3,
					'active_callback' => array(
						array(
							'field' => 'showRecentPosts',
						),
					),
				),
			),
			'template'     => dirname( __FILE__ ) . '/block/render.php',

			// enqueue registered scripts/styles.
			'editor_style' => 'powerkit-contributors-block-editor-style',
		);

		return $blocks;
	}
}

new Powerkit_Contributors_Block();
