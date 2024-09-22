<?php
/**
 * Share Buttons
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Share_Buttons extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Share Buttons', 'powerkit' );
			$this->desc     = esc_html__( 'Display share buttons in theme-predefined or standard locations. Select from various social networks and add per-button and the total share counts with just a few clicks.', 'powerkit' );
			$this->slug     = 'share_buttons';
			$this->type     = 'default';
			$this->category = 'social';
			$this->priority = 10;
			$this->public   = true;
			$this->enabled  = true;
			$this->links    = array(
				array(
					'name' => esc_html__( 'Go to settings', 'powerkit' ),
					'url'  => powerkit_get_page_url( $this->slug ),
				),
				array(
					'name' => esc_html__( 'Clear cache', 'powerkit' ),
					'url'  => powerkit_get_page_url( $this->slug . '&action=powerkit_reset_cache' ),
				),
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/social-integrations/share-buttons/',
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
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-buttons.php';
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-buttons-list.php';
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-buttons-api.php';

			// The classes responsible for defining all actions.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-share-buttons-shortcode.php';

			if ( function_exists( 'register_block_type' ) ) {
				require_once dirname( __FILE__ ) . '/public/class-powerkit-share-buttons-block.php';
			}

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-share-buttons-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-share-buttons-public.php';

			new Powerkit_Share_Buttons_Admin( $this->slug );
			new Powerkit_Share_Buttons_Public( $this->slug );
		}
	}

	new Powerkit_Share_Buttons();
}
