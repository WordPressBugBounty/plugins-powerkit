<?php
/**
 * Register Connect List
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Add item twitter to list
 *
 * @param array $list Array social list.
 */
function powerkit_connect_twitter( $list = array() ) {

	// X (Twitter).
	$list['twitter'] = array(
		'id'   => 'twitter',
		'name' => esc_html__( 'X (Twitter)', 'powerkit' ),
	);

	return $list;
}
add_filter( 'powerkit_register_connect_list', 'powerkit_connect_twitter' );
