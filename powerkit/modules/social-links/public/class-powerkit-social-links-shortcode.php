<?php
/**
 * Shortcode Social Links
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Powerkit/shortcodes
 */

/**
 * Social Account Shortcode
 *
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_social_link_shortcode( $atts, $content = '' ) {
	$params = powerkit_shortcode_atts( shortcode_atts( array(
		'network' => false,
		'cache'   => true,
		'mode'    => apply_filters( 'powerkit_social_links_counter_mode', 'mixed' ),
	), $atts ) );

	$params['cache'] = filter_var( $params['cache'], FILTER_VALIDATE_BOOLEAN );

	if ( $params['network'] ) {
		return powerkit_social_links_get_count( $params['network'], $params['cache'] );
	} else {
		return esc_html__( 'Network name is incorrect!', 'powerkit' );
	}
}
add_shortcode( 'powerkit_social_link', 'powerkit_social_link_shortcode' );


/**
 * Social Links Shortcode
 *
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_social_links_shortcode( $atts, $content = '' ) {
	$params = powerkit_shortcode_atts( shortcode_atts( array(
		'title'    => esc_html__( 'Social Links', 'powerkit' ),
		'template' => 'inline',
		'scheme'   => 'light',
		'maximum'  => -1,
		'cache'    => true,
		'labels'   => true,
		'titles'   => true,
		'counts'   => true,
		'mode'     => apply_filters( 'powerkit_social_links_counter_mode', 'mixed' ),
	), $atts ) );

	ob_start();

	$params['cache']  = filter_var( $params['cache'], FILTER_VALIDATE_BOOLEAN );
	$params['labels'] = filter_var( $params['labels'], FILTER_VALIDATE_BOOLEAN );
	$params['titles'] = filter_var( $params['titles'], FILTER_VALIDATE_BOOLEAN );
	$params['counts'] = filter_var( $params['counts'], FILTER_VALIDATE_BOOLEAN );

	powerkit_social_links_appearance( $params );

	return ob_get_clean();
}
add_shortcode( 'powerkit_social_links', 'powerkit_social_links_shortcode' );

/**
 * Map Social Links Shortcode into the Basic Shortcodes Plugin
 */
if ( function_exists( 'powerkit_basic_shortcodes_register' ) ) :

	add_action( 'init', function() {

		$shortcode_map = array(
			'name'         => 'links',
			'title'        => esc_html__( 'Social Links', 'powerkit' ),
			'priority'     => 110,
			'base'         => 'powerkit_social_links',
			'autoregister' => false,
			'fields'       => array(
				array(
					'type'    => 'checkbox',
					'name'    => 'labels',
					'label'   => esc_html__( 'Labels', 'powerkit' ),
					'default' => true,
				),
				array(
					'type'    => 'checkbox',
					'name'    => 'titles',
					'label'   => esc_html__( 'Titles', 'powerkit' ),
					'default' => true,
				),
				array(
					'type'    => 'checkbox',
					'name'    => 'counts',
					'label'   => esc_html__( 'Counts', 'powerkit' ),
					'default' => true,
				),
				array(
					'type'    => 'input',
					'name'    => 'maximum',
					'label'   => esc_html__( 'Maximum number of social links', 'powerkit' ),
					'desc'    => esc_html__( 'Input -1 to remove the maximum limit of the social links.', 'powerkit' ),
					'default' => -1,
				),
			),
		);

		$templates = apply_filters( 'powerkit_social_links_templates', array() );

		if ( count( (array) $templates ) > 1 ) {
			$shortcode_map['fields'][] = array(
				'type'    => 'select',
				'name'    => 'template',
				'label'   => esc_html__( 'Template', 'powerkit' ),
				'default' => 'default',
				'options' => powerkit_social_links_get_templates(),
			);
		}

		$color_schemes = apply_filters( 'powerkit_social_links_color_schemes', array() );

		if ( count( (array) $color_schemes ) > 1 ) {
			$shortcode_map['fields'][] = array(
				'type'    => 'select',
				'name'    => 'scheme',
				'label'   => esc_html__( 'Color scheme', 'powerkit' ),
				'default' => 'default',
				'options' => powerkit_social_links_get_color_schemes(),
			);
		}

		powerkit_basic_shortcodes_register( $shortcode_map );

	});

endif;
