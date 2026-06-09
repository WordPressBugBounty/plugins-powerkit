<?php
/**
 * Helpers Facebook
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Facebook load sdk.
 */
function powerkit_facebook_load_sdk() {
	// The Facebook JS SDK reads its configuration (xfbml, version, appId,
	// autoLogAppEvents) from the URL fragment, so it must be kept after the "#".
	$src = sprintf(
		'https://connect.facebook.net/%1$s/sdk.js#xfbml=1&version=v17.0&appId=%2$s&autoLogAppEvents=1',
		rawurlencode( powerkit_get_locale() ),
		rawurlencode( get_option( 'powerkit_connect_facebook_app_id' ) )
	);

	wp_enqueue_script( 'powerkit-facebook-sdk', $src, array(), powerkit_get_setting( 'version' ), true );

	echo '<div id="fb-root"></div>';
}
