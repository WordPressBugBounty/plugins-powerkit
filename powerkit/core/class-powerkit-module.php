<?php
/**
 * Module carcase
 *
 * @package    Powerkit
 * @subpackage Core
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Module main class.
 */
class Powerkit_Module {

	/**
	 * The module name.
	 *
	 * @var string $name The module name.
	 */
	public $name = null;

	/**
	 * The module description.
	 *
	 * @var string $desc The module description.
	 */
	public $desc = null;

	/**
	 * The module slug.
	 *
	 * @var string $slug The module slug.
	 */
	public $slug = null; // Required.

	/**
	 * The module category.
	 *
	 * @var string $category The module category.
	 */
	public $category = 'basic';

	/**
	 * The module priority.
	 *
	 * @var string $priority The module priority.
	 */
	public $priority = 9999;

	/**
	 * The module type.
	 *
	 * @var string $type The module type.
	 */
	public $type = 'default';

	/**
	 * The module public side of the site.
	 *
	 * @var string $category The module public side of the site.
	 */
	public $public = true;

	/**
	 * The module enabled.
	 *
	 * @var string $enabled The module enabled.
	 */
	public $enabled = true;

	/**
	 * The module badge.
	 *
	 * @var string $badge The module badge.
	 */
	public $badge = null;

	/**
	 * The module load extensions.
	 *
	 * @var string $load_extensions The module load extensions.
	 */
	public $load_extensions = array();

	/**
	 * The module actions links.
	 *
	 * @var string $links The module actions links.
	 */
	public $links = array();

	/**
	 * __construct
	 *
	 * This function will initialize the initialize
	 */
	public function __construct() {

		// Initialize.
		$this->register();

		// Register module info.
		powerkit_register_module_info( array(
			'name'            => $this->name,
			'desc'            => $this->desc,
			'slug'            => $this->slug,
			'type'            => $this->type,
			'category'        => $this->category,
			'priority'        => $this->priority,
			'public'          => $this->public,
			'enabled'         => $this->enabled,
			'badge'           => $this->badge,
			'load_extensions' => $this->load_extensions,
			'links'           => $this->links,
		) );

		// Check enabled module.
		if ( ! powerkit_module_enabled( $this->slug ) ) {
			return false;
		}

		// Define the functionality of the module.
		$this->initialize();
	}

	/**
	 * Register
	 *
	 * This function will register the module
	 */
	public function register() {

		/* do nothing */
	}

	/**
	 * Initialize
	 *
	 * This function will initialize the module
	 */
	public function initialize() {

		/* do nothing */
	}
}
