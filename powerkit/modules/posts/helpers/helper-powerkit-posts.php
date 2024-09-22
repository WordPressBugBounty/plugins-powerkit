<?php
/**
 * Helpers Posts
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Widget template handler
 *
 * @param string $name     Specific template.
 * @param array  $posts    Array of posts.
 * @param array  $params   Array of params.
 * @param array  $instance Widget instance.
 */
function powerkit_widget_featured_posts_handler( $name, $posts, $params, $instance ) {
	$templates = apply_filters( 'powerkit_featured_posts_templates', array() );

	if ( isset( $templates[ $name ] ) && function_exists( $templates[ $name ]['func'] ) ) {
		call_user_func( $templates[ $name ]['func'], $posts, $params, $instance );
	} else {
		call_user_func( 'powerkit_widget_featured_posts_template', $posts, $params, $instance );
	}
}
