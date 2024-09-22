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
class Powerkit_Table_Of_Contents_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		// Apply toc to content.
		add_filter( 'the_content', array( $this, 'the_content' ), 999 );
	}

	/**
	 * Filters the post content.
	 *
	 * @param string $content The content.
	 */
	public function the_content( $content ) {
		if ( is_single() ) {
			$toc_exclude = get_option( 'powerkit_toc_exclude' );

			// Exclude of posts.
			$toc_exclude = array_map( 'trim', explode( ',', $toc_exclude ) );
			$toc_exclude = array_map( 'intval', $toc_exclude );

			if ( ! in_array( (int) get_the_ID(), $toc_exclude, true ) ) {
				$shortcode = sprintf( '[powerkit_toc title="%s" depth="%d" min_count="%d" min_characters="%d" btn_hide="%s" default_state="%s"]',
					get_option( 'powerkit_toc_title', esc_html__( 'Table of Contents', 'powerkit' ) ),
					get_option( 'powerkit_toc_depth', 2 ),
					get_option( 'powerkit_toc_min_count', 4 ),
					get_option( 'powerkit_toc_min_characters', 1000 ),
					get_option( 'powerkit_toc_btn_hide', false ) ? 'true' : 'false',
					get_option( 'powerkit_toc_default_state', 'expanded' )
				);

				if ( get_option( 'powerkit_toc_enable_automatically', false ) ) {
					$content = do_shortcode( $shortcode ) . $content;
				}
			}
		}

		// Process.
		$toc = powerkit_toc_process( $content );

		if ( isset( $toc['content'] ) && $toc['content'] ) {
			$content = $toc['content'];
		}

		return $content;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		if ( is_singular() ) {
			wp_enqueue_script( 'powerkit-table-of-contents', plugin_dir_url( __FILE__ ) . 'js/public-powerkit-table-of-contents.js', array( 'jquery' ), powerkit_get_setting( 'version' ), true );

			wp_localize_script( 'powerkit-table-of-contents', 'powerkit_toc_config', array(
				'label_show' => esc_html__( 'Show', 'powerkit' ),
				'label_hide' => esc_html__( 'Hide', 'powerkit' ),
			) );

			wp_enqueue_style( 'powerkit-table-of-contents', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-table-of-contents.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

			// Add RTL support.
			wp_style_add_data( 'powerkit-table-of-contents', 'rtl', 'replace' );
		}
	}
}
