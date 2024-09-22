<?php
/**
 * The admin-specific functionality of the module.
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Modules/Admin
 */

/**
 * The admin-specific functionality of the module.
 */
class Powerkit_Post_Featured_UI_Admin extends Powerkit_Module_Admin {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'admin_head', array( $this, 'admin_head' ), 10 );

		$this->register_featured();
		$this->add_featured_terms();
	}

	/**
	 * Register custom featured taxonomy
	 */
	public function register_featured() {

		$labels = array(
			'name'          => esc_html__( 'Featured Locations', 'powerkit' ),
			'singular_name' => esc_html__( 'Featured Location', 'powerkit' ),
			'all_items'     => esc_html__( 'All Locations', 'powerkit' ),
		);

		$args = array(
			'label'              => esc_html__( 'Featured Locations', 'powerkit' ),
			'labels'             => apply_filters( 'powerkit_featured_taxonomy_labels', $labels ),
			'public'             => false,
			'hierarchical'       => true,
			'show_in_rest'       => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'show_in_nav_menus'  => false,
			'query_var'          => true,
			'rewrite'            => false,
			'show_admin_column'  => true,
			'show_in_quick_edit' => true,
			'sort'               => true,
			'capabilities'       => array(
				'manage_terms' => false,
				'edit_terms'   => false,
				'delete_terms' => false,
			),
		);

		register_taxonomy( 'powerkit_post_featured', array( 'post' ), apply_filters( 'powerkit_featured_taxonomy_args', $args ) );
	}

	/**
	 * Add default featured terms upon theme activation
	 */
	public function add_featured_terms() {

		if ( get_option( 'powerkit_featured_terms_added' ) ) {

			// Return if terms have already been added.
			return;

		} else {

			// Array of Featured Locations.
			$featured_terms = array(
				'archive'  => esc_html__( 'Post Archive', 'powerkit' ),
				'slider'   => esc_html__( 'Post Slider', 'powerkit' ),
				'tiles'    => esc_html__( 'Post Tiles', 'powerkit' ),
				'carousel' => esc_html__( 'Post Carousel', 'powerkit' ),
				'widget'   => esc_html__( 'Posts Widget', 'powerkit' ),
			);

			// Add terms to custom taxonomy Featured Locations.
			foreach ( apply_filters( 'powerkit_featured_terms', $featured_terms ) as $term => $name ) {
				if ( ! term_exists( $name, 'powerkit_post_featured' ) ) {
					wp_insert_term( $name, 'powerkit_post_featured', array( 'slug' => $term ) );
				}
			}

			// Set an option, that terms have been added.
			update_option( 'powerkit_featured_terms_added', true );
		}
	}

	/**
	 * Register the stylesheets and JavaScript for the admin area.
	 *
	 * @param string $page Current page.
	 */
	public function admin_head( $page ) {
		?>
		<style>.column-taxonomy-powerkit_post_featured{ width: 10%; }</style>
		<?php
	}
}
