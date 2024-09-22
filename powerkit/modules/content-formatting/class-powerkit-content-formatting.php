<?php
/**
 * Content Formatting
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Content_Formatting extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Content Formatting', 'powerkit' );
			$this->desc     = esc_html__( 'A few nice extra content formatting features: numbered headings, styled lists, badges, drop-caps, and content blocks.', 'powerkit' );
			$this->slug     = 'content_formatting';
			$this->type     = 'default';
			$this->category = 'content';
			$this->priority = 140;
			$this->public   = true;
			$this->enabled  = true;
			$this->links    = array(
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/content-presentation/content-formatting/',
					'target' => '_blank',
				),
			);
		}

		/**
		 * Initialize module
		 */
		public function initialize() {

			/* Load the required dependencies for this module */

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-content-formatting-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-content-formatting-public.php';

			new Powerkit_Content_Formatting_Admin( $this->slug );
			new Powerkit_Content_Formatting_Public( $this->slug );
		}
	}

	new Powerkit_Content_Formatting();
}
