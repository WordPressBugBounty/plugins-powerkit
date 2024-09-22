<?php
/**
 * Shortcode Buttons config
 *
 * @package    Powerkit
 * @subpackage Templates
 */

/**
 * Buttons
 */
powerkit_basic_shortcodes_register( array(
	'name'         => 'buttons',
	'title'        => esc_html__( 'Buttons', 'powerkit' ),
	'priority'     => 20,
	'base'         => 'powerkit_button',
	'autoregister' => true,
	'fields'       => array(
		array(
			'type'  => 'section',
			'label' => esc_html__( 'Style Options', 'powerkit' ),
		),
		array(
			'type'    => 'radio',
			'name'    => 'size',
			'label'   => esc_html__( 'Size', 'powerkit' ),
			'style'   => 'horizontal',
			'default' => 'md',
			'options' => array(
				'sm' => esc_html__( 'Small', 'powerkit' ),
				'md' => esc_html__( 'Default', 'powerkit' ),
				'lg' => esc_html__( 'Large', 'powerkit' ),
			),
		),
		array(
			'type'    => 'radio',
			'name'    => 'style',
			'label'   => esc_html__( 'Style', 'powerkit' ),
			'style'   => 'vertical',
			'default' => 'primary',
			'options' => array(
				'primary'   => esc_html__( 'Primary', 'powerkit' ),
				'secondary' => esc_html__( 'Secondary', 'powerkit' ),
				'success'   => esc_html__( 'Success', 'powerkit' ),
				'info'      => esc_html__( 'Info', 'powerkit' ),
				'warning'   => esc_html__( 'Warning', 'powerkit' ),
				'danger'    => esc_html__( 'Danger', 'powerkit' ),
				'link'      => esc_html__( 'Link', 'powerkit' ),
			),
		),
		array(
			'type'    => 'checkbox',
			'name'    => 'block',
			'label'   => esc_html__( 'Block', 'powerkit' ),
			'default' => false,
		),
		array(
			'type'  => 'section',
			'label' => esc_html__( 'Link Options', 'powerkit' ),
		),
		array(
			'type'    => 'input',
			'name'    => 'url',
			'label'   => esc_html__( 'URL', 'powerkit' ),
			'default' => 'http://',
		),
		array(
			'type'    => 'radio',
			'name'    => 'target',
			'label'   => esc_html__( 'Link target', 'powerkit' ),
			'style'   => 'vertical',
			'default' => '_self',
			'options' => array(
				'_self'  => esc_html__( 'Open in same window', 'powerkit' ),
				'_blank' => esc_html__( 'Open in new window/tab', 'powerkit' ),
			),
		),
		array(
			'type'    => 'content',
			'name'    => 'title',
			'label'   => esc_html__( 'Title', 'powerkit' ),
			'default' => 'Button',
			'attrs'   => array(
				'class' => 'widefat',
				'rows'  => 6,
			),
		),
		array(
			'type'    => 'checkbox',
			'name'    => 'nofollow',
			'label'   => esc_html__( 'Apply "nofollow" attribute', 'powerkit' ),
			'default' => false,
		),
	),
) );


/**
 * Button Shortcode
 *
 * @param array  $output    Shortcode HTML.
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_basic_shortcodes_button( $output, $atts, $content ) {
	$block = ( 'true' === $atts['block'] ) ? ' pk-button-block' : '';

	if ( isset( $atts['title'] ) && $atts['title'] ) {
		$title = $atts['title'];
	}

	if ( $content ) {
		$title = $content;
	}

	$output = sprintf(
		'<a class="pk-button pk-button-%s pk-button-%s%s pk-font-primary" href="%s" target="%s" %s>
			%s
		</a>',
		esc_attr( $atts['size'] ),
		esc_attr( $atts['style'] ),
		esc_attr( $block ),
		esc_url( $atts['url'] ),
		esc_attr( $atts['target'] ),
		( ( 'true' === $atts['nofollow'] ) ? 'rel="nofollow"' : '' ),
		wp_kses( $title, 'post' )
	);

	return $output;
}
add_filter( 'powerkit_button_shortcode', 'powerkit_basic_shortcodes_button', 10, 3 );
