<?php
/**
 * Typekit Fonts
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Typekit_Fonts extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name            = esc_html__( 'Adobe Fonts (formerly Typekit)', 'powerkit' );
			$this->desc            = esc_html__( 'Easily integrate Adobe fonts on your site by using the Adobe Fonts module.', 'powerkit' );
			$this->slug            = 'typekit_fonts';
			$this->type            = 'default';
			$this->category        = 'basic';
			$this->priority        = 1030;
			$this->public          = true;
			$this->enabled         = false;
			$this->badge           = esc_html__( 'Advanced', 'powerkit' );
			$this->load_extensions = array(
				'fonts',
			);
			$this->links           = array(
				array(
					'name' => esc_html__( 'Go to settings', 'powerkit' ),
					'url'  => powerkit_get_page_url( 'fonts&tab=' . $this->slug, 'themes' ),
				),
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/content-presentation/typekit-fonts/',
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
			require_once dirname( __FILE__ ) . '/helpers/helper-typekit-fonts.php';
			require_once dirname( __FILE__ ) . '/helpers/helper-typekit-fonts-api.php';

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-typekit-fonts-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-typekit-fonts-public.php';

			new Powerkit_Typekit_Fonts_Admin( $this->slug );
			new Powerkit_Typekit_Fonts_Public( $this->slug );
		}
	}

	new Powerkit_Typekit_Fonts();
}
