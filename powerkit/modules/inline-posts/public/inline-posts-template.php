<?php
/**
 * Posts Template
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    PowerKit
 * @subpackage PowerKit/templates
 */

/**
 * Default Template
 *
 * @param array $posts    Array of posts.
 * @param array $settings Array of settings.
 */
function powerkit_inline_posts_default_template( $posts, $settings ) {
	?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="pk-post-outer">

				<?php if ( has_post_thumbnail() ) { ?>
				<div class="pk-post-inner">
					<div class="entry-thumbnail">
						<div class="pk-overlay pk-overlay-ratio pk-ratio-landscape">
							<div class="pk-overlay-background">
								<a href="<?php the_permalink(); ?>" class="pk-overlay-link">
									<?php the_post_thumbnail( $settings['image_size'] ); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>

				<div class="pk-post-inner">
					<header>
						<?php
						if ( function_exists( 'csco_get_post_meta' ) ) {
							csco_get_post_meta( array( 'category' ), false, true, array( 'category' ) );
						}

						// Post Title.
						switch ( $settings['template'] ) {
							case 'list':
								$tag = 'h3';
								break;
							case 'grid-2':
								$tag = 'h3';
								break;
							default:
								$tag = 'h5';
								break;
						}

						$tag = apply_filters( 'powerkit_widget_inline_posts_title_tag', $tag, $settings );

						the_title( '<' . $tag . ' class="pk-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></' . $tag . '>' );

						// Post Meta.
						if ( function_exists( 'csco_get_post_meta' ) ) {
							csco_get_post_meta( array( 'author', 'date' ), false, true, array( 'author', 'date' ) );
						} else {
							?>
							<div class="pk-post-meta">
								<?php echo powerkit_get_meta_author(); // XSS. ?>
								<span class="sep">·</span>
								<?php echo powerkit_get_meta_date(); // XSS. ?>
							</div>
							<?php
						}
						?>
					</header><!-- .entry-header -->
				</div><!-- .post-inner -->

			</div><!-- .post-outer -->
		</article>
	<?php
}
