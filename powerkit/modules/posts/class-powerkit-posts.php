<?php
/**
 * Posts
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Posts extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Posts', 'powerkit' );
			$this->desc     = esc_html__( 'Display a list of posts in your sidebar, including post meta and preview image in multiple available layouts.', 'powerkit' );
			$this->slug     = 'posts';
			$this->type     = 'default';
			$this->category = 'content';
			$this->priority = 150;
			$this->public   = true;
			$this->enabled  = true;
			$this->links    = array(
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/posts/',
					'target' => '_blank',
				),
			);
		}

		/**
		 * Initialize module
		 */
		public function initialize() {
			// Helpers Functions for the module.
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-posts.php';

			// The classes responsible for defining all actions.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-posts-widget.php';

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-posts-public.php';

			new Powerkit_Posts_Public( $this->slug );
		}
	}

	new Powerkit_Posts();
}
