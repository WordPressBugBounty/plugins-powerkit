<?php
/**
 * Helpers Post Format UI
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Get post format audio.
 */
function powerkit_post_format_audio() {
	return get_post_meta( get_the_ID(), 'powerkit_post_format_audio', true );
}

/**
 * Get post format video.
 */
function powerkit_post_format_video() {
	return get_post_meta( get_the_ID(), 'powerkit_post_format_video', true );
}

/**
 * Get post format gallery.
 */
function powerkit_post_format_gallery() {
	return get_post_meta( get_the_ID(), 'powerkit_post_format_gallery', true );
}
