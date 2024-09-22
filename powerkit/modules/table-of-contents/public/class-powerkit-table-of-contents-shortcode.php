<?php
/**
 * Shortcode Table of Contents
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Powerkit/shortcodes
 */

/**
 * Table of Contents Shortcode
 *
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_toc_shortcode( $atts, $content = '' ) {

	$params = powerkit_shortcode_atts( shortcode_atts( array(
		'title'          => '',
		'depth'          => 2,
		'min_count'      => 4,
		'min_characters' => 1000,
		'btn_hide'       => false,
		'default_state'  => 'expanded',
	), $atts ) );

	ob_start();

	powerkit_toc_list( $params );

	return ob_get_clean();
}
add_shortcode( 'powerkit_toc', 'powerkit_toc_shortcode' );

/**
 * Map Shortcode
 */
if ( function_exists( 'powerkit_basic_shortcodes_register' ) ) :

	add_action( 'init', function() {

		$shortcode_map = array(
			'name'         => 'toc',
			'title'        => esc_html__( 'Table of Contents', 'powerkit' ),
			'priority'     => 200,
			'base'         => 'powerkit_toc',
			'autoregister' => false,
			'fields'       => array(
				array(
					'type'    => 'input',
					'name'    => 'title',
					'label'   => esc_html__( 'Title', 'powerkit' ),
					'default' => esc_html__( 'Table of Contents', 'powerkit' ),
				),
				array(
					'type'    => 'input',
					'name'    => 'depth',
					'label'   => esc_html__( 'Depth of headings', 'powerkit' ),
					'default' => 2,
				),
				array(
					'type'    => 'input',
					'name'    => 'min_count',
					'label'   => esc_html__( 'Minimum number of headings in page content', 'powerkit' ),
					'default' => 4,
				),
				array(
					'type'    => 'input',
					'name'    => 'min_characters',
					'label'   => esc_html__( 'Minimum number of characters of post content', 'powerkit' ),
					'default' => 1000,
				),
				array(
					'type'    => 'checkbox',
					'name'    => 'btn_hide',
					'label'   => esc_html__( 'Display Button Show\Hide', 'powerkit' ),
					'default' => false,
				),
				array(
					'type'    => 'select',
					'name'    => 'default_state',
					'label'   => esc_html__( 'Default State', 'powerkit' ),
					'default' => 'expanded',
					'options' => array(
						'expanded'  => esc_html__( 'Expanded', 'powerkit' ),
						'collapsed' => esc_html__( 'Collapsed', 'powerkit' ),
					),
				),
			),
		);

		powerkit_basic_shortcodes_register( $shortcode_map );

	});

endif;
