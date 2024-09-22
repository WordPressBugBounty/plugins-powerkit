<?php
/**
 * Social Links
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Opt_In_Forms extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Opt-in Forms', 'powerkit' );
			$this->desc     = esc_html__( 'Easily add opt-in (subscription) forms to your website and grow your subscribers’ list with the Opt-In Forms module.', 'powerkit' );
			$this->slug     = 'opt_in_forms';
			$this->type     = 'default';
			$this->category = 'forms';
			$this->priority = 70;
			$this->public   = true;
			$this->enabled  = true;
			$this->links    = array(
				array(
					'name' => esc_html__( 'Go to settings', 'powerkit' ),
					'url'  => powerkit_get_page_url( $this->slug ),
				),
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/marketing/opt-in-forms/',
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
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-opt-in-forms.php';

			// The classes responsible for defining all actions.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-subscription-form-shortcode.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-subscription-form-widget.php';

			if ( function_exists( 'register_block_type' ) ) {
				require_once dirname( __FILE__ ) . '/public/class-powerkit-subscription-form-block.php';
			}

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-opt-in-forms-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-opt-in-forms-public.php';

			new Powerkit_Opt_In_Forms_Admin( $this->slug );
			new Powerkit_Opt_In_Forms_Public( $this->slug );
		}
	}

	new Powerkit_Opt_In_Forms();
}
