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
class Powerkit_Coming_Soon_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_filter( 'wp', array( $this, 'init_page' ) );
		add_action( 'wp', array( $this, 'change_httpcode' ) );
	}

	/**
	 * Set HTTP status header.
	 */
	public function change_httpcode() {
		// Check location.
		if ( is_admin() ) {
			return;
		}

		if ( powerkit_coming_soon_page_visible() || powerkit_coming_soon_site_visible() ) {
			$httpcode = get_option( 'powerkit_coming_soon_httpcode', 404 );

			status_header( $httpcode );
		}
	}

	/**
	 * Init Page
	 */
	public function init_page() {
		// Check location.
		if ( is_admin() ) {
			return;
		}

		if ( powerkit_coming_soon_page_visible() || powerkit_coming_soon_site_visible() ) {
			add_filter( 'pre_get_document_title', array( $this, 'title' ) );
			add_action( 'template_redirect', array( $this, 'template' ) );
		}
	}

	/**
	 * Title
	 */
	public function title() {
		$page_id = get_option( 'powerkit_coming_soon_page' );

		if ( $page_id ) {

			$page = get_post( $page_id );

			// Set title.
			$title = $page ? $page->post_title : esc_html__( 'Coming Soon', 'powerkit' );

			return $title;
		}
	}

	/**
	 * Template
	 */
	public function template() {
		$page_id = get_option( 'powerkit_coming_soon_page' );

		if ( $page_id ) {
			// Get page object.
			$object = get_post( $page_id );

			// Get title.
			$title = $object ? $object->post_title : esc_html__( 'Coming Soon', 'powerkit' );

			// Get content.
			$content = $object ? $object->post_content : esc_html__( 'Get Ready... Something Really Cool Is Coming Soon', 'powerkit' );

			// Set template path.
			$template_path = apply_filters( 'powerkit_coming_soon_template', POWERKIT_CS_TEMPLATES . '/default.php' );

			if ( ! file_exists( $template_path ) ) {
				wp_die( 'Template file does not exist!' );
			}

			include_once $template_path;
		}

		die();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		// Styles.
		wp_enqueue_style( 'powerkit-coming-soon', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-coming-soon.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-coming-soon', 'rtl', 'replace' );
	}
}
