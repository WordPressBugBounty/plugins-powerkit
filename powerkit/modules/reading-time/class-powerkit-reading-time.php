<?php
/**
 * Reading Time
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Reading_Time extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Reading Time', 'powerkit' );
			$this->desc     = esc_html__( 'Let’s you easily add an estimated reading time to your WordPress posts.', 'powerkit' );
			$this->slug     = 'reading_time';
			$this->type     = 'default';
			$this->category = 'tools';
			$this->priority = 140;
			$this->public   = true;
			$this->enabled  = true;
			$this->links    = array(
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/reading-time/',
					'target' => '_blank',
				),
			);
		}

		/**
		 * Initialize module
		 */
		public function initialize() {

			/* Load the required dependencies for this module */

			// Helpers Functions for the module.
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-reading-time.php';

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-reading-time-public.php';

			new Powerkit_Reading_Time_Public( $this->slug );
		}
	}

	new Powerkit_Reading_Time();
}
