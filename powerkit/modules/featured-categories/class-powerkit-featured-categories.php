<?php
/**
 * Featured Categories
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Featured_Categories extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Featured Categories', 'powerkit' );
			$this->desc     = esc_html__( 'Display Featured Categories.', 'powerkit' );
			$this->slug     = 'featured_categories';
			$this->type     = 'default';
			$this->category = 'content';
			$this->priority = 150;
			$this->public   = true;
			$this->enabled  = true;
			$this->links    = array(
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/featured-categories/',
					'target' => '_blank',
				),
			);
		}

		/**
		 * Initialize module
		 */
		public function initialize() {
			// Helpers Functions for the module.
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-featured-categories.php';

			// The classes responsible for defining all actions.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-featured-categories-widget.php';

			if ( function_exists( 'register_block_type' ) ) {
				require_once dirname( __FILE__ ) . '/public/class-powerkit-featured-categories-block.php';
			}

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-featured-categories-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-featured-categories-public.php';

			new Powerkit_Featured_Categories_Admin( $this->slug );
			new Powerkit_Featured_Categories_Public( $this->slug );
		}
	}

	new Powerkit_Featured_Categories();
}
