<?php
/**
 * Lazy Load
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Lazyload extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Lazy Load', 'powerkit' );
			$this->desc     = esc_html__( 'The Lazy Load module enables loading images when a user scrolls close to them, making images load only when needed and saving the user’s bandwidth.', 'powerkit' );
			$this->slug     = 'lazyload';
			$this->type     = 'default';
			$this->category = 'basic';
			$this->priority = 1020;
			$this->public   = true;
			$this->enabled  = false;
			$this->badge    = esc_html__( 'Advanced', 'powerkit' );
			$this->links    = array(
				array(
					'name' => esc_html__( 'Go to settings', 'powerkit' ),
					'url'  => admin_url( sprintf( 'options-media.php#%s', powerkit_get_page_slug( $this->slug ) ) ),
				),
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/image-optimization/lazy-load/',
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
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-lazyload.php';

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-lazyload-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-lazyload-public.php';

			new Powerkit_Lazyload_Admin( $this->slug );
			new Powerkit_Lazyload_Public( $this->slug );
		}
	}

	new Powerkit_Lazyload();
}
