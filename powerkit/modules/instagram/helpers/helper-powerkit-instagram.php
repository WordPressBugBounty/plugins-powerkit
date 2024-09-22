<?php
/**
 * Helpers Instagram
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Template handler
 *
 * @param string $name      Specific template.
 * @param array  $feed      Array of instagram feed.
 * @param array  $instagram Array of instagram items.
 * @param array  $params    Array of params.
 */
function powerkit_instagram_template_handler( $name, $feed, $instagram, $params ) {
	$templates = apply_filters( 'powerkit_instagram_templates', array() );

	$new = isset( $templates['default'] ) ? false : true;

	if ( $new && count( $templates ) > 0 ) {
		$first_item = array_shift( $templates );

		if ( function_exists( $first_item['func'] ) ) {
			call_user_func( $first_item['func'], $feed, $instagram, $params );
		} else {
			call_user_func( 'powerkit_instagram_default_template', $feed, $instagram, $params );
		}
	} elseif ( isset( $templates[ $name ] ) && function_exists( $templates[ $name ]['func'] ) ) {
		call_user_func( $templates[ $name ]['func'], $feed, $instagram, $params );
	} else {
		call_user_func( 'powerkit_instagram_default_template', $feed, $instagram, $params );
	}
}

/**
 * Get templates options
 *
 * @return array Items.
 */
function powerkit_instagram_get_templates_options() {
	$options = array();

	$templates = apply_filters( 'powerkit_instagram_templates', array() );

	if ( $templates ) {
		foreach ( $templates as $key => $item ) {
			if ( isset( $item['name'] ) ) {
				$options[ $key ] = $item['name'];
			}
		}
	}

	return $options;
}

/**
 * Get recent photos instagram
 *
 * @param array  $params     Recent options.
 * @param string $cache_name The cache name.
 */
function powerkit_instagram_get_recent( $params, $cache_name = null ) {

	$params = array_merge( array(
		'header'   => false,
		'button'   => false,
		'number'   => 4,
		'columns'  => 1,
		'size'     => 'small',
		'target'   => '_blank',
		'template' => 'default',
	), (array) $params );

	// Apply filters.
	$instagram_feed_manual = apply_filters( 'powerkit_instagram_feed', array(), array(), $params );

	if ( $instagram_feed_manual ) {

		$instagram_feed = $instagram_feed_manual;

		$instagram = array();

		$placeholder_image = apply_filters( 'powerkit_lazyload_instagram_output', false );

		$ins_feed_class = null;

		$ins_feed_class .= ' pk-instagram-template-' . $params['template'];
		$ins_feed_class .= ' pk-instagram-size-' . $params['size'];
		$ins_feed_class .= ' pk-instagram-columns-' . $params['columns'];

		if ( isset( $instagram_feed['images'] ) && $instagram_feed['images'] ) {
			?>
			<div class="pk-instagram-feed <?php echo esc_attr( $ins_feed_class ); ?>">
				<?php
				foreach ( $instagram_feed['images'] as $item ) {

					$item = apply_filters( 'powerkit_instagram_item_data', $item );

					$class = 'pk-instagram-image';

					$image_thumbnail = $item['sizes']['thumbnail'];
					$image_small     = $item['sizes']['small'];
					$image_large     = $item['sizes']['large'];

					// Image Resolution.
					if ( 'thumbnail' === $params['size'] ) {
						$user_image = $image_thumbnail;
					} elseif ( 'small' === $params['size'] ) {
						$user_image = $image_small;
					} else {
						$user_image = $image_large;
					}

					// Columns.
					if ( 3 === (int) $params['columns'] ) {
						$user_image = $image_thumbnail;
					} elseif ( 2 === (int) $params['columns'] ) {
						$user_image = $image_small;
					} elseif ( 1 === (int) $params['columns'] ) {
						$user_image = $image_small;
					}

					// Retina sizes.
					if ( 'auto' === $params['size'] ) {
						if ( 3 === (int) $params['columns'] ) {
							$width = 150;
						} elseif ( 2 === (int) $params['columns'] ) {
							$width = 320;
						} elseif ( 1 === (int) $params['columns'] ) {
							$width = 640;
						}
					} elseif ( 'small' === $params['size'] ) {
						$width = 320;
					} elseif ( 'thumbnail' === $params['size'] ) {
						$width = 150;
					} else {
						$width = 640;
					}

					// Placeholder image.
					if ( $placeholder_image ) {
						$class .= ' pk-lazyload';
					}

					// Instagram item.
					$instagram[] = array(
						'class'       => $class,
						'description' => $item['text'],
						'link'        => $item['link'],
						'user_link'   => $item['link'],
						'comments'    => $item['comment_count'],
						'likes'       => $item['liked_count'],
						'time'        => $item['timestamp'],
						'thumbnail'   => $image_thumbnail,
						'small'       => $image_small,
						'large'       => $image_large,
						'user_image'  => $placeholder_image ? $placeholder_image : $user_image,
						'sizes'       => $placeholder_image ? 'auto' : sprintf( '(max-width: %1$spx) 100vw, %1$spx', $width ),
						'srcset'      => sprintf( '%s 150w, %s 320w, %s 640w', $image_thumbnail, $image_small, $image_large ),
					);
				}

				ob_start();

				powerkit_instagram_template_handler( $params['template'], $instagram_feed, $instagram, $params );

				$template_html = ob_get_clean();

				// Placeholder adaptation.
				if ( $placeholder_image ) {
					$placeholder_containers = apply_filters( 'powerkit_instagram_placeholder_containers', array( 'pk-instagram-items' ) );

					foreach ( $placeholder_containers as $container ) {
						preg_match( '/<div class="' . $container . '">.*?<\/div>/msU', $template_html, $template_match );

						if ( $template_match ) {
							$template_items = array_shift( $template_match );

							$output_items = str_replace( 'src=', sprintf( 'data-pk-src="%s" src=', $user_image ), $template_items );
							$output_items = str_replace( 'srcset=', 'data-pk-srcset=', $output_items );
							$output_items = str_replace( 'sizes=', 'data-pk-sizes=', $output_items );

							$template_html = str_replace( $template_items, $output_items, $template_html );
						}
					}
				}

				// Template Output.
				call_user_func( 'printf', '%s', $template_html );
				?>
			</div>
			<?php
		} else {
			powerkit_alert_warning( sprintf( __( 'The list is empty. To display the feed, add elements on the <a href="%s" target="_blank">settings page</a>.', 'powerkit' ), admin_url( 'options-general.php?page=powerkit_connect&tab=instagram' ) ) );
		}
	} else {
		powerkit_alert_warning( sprintf( __( 'No data found, please fill in the fields on the <a href="%s" target="_blank">settings page</a>.', 'powerkit' ), admin_url( 'options-general.php?page=powerkit_connect&tab=instagram' ) ) );
	}
}

/**
 * Set manual instagram data.
 *
 * @param array $feed    The feed.
 * @param array $request The request.
 * @param array $params  The params.
 */
function powerkit_set_manual_instagram_data( $feed, $request, $params ) {

	$feed['name']      = 'Unknown';
	$feed['username']  = 'unknown';
	$feed['following'] = 0;
	$feed['followers'] = 0;
	$feed['avatar_1x'] = '';
	$feed['avatar_2x'] = '';

	if ( is_array( $feed ) && get_option( 'powerkit_connect_instagram_username' ) ) {
		$feed['username'] = get_option( 'powerkit_connect_instagram_username' );
	}

	if ( is_array( $feed ) && get_option( 'powerkit_connect_instagram_custom_name' ) ) {
		$feed['name'] = get_option( 'powerkit_connect_instagram_custom_name' );
	}

	if ( is_array( $feed ) && get_option( 'powerkit_connect_instagram_following' ) ) {
		$feed['following'] = (int) get_option( 'powerkit_connect_instagram_following' );
	}

	if ( is_array( $feed ) && get_option( 'powerkit_connect_instagram_custom_followers' ) ) {
		$feed['followers'] = (int) get_option( 'powerkit_connect_instagram_custom_followers' );
	}

	if ( is_array( $feed ) && get_option( 'powerkit_connect_instagram_custom_avatar' ) ) {
		$feed['avatar_1x'] = get_option( 'powerkit_connect_instagram_custom_avatar' );
	}

	if ( is_array( $feed ) && get_option( 'powerkit_connect_instagram_custom_avatar_2x' ) ) {
		$feed['avatar_2x'] = get_option( 'powerkit_connect_instagram_custom_avatar_2x' );
	} elseif ( is_array( $feed ) && get_option( 'powerkit_connect_instagram_custom_avatar' ) ) {
		$feed['avatar_2x'] = get_option( 'powerkit_connect_instagram_custom_avatar' );
	}

	$manual_feed = get_option( 'powerkit_connect_instagram_feed' );

	if ( is_array( $manual_feed ) && ! empty( $manual_feed ) ) {
		foreach ( $manual_feed as $key => $element ) {

			$image_thumbnail = $element['image_thumbnail'] ? $element['image_thumbnail'] : ( $element['image_small'] ? $element['image_small'] : $element['image_large'] );
			$image_small     = $element['image_small'] ? $element['image_small'] : ( $element['image_thumbnail'] ? $element['image_thumbnail'] : $element['image_large'] );
			$image_large     = $element['image_large'] ? $element['image_large'] : ( $element['image_small'] ? $element['image_small'] : $element['image_thumbnail'] );

			$feed['images'][ $key ]['sizes']['thumbnail'] = $image_thumbnail;
			$feed['images'][ $key ]['sizes']['small']     = $image_small;
			$feed['images'][ $key ]['sizes']['large']     = $image_large;
			$feed['images'][ $key ]['link']               = $element['link'];
			$feed['images'][ $key ]['text']               = $element['text'];
			$feed['images'][ $key ]['timestamp']          = $element['date'];
			$feed['images'][ $key ]['comment_count']      = (int) $element['comments'];
			$feed['images'][ $key ]['liked_count']        = (int) $element['likes'];
		}
	}

	return $feed;
}
add_filter( 'powerkit_instagram_feed', 'powerkit_set_manual_instagram_data', 0, 3 );
