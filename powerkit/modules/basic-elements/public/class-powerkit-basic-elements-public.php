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
class Powerkit_Basic_Elements_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'init', array( $this, 'register_custom_shortcodes' ) );

		$this->register_shortcodes();
	}

	/**
	 * Shortcodes custom.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function register_custom_shortcodes() {

		$dir_path = apply_filters( 'powerkit_basic_shortcodes_autoload_path', 'shortcodes' );

		$custom_path = wp_normalize_path( get_template_directory() . '/' . $dir_path );

		if ( file_exists( $custom_path ) ) {
			powerkit_basic_shortcodes_autoload( $custom_path );
		}
	}

	/**
	 * Register Shortcodes
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function register_shortcodes() {

		// Get all shortcodes.
		$sections = apply_filters( 'powerkit_basic_shortcodes_ui_args', array() );

		// Add shortcodes.
		foreach ( $sections as $section ) {
			if ( true === $section['autoregister'] ) {
				add_shortcode( $section['base'], array( $this, 'shortcode_display' ) );
			}

			// Repeat Shortcodes.
			if ( ! empty( $section['fields'] ) ) {
				foreach ( $section['fields'] as $field ) {
					if ( 'repeater' === $field['type'] && true === $field['autoregister'] ) {
						add_shortcode( $field['base'], array( $this, 'shortcode_display' ) );
					}
				}
			}
		}
	}

	/**
	 * Shortcode Public Display
	 *
	 * @param array  $atts      User defined attributes in shortcode tag.
	 * @param string $content   Shorcode Content.
	 * @param string $shortcode The name of the shortcode.
	 * @return string           Shortcode result HTML.
	 */
	public function shortcode_display( $atts, $content = false, $shortcode = '' ) {
		// Get all shortcodes.
		$sections = apply_filters( 'powerkit_basic_shortcodes_ui_args', array() );

		// Get Default Attrs.
		$default_attrs = array();
		foreach ( $sections as $section ) {
			if ( isset( $section['fields'] ) && is_array( $section['fields'] ) ) {
				foreach ( $section['fields'] as $field ) {
					switch ( $field['type'] ) {
						case 'section':
							break;
						case 'repeater':
							if ( $field['base'] === $shortcode ) {
								foreach ( $field['fields'] as $repeater_field ) {
									$default_attrs[ $repeater_field['name'] ] = $repeater_field['default'] ? $repeater_field['default'] : '';
								}
							}
							break;
						default:
							if ( $section['base'] === $shortcode ) {
								$default_attrs[ $field['name'] ] = $field['default'] ? $field['default'] : '';
							}
							break;
					}
				}
			}
		}

		// Merge Attrs.
		$atts = shortcode_atts( $default_attrs, $atts );

		// Content.
		$content = do_shortcode( $content );

		/**
		 * Filters a shortcode's HTML.
		 *
		 * @param array  $output    Shortcode HTML.
		 * @param array  $atts      User defined attributes in shortcode tag.
		 * @param string $content   Shorcode tag content.
		 * @return string           Shortcode result HTML.
		 */
		$output = apply_filters( $shortcode . '_shortcode', '', $atts, $content );

		return $output;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		// Styles.
		wp_enqueue_style( 'powerkit-basic-elements', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-basic-elements.css' ), false, powerkit_get_setting( 'version' ), 'screen' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-basic-elements', 'rtl', 'replace' );

		// Scripts.
		wp_enqueue_script( 'powerkit-basic-elements', plugin_dir_url( __FILE__ ) . 'js/public-powerkit-basic-elements.js', array( 'jquery' ), '4.0.0', true );
	}
}
