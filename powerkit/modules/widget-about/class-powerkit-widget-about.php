<?php
/**
 * About Widget
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Widget_About extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'About Widget', 'powerkit' );
			$this->desc     = esc_html__( 'Display Image, Text and Social Accounts.', 'powerkit' );
			$this->slug     = 'widget_about';
			$this->type     = 'default';
			$this->category = 'widget';
			$this->priority = 155;
			$this->public   = true;
			$this->enabled  = true;
		}

		/**
		 * Initialize module
		 */
		public function initialize() {
			// Helpers Functions for the module.
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-about.php';

			// The classes responsible for defining all actions.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-widget-about-init.php';

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/public/class-powerkit-widget-about-public.php';

			new Powerkit_Widget_About_Public( $this->slug );
		}
	}

	new Powerkit_Widget_About();
}
