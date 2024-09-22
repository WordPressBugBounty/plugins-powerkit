<?php
/**
 * Author Box
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Author_Box extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Author Box', 'powerkit' );
			$this->desc     = esc_html__( 'Display a post author box in your sidebar, including author name, bio and avatar in multiple available layouts.', 'powerkit' );
			$this->slug     = 'author_box';
			$this->type     = 'default';
			$this->category = 'widget';
			$this->priority = 160;
			$this->public   = true;
			$this->enabled  = true;
			$this->links    = array(
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/author-box/',
					'target' => '_blank',
				),
			);
		}

		/**
		 * Initialize module
		 */
		public function initialize() {
			// Helpers Functions for the module.
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-author-box.php';

			// The classes responsible for defining all actions.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-author-box-widget.php';

			if ( function_exists( 'register_block_type' ) ) {
				require_once dirname( __FILE__ ) . '/public/class-powerkit-author-box-block.php';
			}

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-author-box-public.php';

			new Powerkit_Author_Box_Public( $this->slug );
		}
	}

	new Powerkit_Author_Box();
}
