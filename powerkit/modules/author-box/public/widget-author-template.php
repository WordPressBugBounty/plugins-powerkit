<?php
/**
 * Widget Author Template
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
 * @param int   $author   The author.
 * @param array $args     Array of args.
 * @param array $params   Array of params.
 * @param array $instance Widget instance.
 */
function powerkit_widget_author_default_template( $author, $args, $params, $instance ) {
	// Before Widget.
	echo $args['before_widget']; // XSS.

	$avatar_size = apply_filters( 'powerkit_widget_author_avatar_size', 80 );
	?>
		<div class="widget-body">
			<div class="pk-widget-author<?php echo esc_attr( $params['bg_image_id'] ? ' pk-widget-author-with-bg' : '' ); ?>">
				<?php if ( $params['bg_image_id'] ) { ?>
					<div class="pk-widget-author-bg">
						<?php echo wp_get_attachment_image( $params['bg_image_id'], apply_filters( 'powerkit_widget_author_image_size', 'large' ) ); ?>
					</div>
				<?php } ?>

				<div class="pk-widget-author-container<?php echo esc_attr( $params['bg_image_id'] ? ' pk-bg-overlay' : '' ); ?>">
					<?php
					// Title.
					if ( $params['title'] ) {
						$params['widget_title'] = $args['before_title'] . apply_filters( 'widget_title', $params['title'], $instance, 0 ) . $args['after_title']; // XSS.

						echo wp_kses_post( apply_filters( 'powerkit_widget_author_title', $params['widget_title'], $params['title'] ) );
					}
					?>

					<?php $tag = apply_filters( 'powerkit_widget_author_title_tag', 'h5' ); ?>

					<<?php echo esc_html( $tag ); ?> class="pk-author-title">
						<a href="<?php echo esc_url( get_author_posts_url( $author ) ); ?>" rel="author">
							<?php echo esc_html( get_the_author_meta( 'display_name', $author ) ); ?>
						</a>
					</<?php echo esc_html( $tag ); ?>>

					<?php if ( $params['avatar'] ) { ?>
						<div class="pk-author-avatar">
							<a href="<?php echo esc_url( get_author_posts_url( $author ) ); ?>" rel="author">
								<?php echo get_avatar( $author, $avatar_size ); ?>
							</a>
						</div>
					<?php } ?>

					<div class="pk-author-data">
						<?php
						$description = $params['description_override'] ? $params['description_override'] : get_the_author_meta( 'description', $author );

						if ( $params['description'] && $description ) {
							?>
							<div class="author-description pk-color-secondary">
								<?php echo wp_kses_post( powerkit_str_truncate( $description, $params['description_length'] ) ); ?>
							</div>
							<?php
						}
						if ( $params['social_accounts'] && powerkit_module_enabled( 'social_links' ) ) {
							powerkit_author_social_links( $author );
						}
						?>

						<?php
						if ( $params['archive_btn'] ) {
							$href = get_author_posts_url( $author );
							$text = apply_filters( 'powerkit_widget_author_button', esc_html__( 'View Posts', 'powerkit' ) );

							if ( isset( $params['is_block'] ) && isset( $params['block_attrs'] ) && $params['is_block'] ) {
								?>
								<div class="pk-author-footer">
									<?php powerkit_print_gutenberg_blocks_button( $text, $href, '', 'button', $params['block_attrs'] ); ?>
								</div>
								<?php
							} else {
								?>
								<a href="<?php echo esc_url( $href ); ?>" class="pk-author-button button">
									<?php echo wp_kses( $text, 'post' ); ?>
								</a>
								<?php
							}
						}
						?>
					</div>
				</div>
			</div>
		</div>
	<?php
	// After Widget.
	echo $args['after_widget']; // XSS.
}
