<?php
/**
 * Lightbox
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Lightbox extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Lightbox', 'powerkit' );
			$this->desc     = esc_html__( 'Instead of opening images in a new window display them in a fullscreen Lightbox for a distraction-free user experience.', 'powerkit' );
			$this->slug     = 'lightbox';
			$this->type     = 'default';
			$this->category = 'basic';
			$this->priority = 110;
			$this->public   = true;
			$this->enabled  = true;
			$this->links    = array(
				array(
					'name' => esc_html__( 'Go to settings', 'powerkit' ),
					'url'  => admin_url( sprintf( 'options-media.php#%s', powerkit_get_page_slug( $this->slug ) ) ),
				),
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/content-presentation/lightbox/',
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
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-lightbox.php';

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-lightbox-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-lightbox-public.php';

			new Powerkit_Lightbox_Admin( $this->slug );
			new Powerkit_Lightbox_Public( $this->slug );
		}
	}

	new Powerkit_Lightbox();
}
