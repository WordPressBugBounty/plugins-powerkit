<?php
/**
 * Scroll To Top
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Scroll_To_Top extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Scroll To Top Button', 'powerkit' );
			$this->desc     = esc_html__( 'A simple and light-weight Scroll To Top button will appear automatically as soon as a user scrolls past the first screen.', 'powerkit' );
			$this->slug     = 'scroll_to_top';
			$this->type     = 'default';
			$this->category = 'basic';
			$this->priority = 180;
			$this->public   = true;
			$this->enabled  = true;
			$this->links    = array(
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/utilities/scroll-to-top-button/',
					'target' => '_blank',
				),
			);
		}

		/**
		 * Initialize module
		 */
		public function initialize() {

			/* Load the required dependencies for this module */

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-scroll-to-top-public.php';

			new Powerkit_Scroll_To_Top_Public( $this->slug );
		}
	}

	new Powerkit_Scroll_To_Top();
}
