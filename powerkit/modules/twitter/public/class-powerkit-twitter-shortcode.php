<?php
/**
 * Shortcode Twitter
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    PowerKit
 * @subpackage PowerKit/shortcodes
 */

/**
 * Twitter Shortcode
 *
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_twitter_shortcode( $atts, $content = '' ) {
	$params = powerkit_shortcode_atts( shortcode_atts( array(
		'title'    => esc_html__( 'Twitter Feed', 'powerkit' ),
		'number'   => 5,
		'template' => 'default',
		'header'   => true,
		'button'   => true,
	), $atts ) );

	ob_start();

	powerkit_twitter_get_recent( $params );

	return ob_get_clean();
}
add_shortcode( 'powerkit_twitter_feed', 'powerkit_twitter_shortcode' );

/**
 * Map Twitter Shortcode into the Basic Shortcodes Plugin
 */
if ( function_exists( 'powerkit_basic_shortcodes_register' ) ) :

	$shortcode_map = array(
		'name'         => 'twitter',
		'title'        => esc_html__( 'Twitter Feed', 'powerkit' ),
		'priority'     => 100,
		'base'         => 'powerkit_twitter_feed',
		'autoregister' => false,
		'fields'       => array(
			array(
				'type'    => 'input',
				'name'    => 'number',
				'label'   => esc_html__( 'Number of tweets to displays', 'powerkit' ),
				'default' => 5,
			),
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
		),
	);

	$templates = apply_filters( 'powerkit_twitter_templates', array() );

	if ( count( (array) $templates ) > 1 ) {
		$shortcode_map['fields'][] = array(
			'type'    => 'select',
			'name'    => 'template',
			'label'   => esc_html__( 'Template', 'powerkit' ),
			'default' => 'default',
			'options' => powerkit_twitter_get_templates_options(),
		);
	}

	powerkit_basic_shortcodes_register( $shortcode_map );

endif;
