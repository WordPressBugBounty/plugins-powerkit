<?php
/**
 * Shortcode Collapsibles config
 *
 * @package    Powerkit
 * @subpackage Templates
 */

/**
 * Collapsibles
 */
powerkit_basic_shortcodes_register( array(
	'name'         => 'collapsibles',
	'title'        => esc_html__( 'Collapsibles', 'powerkit' ),
	'priority'     => 50,
	'base'         => 'powerkit_collapsibles',
	'autoregister' => true,
	'fields'       => array(
		array(
			'type'  => 'section',
			'label' => esc_html__( 'Content', 'powerkit' ),
		),
		array(
			'type'         => 'repeater',
			'base'         => 'powerkit_collapsible',
			'autoregister' => true,
			'label'        => esc_html__( 'Collapsibles', 'powerkit' ),
			'fields'       => array(
				array(
					'type'    => 'input',
					'name'    => 'title',
					'label'   => esc_html__( 'Title', 'powerkit' ),
					'default' => '',
					'attrs'   => array(
						'class' => 'widefat',
					),
				),
				array(
					'type'    => 'content',
					'name'    => 'content',
					'label'   => esc_html__( 'Content', 'powerkit' ),
					'default' => '',
					'attrs'   => array(
						'class' => 'widefat',
						'rows'  => 6,
					),
				),
				array(
					'type'    => 'checkbox',
					'name'    => 'opened',
					'label'   => esc_html__( 'Opened', 'powerkit' ),
					'default' => false,
				),
			),
		),
	),
) );


/**
 * Collapsibles Shortcode
 *
 * @param array  $output    Shortcode HTML.
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_basic_shortcodes_collapsibles( $output, $atts, $content ) {

	$collapse_id = uniqid();

	$output = sprintf(
		'<div id="collapsibles-%1$s" class="pk-collapsibles" role="tablist" aria-multiselectable="true">%2$s</div>
		',
		$collapse_id,
		str_replace( 'data-parent="#"', 'data-parent="#pk-collapsibles-' . $collapse_id . '"', $content )
	);

	return $output;
}
add_filter( 'powerkit_collapsibles_shortcode', 'powerkit_basic_shortcodes_collapsibles', 10, 3 );


/**
 * Collapsible Shortcode
 *
 * @param array  $output    Shortcode HTML.
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_basic_shortcodes_collapsible( $output, $atts, $content ) {

	$item_id = uniqid();
	$output  = sprintf(
		'<div class="pk-collapsible pk-card %4$s">
			<div class="pk-card-header" role="tab" id="card-%1$s">
				<h6 class="pk-card-title pk-title">
					<a data-toggle="collapse" class="pk-font-heading" href="#pk-collapse-%1$s" data-parent="#" aria-controls="collapse-%1$s">
						%2$s
					</a>
				</h6>
			</div>

			<div id="pk-collapse-%1$s" class="pk-collapse" style="%3$s" role="tabpanel" aria-labelledby="card-%1$s">
				<div class="pk-card-body">
					%5$s
				</div>
			</div>
		</div>
		',
		esc_attr( $item_id ),
		wp_kses( $atts['title'], 'post' ),
		( 'true' === $atts['opened'] ) ? 'display:block;' : 'display:none;',
		( 'true' === $atts['opened'] ) ? 'expanded' : '',
		wp_kses( do_shortcode( $content ), 'post' ),
	);

	return $output;
}
add_filter( 'powerkit_collapsible_shortcode', 'powerkit_basic_shortcodes_collapsible', 10, 3 );
