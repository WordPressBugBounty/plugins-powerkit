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
class Powerkit_Social_Links_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_filter( 'powerkit_social_links_templates', array( $this, 'templates_default' ) );
		add_filter( 'powerkit_social_links_color_schemes', array( $this, 'schemes_default' ) );
	}

	/**
	 * Filter Register Templates
	 *
	 * @param array $templates List of Templates.
	 */
	public function templates_default( $templates = array() ) {
		$templates = array(
			'inline'   => array(
				'name' => 'Inline',
			),
			'col-2'    => array(
				'name' => '2 columns',
			),
			'col-3'    => array(
				'name' => '3 columns',
			),
			'col-4'    => array(
				'name' => '4 columns',
			),
			'col-5'    => array(
				'name' => '5 columns',
			),
			'col-6'    => array(
				'name' => '6 columns',
			),
			'vertical' => array(
				'name' => 'Vertical List',
			),
			'nav'      => array(
				'name'   => 'Navigation',
				'public' => false,
			),
		);

		return $templates;
	}

	/**
	 * Filter Register Color Schemes
	 *
	 * @param array $schemes List of Color Schemes.
	 */
	public function schemes_default( $schemes = array() ) {

		$schemes = array(
			'light'         => array(
				'name' => 'Light',
			),
			'bold'          => array(
				'name' => 'Bold',
			),
			'inverse'       => array(
				'name' => 'Inverse',
			),
			'light-bg'      => array(
				'name' => 'Light Background',
			),
			'bold-bg'       => array(
				'name' => 'Bold Background',
			),
			'dark-bg'       => array(
				'name' => 'Dark Background',
			),
			'light-rounded' => array(
				'name' => 'Light Rounded',
			),
			'bold-rounded'  => array(
				'name' => 'Bold Rounded',
			),
			'dark-rounded'  => array(
				'name' => 'Dark Rounded',
			),
		);

		return $schemes;
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'powerkit-social-links', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-social-links.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-social-links', 'rtl', 'replace' );
	}
}
