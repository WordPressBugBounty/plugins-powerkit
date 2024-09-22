<?php
/**
 * Opt In Form block template
 *
 * @var        $attributes - block attributes
 * @var        $options - layout options
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    PowerKit
 * @subpackage PowerKit/templates
 */

if ( ! $attributes['listId'] ) {
	$attributes['listId'] = 'default';
}

$params = array(
	'privacy'      => powerkit_mailchimp_get_privacy_text(),
	'type'         => 'block',
	'title'        => '',
	'text'         => '',
	'bg_image_id'  => 0,
	'list_id'      => $attributes['listId'],
	'display_name' => $attributes['showName'],
);

echo '<div class="' . esc_attr( $attributes['className'] ) . '" ' . ( isset( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '' ) . '>';

// Subscription output.
do_action( 'powerkit_subscribe_template', $params );

echo '</div>';
