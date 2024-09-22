<?php
/**
 * Helpers Contributors
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Get html of contributors block
 *
 * @param array  $params  Recent options.
 * @param string $args    Widget args.
 */
function powerkit_contributors_get_html( $params, $args ) {

	$params = array_merge(
		array(
			'title'              => '',
			'filter_ids'         => '',
			'avatar'             => true,
			'social_accounts'    => true,
			'bio'                => true,
			'recent_posts'       => false,
			'recent_posts_count' => 3,
		),
		$params
	);

	// Before Widget.
	echo $args['before_widget']; // XSS.

	// Title.
	if ( $params['title'] ) {
		echo $args['before_title'] . wp_kses( $params['title'], 'pk-title' ) . $args['after_title']; // XSS.
	}

	// Content.
	?>
		<div class="widget-body">
			<?php
			$authors = powerkit_get_users();

			$avatar_size = apply_filters( 'powerkit_widget_coauthors_avatar_size', 80 );

			if ( isset( $authors ) && ! empty( $authors ) ) {
				?>
				<div class="pk-widget-contributors">
					<?php
					foreach ( $authors as $author ) {

						// Filters ids.
						if ( $params['filter_ids'] ) {
							$filter_ids = explode( ',', str_replace( ' ', '', $params['filter_ids'] ) );

							if ( ! in_array( (string) $author->ID, $filter_ids, true ) ) {
								continue;
							}
						}

						?>
							<div class="pk-author-item">
								<?php if ( $params['avatar'] ) { ?>
									<div class="pk-author-avatar">
										<a href="<?php echo esc_url( get_author_posts_url( $author->ID ) ); ?>" rel="author">
											<?php echo get_avatar( $author->ID, $avatar_size ); ?>
										</a>
									</div>
								<?php } ?>
								<div class="pk-author-data">
									<?php $tag = apply_filters( 'powerkit_widget_contributors_author_name', 'h6' ); ?>

									<<?php echo esc_html( $tag ); ?> class="author-name">
										<a href="<?php echo esc_url( get_author_posts_url( $author->ID ) ); ?>" rel="author">
											<?php echo esc_html( get_the_author_meta( 'display_name', $author->ID ) ); ?>
										</a>
									</<?php echo esc_html( $tag ); ?>>

									<?php
									if ( $params['bio'] ) :
										$author_description = get_the_author_meta( 'description', $author->ID );
										?>
										<div class="author-description pk-color-secondary">
											<?php echo wp_kses_post( powerkit_str_truncate( $author_description, apply_filters( 'powerkit_widget_contributors_description_length', 100 ) ) ); ?>
										</div>
									<?php endif; ?>

									<?php
									if ( $params['social_accounts'] && powerkit_module_enabled( 'social_links' ) ) {
										powerkit_author_social_links( $author->ID, 'template-nav' );
									}
									?>

									<?php
									if ( $params['recent_posts'] && $params['recent_posts_count'] ) {
										$author_posts = get_posts(
											array(
												'author' => $author->ID,
												'posts_per_page' => $params['recent_posts_count'],
											)
										);

										if ( ! empty( $author_posts ) ) {
											?>
											<div class="pk-author-posts">
												<h6><?php echo sprintf( esc_html__( 'Latest from %s:', 'powerkit' ), get_the_author_meta( 'display_name', $author->ID ) ); ?></h6>
												<?php

												foreach ( $author_posts as $post ) {
													?>
													<div class="pk-author-posts-single">
														<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>"><?php echo esc_html( get_the_title( $post->ID ) ); ?></a>
													</div>
													<?php
												}

												?>
											</div>
											<?php
										}
									}
									?>
								</div>
							</div>
						<?php
					}
					?>
				</div>
				<?php
			}
			?>
		</div>
	<?php

	// After Widget.
	echo $args['after_widget']; // XSS.
}
