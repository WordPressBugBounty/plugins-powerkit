<?php
/**
 * Twitter Template
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
 * @param array $tweets List of tweets.
 * @param array $params Parameters.
 */
function powerkit_twitter_default_template( $tweets, $params ) {

	$template = isset( $params['template'] ) ? $params['template'] : 'default';
	?>

	<div class="pk-twitter-wrap pk-twitter-<?php echo esc_attr( $template ); ?>">

		<?php
		if ( is_array( $tweets ) && $tweets ) {
			if ( $params['header'] ) {
				?>
				<div class="pk-twitter-header">
					<div class="pk-twitter-container">
						<?php if ( $tweets['avatar_1x'] ) { ?>
							<a href="<?php echo esc_url( sprintf( 'https://twitter.com/%s/', $tweets['username'] ) ); ?>" class="pk-twitter-link" target="_blank">
								<?php
									$image_avatar = sprintf(
										'<img src="%s" srcset="%s" alt="avatar" class="pk-twitter-avatar">',
										esc_url( $tweets['avatar_1x'] ), esc_url( $tweets['avatar_1x'] ) . ' 1x, ' . esc_url( $tweets['avatar_2x'] ) . ' 2x'
									);

									echo wp_kses_post( apply_filters( 'powerkit_lazy_process_images', $image_avatar ) );
								?>
							</a>
						<?php } ?>

						<?php $tag = apply_filters( 'powerkit_twitter_name_tag', 'h6' ); ?>

						<div class="pk-twitter-info">
							<<?php echo esc_html( $tag ); ?> class="pk-twitter-name pk-title pk-font-heading">
								<a href="<?php echo esc_url( sprintf( 'https://twitter.com/%s/', $tweets['username'] ) ); ?>" target="_blank">
									<?php echo esc_html( $tweets['name'] ); ?>
								</a>
							</<?php echo esc_html( $tag ); ?>>

							<?php if ( $tweets['name'] !== $tweets['username'] ) { ?>
								<span class="pk-twitter-username pk-color-secondary">
									<a href="<?php echo esc_url( sprintf( 'https://twitter.com/%s/', $tweets['username'] ) ); ?>" target="_blank">
									@<?php echo wp_kses_post( $tweets['username'] ); ?>
									</a>
								</span>
							<?php } ?>
						</div>
					</div>

					<div class="pk-twitter-counters pk-color-secondary">
						<div class="counter following">
							<span class="number"><?php echo esc_html( powerkit_abridged_number( $tweets['following'], 0 ) ); ?></span> <?php esc_html_e( 'Following', 'powerkit' ); ?>
						</div>
						<div class="counter followers">
							<span class="number"><?php echo esc_html( powerkit_abridged_number( $tweets['followers'], 0 ) ); ?></span> <?php esc_html_e( 'Followers', 'powerkit' ); ?>
						</div>
					</div>
				</div>
				<?php
			}
			?>

			<div class="pk-tweets">
				<?php
				if ( isset( $tweets['items'] ) && $tweets['items'] ) {
					$counter = 1;
					foreach ( $tweets['items'] as $tweet ) {

						if ( $counter > $params['number'] ) {
							break;
						}

						$time = powerkit_relative_time( $tweet['date'] );
						$text = powerkit_twitter_convert_links( $tweet['text'] );
						?>
							<div class="pk-twitter-tweet">
								<div class="pk-twitter-content pk-color-secondary"><?php echo wp_kses_post( $text ); ?></div>
								<a href="https://twitter.com/<?php echo esc_attr( $tweets['username'] ); ?>/status/<?php echo esc_attr( $tweet['tweet_id'] ); ?>" class="pk-twitter-time pk-font-secondary timestamp" target="_blank"><?php echo esc_html( $time ); ?></a>

								<div class="pk-twitter-actions">
									<ul>
										<li>
											<a onClick="window.open('https://twitter.com/intent/tweet?in_reply_to=<?php echo esc_attr( $tweet['tweet_id'] ); ?>','Twitter','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" class="tweet-reply" href="https://twitter.com/intent/tweet?in_reply_to=<?php echo esc_attr( $tweet['tweet_id'] ); ?>">
												<i class="pk-icon pk-icon-reply"></i>
												<span class="pk-twitter-label pk-twitter-reply"><?php esc_html_e( 'Reply', 'powerkit' ); ?></span>
											</a>
										</li>
										<li>
											<a onClick="window.open('https://twitter.com/intent/retweet?tweet_id=<?php echo esc_attr( $tweet['tweet_id'] ); ?>','Twitter','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" class="tweet-retweet" href="https://twitter.com/intent/retweet?tweet_id=<?php echo esc_attr( $tweet['tweet_id'] ); ?>">
												<i class="pk-icon pk-icon-retweet"></i>
												<span class="pk-twitter-count"><?php echo wp_kses_post( $tweet['retweets'] ? $tweet['retweets'] : '' ); ?></span>
												<span class="pk-twitter-label pk-twitter-retweet"><?php esc_html_e( 'Retweet', 'powerkit' ); ?></span>
											</a>
										</li>
										<li>
											<a onClick="window.open('https://twitter.com/intent/favorite?tweet_id=<?php echo esc_attr( $tweet['tweet_id'] ); ?>','Twitter','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" class="tweet-favorite" href="https://twitter.com/intent/favorite?tweet_id=<?php echo esc_attr( $tweet['tweet_id'] ); ?>">
												<i class="pk-icon pk-icon-like"></i>
												<span class="pk-twitter-label pk-twitter-favorite"><?php esc_html_e( 'Favorite', 'powerkit' ); ?></span>
											</a>
										</li>
									</ul>
								</div>

							</div>
						<?php
						$counter++;
					}
				} else {
					powerkit_alert_warning( sprintf( __( 'The list is empty. To display the feed, add elements on the <a href="%s" target="_blank">settings page</a>.', 'powerkit' ), admin_url( 'options-general.php?page=powerkit_connect&tab=twitter' ) ) );
				}
				?>
			</div>

			<?php
			if ( $params['button'] ) {
				$href = sprintf( 'https://twitter.com/%s/', $tweets['username'] );
				$text = apply_filters( 'powerkit_twitter_follow', esc_html__( 'Follow', 'powerkit' ) );

				if ( isset( $params[ 'is_block' ] ) && isset( $params[ 'block_attrs' ] ) && $params[ 'is_block' ] ) {
					?>
					<div class="pk-twitter-footer">
						<?php powerkit_print_gutenberg_blocks_button( $text, $href, '_blank', 'button', $params[ 'block_attrs' ] ); ?>
					</div>
					<?php
				} else {
					?>
					<div class="pk-twitter-footer">
						<a class="pk-twitter-btn button" href="<?php echo esc_url( $href ); ?>" target="_blank">
							<span class="pk-twitter-follow"><?php echo wp_kses( $text, 'post' ); ?></span>
						</a>
					</div>
					<?php
				}
			}
		} else {
			powerkit_alert_warning( sprintf( __( 'No data found, please fill in the fields on the <a href="%s" target="_blank">settings page</a>.', 'powerkit' ), admin_url( 'options-general.php?page=powerkit_connect&tab=twitter' ) ) );
		}
		?>
	</div>
	<?php
}
