<?php
/**
 * Headers Footers
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Headers_Footers extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Header and Footer Scripts', 'powerkit' );
			$this->desc     = esc_html__( 'You insert code like Google Analytics, custom CSS, Facebook Pixel, and more to your WordPress site header and footer.', 'powerkit' );
			$this->slug     = 'headers_footers';
			$this->type     = 'default';
			$this->category = 'tools';
			$this->priority = 140;
			$this->public   = false;
			$this->enabled  = false;
			$this->links    = array(
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/headers-footers/',
					'target' => '_blank',
				),
			);
		}

		/**
		 * Initialize module
		 */
		public function initialize() {

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-headers-footers-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-headers-footers-public.php';

			new Powerkit_Headers_Footers_Admin( $this->slug );
			new Powerkit_Headers_Footers_Public( $this->slug );
		}
	}

	new Powerkit_Headers_Footers();
}
