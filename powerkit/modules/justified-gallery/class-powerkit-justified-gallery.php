<?php
/**
 * Justified Gallery
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Justified_Gallery extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name            = esc_html__( 'Justified Gallery', 'powerkit' );
			$this->desc            = esc_html__( 'Create beautiful tiled galleries with the Justified Gallery module. Control the image height and padding between images per gallery or globally.', 'powerkit' );
			$this->slug            = 'justified_gallery';
			$this->type            = 'default';
			$this->category        = 'basic';
			$this->priority        = 90;
			$this->public          = true;
			$this->enabled         = true;
			$this->links           = array(
				array(
					'name' => esc_html__( 'Go to settings', 'powerkit' ),
					'url'  => admin_url( sprintf( 'options-media.php#%s', powerkit_get_page_slug( $this->slug ) ) ),
				),
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/content-presentation/justified-gallery/',
					'target' => '_blank',
				),
			);
			$this->load_extensions = array(
				'gallery',
			);
		}

		/**
		 * Initialize module
		 */
		public function initialize() {

			/* Load the required dependencies for this module */

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-justified-gallery-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-justified-gallery-public.php';

			new Powerkit_Justified_Gallery_Admin( $this->slug );
			new Powerkit_Justified_Gallery_Public( $this->slug );
		}
	}

	new Powerkit_Justified_Gallery();
}
