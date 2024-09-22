<?php
/**
 * Slider Gallery
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Slider_Gallery extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name            = esc_html__( 'Slider Gallery', 'powerkit' );
			$this->desc            = esc_html__( 'Create beautiful slider galleries with the Slider Gallery module. Show or hide slider bullets, current and total slide numbers, next and previous buttons per gallery or globally.', 'powerkit' );
			$this->slug            = 'slider_gallery';
			$this->type            = 'default';
			$this->category        = 'basic';
			$this->priority        = 100;
			$this->public          = true;
			$this->enabled         = true;
			$this->links           = array(
				array(
					'name' => esc_html__( 'Go to settings', 'powerkit' ),
					'url'  => admin_url( sprintf( 'options-media.php#%s', powerkit_get_page_slug( $this->slug ) ) ),
				),
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/content-presentation/slider-gallery/',
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
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-slider-gallery-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-slider-gallery-public.php';

			new Powerkit_Slider_Gallery_Admin( $this->slug );
			new Powerkit_Slider_Gallery_Public( $this->slug );
		}
	}

	new Powerkit_Slider_Gallery();
}
