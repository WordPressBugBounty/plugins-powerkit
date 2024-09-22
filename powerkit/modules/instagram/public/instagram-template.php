<?php
/**
 * Instagram Template
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
 * @param array $feed      The instagram feed.
 * @param array $instagram The instagram items.
 * @param array $params    The user parameters.
 */
function powerkit_instagram_default_template( $feed, $instagram, $params ) {

	if ( $params['header'] ) {
		?>
		<div class="pk-instagram-header">
			<div class="pk-instagram-container">
				<?php if ( $feed['avatar_1x'] ) { ?>
					<a href="<?php echo esc_url( sprintf( 'https://www.instagram.com/%s/', $feed['username'] ) ); ?>" class="pk-avatar-link" target="<?php echo esc_attr( $params['target'] ); ?>">
						<?php
							$image_avatar = sprintf(
								'<img src="%s" alt="avatar" class="pk-instagram-avatar">', esc_url( $feed['avatar_1x'] )
							);

							echo wp_kses_post( apply_filters( 'powerkit_lazy_process_images', $image_avatar ) );
						?>
					</a>
				<?php } ?>

				<?php $tag = apply_filters( 'powerkit_instagram_username_tag', 'h6' ); ?>

				<div class="pk-instagram-info">
					<?php if ( $feed['name'] !== $feed['username'] ) { ?>
						<<?php echo esc_html( $tag ); ?>  class="pk-instagram-username pk-title pk-font-heading">
							<a href="<?php echo esc_url( sprintf( 'https://www.instagram.com/%s/', $feed['username'] ) ); ?>" target="<?php echo esc_attr( $params['target'] ); ?>">
								<?php echo wp_kses_post( $feed['username'] ); ?>
							</a>
						</<?php echo esc_html( $tag ); ?>>
					<?php } ?>

					<?php if ( $feed['name'] ) { ?>
						<span class="pk-instagram-name pk-color-secondary">
							<a href="<?php echo esc_url( sprintf( 'https://www.instagram.com/%s/', $feed['username'] ) ); ?>" target="<?php echo esc_attr( $params['target'] ); ?>">
								<?php echo esc_html( $feed['name'] ); ?>
							</a>
						</span>
					<?php } ?>
				</div>
			</div>

			<?php if ( is_int( $feed['following'] ) || is_int( $feed['followers'] ) ) { ?>
				<div class="pk-instagram-counters pk-color-secondary">
					<?php if ( is_int( $feed['following'] ) ) { ?>
						<div class="counter following">
							<span class="number"><?php echo esc_html( powerkit_abridged_number( $feed['following'], 0 ) ); ?></span> <?php esc_html_e( 'Following', 'powerkit' ); ?>
						</div>
					<?php } ?>

					<?php if ( is_int( $feed['followers'] ) ) { ?>
						<div class="counter followers">
							<span class="number"><?php echo esc_html( powerkit_abridged_number( $feed['followers'], 0 ) ); ?></span> <?php esc_html_e( 'Followers', 'powerkit' ); ?>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	<?php } ?>

	<?php if ( is_array( $instagram ) && $instagram ) { ?>
		<div class="pk-instagram-items">
			<?php
			$counter = 1;
			foreach ( $instagram as $item ) {
				if ( $counter > $params['number'] ) {
					break;
				}
				?>
				<div class="pk-instagram-item">
					<a class="pk-instagram-link" href="<?php echo esc_url( $item['user_link'] ); ?>" target="<?php echo esc_attr( $params['target'] ); ?>">
						<img src="<?php echo esc_attr( $item['user_image'] ); ?>" class="<?php echo esc_attr( $item['class'] ); ?>" alt="<?php echo esc_html( $item['description'] ); ?>" srcset="<?php echo esc_attr( $item['srcset'] ); ?>" sizes="<?php echo esc_attr( $item['sizes'] ); ?>">

						<?php if ( is_int( $item['likes'] ) || is_int( $item['comments'] ) ) { ?>
							<span class="pk-instagram-data">
								<span class="pk-instagram-meta">
									<?php if ( is_int( $item['likes'] ) ) { ?>
										<span class="pk-meta pk-meta-likes"><i class="pk-icon pk-icon-like"></i> <?php echo esc_attr( powerkit_abridged_number( $item['likes'], 0 ) ); ?></span>
									<?php } ?>
									<?php if ( is_int( $item['comments'] ) ) { ?>
										<span class="pk-meta pk-meta-comments"><i class="pk-icon pk-icon-comment"></i> <?php echo esc_attr( powerkit_abridged_number( $item['comments'], 0 ) ); ?></span>
									<?php } ?>
								</span>
							</span>
						<?php } ?>
					</a>
				</div>
				<?php
				$counter++;
			}
			?>
		</div>
	<?php } else { ?>
		<?php powerkit_alert_warning( sprintf( __( 'The list is empty. To display the feed, add elements on the <a href="%s" target="_blank">settings page</a>.', 'powerkit' ), admin_url( 'options-general.php?page=powerkit_connect&tab=instagram' ) ) ); ?>
	<?php } ?>

	<?php
	if ( $params['button'] ) {
		$href = sprintf( 'https://www.instagram.com/%s/', $feed['username'] );
		$text = apply_filters( 'powerkit_instagram_follow', esc_html__( 'Follow', 'powerkit' ) );

		if ( isset( $params['is_block'] ) && isset( $params['block_attrs'] ) && $params['is_block'] ) {
			?>
			<div class="pk-instagram-footer">
				<?php powerkit_print_gutenberg_blocks_button( $text, $href, $params['target'], 'button', $params['block_attrs'] ); ?>
			</div>
			<?php
		} else {
			?>
			<div class="pk-instagram-footer">
				<a class="pk-instagram-btn button" href="<?php echo esc_url( $href ); ?>" target="<?php echo esc_attr( $params['target'] ); ?>">
					<span class="pk-instagram-follow"><?php echo wp_kses( $text, 'post' ); ?></span>
				</a>
			</div>
			<?php
		}
	}
}
