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
class Powerkit_Lightbox_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_filter( 'wp_kses_allowed_html', array( $this, 'filter_allowed_html' ), 10, 2 );
	}

	/**
	 * Allow pinterest data attributes on our links.
	 *
	 * @param array  $allowed array  The allowed.
	 * @param string $context string The context.
	 */
	public function filter_allowed_html( $allowed, $context ) {
		if ( 'post' === $context ) {
			$allowed['img']['data-lightbox-description'] = true;
		}

		return $allowed;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		// Styles.
		wp_enqueue_style( 'glightbox', plugin_dir_url( __FILE__ ) . 'css/glightbox.min.css', array(), powerkit_get_setting( 'version' ), 'all' );

		wp_enqueue_style( 'powerkit-lightbox', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-lightbox.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-lightbox', 'rtl', 'replace' );

		// Scripts.
		wp_enqueue_script( 'glightbox', plugin_dir_url( __FILE__ ) . 'js/glightbox.min.js', array( 'jquery', 'imagesloaded' ), powerkit_get_setting( 'version' ), true );

		wp_enqueue_script( 'powerkit-lightbox', plugin_dir_url( __FILE__ ) . 'js/public-powerkit-lightbox.js', array( 'jquery', 'imagesloaded' ), powerkit_get_setting( 'version' ), true );

		$single_image_selectors = get_option( 'powerkit_lightbox_single_image_selectors', array( '.entry-content img' ) );
		$gallery_selectors      = get_option( 'powerkit_lightbox_gallery_selectors', array( '.wp-block-gallery', '.gallery' ) );
		$exclude_selectors      = get_option( 'powerkit_lightbox_exclude_selectors', array() );

		$single_image_selectors = powerkit_lightbox_process_selectors( $single_image_selectors );
		$gallery_selectors      = powerkit_lightbox_process_selectors( $gallery_selectors );
		$exclude_selectors      = powerkit_lightbox_process_selectors( $exclude_selectors );

		$single_image_selectors = apply_filters( 'powerkit_lightbox_image_selectors', $single_image_selectors );
		$gallery_selectors      = apply_filters( 'powerkit_lightbox_gallery_selectors', $gallery_selectors );
		$exclude_selectors      = apply_filters( 'powerkit_lightbox_exclude_selectors', $exclude_selectors );

		wp_localize_script( 'powerkit-lightbox', 'powerkit_lightbox_localize', array(
			'text_previous'          => esc_html__( 'Previous', 'powerkit' ),
			'text_next'              => esc_html__( 'Next', 'powerkit' ),
			'text_close'             => esc_html__( 'Close', 'powerkit' ),
			'text_loading'           => esc_html__( 'Loading', 'powerkit' ),
			'text_counter'           => esc_html__( 'of', 'powerkit' ),
			'single_image_selectors' => implode( ',', $single_image_selectors ),
			'gallery_selectors'      => implode( ',', $gallery_selectors ),
			'exclude_selectors'      => implode( ',', $exclude_selectors ),
			'zoom_icon'              => get_option( 'powerkit_lightbox_zoom_icon', true ),
		) );
	}
}
