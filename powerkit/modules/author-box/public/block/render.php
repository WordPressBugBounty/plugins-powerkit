<?php
/**
 * Author block template
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
	'title'                => '',
	'author'               => $attributes['author'],
	'bg_image_id'          => isset( $attributes['bgImage']['id'] ) ? $attributes['bgImage']['id'] : 0,
	'avatar'               => $attributes['showAvatar'],
	'description'          => $attributes['showDescription'],
	'description_override' => $attributes['overrideDescription'],
	'description_length'   => isset( $attributes['descriptionLength'] ) ? $attributes['descriptionLength'] : 100,
	'social_accounts'      => $attributes['showSocialAccounts'],
	'archive_btn'          => $attributes['showArchiveBtn'],
	'template'             => 'default',
	'is_block'             => true,
	'block_attrs'          => $attributes,
);

echo '<div class="' . esc_attr( $attributes['className'] ) . '" ' . ( isset( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '' ) . '>';

// Title.
if ( $params['title'] ) {
	$params['title'] = '<h5 class="pk-author-title">' . $params['title'] . '<h5>';
}

$authors = array();

// Get authors.
if ( 'current' === $params['author'] ) {
	$params['posts_only'] = true;

	$coauthors = array();

	if ( function_exists( 'get_coauthors' ) ) {
		$coauthors = get_coauthors();
	}

	if ( $coauthors ) {
		// Get co authors.
		foreach ( $coauthors as $author ) {
			$authors[] = $author->ID;
		}
	} else {
		// Get the default WP author.
		$authors[] = get_the_author_meta( 'ID' );
	}
} else {

	if ( get_the_author_meta( 'display_name', $params['author'] ) ) {

		$authors[] = $params['author'];

	} elseif ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
		?>
		<p class="pk-alert pk-alert-warning" role="alert">
			<?php esc_html_e( 'Author not found.', 'powerkit' ); ?>
		</p>
		<?php
	}
}

if ( ! empty( $authors ) ) {
	foreach ( $authors as $author ) {
		if ( ! @ is_author( $author ) ) {
			$args = array(
				'before_title'  => '',
				'after_title'   => '',
				'before_widget' => '',
				'after_widget'  => '',
			);
			powerkit_widget_author_template_handler( $params['template'], $author, $args, $params, array() );
		}
	}
}

echo '</div>';
