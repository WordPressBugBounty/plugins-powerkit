<?php
/**
 * Register Connect List
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Add all items to list
 *
 * @param array $list Array social list.
 */
function powerkit_connect_total( $list = array() ) {

	// Facebook.
	$list['facebook'] = array(
		'id'   => 'facebook',
		'name' => esc_html__( 'Facebook', 'powerkit' ),
	);

	// Instagram.
	$list['instagram'] = array(
		'id'   => 'instagram',
		'name' => esc_html__( 'Instagram', 'powerkit' ),
	);

	// Twitter.
	$list['twitter'] = array(
		'id'   => 'twitter',
		'name' => esc_html__( 'Twitter', 'powerkit' ),
	);

	return $list;
}
add_filter( 'powerkit_register_connect_list', 'powerkit_connect_total' );
