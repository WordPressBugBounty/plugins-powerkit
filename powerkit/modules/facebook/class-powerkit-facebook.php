<?php
/**
 * Facebook Integration
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Facebook extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name            = esc_html__( 'Facebook Integration', 'powerkit' );
			$this->desc            = esc_html__( 'Display your Facebook Fanpage widget in your sidebar or post content via a shortcode. Enable Facebook comments next to or instead of WordPress comments.', 'powerkit' );
			$this->slug            = 'facebook_integration';
			$this->type            = 'default';
			$this->category        = 'social';
			$this->priority        = 30;
			$this->public          = true;
			$this->enabled         = true;
			$this->load_extensions = array(
				'connect',
			);
			$this->links           = array(
				array(
					'name' => esc_html__( 'Go to settings', 'powerkit' ),
					'url'  => admin_url( sprintf( 'options-discussion.php#%s', powerkit_get_page_slug( $this->slug ) ) ),
				),
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/social-integrations/facebook-integration/',
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
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-facebook.php';

			// The classes responsible for defining all actions.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-facebook-public.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-facebook-fanpage-widget.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-facebook-fanpage-shortcode.php';

			// Gutenberg blocks.
			if ( function_exists( 'register_block_type' ) ) {
				require_once dirname( __FILE__ ) . '/public/class-powerkit-facebook-fanpage-block.php';
			}

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-facebook-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-facebook-public.php';

			new Powerkit_Facebook_Admin( $this->slug );
			new Powerkit_Facebook_Public( $this->slug );
		}
	}

	new Powerkit_Facebook();
}
