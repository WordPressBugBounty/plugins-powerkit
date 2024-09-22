<?php
/**
 * Shortcode Progress Bars config
 *
 * @package    Powerkit
 * @subpackage Templates
 */

/**
 * Progress Bars
 */
powerkit_basic_shortcodes_register( array(
	'name'         => 'progressbars',
	'title'        => esc_html__( 'Progress Bars', 'powerkit' ),
	'priority'     => 60,
	'base'         => 'powerkit_progressbar',
	'autoregister' => true,
	'fields'       => array(
		array(
			'type'  => 'section',
			'label' => esc_html__( 'Options', 'powerkit' ),
		),
		array(
			'type'    => 'input',
			'name'    => 'value',
			'label'   => esc_html__( 'Value', 'powerkit' ),
			'default' => '25',
			'suffix'  => ' %',
			'desc'    => '(0-100)',
		),
		array(
			'type'  => 'section',
			'label' => esc_html__( 'Style', 'powerkit' ),
		),
		array(
			'type'    => 'input',
			'name'    => 'height',
			'label'   => esc_html__( 'Height (thickness)', 'powerkit' ),
			'default' => '20',
			'suffix'  => ' px',
		),
		array(
			'type'    => 'radio',
			'name'    => 'color',
			'label'   => esc_html__( 'Color', 'powerkit' ),
			'style'   => 'vertical',
			'default' => 'primary',
			'options' => array(
				'primary'   => esc_html__( 'Primary', 'powerkit' ),
				'secondary' => esc_html__( 'Secondary', 'powerkit' ),
				'success'   => esc_html__( 'Success', 'powerkit' ),
				'info'      => esc_html__( 'Info', 'powerkit' ),
				'warning'   => esc_html__( 'Warning', 'powerkit' ),
				'danger'    => esc_html__( 'Danger', 'powerkit' ),
			),
		),
		array(
			'type'    => 'checkbox',
			'name'    => 'display_value',
			'label'   => esc_html__( 'Display value', 'powerkit' ),
			'default' => false,
		),
		array(
			'type'    => 'checkbox',
			'name'    => 'striped',
			'label'   => esc_html__( 'Striped', 'powerkit' ),
			'default' => false,
		),
		array(
			'type'    => 'checkbox',
			'name'    => 'animated',
			'label'   => esc_html__( 'Animated', 'powerkit' ),
			'default' => false,
		),
	),
) );


/**
 * Progress Bar Shortcode
 *
 * @param array  $output    Shortcode HTML.
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_basic_shortcodes_progressbar( $output, $atts, $content ) {

	// Value.
	$atts['value'] = $atts['value'] > 100 ? 100 : $atts['value'];

	// Display value.
	$display_value = ( 'true' === $atts['display_value'] ) ? $atts['value'] . '%' : '';

	// Striped and animated.
	$class  = 'pk-progress-bar';
	$class .= ( 'true' === $atts['striped'] ) ? ' pk-progress-bar-striped' : '';
	$class .= ( 'true' === $atts['animated'] ) ? ' pk-progress-bar-animated' : '';

	// Color.
	$class .= sprintf( ' pk-bg-%s', $atts['color'] );

	$output = sprintf(
		'<div class="pk-progress" style="height: %2$spx;">
			<div class="%s" role="progressbar" style="width: %3$s%%;" aria-valuenow="%3$s" aria-valuemin="0" aria-valuemax="100">%4$s</div>
		</div>',
		$class,
		$atts['height'],
		$atts['value'],
		$display_value
	);

	return $output;
}
add_filter( 'powerkit_progressbar_shortcode', 'powerkit_basic_shortcodes_progressbar', 10, 3 );
