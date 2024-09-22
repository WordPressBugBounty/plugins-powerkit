<?php
/**
 * Shortcode Subscription Form
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Powerkit/shortcodes
 */

/**
 * Subscription Form Shortcode
 *
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_subscription_shortcode( $atts, $content = '' ) {

	$params = powerkit_shortcode_atts( shortcode_atts( array(
		'privacy'     => powerkit_mailchimp_get_privacy_text(),
		'title'       => '',
		'text'        => '',
		'bg_image_id' => '',
		'list_id'     => 'default',
		'type'        => 'block',
		'display_name' => false,
	), $atts ) );

	ob_start();

	$tag = apply_filters( 'powerkit_subscription_title_tag', 'h3' );

	if ( $params['title'] ) {
		$params['title'] = sprintf( '<%1$s class="pk-title">%2$s</%1$s>', $tag, $params['title'] );
	}

	do_action( 'powerkit_subscribe_template', $params );

	return ob_get_clean();
}
add_shortcode( 'powerkit_subscription_form', 'powerkit_subscription_shortcode' );

/**
 * Map Facebook Page Shortcode into the Basic Shortcodes Plugin
 */
if ( function_exists( 'powerkit_basic_shortcodes_register' ) ) {

	$shortcode_map = array(
		'name'         => 'subscription_form',
		'title'        => esc_html__( 'Subscription Form', 'powerkit' ),
		'priority'     => 160,
		'base'         => 'powerkit_subscription_form',
		'autoregister' => false,
		'fields'       => array(
			array(
				'type'  => 'input',
				'name'  => 'title',
				'label' => esc_html__( 'Title', 'powerkit' ),
			),
			array(
				'type'  => 'input',
				'name'  => 'bg_image_id',
				'label' => esc_html__( 'Background image ID', 'powerkit' ),
			),
			array(
				'type'  => 'input',
				'name'  => 'text',
				'label' => esc_html__( 'Message', 'powerkit' ),
			),
			array(
				'type'  => 'input',
				'name'  => 'list_id',
				'label' => esc_html__( 'List ID', 'powerkit' ),
				'desc'  => '1. ' . esc_html__( 'Log in to your', 'powerkit' ) . ' ' . sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( 'https://mailchimp.com' ), esc_html__( 'MailChimp account', 'powerkit' ) )
						. '<br>2. ' . esc_html__( 'Go to your Lists.', 'powerkit' )
						. '<br>3. ' . esc_html__( 'Select the desired list and in the drop-down menu and go to Settings.', 'powerkit' )
						. '<br>4. ' . esc_html__( 'Copy your list ID from the field “Unique ID for list …”.', 'powerkit' ),
			),
			array(
				'type'    => 'checkbox',
				'name'    => 'display_name',
				'label'   => esc_html__( 'Display first name field', 'powerkit' ),
				'desc'    => esc_html__( 'Make sure you map the field in the MailChimp settings', 'powerkit' ),
				'default' => false,
			),
		),
	);

	powerkit_basic_shortcodes_register( $shortcode_map );
}
