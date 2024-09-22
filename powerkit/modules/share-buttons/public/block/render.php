<?php
/**
 * Share Buttons block template
 *
 * @var        $attributes - block attributes
 * @var        $options - layout options
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    PowerKit
 * @subpackage PowerKit/templates
 */

$params = array(
	'title'          => esc_html__( 'Share Buttons', 'powerkit' ),
	'accounts'       => $attributes['accounts'] ? : array(),
	'total'          => $attributes['showTotal'],
	'icons'          => $attributes['showIcons'],
	'labels'         => $attributes['showLabels'],
	'counts'         => $attributes['showCounts'],
	'titles'         => $attributes['showTitles'],
	'title_location' => 'inside',
	'label_location' => 'inside',
	'count_location' => 'inside',
	'mode'           => apply_filters( 'powerkit_share_buttons_block_counter_mode', 'mixed' ),
	'layout'         => $attributes['layout'] ? : 'default',
	'scheme'         => 'gutenberg-block',
);

echo '<div class="' . esc_attr( $attributes['className'] ) . '" ' . ( isset( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '' ) . '>';

powerkit_share_buttons( $params['accounts'], $params['total'], $params['icons'], $params['titles'], $params['labels'], $params['counts'], $params['title_location'], $params['label_location'], $params['count_location'], $params['mode'], $params['layout'], $params['scheme'], '' );

echo '</div>';
