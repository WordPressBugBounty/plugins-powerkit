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
class Powerkit_Typekit_Fonts_Public extends Powerkit_Module_Public {

	/**
	 * Webfonts method
	 *
	 * @var string $load_method Webfonts method.
	 */
	public $load_method = 'async';

	/**
	 * Font base.
	 *
	 * This is used in case of Elementor's Font param
	 *
	 * @var string
	 */
	private static $font_base = 'pk-typekit';

	/**
	 * Initialize
	 */
	public function initialize() {
		add_filter( 'language_attributes', array( $this, 'html_attributes' ), 10, 2 );
		add_filter( 'powerkit_fonts_list', array( $this, 'typekit_fonts' ), 20 );
		add_filter( 'csco_customizer_fonts_choices', array( $this, 'csco_typekit_fonts' ), 20 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'editor_enqueue_scripts' ) );
		add_filter( 'init', array( $this, 'set_load_method' ) );
		add_filter( 'init', array( $this, 'kirki_support' ) );
		add_filter( 'elementor/fonts/groups', array( $this, 'elementor_group' ) );
		add_filter( 'elementor/fonts/additional_fonts', array( $this, 'add_elementor_fonts' ) );
		add_filter( 'init', array( $this, 'change_elementor_labels' ) );
	}

	/**
	 * Set Webfonts method
	 */
	public function set_load_method() {
		$this->load_method = apply_filters( 'powerkit_webfonts_load_method', 'async' );
	}

	/**
	 * Return font list from Typekit.
	 */
	public function fonts_list() {

		$fonts = array();

		$token = get_option( 'powerkit_typekit_fonts_token' );
		$kit   = get_option( 'powerkit_typekit_fonts_kit' );

		if ( $token && $kit ) {

			$data = wp_cache_get( 'powerkit_typekit_fonts_kit_cache' );

			if ( ! $data ) {
				$typekit = new Powerkit_Typekit_Api();

				$data = $typekit->get( $kit, $token );

				wp_cache_set( 'powerkit_typekit_fonts_kit_cache', $data, 'powerkit', 1 );
			}

			if ( $data && isset( $data['kit']['families'] ) && $data['kit']['families'] ) {

				foreach ( $data['kit']['families'] as $item ) {
					$id = $item['slug'];

					$fonts[ $id ] = $item;
				}
			}
		}

		return $fonts;
	}

	/**
	 * Add typekit fonts
	 *
	 * @since 1.0.0
	 * @param array $fonts List fonts.
	 */
	public function typekit_fonts( $fonts ) {

		if ( is_customize_preview() ) {

			$fonts_list = $this->fonts_list();

			if ( $fonts_list ) {
				$fonts['families']['typekit'] = array(
					'text'     => esc_html__( 'Typekit', 'powerkit' ),
					'children' => array(),
				);

				foreach ( $fonts_list as $item ) {
					$id = isset( $item['css_names'][0] ) ? $item['css_names'][0] : $item['slug'];

					$fonts['families']['typekit']['children'][] = array(
						'id'   => $id,
						'text' => $item['name'],
					);

					$fonts['variants'][ $id ] = powerkit_typekit_font_variations_format( $item['variations'] );
				}
			}
		}

		return $fonts;
	}

	/**
	 * Add typekit fonts to csco theme
	 *
	 * @since 1.0.0
	 * @param array $fonts List fonts.
	 */
	public function csco_typekit_fonts( $fonts ) {

		if ( is_customize_preview() ) {

			$fonts_list = $this->fonts_list();

			if ( $fonts_list ) {
				$fonts['fonts']['families']['typekit'] = array(
					'text'     => esc_html__( 'Typekit', 'powerkit' ),
					'children' => array(),
				);

				foreach ( $fonts_list as $item ) {
					$id = isset( $item['css_names'][0] ) ? $item['css_names'][0] : $item['slug'];

					$fonts['fonts']['families']['typekit']['children'][] = array(
						'id'   => $id,
						'text' => $item['name'],
					);

					$fonts['fonts']['variants'][ $id ] = powerkit_typekit_font_variations_format( $item['variations'] );
				}
			}
		}

		return $fonts;
	}

	/**
	 * Filters the language attributes for display in the html tag.
	 *
	 * @param string $output A space-separated list of language attributes.
	 * @param string $doctype The type of html document (xhtml|html).
	 */
	public function html_attributes( $output, $doctype ) {
		$token = get_option( 'powerkit_typekit_fonts_token' );
		$kit   = get_option( 'powerkit_typekit_fonts_kit' );

		if ( $token && $kit && 'async' === $this->load_method && ! is_admin() ) {
			$output .= ' class="wf-loading"';
		}

		return $output;
	}

	/**
	 * Add support Kirki.
	 */
	public function kirki_support() {
		if ( class_exists( 'CSCO_Kirki' ) ) {
			add_filter( 'csco_kirki_dynamic_css', array( $this, 'kirki_dynamic_css' ) );
		}
		if ( class_exists( 'Kirki' ) ) {
			$configs = (array) Kirki::$config;

			foreach ( $configs as $config_id => $args ) {
				add_filter( "kirki_{$config_id}_dynamic_css", array( $this, 'kirki_dynamic_css' ) );
			}
		}
	}

	/**
	 * Change font-family stack.
	 *
	 * @param string $style The dynamic css.
	 */
	public function kirki_dynamic_css( $style ) {
		$token = get_option( 'powerkit_typekit_fonts_token' );
		$kit   = get_option( 'powerkit_typekit_fonts_kit' );

		if ( $token && $kit ) {
			$typekit = new Powerkit_Typekit_Api();

			$typekit_data = $typekit->get( $kit, $token );
			if ( isset( $typekit_data['kit']['families'] ) && $typekit_data['kit']['families'] ) {
				foreach ( $typekit_data['kit']['families'] as $family ) {
					$slug  = sprintf( 'font-family:%s', $family['slug'] );
					$stack = sprintf( 'font-family:%s', $family['css_stack'] );
					// Replace font slug to css stack.
					$style = str_replace( $slug, $stack, $style );
				}
			}
		}

		return $style;
	}

	/**
	 * Register fonts in the editor.
	 *
	 * @param string $page Current page.
	 */
	public function editor_enqueue_scripts( $page ) {

		if ( 'post.php' === $page || 'post-new.php' === $page ) {
			$this->wp_enqueue_scripts();
		}
	}

	/**
	 * Add Custom Font group to elementor font list.
	 *
	 * Group name "Custom" is added as the first element in the array.
	 *
	 * @param  Array $font_groups default font groups in elementor.
	 * @return Array              Modified font groups with newly added font group.
	 */
	public function elementor_group( $font_groups ) {
		$new_group[ self::$font_base ] = esc_html__( 'Typekit', 'powerkit' );

		$font_groups = $new_group + $font_groups;

		return $font_groups;
	}

	/**
	 * Add Custom Fonts to the Elementor Page builder's font param.
	 *
	 * @param Array $fonts Custom Font's array.
	 */
	public function add_elementor_fonts( $fonts ) {

		$fonts_list = $this->fonts_list();

		if ( $fonts_list ) {
			foreach ( $fonts_list as $item ) {
				$id = isset( $item['css_names'][0] ) ? $item['css_names'][0] : $item['slug'];

				$fonts[ $id ] = self::$font_base;
			}
		}

		return $fonts;
	}

	/**
	 * Change typography labels in the Elementor.
	 */
	public function change_elementor_labels() {
		add_action( 'elementor/editor/footer', function() {
			$fonts_list = $this->fonts_list();

			ob_start( function( $html ) use( $fonts_list ) {
				if ( $fonts_list ) {
					foreach ( $fonts_list as $item ) {
						$find_option    = '<option value="' . $item['slug'] . '">' . $item['slug'] . '</option>';
						$replace_option = '<option value="' . $item['slug'] . '">' . $item['name'] . '</option>';

						$html = str_replace( $find_option, $replace_option, $html );
					}
				}

				return $html;
			} );
		}, -1 );

		add_action( 'elementor/editor/footer', function() {
			ob_end_flush();
		}, 9999 );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		$token = get_option( 'powerkit_typekit_fonts_token' );
		$kit   = get_option( 'powerkit_typekit_fonts_kit' );

		if ( $token && $kit ) {
			if ( 'async' === $this->load_method ) {
				wp_enqueue_script( 'powerkit-typekit', plugin_dir_url( __FILE__ ) . 'js/public-powerkit-typekit.js', array( 'jquery' ), powerkit_get_setting( 'version' ), true );

				wp_localize_script( 'powerkit-typekit', 'powerkit_typekit', array(
					'kit' => $kit,
				) );
			} else {
				wp_enqueue_style( 'powerkit-typekit', sprintf( 'https://use.typekit.net/%s.css', $kit ), array(), powerkit_get_setting( 'version' ), 'all' );
			}
		}
	}
}
