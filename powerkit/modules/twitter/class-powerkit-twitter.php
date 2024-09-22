<?php
/**
 * Social Accounts
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Twitter extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name            = esc_html__( 'Twitter Integration', 'powerkit' );
			$this->desc            = esc_html__( 'Display your Twitter feed in your sidebar with a widget or post content via shortcode, including your Twitter profile image, number of followers, as well as number of replies and likes per each tweet in the feed.', 'powerkit' );
			$this->slug            = 'twitter_integration';
			$this->type            = 'default';
			$this->category        = 'social';
			$this->priority        = 50;
			$this->public          = true;
			$this->enabled         = true;
			$this->load_extensions = array(
				'connect',
			);
			$this->links           = array(
				array(
					'name' => esc_html__( 'Clear cache', 'powerkit' ),
					'url'  => powerkit_get_page_url( $this->slug . '&action=powerkit_reset_cache' ),
				),
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/social-integrations/twitter-integration/',
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
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-twitter.php';
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-twitter-connect-list.php';

			// The classes responsible for defining all actions.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-twitter-shortcode.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-twitter-widget.php';

			if ( function_exists( 'register_block_type' ) ) {
				require_once dirname( __FILE__ ) . '/public/class-powerkit-twitter-block.php';
			}

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-twitter-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-twitter-public.php';

			new Powerkit_Twitter_Admin( $this->slug );
			new Powerkit_Twitter_Public( $this->slug );
		}
	}

	new Powerkit_Twitter();
}
