<?php
/**
 * Contributors block template
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
	'title'              => '',
	'filter_ids'         => implode( ',', (array) $attributes['contributors'] ),
	'avatar'             => $attributes['showAvatar'],
	'social_accounts'    => $attributes['showSocialAccounts'],
	'bio'                => $attributes['showBio'],
	'recent_posts'       => $attributes['showRecentPosts'],
	'recent_posts_count' => isset( $attributes['countRecentPosts'] ) ? $attributes['countRecentPosts'] : 0,
);

echo '<div class="' . esc_attr( $attributes['className'] ) . '" ' . ( isset( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '' ) . '>';

// Title.
if ( $params['title'] ) {
	$params['title'] = '<h5 class="pk-contributors-title">' . $params['title'] . '<h5>';
}

$args = array(
	'before_title'  => '',
	'after_title'   => '',
	'before_widget' => '',
	'after_widget'  => '',
);

powerkit_contributors_get_html( $params, $args );

echo '</div>';
