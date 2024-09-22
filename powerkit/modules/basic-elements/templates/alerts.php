<?php
/**
 * Shortcode Alerts config
 *
 * @package    Powerkit
 * @subpackage Templates
 */

/**
 * Alerts
 */
powerkit_basic_shortcodes_register( array(
	'name'         => 'alerts',
	'title'        => esc_html__( 'Alerts', 'powerkit' ),
	'priority'     => 30,
	'base'         => 'powerkit_alert',
	'autoregister' => true,
	'fields'       => array(
		array(
			'type'  => 'section',
			'label' => esc_html__( 'Options', 'powerkit' ),
		),
		array(
			'type'    => 'radio',
			'name'    => 'type',
			'label'   => esc_html__( 'Type', 'powerkit' ),
			'style'   => 'vertical',
			'default' => 'info',
			'options' => array(
				'danger'  => esc_html__( 'Danger', 'powerkit' ),
				'info'    => esc_html__( 'Info', 'powerkit' ),
				'link'    => esc_html__( 'Link', 'powerkit' ),
				'success' => esc_html__( 'Success', 'powerkit' ),
				'warning' => esc_html__( 'Warning', 'powerkit' ),
			),
		),
		array(
			'type'    => 'checkbox',
			'name'    => 'dismissible',
			'label'   => esc_html__( 'Display close button', 'powerkit' ),
			'default' => false,
		),
		array(
			'type'    => 'checkbox',
			'name'    => 'multiline',
			'label'   => esc_html__( 'Multiline', 'powerkit' ),
			'default' => false,
		),
		array(
			'type'  => 'section',
			'label' => esc_html__( 'Content', 'powerkit' ),
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
	),
) );


/**
 * Alert Shortcode
 *
 * @param array  $output    Shortcode HTML.
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_basic_shortcodes_alert( $output, $atts, $content ) {
	$dm_class  = null;
	$dm_button = null;

	if ( 'true' === $atts['dismissible'] ) {
		$dm_class  .= ' pk-alert-dismissible';
		$dm_button .= '
					<button type="button" class="pk-close" data-dismiss="alert" aria-label="' . esc_attr__( 'Close', 'powerkit' ) . '">
						<i class="pk-icon-x"></i>
					</button>';
	}

	if ( 'true' === $atts['multiline'] ) {
		$dm_class .= ' pk-alert-multiline';
	}

	$output = sprintf(
		'<div class="pk-alert pk-alert-%s%s" role="alert" >%s%s</div>',
		esc_attr( $atts['type'] ),
		esc_attr( $dm_class ),
		wp_kses( $dm_button, 'post' ),
		wp_kses( $content, 'post' ),
	);

	return $output;
}
add_filter( 'powerkit_alert_shortcode', 'powerkit_basic_shortcodes_alert', 10, 3 );
