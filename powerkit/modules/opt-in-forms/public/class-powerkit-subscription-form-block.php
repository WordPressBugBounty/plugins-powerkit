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
class Powerkit_Subscription_Block {

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
			'powerkit-subscription-block-editor-style',
			plugins_url( 'css/public-powerkit-opt-in-forms.css', __FILE__ ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'css/public-powerkit-opt-in-forms.css' )
		);

		wp_style_add_data( 'powerkit-subscription-block-editor-style', 'rtl', 'replace' );
	}

	/**
	 * Register block
	 *
	 * @param array $blocks all registered blocks.
	 * @return array
	 */
	public function register_block_type( $blocks ) {
		$blocks[] = array(
			'name'         => 'canvas/opt-in-form',
			'title'        => esc_html__( 'Opt-In Form', 'powerkit' ),
			'description'  => '',
			'category'     => 'canvas',
			'keywords'     => array( 'form', 'subscription', 'mailchimp' ),
			'icon'         => '
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
					<path d="M19 15V19H5V15H19ZM20 13H4C3.45 13 3 13.45 3 14V20C3 20.55 3.45 21 4 21H20C20.55 21 21 20.55 21 20V14C21 13.45 20.55 13 20 13ZM7 18.5C6.18 18.5 5.5 17.83 5.5 17C5.5 16.17 6.18 15.5 7 15.5C7.82 15.5 8.5 16.17 8.5 17C8.5 17.83 7.83 18.5 7 18.5ZM19 5V9H5V5H19ZM20 3H4C3.45 3 3 3.45 3 4V10C3 10.55 3.45 11 4 11H20C20.55 11 21 10.55 21 10V4C21 3.45 20.55 3 20 3ZM7 8.5C6.18 8.5 5.5 7.83 5.5 7C5.5 6.17 6.18 5.5 7 5.5C7.82 5.5 8.5 6.18 8.5 7C8.5 7.82 7.83 8.5 7 8.5Z" />
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
					'key'     => 'listId',
					'label'   => esc_html__( 'List ID', 'powerkit' ),
					'help'    => '
						<em>' . esc_html__( 'If empty, List ID from Settings &rarr; Opt-In Forms will be used.', 'powerkit' ) . '</em>
						<ol>
							<li>' . esc_html__( 'Log in to your', 'powerkit' ) . ' <a href="https://mailchimp.com" target="_blank">' . esc_html__( 'MailChimp account', 'powerkit' ) . '</a></li>
							<li>' . esc_html__( 'Go to your Lists.', 'powerkit' ) . '</li>
							<li>' . esc_html__( 'Select the desired list and in the drop-down menu and go to Settings.', 'powerkit' ) . '</li>
							<li>' . esc_html__( 'Copy your list ID from the field “Unique ID for list …”.', 'powerkit' ) . '</li>
						</ol>
					',
					'section' => 'general',
					'type'    => 'text',
					'default' => '',
				),
				array(
					'key'     => 'showName',
					'label'   => esc_html__( 'Display First Name Field', 'powerkit' ),
					'help'    => esc_html__( 'Make sure you map the field in the MailChimp settings', 'powerkit' ),
					'section' => 'general',
					'type'    => 'toggle',
					'default' => false,
				),

				array(
					'key'     => 'colorLegend',
					'label'   => esc_html__( 'Color Legend', 'powerkit' ),
					'section' => 'general',
					'type'    => 'color',
					'output'  => array(
						array(
							'element'  => '$ .pk-subscribe-form-wrap .pk-privacy label',
							'property' => 'color',
						),
					),
				),
			),
			'template'     => dirname( __FILE__ ) . '/block/render.php',

			// enqueue registered scripts/styles.
			'editor_style' => 'powerkit-subscription-block-editor-style',
		);

		return $blocks;
	}
}

new Powerkit_Subscription_Block();
