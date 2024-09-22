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
class Powerkit_Justified_Gallery_Public extends Powerkit_Module_Public {

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
			'justified' => esc_html__( 'Justified', 'powerkit' ),
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

		if ( isset( $attr['type'] ) && 'justified' === $attr['type'] ) {

			$data = array(
				'pk-jg-margins'        => (int) get_option( 'powerkit_justified_gallery_margins', '10' ),
				'pk-jg-row-height'     => (int) get_option( 'powerkit_justified_gallery_row_height', '160' ),
				'pk-jg-max-row-height' => (int) get_option( 'powerkit_justified_gallery_max_row_height', '-1' ),
				'pk-jg-last-row'       => (string) get_option( 'powerkit_justified_gallery_last_row', 'justify' ),
			);

			$data = array_merge( (array) $data, (array) $attr );

			if ( -1 === $data['pk-jg-max-row-height'] ) {
				$data['pk-max-row-height'] = 'false';
			}

			$settings['custom_attrs'] .= sprintf( ' data-jg-margins="%s" data-jg-row-height="%s" data-jg-max-row-height="%s" data-jg-last-row="%s"', $data['pk-jg-margins'], $data['pk-jg-row-height'], $data['pk-jg-max-row-height'], $data['pk-jg-last-row'] );
		}

		return $settings;
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		// Styles.
		wp_enqueue_style( 'powerkit-justified-gallery', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-justified-gallery.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-justified-gallery', 'rtl', 'replace' );

		// Scripts.
		wp_enqueue_script( 'justifiedgallery', plugin_dir_url( __FILE__ ) . 'js/jquery.justifiedGallery.min.js', array( 'jquery' ), powerkit_get_setting( 'version' ), true );

		wp_enqueue_script( 'powerkit-justified-gallery', plugin_dir_url( __FILE__ ) . 'js/public-powerkit-justified-gallery.js', array( 'jquery' ), powerkit_get_setting( 'version' ), true );

		wp_localize_script( 'powerkit-justified-gallery', 'powerkitJG', array(
			'rtl' => is_rtl(),
		) );
	}
}
