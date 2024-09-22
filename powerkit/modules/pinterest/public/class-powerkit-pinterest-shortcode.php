<?php
/**
 * Shortcodes Pinterest
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    PowerKit
 * @subpackage PowerKit/shortcodes
 */

/**
 * Pinterest Board Shortcode
 *
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_pinterest_board_shortcode( $atts, $content = '' ) {

	$params = powerkit_shortcode_atts( shortcode_atts( array(
		'href' => '',
	), $atts ) );

	ob_start();

	if ( $params['href'] ) {
	?>
		<div class="pinterest-board-wrapper">
			<a data-pin-do="embedBoard" data-pin-board-width="100%" href="<?php echo esc_url( $params['href'] ); ?>"></a>
		</div>
	<?php
	} else {
		powerkit_alert_warning( esc_html__( 'The "Pinterest Board URL" field is required!', 'powerkit' ) );
	}

	return ob_get_clean();
}
add_shortcode( 'powerkit_pinterest_board', 'powerkit_pinterest_board_shortcode' );

/**
 * Map Pinterest Board Shortcode into the Basic Shortcodes Plugin
 */
if ( function_exists( 'powerkit_basic_shortcodes_register' ) ) :

	powerkit_basic_shortcodes_register( array(
		'name'         => 'pinterest_board',
		'title'        => esc_html__( 'Pinterest Board', 'powerkit' ),
		'priority'     => 150,
		'base'         => 'powerkit_pinterest_board',
		'autoregister' => false,
		'fields'       => array(
			array(
				'type'  => 'input',
				'name'  => 'href',
				'label' => esc_html__( 'Pinterest board URL', 'powerkit' ),
			),
		),
	) );

endif;

/**
 * Pinterest Profile Shortcode
 *
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_pinterest_profile_shortcode( $atts, $content = '' ) {

	$params = powerkit_shortcode_atts( shortcode_atts( array(
		'href' => '',
	), $atts ) );

	ob_start();

	if ( $params['href'] ) {
	?>
		<div class="pinterest-profile-wrapper">
			<a data-pin-do="embedUser" data-pin-board="100%" href="<?php echo esc_url( $params['href'] ); ?>"></a>
		</div>
	<?php
	} else {
		powerkit_alert_warning( esc_html__( 'The "Pinterest Profile URL" field is required!', 'powerkit' ) );
	}

	return ob_get_clean();
}
add_shortcode( 'powerkit_pinterest_profile', 'powerkit_pinterest_profile_shortcode' );

/**
 * Map Pinterest Profile Shortcode into the Basic Shortcodes Plugin
 */
if ( function_exists( 'powerkit_basic_shortcodes_register' ) ) :

	powerkit_basic_shortcodes_register( array(
		'name'         => 'pinterest_profile',
		'title'        => esc_html__( 'Pinterest Profile', 'powerkit' ),
		'priority'     => 150,
		'base'         => 'powerkit_pinterest_profile',
		'autoregister' => false,
		'fields'       => array(
			array(
				'type'  => 'input',
				'name'  => 'href',
				'label' => esc_html__( 'Pinterest profile URL', 'powerkit' ),
			),
		),
	) );

endif;
