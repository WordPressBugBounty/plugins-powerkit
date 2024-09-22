<?php
/**
 * Contributors
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Contributors extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Contributors', 'powerkit' );
			$this->desc     = esc_html__( 'Display a list of your site authors (contributors) in your sidebar, including author name, bio,  and avatar in multiple available layouts.', 'powerkit' );
			$this->slug     = 'contributors';
			$this->type     = 'default';
			$this->category = 'content';
			$this->priority = 150;
			$this->public   = true;
			$this->enabled  = true;
			$this->links    = array(
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/contributors/',
					'target' => '_blank',
				),
			);
		}

		/**
		 * Initialize module
		 */
		public function initialize() {
			// Helpers Functions for the module.
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-contributors.php';

			// The classes responsible for defining all actions.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-contributors-widget.php';

			if ( function_exists( 'register_block_type' ) ) {
				require_once dirname( __FILE__ ) . '/public/class-powerkit-contributors-block.php';
			}

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-contributors-public.php';

			new Powerkit_Contributors_Public( $this->slug );
		}
	}

	new Powerkit_Contributors();
}
