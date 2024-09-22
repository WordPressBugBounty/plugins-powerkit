<?php
/**
 * Inline Posts
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Inline_Posts extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Inline Posts', 'powerkit' );
			$this->desc     = esc_html__( 'Display related posts inline with other post content for ultra-high conversion rate and user engagement in multiple different layouts.', 'powerkit' );
			$this->slug     = 'inline_posts';
			$this->type     = 'default';
			$this->category = 'content';
			$this->priority = 120;
			$this->public   = true;
			$this->enabled  = true;
			$this->links    = array(
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/content-presentation/inline-posts/',
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
			require_once dirname( __FILE__ ) . '/helpers/class-powerkit-inline-posts-snippet.php';

			// The classes responsible for defining all actions.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-inline-posts-shortcode.php';

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-inline-posts-public.php';

			new Powerkit_Inline_Posts_Public( $this->slug );
		}
	}

	new Powerkit_Inline_Posts();
}
