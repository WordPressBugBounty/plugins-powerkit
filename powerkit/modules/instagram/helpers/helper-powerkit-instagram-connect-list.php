<?php
/**
 * Register Connect List
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Add item instagram to list
 *
 * @param array $list Array social list.
 */
function powerkit_connect_instagram( $list = array() ) {

	// Instagram.
	$list['instagram'] = array(
		'id'   => 'instagram',
		'name' => esc_html__( 'Instagram', 'powerkit' ),
	);

	return $list;
}
add_filter( 'powerkit_register_connect_list', 'powerkit_connect_instagram' );
