<?php
/**
 * Helpers Featured Posts
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Template handler
 *
 * @param string $name     Specific template.
 * @param int    $author   The author.
 * @param array  $args     Array of args.
 * @param array  $params   Array of params.
 * @param array  $instance Widget instance.
 */
function powerkit_widget_author_template_handler( $name, $author, $args, $params, $instance ) {
	$templates = apply_filters( 'powerkit_widget_author_templates', array() );

	if ( isset( $templates[ $name ] ) && function_exists( $templates[ $name ]['func'] ) ) {
		call_user_func( $templates[ $name ]['func'], $author, $args, $params, $instance );
	} else {
		call_user_func( 'powerkit_widget_author_default_template', $author, $args, $params, $instance );
	}
}
