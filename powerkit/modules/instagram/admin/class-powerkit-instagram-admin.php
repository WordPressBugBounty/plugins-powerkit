<?php
/**
 * The admin-specific functionality of the module.
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Modules/Admin
 */

/**
 * The admin-specific functionality of the module.
 */
class Powerkit_Instagram_Admin extends Powerkit_Module_Admin {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_filter( 'powerkit_reset_cache', array( $this, 'register_reset_cache' ) );
		add_filter( 'powerkit_ajax_reset_cache', array( $this, 'register_reset_cache' ) );
	}

	/**
	 * Register Reset Cache
	 *
	 * @since    1.0.0
	 * @param    array $list Change list reset cache.
	 * @access   private
	 */
	public function register_reset_cache( $list ) {
		$slug = powerkit_get_page_slug( $this->slug );

		$list[ $slug ] = array(
			'powerkit_instagram_data',
			'powerkit_instagram_recent',
		);

		return $list;
	}
}
