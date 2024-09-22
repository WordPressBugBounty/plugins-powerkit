<?php
/**
 * Helpers Facebook
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Facebook load sdk.
 */
function powerkit_facebook_load_sdk() {
	?>
		<div id="fb-root"></div>
		<script async defer crossorigin="anonymous" src="https://connect.facebook.net/<?php echo esc_html( powerkit_get_locale() ); ?>/sdk.js#xfbml=1&version=v17.0&appId=<?php echo esc_html( get_option( 'powerkit_connect_facebook_app_id' ) ); ?>&autoLogAppEvents=1" nonce="Ci8te34e"></script>
	<?php
}
