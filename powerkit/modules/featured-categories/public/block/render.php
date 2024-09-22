<?php
/**
 * Featured Categories block template
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
	'layout'     => $attributes['layout'],
	'filter_ids' => $attributes['filter_ids'],
	'orderby'    => $attributes['orderby'],
	'order'      => $attributes['order'],
	'maximum'    => $attributes['maximum'],
	'number'     => $attributes['number'],
);

echo '<div class="' . esc_attr( $attributes['className'] ) . '" ' . ( isset( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '' ) . '>';

// Convert filter ids.
if ( $params['filter_ids'] ) {

	$params['filter_ids'] = wp_parse_list( $params['filter_ids'] );

	foreach ( $params['filter_ids'] as $key => $slug ) {
		$term = get_term_by( 'slug', $slug, apply_filters( 'powerkit_featured_categories_filter_taxonomy', 'category', $attributes, $params ) );

		if ( isset( $term->term_id ) ) {
			$params['filter_ids'][ $key ] = $term->term_id;
		}
	}
}

powerkit_featured_categories_output( $params );

echo '</div>';
