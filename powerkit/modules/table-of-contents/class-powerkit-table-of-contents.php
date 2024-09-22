<?php
/**
 * Table of Contents
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Table_Of_Contents extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Table of Contents', 'powerkit' );
			$this->desc     = esc_html__( 'Display table of contents automatically with a shortcode or in a widget based on the page headings.', 'powerkit' );
			$this->slug     = 'table_of_contents';
			$this->type     = 'default';
			$this->category = 'content';
			$this->priority = 130;
			$this->public   = true;
			$this->enabled  = true;
			$this->links    = array(
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/content-presentation/table-of-contents/',
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
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-table-of-contents-parser.php';
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-table-of-contents.php';

			// The classes responsible for defining all actions.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-table-of-contents-shortcode.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-table-of-contents-widget.php';

			if ( function_exists( 'register_block_type' ) ) {
				require_once dirname( __FILE__ ) . '/public/class-powerkit-table-of-contents-block.php';
			}

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-table-of-contents-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-table-of-contents-public.php';

			new Powerkit_Table_Of_Contents_Admin( $this->slug );
			new Powerkit_Table_Of_Contents_Public( $this->slug );
		}
	}

	new Powerkit_Table_Of_Contents();
}
