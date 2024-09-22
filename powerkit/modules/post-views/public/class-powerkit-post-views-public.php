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
class Powerkit_Post_Views_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_filter( 'wp_ajax_post_views_reset', array( $this, 'post_views_reset' ) );
		add_filter( 'wp_ajax_nopriv_post_views_reset', array( $this, 'post_views_reset' ) );
	}

	/**
	 * Post Views Reset
	 */
	public function post_views_reset() {
		powerkit_uuid_hash();

		// Check wpnonce.
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'] ) ) { // Input var ok; sanitization ok.
			return;
		}

		if ( isset( $_REQUEST['post_id'] ) && $_REQUEST['post_id'] ) { // Input var ok; sanitization ok.
			$post_id = (int) sanitize_text_field( $_REQUEST['post_id'] ); // Input var ok; sanitization ok.

			$result = esc_html( powerkit_get_post_views( $post_id, true, false ) );

			wp_send_json_success( $result );
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		if ( ! is_singular() ) {
			return;
		}

		$options = powerkit_post_views_options();

		if ( $options['token'] && $options['clientid'] && $options['psecret'] ) {
			// Scripts.
			wp_enqueue_script( 'powerkit-post-views', plugin_dir_url( __FILE__ ) . 'js/public-powerkit-post-views.js', array( 'jquery' ), powerkit_get_setting( 'version' ), true );

			// Localize scripts.
			wp_localize_script( 'powerkit-post-views', 'pkPostViews', array(
				'ajaxurl' => admin_url( 'admin-ajax.php?action=post_views_reset' ),
				'post_id' => get_queried_object_id(),
				'nonce'   => wp_create_nonce( ),
			) );
		}
	}
}
