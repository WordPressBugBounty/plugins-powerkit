<?php
/**
 * The admin-specific functionality of the module.
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Modules/Admin
 */

/**
 * The admin-specific functionality of the module.
 */
class Powerkit_Content_Formatting_Admin extends Powerkit_Module_Admin {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'mce_buttons_2', array( $this, 'mce_buttons_2' ) );
		add_filter( 'tiny_mce_before_init', array( $this, 'mce_before_init_insert_formats' ) );
	}

	/**
	 * Unshift Styleselect.
	 *
	 * @param array $buttons array of buttons.
	 */
	public function mce_buttons_2( $buttons ) {
		array_unshift( $buttons, 'styleselect' );
		return $buttons;
	}

	/**
	 * Insert formats
	 *
	 * @param array $init_array array of style formats.
	 */
	public function mce_before_init_insert_formats( $init_array ) {

		$style_formats = array(
			array(
				'title' => esc_html__( 'Drop Cap', 'powerkit' ),
				'items' => array(
					array(
						'title'   => esc_html__( 'Simple', 'powerkit' ),
						'block'   => 'p',
						'classes' => 'pk-dropcap pk-dropcap-simple',
						'wrapper' => false,
					),
					array(
						'title'   => esc_html__( 'Bordered', 'powerkit' ),
						'block'   => 'p',
						'classes' => 'pk-dropcap pk-dropcap-borders',
						'wrapper' => false,
					),
					array(
						'title'   => esc_html__( 'Border Right', 'powerkit' ),
						'block'   => 'p',
						'classes' => 'pk-dropcap pk-dropcap-border-right',
						'wrapper' => false,
					),
					array(
						'title'   => esc_html__( 'Background Light', 'powerkit' ),
						'block'   => 'p',
						'classes' => 'pk-dropcap pk-block-bg pk-dropcap-bg-light',
						'wrapper' => false,
					),
					array(
						'title'   => esc_html__( 'Background Inverse', 'powerkit' ),
						'block'   => 'p',
						'classes' => 'pk-dropcap pk-block-bg pk-dropcap-bg-inverse',
						'wrapper' => false,
					),
				),
			),
			array(
				'title'   => esc_html__( 'Callout', 'powerkit' ),
				'block'   => 'div',
				'classes' => 'pk-callout',
			),
			array(
				'title' => esc_html__( 'Block', 'powerkit' ),
				'items' => array(
					array(
						'title' => esc_html__( 'Borders', 'powerkit' ),
						'items' => array(
							array(
								'title'   => esc_html__( 'Top', 'powerkit' ),
								'block'   => 'div',
								'classes' => 'pk-content-block pk-block-border-top',
							),
							array(
								'title'   => esc_html__( 'Bottom', 'powerkit' ),
								'block'   => 'div',
								'classes' => 'pk-content-block pk-block-border-bottom',
							),
							array(
								'title'   => esc_html__( 'Left', 'powerkit' ),
								'block'   => 'div',
								'classes' => 'pk-content-block pk-block-border-left',
							),
							array(
								'title'   => esc_html__( 'Right', 'powerkit' ),
								'block'   => 'div',
								'classes' => 'pk-content-block pk-block-border-right',
							),
							array(
								'title'   => esc_html__( 'All', 'powerkit' ),
								'block'   => 'div',
								'classes' => 'pk-content-block pk-block-border-all',
							),
						),
					),
					array(
						'title' => esc_html__( 'Background', 'powerkit' ),
						'block' => 'div',
						'items' => array(
							array(
								'title'   => esc_html__( 'Light', 'powerkit' ),
								'block'   => 'div',
								'classes' => 'pk-content-block pk-block-bg pk-block-bg-light',
							),
							array(
								'title'   => esc_html__( 'Inverse', 'powerkit' ),
								'block'   => 'div',
								'classes' => 'pk-content-block pk-block-bg pk-block-bg-inverse',
							),
						),
					),
					array(
						'title'   => esc_html__( 'Shadows', 'powerkit' ),
						'block'   => 'div',
						'classes' => 'pk-content-block pk-block-shadows',
					),
					array(
						'title' => esc_html__( 'Alignment', 'powerkit' ),
						'items' => array(
							array(
								'title'   => esc_html__( 'Left', 'powerkit' ),
								'block'   => 'div',
								'classes' => 'pk-content-block pk-block-alignment-left',
							),
							array(
								'title'   => esc_html__( 'Right', 'powerkit' ),
								'block'   => 'div',
								'classes' => 'pk-content-block pk-block-alignment-right',
							),
						),
					),
				),
			),
			array(
				'title' => esc_html__( 'Numbered Headings', 'powerkit' ),
				'items' => array(
					array(
						'title'   => esc_html__( 'Heading 2', 'powerkit' ),
						'block'   => 'h2',
						'classes' => 'pk-heading-numbered',
					),
					array(
						'title'   => esc_html__( 'Heading 3', 'powerkit' ),
						'block'   => 'h3',
						'classes' => 'pk-heading-numbered',
					),
					array(
						'title'   => esc_html__( 'Heading 4', 'powerkit' ),
						'block'   => 'h4',
						'classes' => 'pk-heading-numbered',
					),
					array(
						'title'   => esc_html__( 'Heading 5', 'powerkit' ),
						'block'   => 'h5',
						'classes' => 'pk-heading-numbered',
					),
					array(
						'title'   => esc_html__( 'Heading 6', 'powerkit' ),
						'block'   => 'h6',
						'classes' => 'pk-heading-numbered',
					),
				),
			),
			array(
				'title' => esc_html__( 'Lists', 'powerkit' ),
				'items' => array(
					array(
						'title'      => esc_html__( 'Style', 'powerkit' ),
						'selector'   => 'ol,ul',
						'attributes' => array(
							'class' => 'pk-list-styled',
						),
					),
					array(
						'title'      => esc_html__( 'Positive', 'powerkit' ),
						'selector'   => 'ol,ul',
						'attributes' => array(
							'class' => 'pk-list-positive',
						),
					),
					array(
						'title'      => esc_html__( 'Negative', 'powerkit' ),
						'selector'   => 'ol,ul',
						'attributes' => array(
							'class' => 'pk-list-negative',
						),
					),
				),
			),
		);

		$init_array['style_formats'] = wp_json_encode( $style_formats );

		return $init_array;
	}
}
