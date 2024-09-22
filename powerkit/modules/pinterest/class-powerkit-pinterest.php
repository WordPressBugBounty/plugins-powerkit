<?php
/**
 * Pinterest Integration
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Pinterest extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Pinterest Integration', 'powerkit' );
			$this->desc     = esc_html__( 'Display your Pinterest Board widget in your sidebar or post content via shortcode. Enable Pin It buttons on single images in post content and all post galleries for the easy pinning of your images to Pinterest boards.', 'powerkit' );
			$this->slug     = 'pinterest_integration';
			$this->type     = 'default';
			$this->category = 'social';
			$this->priority = 40;
			$this->public   = true;
			$this->enabled  = true;
			$this->links    = array(
				array(
					'name' => esc_html__( 'Go to settings', 'powerkit' ),
					'url'  => admin_url( sprintf( 'options-media.php#%s', powerkit_get_page_slug( $this->slug ) ) ),
				),
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/social-integrations/pinterest-integration/',
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
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-pinterest.php';

			// The classes responsible for defining all actions.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-pinterest-shortcode.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-pinterest-board-widget.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-pinterest-profile-widget.php';

			// Gutenberg blocks.
			if ( function_exists( 'register_block_type' ) ) {
				require_once dirname( __FILE__ ) . '/public/class-powerkit-pinterest-block.php';
			}

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-pinterest-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-pinterest-public.php';

			new Powerkit_Pinterest_Admin( $this->slug );
			new Powerkit_Pinterest_Public( $this->slug );
		}
	}

	new Powerkit_Pinterest();
}
