<?php
/**
 * Coming Soon
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Coming_Soon extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Coming Soon', 'powerkit' );
			$this->desc     = esc_html__( 'Coming soon module to perfectly manage your coming soon, under construction website, under maintenance mode website and offline website.', 'powerkit' );
			$this->slug     = 'coming_soon';
			$this->type     = 'default';
			$this->category = 'tools';
			$this->priority = 140;
			$this->public   = true;
			$this->enabled  = true;
			$this->links    = array(
				array(
					'name' => esc_html__( 'Go to settings', 'powerkit' ),
					'url'  => powerkit_get_page_url( $this->slug ),
				),
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/coming-soon/',
					'target' => '_blank',
				),
			);
		}

		/**
		 * Initialize module
		 */
		public function initialize() {
			define( 'POWERKIT_CS_TEMPLATES', dirname( __FILE__ ) . '/templates' );

			// Set plugin dir url.
			define( 'POWERKIT_CS_URL', plugin_dir_url( __FILE__ ) );

			/* Load the required dependencies for this module */

			// Helpers Functions for the module.
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-coming-soon.php';

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-coming-soon-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-coming-soon-public.php';

			new Powerkit_Coming_Soon_Admin( $this->slug );
			new Powerkit_Coming_Soon_Public( $this->slug );
		}
	}

	new Powerkit_Coming_Soon();
}
