<?php
/**
 * Shortcode Separators config
 *
 * @package    Powerkit
 * @subpackage Templates
 */

/**
 * Separators
 */
powerkit_basic_shortcodes_register( array(
	'name'         => 'separators',
	'title'        => esc_html__( 'Separators', 'powerkit' ),
	'priority'     => 10,
	'base'         => 'powerkit_separator',
	'autoregister' => true,
	'fields'       => array(
		array(
			'type'  => 'section',
			'label' => esc_html__( 'Options', 'powerkit' ),
		),
		array(
			'type'    => 'radio',
			'name'    => 'style',
			'label'   => esc_html__( 'Style', 'powerkit' ),
			'style'   => 'vertical',
			'default' => 'solid',
			'options' => array(
				'solid'  => esc_html__( 'Solid', 'powerkit' ),
				'double' => esc_html__( 'Double', 'powerkit' ),
				'dotted' => esc_html__( 'Dotted', 'powerkit' ),
				'dashed' => esc_html__( 'Dashed', 'powerkit' ),
				'blank'  => esc_html__( 'Blank (empty space)', 'powerkit' ),
			),
		),
		array(
			'type'    => 'input',
			'name'    => 'height',
			'label'   => esc_html__( 'Height', 'powerkit' ),
			'default' => '',
			'suffix'  => ' px',
		),
	),
) );


/**
 * Separator Shortcode
 *
 * @param array  $output    Shortcode HTML.
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_basic_shortcodes_separator( $output, $atts, $content ) {

	if ( 'blank' === $atts['style'] ) {
		$inl_css = sprintf( 'style="height: %dpx;"', absint( $atts['height'] ) );
	} else {
		$inl_css = sprintf( 'style="border-bottom-width: %dpx; border-bottom-style: %s;"', absint( $atts['height'] ), $atts['style'] );
	}

	$output = sprintf(
		'<div class="pk-separator" %s>%s</div>',
		$inl_css,
		wp_kses( $content, 'post' )
	);

	return $output;
}
add_filter( 'powerkit_separator_shortcode', 'powerkit_basic_shortcodes_separator', 10, 3 );
