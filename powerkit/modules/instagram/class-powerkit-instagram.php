<?php
/**
 * Instagram
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Instagram extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name            = esc_html__( 'Instagram Integration', 'powerkit' );
			$this->desc            = esc_html__( 'Display your Instagram feed in your sidebar with a widget or post content via a shortcode, including your Instagram profile image, number of followers, as well as number of comments and likes per each image in the feed.', 'powerkit' );
			$this->slug            = 'instagram_integration';
			$this->type            = 'default';
			$this->category        = 'social';
			$this->priority        = 60;
			$this->public          = true;
			$this->enabled         = true;
			$this->load_extensions = array(
				'connect',
			);
			$this->links           = array(
				array(
					'name' => esc_html__( 'Connect', 'powerkit' ),
					'url'  => powerkit_get_page_url( 'connect&tab=instagram' ),
				),
				array(
					'name' => esc_html__( 'Clear cache', 'powerkit' ),
					'url'  => powerkit_get_page_url( $this->slug . '&action=powerkit_reset_cache' ),
				),
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/social-integrations/instagram-integration/',
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
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-instagram.php';
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-instagram-connect-list.php';

			// The classes responsible for defining all actions.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-instagram-shortcode.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-instagram-widget.php';

			if ( function_exists( 'register_block_type' ) ) {
				require_once dirname( __FILE__ ) . '/public/class-powerkit-instagram-block.php';
			}

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-instagram-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-instagram-public.php';

			new Powerkit_Instagram_Admin( $this->slug );
			new Powerkit_Instagram_Public( $this->slug );
		}
	}

	new Powerkit_Instagram();
}
