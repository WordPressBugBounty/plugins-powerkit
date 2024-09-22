<?php
/**
 * Helpers Coming Soon
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * The function returns the status
 */
function powerkit_coming_soon_status() {
	return get_option( 'powerkit_coming_soon_status', false );
}

/**
 * The function returns the status page
 */
function powerkit_coming_soon_page_visible() {
	$option_coming_soon_page = get_option( 'powerkit_coming_soon_page' );

	if ( ! empty( $option_coming_soon_page ) && is_page( $option_coming_soon_page ) ) {
		return __return_true();
	}
}

/**
 * The function returns the status site
 */
function powerkit_coming_soon_site_visible() {
	// Check Status.
	if ( ! powerkit_coming_soon_status() ) {
		return;
	}

	// Check Administrator.
	if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
		return;
	}

	return __return_true();
}

/**
 * The function returns the default content
 */
function powerkit_coming_soon_default_content() {
	ob_start();
	?>
		<!-- wp:heading {"align":"center","level":1,"canvasClassName":"cnvs-block-core-heading-1"} -->
		<h1 class="has-text-align-center">I'm working on something amazing!<br>But don't worry, I'll back soon. </h1>
		<!-- /wp:heading -->
	<?php
	return ob_get_clean();
}
