<?php
/**
 * Basic Elements
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Basic_Elements extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Basic Elements', 'powerkit' );
			$this->desc     = esc_html__( 'Basic shortcodes with a shortcode generator right in the WordPress editor.', 'powerkit' );
			$this->slug     = 'basic_elements';
			$this->type     = 'default';
			$this->category = 'basic';
			$this->priority = 80;
			$this->public   = true;
			$this->enabled  = true;

			$this->links = array(
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/content-presentation/basic-elements/',
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
			require_once dirname( __FILE__ ) . '/helpers/helper-basic-elements.php';

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-basic-elements-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-basic-elements-public.php';

			// Include default templates.
			powerkit_basic_shortcodes_autoload( dirname( __FILE__ ) . '/templates' );

			new Powerkit_Basic_Elements_Admin( $this->slug );
			new Powerkit_Basic_Elements_Public( $this->slug );
		}
	}

	new Powerkit_Basic_Elements();
}
