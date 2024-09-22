<?php
/**
 * Helpers
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Get locations of featured categories
 */
function powerkit_featured_categories_locations() {

	$locations = apply_filters( 'powerkit_featured_categories_locations', array(
		'tiles'         => array(
			'name'     => esc_html__( 'Tiles', 'powerkit' ),
			'icon'     => '<svg width="52" height="44" xmlns="http://www.w3.org/2000/svg"><g transform="translate(1 1)" stroke="#2D2D2D" fill="none" fill-rule="evenodd"><rect stroke-width="1.5" width="50" height="42" rx="3"/><g transform="translate(7 9)"><rect stroke-width="1.5" width="36" height="24" rx="1"/><path d="M11 10.5h14m-11 3h9" stroke-linecap="round" stroke-linejoin="round"/></g></g></svg>',
			'location' => array(),
			'template' => dirname( __FILE__ ) . '/block/tiles.php',
			'sections' => array(),
			'fields'   => array(),
		),
		'vertical-list' => array(
			'name'     => esc_html__( 'Vertical List', 'powerkit' ),
			'icon'     => '<svg width="52" height="44" xmlns="http://www.w3.org/2000/svg"><g transform="translate(1 1)" fill="none" fill-rule="evenodd"><rect stroke="#2D2D2D" stroke-width="1.5" width="50" height="42" rx="3"/><g transform="translate(5 5)"><rect stroke="#2D2D2D" stroke-width="1.5" width="40" height="8" rx="1"/><path fill="#2D2D2D" d="M34 2h4v4h-4z"/></g><g transform="translate(5 17)"><rect stroke="#2D2D2D" stroke-width="1.5" width="40" height="8" rx="1"/><path fill="#2D2D2D" d="M34 2h4v4h-4z"/></g><g transform="translate(5 29)"><rect stroke="#2D2D2D" stroke-width="1.5" width="40" height="8" rx="1"/><path fill="#2D2D2D" d="M34 2h4v4h-4z"/></g></g></svg>',
			'location' => array(),
			'template' => dirname( __FILE__ ) . '/block/vertical-list.php',
			'sections' => array(),
			'fields'   => array(),
		),
	) );

	return $locations;
}

/**
 * Output featured categories
 *
 * @param array $params Recent options.
 */
function powerkit_featured_categories_output( $params ) {

	$params = array_merge( array(
		'title'      => '',
		'layout'     => 'tiles',
		'filter_ids' => '',
		'orderby'    => 'name',
		'order'      => 'ASC',
		'maximum'    => 0,
		'number'     => true,
	), $params );

	$params = apply_filters( 'powerkit_featured_categories_params_output', $params );

	// Set class.
	$class = 'pk-featured-categories';

	// Add class of layout.
	$class .= ' pk-featured-categories-' . $params['layout'];

	// Content.
	?>
	<div class="<?php echo esc_attr( $class ); ?>">
		<?php
		$params['maximum'] = intval( $params['maximum'] );

		// Get terms.
		$categories = get_terms( array(
			'include'    => $params['filter_ids'],
			'orderby'    => $params['orderby'],
			'order'      => $params['order'],
			'number'     => $params['maximum'] > 0 ? $params['maximum'] : '',
			'taxonomy'   => 'category',
			'hide_empty' => true,
		) );

		foreach ( $categories as $category ) {
			$featured_image = get_term_meta( $category->term_id, 'powerkit_featured_image', true );
			?>
			<div class="pk-featured-item">
				<?php if ( $featured_image ) { ?>
					<div class="pk-featured-image">
						<?php echo wp_get_attachment_image( $featured_image, 'large' ); ?>
					</div>
				<?php } ?>

				<div class="pk-featured-content">
					<div class="pk-featured-inner">
						<div class="pk-featured-name">
							<?php echo esc_html( $category->name ); ?>
						</div>

						<?php if ( $params['number'] ) { ?>
							<div class="pk-featured-count">
								<span class="pk-featured-number"><?php echo esc_html( $category->count ); ?></span>
								<span class="pk-featured-label"><?php esc_html_e( ' Posts', 'powerkit' ); ?></span>
							</div>
						<?php } ?>
					</div>
				</div>

				<a class="pk-featured-link" href="<?php echo esc_url( get_term_link( $category->term_id ) ); ?>">
					<span><?php esc_html_e( 'View Posts', 'powerkit' ); ?></span>
				</a>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}
