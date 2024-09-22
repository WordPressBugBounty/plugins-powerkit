<?php
/**
 * The public-facing functionality of the module.
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Modules/public
 */

/**
 * The public-facing functionality of the module.
 */
class Powerkit_Facebook_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'init', array( $this, 'facebook_load_sdk' ) );
		add_action( 'current_screen', array( $this, 'facebook_load_sdk_gutenberg' ) );
		add_action( 'init', array( $this, 'register_locations' ) );
		add_filter( 'powerkit_facebook_comments_location', array( $this, 'location_default' ) );
	}

	/**
	 * Init Facebook
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function facebook_load_sdk() {
		add_action( 'wp_footer', 'powerkit_facebook_load_sdk' );
	}

	/**
	 * Init Facebook in Gutenberg editor
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function facebook_load_sdk_gutenberg() {
		global $current_screen;

		$current_screen = get_current_screen();

		// add on the editor page.
		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			add_action( 'admin_footer', 'powerkit_facebook_load_sdk' );
		}
	}

	/**
	 * Facebook Register Locations
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function register_locations() {

		$locations = apply_filters( 'powerkit_facebook_comments_location', array() );

		$powerkit_facebook_enable_comments = get_option( 'powerkit_facebook_enable_comments' );
		$powerkit_facebook_location        = get_option( 'powerkit_facebook_location' );

		// Select location.
		if ( isset( $locations[ $powerkit_facebook_location ] ) ) {

			$location = $locations[ $powerkit_facebook_location ];

		} elseif ( isset( $locations['default'] ) ) {

			$location = $locations['default'];

		} else {
			$location = false;
		}

		if ( $powerkit_facebook_enable_comments && $location ) {
			$priority = (int) isset( $location['priority'] ) ? $location['priority'] : 99;

			if ( 'comments_template' === $location['action'] ) {

				add_filter( 'comments_template', function () {
					return plugin_dir_path( dirname( __FILE__ ) ) . 'public/facebook-comments-template.php';
				}, $priority );

			} elseif ( 'before_comments' === $location['action'] ) {

				add_action( 'comments_template', function () {
					require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/facebook-comments-template.php';
				}, $priority );

			} elseif ( 'after_comments' === $location['action'] ) {
				/**
				 * Filter After WordPress Comments
				 */
				function powerkit_filter_comments_template() {
					remove_filter( 'comments_template', 'powerkit_filter_comments_template', 99 );

					comments_template();

					return plugin_dir_path( dirname( __FILE__ ) ) . 'public/facebook-comments-template.php';
				}
				add_filter( 'comments_template', 'powerkit_filter_comments_template', 99 );

			} else {

				add_action( $location['action'], function () {
					require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/facebook-comments-template.php';
				}, $priority );
			}
		}
	}

	/**
	 * Filter Register Locations
	 *
	 * @since    1.0.0
	 * @access   private
	 *
	 * @param array $locations List of Locations.
	 */
	public function location_default( $locations = array() ) {

		$locations = array(
			'before_comments' => array(
				'name'     => 'Before WordPress Comments',
				'action'   => 'before_comments',
				'priority' => 99,
			),
			'after_comments'  => array(
				'name'     => 'After WordPress Comments',
				'action'   => 'after_comments',
				'priority' => 99,
			),
			'default'         => array(
				'name'     => 'In place of WordPress Comments',
				'action'   => 'comments_template',
				'priority' => 99,
			),
		);

		return $locations;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'powerkit-facebook', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-facebook.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-facebook', 'rtl', 'replace' );
	}
}
