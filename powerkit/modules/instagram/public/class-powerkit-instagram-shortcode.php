<?php
/**
 * Shortcode Instagram
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    PowerKit
 * @subpackage PowerKit/shortcodes
 */

/**
 * Instagram Shortcode
 *
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_instagram_shortcode( $atts, $content = '' ) {

	$params = powerkit_shortcode_atts( shortcode_atts( array(
		'header'   => true,
		'button'   => true,
		'number'   => 4,
		'columns'  => 1,
		'size'     => 'small',
		'target'   => '_blank',
		'template' => 'default',
	), $atts ) );

	ob_start();

	powerkit_instagram_get_recent( $params );

	return ob_get_clean();
}
add_shortcode( 'powerkit_instagram', 'powerkit_instagram_shortcode' );

/**
 * Map Instagram Shortcode into the Basic Shortcodes Plugin
 */
if ( function_exists( 'powerkit_basic_shortcodes_register' ) ) :

	add_action( 'init', function() {

		$shortcode_map = array(
			'name'         => 'instagram',
			'title'        => esc_html__( 'Instagram', 'powerkit' ),
			'priority'     => 100,
			'base'         => 'powerkit_instagram',
			'autoregister' => false,
			'fields'       => array(
				array(
					'type'    => 'checkbox',
					'name'    => 'header',
					'label'   => esc_html__( 'Display header', 'powerkit' ),
					'default' => true,
				),
				array(
					'type'    => 'checkbox',
					'name'    => 'button',
					'label'   => esc_html__( 'Display follow button', 'powerkit' ),
					'default' => true,
				),
				array(
					'type'    => 'input',
					'name'    => 'number',
					'label'   => esc_html__( 'Number of images', 'powerkit' ),
					'default' => 4,
				),
				array(
					'type'    => 'select',
					'name'    => 'columns',
					'label'   => esc_html__( 'Number of columns', 'powerkit' ),
					'default' => '1',
					'options' => array(
						'1' => esc_html__( '1', 'powerkit' ),
						'2' => esc_html__( '2', 'powerkit' ),
						'3' => esc_html__( '3', 'powerkit' ),
						'4' => esc_html__( '4', 'powerkit' ),
						'5' => esc_html__( '5', 'powerkit' ),
						'6' => esc_html__( '6', 'powerkit' ),
						'7' => esc_html__( '7', 'powerkit' ),
					),
				),
				array(
					'type'    => 'select',
					'name'    => 'size',
					'label'   => esc_html__( 'Photo size', 'powerkit' ),
					'default' => 'thumbnail',
					'options' => array(
						'thumbnail' => esc_html__( 'Thumbnail', 'powerkit' ),
						'small'     => esc_html__( 'Small', 'powerkit' ),
						'large'     => esc_html__( 'Large', 'powerkit' ),
					),
				),
				array(
					'type'    => 'select',
					'name'    => 'target',
					'label'   => esc_html__( 'Open links in', 'powerkit' ),
					'default' => '_blank',
					'options' => array(
						'_blank' => esc_html__( 'New window (_blank)', 'powerkit' ),
						'_self'  => esc_html__( 'Current window (_self)', 'powerkit' ),
					),
				),
			),
		);

		$templates = apply_filters( 'powerkit_instagram_templates', array() );

		if ( count( (array) $templates ) > 1 ) {
			$shortcode_map['fields'][] = array(
				'type'    => 'select',
				'name'    => 'template',
				'label'   => esc_html__( 'Template', 'powerkit' ),
				'default' => 'default',
				'options' => powerkit_instagram_get_templates_options(),
			);
		}

		powerkit_basic_shortcodes_register( $shortcode_map );
	});

endif;
