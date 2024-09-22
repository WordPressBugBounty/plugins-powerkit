<?php
/**
 * Post Featured UI
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Post_Featured_UI extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Post Featured UI', 'powerkit' );
			$this->desc     = null;
			$this->slug     = 'post_featured_ui';
			$this->type     = 'default';
			$this->category = 'basic';
			$this->priority = 0;
			$this->public   = false;
			$this->enabled  = true;
		}

		/**
		 * Initialize module
		 */
		public function initialize() {

			/* Load the required dependencies for this module */
			add_action( 'init', array( $this, 'deferred_init' ) );
		}

		/**
		 * Initialize based on theme customization
		 */
		public function deferred_init() {
			if ( get_theme_support( 'powerkit-post-featured-ui' ) ) {
				// Admin and public area.
				require_once dirname( __FILE__ ) . '/admin/class-powerkit-post-featured-ui-admin.php';

				new Powerkit_Post_Featured_UI_Admin( $this->slug );
			}
		}
	}

	new Powerkit_Post_Featured_UI();
}
