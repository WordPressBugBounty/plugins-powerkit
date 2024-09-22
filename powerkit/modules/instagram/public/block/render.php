<?php
/**
 * Instagram block template
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
	'header'      => $attributes['showHeader'],
	'button'      => $attributes['showFollowButton'],
	'number'      => $attributes['number'],
	'columns'     => $attributes['columns'],
	'size'        => $attributes['size'],
	'target'      => $attributes['target'],
	'template'    => 'default',
	'is_block'    => true,
	'block_attrs' => $attributes,
);

echo '<div class="' . esc_attr( $attributes['className'] ) . '" ' . ( isset( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '' ) . '>';

powerkit_instagram_get_recent( $params );

echo '</div>';
