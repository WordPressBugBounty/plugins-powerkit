<?php
/**
 * Shortcode Tabs config
 *
 * @package    Powerkit
 * @subpackage Templates
 */

/**
 * Tabs
 */
powerkit_basic_shortcodes_register( array(
	'name'         => 'tabs',
	'title'        => esc_html__( 'Tabs', 'powerkit' ),
	'priority'     => 40,
	'base'         => 'powerkit_tabs',
	'autoregister' => true,
	'fields'       => array(
		array(
			'type'  => 'section',
			'label' => esc_html__( 'Options', 'powerkit' ),
		),
		array(
			'type'    => 'radio',
			'name'    => 'type',
			'label'   => esc_html__( 'Type', 'powerkit' ),
			'style'   => 'horizontal',
			'default' => 'tabs',
			'options' => array(
				'tabs'  => esc_html__( 'Tabs', 'powerkit' ),
				'pills' => esc_html__( 'Pills', 'powerkit' ),
			),
		),
		array(
			'type'    => 'radio',
			'name'    => 'nav',
			'label'   => esc_html__( 'Navigation type', 'powerkit' ),
			'style'   => 'horizontal',
			'default' => 'horizontal',
			'options' => array(
				'horizontal' => esc_html__( 'Horizontal', 'powerkit' ),
				'vertical'   => esc_html__( 'Vertical', 'powerkit' ),
			),
		),
		array(
			'type'  => 'section',
			'label' => esc_html__( 'Content', 'powerkit' ),
		),
		array(
			'type'         => 'repeater',
			'base'         => 'powerkit_tab',
			'autoregister' => true,
			'label'        => esc_html__( 'Tabs', 'powerkit' ),
			'fields'       => array(
				array(
					'type'    => 'input',
					'name'    => 'title',
					'label'   => esc_html__( 'Title', 'powerkit' ),
					'default' => '',
					'attrs'   => array(
						'class' => 'widefat',
					),
				),
				array(
					'type'    => 'content',
					'name'    => 'content',
					'label'   => esc_html__( 'Content', 'powerkit' ),
					'default' => '',
					'attrs'   => array(
						'class' => 'widefat',
						'rows'  => 6,
					),
				),
			),
		),
	),
) );


/**
 * Tabs Wrap Shortcode
 *
 * @param array  $output    Shortcode HTML.
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_basic_shortcodes_tabs( $output, $atts, $content ) {
	global $powerkit_basic_shortcodes_tabs;
	if ( ! is_array( $powerkit_basic_shortcodes_tabs ) ) {
		$powerkit_basic_shortcodes_tabs = array();
	}

	// Output.
	$nav_items  = '';
	$pane_items = '';
	$num        = 1;

	foreach ( $powerkit_basic_shortcodes_tabs as $tab ) {
		$data_toggle = ( 'tabs' === $atts['type'] ) ? 'tab' : 'pill';
		$item_id     = uniqid();
		$content_id  = $data_toggle . '-' . $item_id;

		$nav_items .= sprintf(
			'<li class="pk-nav-item"><a class="pk-nav-link%s pk-font-heading" data-toggle="%s" href="#%s">%s</a></li>',
			( 1 === $num ) ? ' pk-active' : '',
			$data_toggle,
			$content_id,
			wp_kses( $tab['title'], 'post' )
		);

		$pane_items .= sprintf(
			'<div id="%s" class="pk-tab-pane pk-fade%s" role="tabpanel">%s</div>',
			$content_id,
			( 1 === $num ) ? ' pk-show pk-active' : '',
			wp_kses( $tab['content'], 'post' )
		);

		$num++;
	}

	// Reset Tabs.
	$powerkit_basic_shortcodes_tabs = array();

	// Tabs Output.
	$output  = '';
	$output .= '<div class="pk-tabs pk-tabs-' . $atts['nav'] . '">';
	$output .= '<div class="pk-tabs-container">';
	$output .= '<div class="pk-tabs-navigation"><ul class="pk-nav pk-nav-' . $atts['type'] . '" role="tablist">' . $nav_items . '</ul></div>';
	$output .= '<div class="pk-tabs-content"><div class="pk-tab-content">' . $pane_items . '</div></div>';
	$output .= '</div>';
	$output .= '</div>';

	return $output;
}
add_filter( 'powerkit_tabs_shortcode', 'powerkit_basic_shortcodes_tabs', 10, 3 );


/**
 * Tab Item Shortcode
 *
 * @param array  $output    Shortcode HTML.
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_basic_shortcodes_tab( $output, $atts, $content ) {
	global $powerkit_basic_shortcodes_tabs;
	if ( ! is_array( $powerkit_basic_shortcodes_tabs ) ) {
		$powerkit_basic_shortcodes_tabs = array();
	}

	$powerkit_basic_shortcodes_tabs[] = array(
		'title'   => wp_kses( $atts['title'], 'post' ),
		'content' => wp_kses( $content, 'post' ),
	);

	return false;
}
add_filter( 'powerkit_tab_shortcode', 'powerkit_basic_shortcodes_tab', 10, 3 );
