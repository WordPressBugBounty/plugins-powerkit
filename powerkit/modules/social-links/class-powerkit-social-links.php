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
	class Powerkit_Social_Links extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name            = esc_html__( 'Social Links', 'powerkit' );
			$this->desc            = esc_html__( 'Display a list of links to your social accounts with beautiful icons and followers’ counts in pre-defined theme locations, sidebar widget or post content via shortcode. Add social links for post authors with the help of the new user contact fields.', 'powerkit' );
			$this->slug            = 'social_links';
			$this->type            = 'default';
			$this->category        = 'social';
			$this->priority        = 20;
			$this->public          = true;
			$this->enabled         = true;
			$this->load_extensions = array(
				'connect',
			);

			$this->links = array(
				array(
					'name' => esc_html__( 'Go to settings', 'powerkit' ),
					'url'  => powerkit_get_page_url( $this->slug ),
				),
				array(
					'name' => esc_html__( 'Connect', 'powerkit' ),
					'url'  => powerkit_get_page_url( 'connect' ),
				),
				array(
					'name' => esc_html__( 'Clear cache', 'powerkit' ),
					'url'  => powerkit_get_page_url( $this->slug . '&action=powerkit_reset_cache' ),
				),
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/social-integrations/social-links/',
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
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-links.php';
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-links-connect-list.php';
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-links-list.php';
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-links-api-config.php';
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-links-api.php';

			// The classes responsible for defining all actions.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-social-links-shortcode.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-social-links-widget.php';

			if ( function_exists( 'register_block_type' ) ) {
				require_once dirname( __FILE__ ) . '/public/class-powerkit-social-links-block.php';
			}

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-social-links-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-social-links-public.php';

			new Powerkit_Social_Links_Admin( $this->slug );
			new Powerkit_Social_Links_Public( $this->slug );
		}
	}

	new Powerkit_Social_Links();
}
