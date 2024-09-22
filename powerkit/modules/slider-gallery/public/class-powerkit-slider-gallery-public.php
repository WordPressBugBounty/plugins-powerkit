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
class Powerkit_Slider_Gallery_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_filter( 'powerkit_gallery_types', array( $this, 'gallery_types' ) );
		add_filter( 'powerkit_gallery_settings', array( $this, 'gallery_settings' ), 10, 2 );
	}

	/**
	 * Add new gallery types
	 *
	 * @param array $types Gallery types.
	 */
	public function gallery_types( $types ) {

		$types = array_merge( $types, array(
			'slider' => esc_html__( 'Slider', 'powerkit' ),
		) );

		return $types;
	}

	/**
	 * Set gallery settings
	 *
	 * @param array $settings Gallery settings.
	 * @param array $attr     Gallery attr.
	 */
	public function gallery_settings( $settings, $attr ) {

		if ( isset( $attr['type'] ) && 'slider' === $attr['type'] ) {
			$data = array(
				'pk-sg-page-dots' => (bool) get_option( 'powerkit_slider_gallery_page_dots', true ) ? 'true' : 'false',
				'pk-sg-page-info' => (bool) get_option( 'powerkit_slider_gallery_page_info', true ) ? 'true' : 'false',
				'pk-sg-nav'       => (bool) get_option( 'powerkit_slider_gallery_nav', true ) ? 'true' : 'false',
			);

			$data = array_merge( (array) $data, (array) $attr );

			$settings['custom_attrs'] .= sprintf( ' pk-flickity="init" data-sg-page-dots="%s" data-sg-page-info="%s" data-sg-nav="%s"', $data['pk-sg-page-dots'], $data['pk-sg-page-info'], $data['pk-sg-nav'] );
		}

		return $settings;
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		// Styles.
		wp_enqueue_style( 'powerkit-slider-gallery', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-slider-gallery.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-slider-gallery', 'rtl', 'replace' );

		// Scripts.
		wp_enqueue_script( 'flickity', plugin_dir_url( __FILE__ ) . 'js/flickity.pkgd.min.js', array( 'jquery', 'imagesloaded' ), powerkit_get_setting( 'version' ), true );

		wp_enqueue_script( 'powerkit-slider-gallery', plugin_dir_url( __FILE__ ) . 'js/public-powerkit-slider-gallery.js', array( 'jquery', 'imagesloaded' ), powerkit_get_setting( 'version' ), true );

		wp_localize_script( 'powerkit-slider-gallery', 'powerkit_sg_flickity', array(
			'page_info_sep' => esc_html__( ' of ', 'powerkit' ),
		) );
	}
}
