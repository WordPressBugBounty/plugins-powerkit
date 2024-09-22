<?php
/**
 * Module admin carcase
 *
 * @package    Powerkit
 * @subpackage Core
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Module admin class.
 */
class Powerkit_Module_Admin {

	/**
	 * The module slug.
	 *
	 * @var string $slug The module slug.
	 */
	public $slug = null;

	/**
	 * __construct
	 *
	 * This function will initialize the initialize
	 *
	 * @param string $slug The module slug.
	 */
	public function __construct( $slug = null ) {

		// Init slug of module.
		$this->slug = $slug;

		// Initialize.
		$this->initialize();

		// Actions.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Initialize
	 *
	 * This function will initialize the module
	 */
	public function initialize() {

		/* do nothing */
	}

	/**
	 * Load the required dependencies for this module.
	 *
	 * @param string $page Current page.
	 */
	public function admin_enqueue_scripts( $page ) {

		/* do nothing */
	}
}
