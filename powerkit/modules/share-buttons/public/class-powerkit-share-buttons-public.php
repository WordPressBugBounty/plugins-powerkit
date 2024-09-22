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
class Powerkit_Share_Buttons_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );
		add_filter( 'the_content', array( $this, 'the_post_content' ) );
		add_filter( 'kses_allowed_protocols', array( $this, 'allow_protocols' ) );
		add_filter( 'powerkit_share_buttons_locations', array( $this, 'locations_default' ), 10 );
		add_filter( 'powerkit_share_buttons_locations', array( $this, 'locations_extra' ), 100 );
		add_filter( 'powerkit_share_buttons_color_layouts', array( $this, 'layouts_default' ), 10, 2 );
		add_filter( 'powerkit_share_buttons_color_schemes', array( $this, 'schemes_default' ) );
		add_filter( 'powerkit_share_buttons_total_label', array( $this, 'mobile_share_buttons_total_label' ), 10, 3 );
	}

	/**
	 * Allow protocols for esc_url.
	 *
	 * @param array $protocols Array of allowed protocols.
	 */
	public function allow_protocols( $protocols ) {

		array_push( $protocols, 'fb-messenger', 'whatsapp', 'viber', 'tg' );

		return $protocols;
	}

	/**
	 * Output mobile share buttons.
	 */
	public function wp_footer() {
		// Check AMP endpoint.
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
			return;
		}

		if ( is_singular( 'post' ) && is_single( get_the_ID() ) ) {
			?>
			<div class="pk-mobile-share-overlay">
				<?php
					powerkit_share_buttons_location( 'mobile-share' );
				?>
			</div>
			<?php
		}
	}

	/**
	 * Filter output buttons in post content.
	 *
	 * @param string $content The content of post.
	 */
	public function the_post_content( $content ) {
		// Check AMP endpoint.
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
			return $content;
		}

		if ( is_singular( 'post' ) && is_single( get_the_ID() ) ) {
			ob_start();
				powerkit_share_buttons_location( 'before-content' );
			$before_shares = ob_get_clean();

			ob_start();
				powerkit_share_buttons_location( 'after-content' );
			$after_shares = ob_get_clean();

			ob_start();
				powerkit_share_buttons_location( 'highlight-text' );
			$highlight_shares = ob_get_clean();

			$content .= $highlight_shares;

			ob_start();
				powerkit_share_buttons_location( 'blockquote' );
			$blockquote = ob_get_clean();

			$content .= $blockquote;

			// Clearfix.
			if ( $after_shares ) {
				$after_shares = '<div class="pk-clearfix"></div>' . $after_shares;
			}

			// Concatenation.
			$content = $before_shares . $content . $after_shares;
		}

		return $content;
	}

	/**
	 * Filter Register Locations
	 *
	 * @since    1.0.0
	 * @access   private
	 *
	 * @param array $locations List of Locations.
	 */
	public function locations_default( $locations = array() ) {
		$locations = array(
			'after-content'  => array(
				'shares'   => array( 'facebook', 'twitter', 'pinterest', 'mail' ),
				'name'     => 'After Post Content',
				'location' => 'after-content',
				'mode'     => 'mixed',
				'before'   => '',
				'after'    => '',
				'display'  => true,
				'fields'   => array(
					'display_total' => true,
					'display_count' => true,
				),
			),
			'before-content' => array(
				'shares'   => array( 'facebook', 'twitter', 'pinterest', 'mail' ),
				'name'     => 'Before Post Content',
				'location' => 'before-content',
				'mode'     => 'mixed',
				'before'   => '',
				'after'    => '',
				'fields'   => array(
					'display_total' => true,
					'display_count' => true,
				),
			),
		);

		return $locations;
	}

	/**
	 * Filter Register Extra Locations
	 *
	 * @since    1.0.0
	 * @access   private
	 *
	 * @param array $locations List of Locations.
	 */
	public function locations_extra( $locations = array() ) {

		$locations['highlight-text'] = array(
			'shares'        => array( 'facebook', 'twitter', 'pinterest', 'mail' ),
			'name'          => '⚡ Highlight Text',
			'location'      => 'highlight-text',
			'mode'          => 'none',
			'before'        => '',
			'after'         => '',
			'meta'          => array(
				'icons'  => true,
				'titles' => false,
				'labels' => false,
			),
			'fields'        => array(
				'display_total'   => false,
				'display_count'   => false,
				'title_locations' => array(),
				'count_locations' => array(),
				'label_locations' => array(),
				'layouts'         => array( 'simple' ),
			),
			'display_total' => false,
			'layout'        => 'simple',
		);

		$locations['blockquote'] = array(
			'shares'        => array( 'facebook', 'twitter' ),
			'name'          => '⭐ Blockquote',
			'location'      => 'blockquote',
			'mode'          => 'none',
			'before'        => '',
			'after'         => '',
			'meta'          => array(
				'icons'  => true,
				'titles' => false,
				'labels' => true,
			),
			'fields'        => array(
				'display_total'   => false,
				'display_count'   => false,
				'title_locations' => array(),
				'count_locations' => array(),
				'label_locations' => array(),
				'layouts'         => array( 'simple' ),
			),
			'display_total' => false,
			'layout'        => 'simple',
		);

		$locations['mobile-share'] = array(
			'shares'   => array( 'facebook', 'pinterest', 'twitter', 'mail' ),
			'name'     => '📱 Mobile Share',
			'location' => 'mobile-share',
			'mode'     => 'none',
			'before'   => '',
			'after'    => '',
			'meta'     => array(
				'icons'  => true,
				'titles' => false,
				'labels' => false,
			),
			'fields'   => array(
				'display_total'   => false,
				'display_count'   => true,
				'title_locations' => array(),
				'count_locations' => array(),
				'label_locations' => array(),
				'schemes'         => array( 'default', 'simple-dark-back', 'bold-bg', 'bold' ),
				'layouts'         => array( 'horizontal', 'left-side', 'right-side', 'popup' ),
			),
			'layout'   => 'horizontal',
		);

		return $locations;
	}

	/**
	 * Filter Register Layouts
	 *
	 * @param array  $layouts  List of Layouts.
	 * @param string $location Name of Location.
	 */
	public function layouts_default( $layouts = array(), $location = null ) {
		$layouts['default'] = array(
			'name' => 'First Two Large Buttons',
		);

		$layouts['equal'] = array(
			'name' => 'Equal Width Buttons',
		);

		$layouts['simple'] = array(
			'name' => 'Simple Buttons',
		);

		if ( 'mobile-share' === $location ) {

			$layouts['horizontal'] = array(
				'name' => 'Horizontal',
			);

			$layouts['left-side'] = array(
				'name' => 'Left side',
			);

			$layouts['right-side'] = array(
				'name' => 'Right side',
			);

			$layouts['popup'] = array(
				'name' => 'Popup',
			);
		}

		return $layouts;
	}

	/**
	 * Filter Register Schemes
	 *
	 * @param array $schemes List of Schemes.
	 */
	public function schemes_default( $schemes = array() ) {

		$schemes['default'] = array( // simple-light-back.
			'name' => 'Simple & Light Background',
		);

		$schemes['simple-dark-back'] = array(
			'name' => 'Simple & Dark Background',
		);

		$schemes['bold-bg'] = array( // simple-bold-back.
			'name' => 'Simple & Bold Background',
		);

		$schemes['simple-light'] = array(
			'name' => 'Simple',
		);

		$schemes['bold'] = array( // bold-light-back.
			'name' => 'Bold & Light Background',
		);

		$schemes['bold-light'] = array(
			'name' => 'Bold',
		);

		$schemes['inverse-light'] = array(
			'name' => 'Inverse',
		);

		return $schemes;
	}
	/**
	 * Change Total Output of Share Buttons
	 *
	 * @param bool   $output  The output.
	 * @param string $class   The class.
	 * @param int    $count   The count.
	 */
	public function mobile_share_buttons_total_label( $output, $class, $count ) {

		if ( false !== strpos( $class, 'pk-share-buttons-mobile-share' ) ) {
			$output = esc_html__( 'Share', 'powerkit' );
		}

		return $output;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'powerkit-share-buttons', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-share-buttons.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-share-buttons', 'rtl', 'replace' );

		// Scripts.
		wp_enqueue_script( 'powerkit-share-buttons', plugin_dir_url( __FILE__ ) . 'js/public-powerkit-share-buttons.js', array( 'jquery' ), powerkit_get_setting( 'version' ), true );
	}

	/**
	 * Add styles in Gutenberg editor.
	 * Used in Featured Posts block.
	 */
	public function enqueue_block_editor_assets() {
		wp_enqueue_style( 'powerkit-share-buttons', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-share-buttons.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-share-buttons', 'rtl', 'replace' );
	}
}
