<?php
/**
 * Shortcode Facebook Fanpage
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    PowerKit
 * @subpackage PowerKit/shortcodes
 */

/**
 * Facebook Fanpage Shortcode
 *
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_facebook_fanpage_shortcode( $atts, $content = '' ) {

	$params = powerkit_shortcode_atts( shortcode_atts( array(
		'href'                  => '',
		'hide_cover'            => false,
		'show_facepile'         => false,
		'show_posts'            => false,
		'small_header'          => false,
		'adapt_container_width' => true,
	), $atts ) );

	$params['hide_cover']            = filter_var( $params['hide_cover'], FILTER_VALIDATE_BOOLEAN );
	$params['show_facepile']         = filter_var( $params['show_facepile'], FILTER_VALIDATE_BOOLEAN );
	$params['show_posts']            = filter_var( $params['show_posts'], FILTER_VALIDATE_BOOLEAN );
	$params['small_header']          = filter_var( $params['small_header'], FILTER_VALIDATE_BOOLEAN );
	$params['adapt_container_width'] = filter_var( $params['adapt_container_width'], FILTER_VALIDATE_BOOLEAN );

	ob_start();

	if ( $params['href'] ) {
	?>
		<div class="fb-page-wrapper">
			<div class="fb-page"
				 data-href="<?php echo esc_url( $params['href'] ); ?>"
				 data-hide-cover="<?php echo esc_attr( $params['hide_cover'] ? 'true' : 'false' ); ?>"
				 data-show-facepile="<?php echo esc_attr( $params['show_facepile'] ? 'true' : 'false' ); ?>"
				 data-show-posts="<?php echo esc_attr( $params['show_posts'] ? 'true' : 'false' ); ?>"
				 data-small-header="<?php echo esc_attr( $params['small_header'] ? 'true' : 'false' ); ?>"
				 data-adapt-container-width="<?php echo esc_attr( $params['adapt_container_width'] ? 'true' : 'false' ); ?>">
			</div>
		</div>
	<?php
	} else {
		powerkit_alert_warning( esc_html__( 'The "Facebook Fanpage URL" field is required!', 'powerkit' ) );
	}

	return ob_get_clean();
}
add_shortcode( 'powerkit_facebook_fanpage', 'powerkit_facebook_fanpage_shortcode' );

/**
 * Map Facebook Fanpage Shortcode into the Basic Shortcodes Plugin
 */
if ( function_exists( 'powerkit_basic_shortcodes_register' ) ) :

	powerkit_basic_shortcodes_register( array(
		'name'         => 'facebook_fanpage',
		'title'        => esc_html__( 'Facebook Fanpage', 'powerkit' ),
		'priority'     => 150,
		'base'         => 'powerkit_facebook_fanpage',
		'autoregister' => false,
		'fields'       => array(
			array(
				'type'  => 'input',
				'name'  => 'href',
				'label' => esc_html__( 'Facebook fanpage URL', 'powerkit' ),
			),
			array(
				'type'    => 'checkbox',
				'name'    => 'hide_cover',
				'label'   => esc_html__( 'Hide cover', 'powerkit' ),
				'default' => false,
			),
			array(
				'type'    => 'checkbox',
				'name'    => 'show_facepile',
				'label'   => esc_html__( 'Show facepile', 'powerkit' ),
				'default' => false,
			),
			array(
				'type'    => 'checkbox',
				'name'    => 'show_posts',
				'label'   => esc_html__( 'Show posts', 'powerkit' ),
				'default' => false,
			),
			array(
				'type'    => 'checkbox',
				'name'    => 'small_header',
				'label'   => esc_html__( 'Small header', 'powerkit' ),
				'default' => false,
			),
		),
	) );

endif;
