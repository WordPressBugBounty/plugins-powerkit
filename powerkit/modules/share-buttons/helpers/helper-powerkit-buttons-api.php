<?php
/**
 * Get Share Buttons Counter
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Add Xing Counter.
 *
 * @param array      $account Account name.
 * @param int|string $post_id Post ID.
 * @param string     $url     Custom URL.
 * @param bool       $suffix  Unique suffix.
 */
function powerkit_share_buttons_xing_counter( $account, $post_id, $url = null, $suffix = false ) {

	// Get Post ID.
	$post_id = $post_id ? $post_id : powerkit_share_buttons_get_current_post_id( $url );

	// Get Chache.
	$count = powerkit_share_buttons_get_cached_account( $account, $post_id, $url, false, $suffix );

	if ( false !== $count ) {
		return intval( $count );
	}

	// Get Count.
	$endpoint = esc_url( 'https://www.xing-share.com/app/share?op=get_share_button;counter=top;url=' . $url, null, '' );

	$response = wp_remote_get( $endpoint, array(
		'timeout' => 3,
	) );
	$response = wp_remote_retrieve_body( $response );

	// Set Count.
	preg_match( '/<span class="xing-count[^"]*?">(\d+)<\/span>/si', $response, $response_result );

	if ( isset( $response_result[1] ) ) {
		$count = $response_result[1];
	} else {
		$count = powerkit_share_buttons_get_cached_account( $account, $post_id, $url, true, $suffix );
	}

	// Set Cache.
	powerkit_share_buttons_set_cache_account( $account, $post_id, intval( $count ), $url, $suffix );

	// Return Result.
	return $count;
}

/**
 * Add LinkedIn Counter.
 *
 * @param array      $account Account name.
 * @param int|string $post_id Post ID.
 * @param string     $url     Custom URL.
 * @param bool       $suffix  Unique suffix.
 */
function powerkit_share_buttons_linkedin_counter( $account, $post_id, $url = null, $suffix = false ) {

	// Get Post ID.
	$post_id = $post_id ? $post_id : powerkit_share_buttons_get_current_post_id( $url );

	// Get Chache.
	$count = powerkit_share_buttons_get_cached_account( $account, $post_id, $url, false, $suffix );

	if ( false !== $count ) {
		return intval( $count );
	}

	// Get Count.
	$endpoint = esc_url( 'https://www.linkedin.com/countserv/count/share?format=json&url=' . $url, null, '' );
	$response = wp_remote_get( $endpoint, array(
		'timeout' => 3,
	) );
	$response = wp_remote_retrieve_body( $response );

	// Set Count.
	$response_result = json_decode( $response, true );

	if ( isset( $response_result['count'] ) ) {
		$count = $response_result['count'];
	} else {
		$count = powerkit_share_buttons_get_cached_account( $account, $post_id, $url, true, $suffix );
	}

	// Set Cache.
	powerkit_share_buttons_set_cache_account( $account, $post_id, intval( $count ), $url, $suffix );

	// Return Result.
	return $count;
}

/**
 * Add Pinterest Counter.
 *
 * @param array      $account Account name.
 * @param int|string $post_id Post ID.
 * @param string     $url     Custom URL.
 * @param bool       $suffix  Unique suffix.
 */
function powerkit_share_buttons_pinterest_counter( $account, $post_id, $url = null, $suffix = false ) {

	// Get Post ID.
	$post_id = $post_id ? $post_id : powerkit_share_buttons_get_current_post_id( $url );

	// Get Chache.
	$count = powerkit_share_buttons_get_cached_account( $account, $post_id, $url, false, $suffix );

	if ( false !== $count ) {
		return intval( $count );
	}

	// Get Count.
	$endpoint = esc_url( 'https://widgets.pinterest.com/v1/urls/count.json?callback=jsonp&url=' . $url, null, '' );
	$response = wp_remote_get( $endpoint, array(
		'timeout' => 3,
	) );
	$response = wp_remote_retrieve_body( $response );

	// Set Count.
	$response_body   = str_replace( array( 'jsonp(', ')' ), '', $response );
	$response_result = json_decode( $response_body, true );

	if ( isset( $response_result['count'] ) ) {
		$count = $response_result['count'];
	} else {
		$count = powerkit_share_buttons_get_cached_account( $account, $post_id, $url, true, $suffix );
	}

	// Set Cache.
	powerkit_share_buttons_set_cache_account( $account, $post_id, intval( $count ), $url, $suffix );

	// Return Result.
	return $count;
}

/**
 * Add Odnoklassniki Counter.
 *
 * @param array      $account Account name.
 * @param int|string $post_id Post ID.
 * @param string     $url     Custom URL.
 * @param bool       $suffix  Unique suffix.
 */
function powerkit_share_buttons_ok_counter( $account, $post_id, $url = null, $suffix = false ) {
	// Get Post ID.
	$post_id = $post_id ? $post_id : powerkit_share_buttons_get_current_post_id( $url );

	// Get Chache.
	$count = powerkit_share_buttons_get_cached_account( $account, $post_id, $url, false, $suffix );

	if ( false !== $count ) {
		return intval( $count );
	}

	// Get Count.
	$endpoint = esc_url( 'https://connect.ok.ru/dk?st.cmd=extLike&ref=' . $url, null, '' );

	$response = wp_remote_get( $endpoint, array(
		'timeout' => 3,
	) );
	$response = wp_remote_retrieve_body( $response );

	// Set Count.
	preg_match( "/'null','(\d*?)'/s", $response, $result );

	if ( isset( $result[1] ) ) {
		$count = $result[1];
	} else {
		$count = powerkit_share_buttons_get_cached_account( $account, $post_id, $url, true, $suffix );
	}

	// Set Cache.
	powerkit_share_buttons_set_cache_account( $account, $post_id, intval( $count ), $url, $suffix );

	// Return Result.
	return $count;
}

/**
 * Add Vkontakte Counter.
 *
 * @param array      $account Account name.
 * @param int|string $post_id Post ID.
 * @param string     $url     Custom URL.
 * @param bool       $suffix  Unique suffix.
 */
function powerkit_share_buttons_vkontakte_counter( $account, $post_id, $url = null, $suffix = false ) {

	// Get Post ID.
	$post_id = $post_id ? $post_id : powerkit_share_buttons_get_current_post_id( $url );

	// Get Chache.
	$count = powerkit_share_buttons_get_cached_account( $account, $post_id, $url, false, $suffix );

	if ( false !== $count ) {
		return intval( $count );
	}

	// Get Count.
	$endpoint = esc_url( 'https://vk.com/share.php?act=count&index=1&url=' . $url, null, '' );
	$response = wp_remote_get( $endpoint, array(
		'timeout' => 3,
	) );
	$response = wp_remote_retrieve_body( $response );

	// Set Count.
	preg_match( '/^VK.Share.count\(1, (\d+)\);$/i', $response, $response_result );

	if ( isset( $response_result[1] ) ) {
		$count = $response_result[1];
	} else {
		$count = powerkit_share_buttons_get_cached_account( $account, $post_id, $url, true, $suffix );
	}

	// Set Cache.
	powerkit_share_buttons_set_cache_account( $account, $post_id, intval( $count ), $url, $suffix );

	// Return Result.
	return $count;
}
