<?php
/**
 * The public-facing functionality of the module.
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Modules/public
 */

/**
 * The public-facing functionality of the module.
 */
class Powerkit_Reading_Time_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'save_post', array( $this, 'update_post_reading_time' ), 10, 3 );
	}

	/**
	 * Update Post Reading Time on Post Save
	 *
	 * @param int  $post_id The post ID.
	 * @param post $post    The post object.
	 * @param bool $update  Whether this is an existing post being updated or not.
	 */
	public function update_post_reading_time( $post_id, $post, $update ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		$reading_time = powerkit_calculate_post_reading_time( $post_id );

		update_post_meta( $post_id, '_powerkit_reading_time', $reading_time );
	}
}
