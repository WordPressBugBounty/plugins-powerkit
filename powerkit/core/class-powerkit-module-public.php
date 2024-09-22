<?php
/**
 * Module public carcase
 *
 * @package    Powerkit
 * @subpackage Core
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Module public class.
 */
class Powerkit_Module_Public {

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
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
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
	 */
	public function wp_enqueue_scripts() {

		/* do nothing */
	}

	/**
	 * Load the required dependencies for this module in block editr.
	 */
	public function enqueue_block_editor_assets() {

		/* do nothing */
	}
}
