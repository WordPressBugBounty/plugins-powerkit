<?php
/**
 * Helpers Share Buttons
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Get cache time
 *
 * @since 1.0.0
 * @param string|int $post_id   Post ID.
 */
function powerkit_share_buttons_get_cache_time( $post_id = false ) {

	// Options cache time.
	if ( 'options' === $post_id ) {
		$seconds = apply_filters( 'powerkit_share_buttons_options_cache_time', 3600 );

		return $seconds;
	}

	// Post Id.
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	// Post age in seconds.
	$post_age = floor( intval( date( 'U' ) ) - intval( get_post_time( 'U', true, $post_id ) ) );

	$two_months_period  = apply_filters( 'powerkit_share_buttons_two_months', 5184000 );
	$three_weeks_period = apply_filters( 'powerkit_share_buttons_three_weeks', 1814400 );

	if ( isset( $post_age ) && $post_age > $two_months_period ) {

		// Post older than 60 days - expire cache after 12 hours.
		$seconds = apply_filters( 'powerkit_share_buttons_refresh_60_days', 43200 );

	} elseif ( isset( $post_age ) && $post_age > $three_weeks_period ) {

		// Post older than 21 days - expire cache after 4 hours.
		$seconds = apply_filters( 'powerkit_share_buttons_refresh_21_days', 14400 );

	} else {

		// Expire cache after one hour.
		$seconds = apply_filters( 'powerkit_share_buttons_refresh_1_hour', 3600 );
	}

	return $seconds;
}

/**
 * Get Current Post ID
 *
 * @since 1.0.0
 * @param string $url  Custom URL.
 */
function powerkit_share_buttons_get_current_post_id( $url ) {

	// Custom URL.
	if ( $url ) {
		$postid = url_to_postid( $url );

		if ( $postid ) {
			return $postid;
		}

		return 'options';
	}

	// Auto.
	global $post;

	if ( isset( $post->ID ) ) {
		return $post->ID;

	} else {
		return 'options';
	}
}

/**
 * Get Share URL
 *
 * @param string|int $post_id   Post ID.
 * @param string     $url       Custom URL.
 */
function powerkit_share_buttons_get_url( $post_id = false, $url = null ) {

	// Custom URL.
	if ( $url ) {
		return $url;
	}

	// Auto URL.
	if ( ! $post_id ) {
		$post_id = powerkit_share_buttons_get_current_post_id( $url );
	}

	if ( 'options' === $post_id ) {
		return preg_replace( '/\?.*/', '', home_url( add_query_arg( null, null ) ) );

	} else {
		return get_permalink( intval( $post_id ) );
	}
}

/**
 * Create a new Bitly short URL
 *
 * This is the method used to interface with the Bitly API with regard to creating
 * new shortened URL's via their service.
 *
 * @param string $api_token The bitly api token.
 * @param string $url     The URL to be shortened.
 * @param string $title   The post title.
 */
function powerkit_share_buttons_make_bitly_url( $api_token, $url, $title = null ) {

	$data = wp_remote_post(
		'https://api-ssl.bitly.com/v4/bitlinks',
		array(
			'method'  => 'POST',
			'headers' => array(
				'Content-Type'  => 'application/json; charset=utf-8',
				'Authorization' => 'Bearer ' . $api_token,
			),
			'body'    => wp_json_encode(
				array(
					'title'    => $title,
					'long_url' => $url,
				)
			),
			'timeout' => 60,
		)
	);

	if ( is_wp_error( $data ) ) {
		return false;
	}

	// Retrieve data.
	$data = wp_remote_retrieve_body( $data );

	// Parse the JSON formated response into an array.
	$data = json_decode( $data, true );

	// If the shortening succeeded....
	if ( isset( $data['link'] ) ) :

		// Store the short URL to return to the plugin.
		$short_url = $data['link'];

		// If the shortening failed....
	else :
		// Return a status of false.
		$short_url = false;
	endif;

	return $short_url;
}

/**
 * Process URL
 *
 * @param string     $account Account Name.
 * @param string|int $post_id Post ID.
 * @param string     $url     Custom URL.
 */
function powerkit_share_buttons_process_url( $account, $post_id = false, $url = null ) {

	// Check post id.
	if ( ! (int) $post_id ) {
		return $url;
	}

	// Check url.
	if ( ! $url ) {
		$url = get_permalink( (int) $post_id );
	}

	$title = get_the_title( $post_id );

	// Bitly Link Shortening.
	$bitly_api_token = get_option( 'powerkit_share_buttons_bitly_api_token' );

	if ( $bitly_api_token ) {
		// These can not have bitly urls created.
		if ( 'pinterest' === $account ) {
			return $url;
		}

		$bitly_cache_name = sprintf( '_powerkit_bitly_url_%s', md5( $url ) );

		// Get bitly from DB.
		$bitly_url = get_post_meta( $post_id, $bitly_cache_name, true );

		// Get bitly url.
		if ( ! $bitly_url ) {
			$bitly_url = powerkit_share_buttons_make_bitly_url( $bitly_api_token, $url, $title );
			if ( $bitly_url ) {
				update_post_meta( $post_id, $bitly_cache_name, $bitly_url );
			}
		}

		// Return bitly url.
		if ( $bitly_url ) {
			$url = $bitly_url;
		}
	}

	return $url;
}

/**
 * Get Alternate Share URL
 *
 * @param string $url Custom URL.
 */
function powerkit_share_buttons_get_alt_url( $url = null ) {
	if ( false === strpos( $url, 'https' ) ) {
		return str_replace( 'http', 'https', $url );
	} else {
		return str_replace( 'https', 'http', $url );
	}
}

/**
 * Get Cached Counts
 *
 * @param string     $account     Account Name.
 * @param string|int $post_id     Post ID.
 * @param string     $url         Custom URL.
 * @param bool       $ignore_time Ignore Cache Time.
 * @param bool       $suffix      Unique suffix.
 */
function powerkit_share_buttons_get_cached_account( $account, $post_id, $url = null, $ignore_time = false, $suffix = false ) {

	// Add suffix.
	if ( $suffix ) {
		$account .= '_' . $suffix;
	}

	if ( 'options' === $post_id ) {

		// Get Url Shares.
		$share_url = powerkit_share_buttons_get_url( $post_id, $url );

		$shares = get_transient( substr( 'powerkit_share_buttons_count_' . $account . '_' . md5( $share_url ), 0, 170 ) );

		$shares = apply_filters( 'powerkit_share_buttons_count', $shares, $account, $post_id );
	} else {

		// Get Post Shares.
		$shares = false;

		if ( $ignore_time ) {
			$shares = get_post_meta( intval( $post_id ), 'powerkit_share_buttons_count_' . $account, true );

		} else {
			$share_transient = get_post_meta( intval( $post_id ), 'powerkit_share_buttons_transient_' . $account, true );

			if ( intval( date( 'U' ) ) < intval( $share_transient ) ) {
				$shares = get_post_meta( intval( $post_id ), 'powerkit_share_buttons_count_' . $account, true );
			}
		}

		$shares = apply_filters( 'powerkit_share_buttons_count', $shares, $account, $post_id );
	}

	return $shares;
}

/**
 * Set Cache Counts
 *
 * @since 1.0.0
 * @param string     $account Account Name.
 * @param string|int $post_id Post ID.
 * @param int        $count   Shares Count.
 * @param string     $url     Custom URL.
 * @param bool       $suffix  Unique suffix.
 */
function powerkit_share_buttons_set_cache_account( $account, $post_id, $count, $url = null, $suffix = false ) {

	// Get Cache Time.
	$cache_time = powerkit_share_buttons_get_cache_time( $post_id );

	// Add suffix.
	if ( $suffix ) {
		$account .= '_' . $suffix;
	}

	// Set Cache.
	if ( 'options' === $post_id ) {

		// Set Url Shares Count.
		$share_url = powerkit_share_buttons_get_url( $post_id, $url );
		set_transient( substr( 'powerkit_share_buttons_count_' . $account . '_' . md5( $share_url ), 0, 170 ), $count, $cache_time );

	} else {

		// Set Post Shares Count.
		$cache_time = powerkit_share_buttons_get_cache_time( $post_id ) + intval( date( 'U' ) );

		update_post_meta( intval( $post_id ), 'powerkit_share_buttons_transient_' . $account, $cache_time );

		if ( $count ) {
			update_post_meta( intval( $post_id ), 'powerkit_share_buttons_count_' . $account, $count );
		}
	}

	return false;
}

/**
 * Get Accounts Data
 *
 * @since 1.0.0
 * @param string     $account   Account Name.
 * @param string|int $post_id   Post ID.
 * @param string     $url       Custom URL.
 */
function powerkit_share_buttons_get_accounts( $account = false, $post_id = false, $url = null ) {

	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		$post_id = false;
	}

	// Get Current Share URL.
	$share_url = powerkit_share_buttons_get_url( $post_id, $url );

	// Get All Accounts.
	$all_accounts = apply_filters( 'powerkit_share_buttons_accounts', array(), $share_url, $post_id );

	if ( ! $account ) {
		return $all_accounts;

	} elseif ( isset( $all_accounts[ $account ] ) ) {

		return $all_accounts[ $account ];
	}

	return false;
}

/**
 * Get Cached Counts
 *
 * @param string     $account      Account Name.
 * @param string|int $post_id      Post ID.
 * @param string     $url          Custom URL.
 * @param bool       $ignore_time  Ignore Cache Time.
 */
function powerkit_share_buttons_get_cached_count( $account, $post_id, $url = null, $ignore_time = false ) {
	$count = powerkit_share_buttons_get_cached_account( $account, $post_id, $url, $ignore_time );

	// Recover shares.
	if ( get_option( 'powerkit_share_buttons_recover', false ) ) {
		$alt_count = powerkit_share_buttons_get_cached_account( $account, $post_id, $url, $ignore_time, 'recover' );

		if ( $alt_count ) {
			$count = intval( $count ) + intval( $alt_count );
		}
	}

	return $count;
}

/**
 * Get Share Count
 *
 * @since 1.0.0
 * @param string     $account   Account Name.
 * @param string|int $post_id   Post ID.
 * @param string     $url       Custom URL.
 */
function powerkit_share_buttons_get_count( $account, $post_id = false, $url = null ) {

	$count = __return_zero();

	// Get Account Data.
	$account_data = powerkit_share_buttons_get_accounts( $account, $post_id, $url );

	if ( isset( $account_data['count_walker'] ) ) {
		$func = $account_data['count_walker'];

		// Get Share URL.
		$url = powerkit_share_buttons_get_url( $post_id, $url );

		if ( function_exists( $func ) ) {
			$count = (int) call_user_func_array( $func, array( $account, $post_id, $url ) );

			// Recover shares.
			if ( get_option( 'powerkit_share_buttons_recover', false ) ) {
				$url = powerkit_share_buttons_get_alt_url( $url );

				$count_recover = call_user_func_array( $func, array( $account, $post_id, $url, 'recover' ) );

				if ( is_numeric( $count_recover ) ) {
					$count += (int) $count_recover;
				}
			}
		}
	}

	$count = apply_filters( 'powerkit_share_buttons_count', $count, $account, $post_id );

	return $count;
}

/**
 * Get Total Count
 *
 * @since 1.0.0
 * @param array      $accounts  Accounts List.
 * @param string|int $post_id   Post ID.
 * @param string     $url       Custom URL.
 * @param bool       $cached    Only cached.
 */
function powerkit_share_buttons_get_total_count( $accounts, $post_id = false, $url = null, $cached = false ) {

	$total_count = 0;

	foreach ( $accounts as $account ) {
		if ( $cached ) {
			$total_count += intval( powerkit_share_buttons_get_cached_count( $account, $post_id, $url ) );
		} else {
			$total_count += intval( powerkit_share_buttons_get_count( $account, $post_id, $url ) );
		}
	}

	return $total_count;
}

/**
 * Get Shares
 *
 * @since 1.0.0
 * @param array  $accounts       Accounts List.
 * @param bool   $total          Total Count.
 * @param bool   $icons          Show Icons.
 * @param bool   $titles         Show Title.
 * @param bool   $labels         Show labels.
 * @param bool   $counts         Show Counts.
 * @param bool   $title_location Title location.
 * @param bool   $label_location Label location.
 * @param bool   $count_location Count location.
 * @param string $mode           Counter mode.
 * @param string $layout         Share layout.
 * @param string $scheme         Color Sheme.
 * @param string $class          Additional class.
 * @param string $url            Custom URL.
 * @param string $attrs          Attrs.
 */
function powerkit_share_buttons( $accounts = array( 'facebook', 'twitter', 'pinterest' ), $total = true, $icons = true, $titles = false, $labels = true, $counts = true, $title_location = 'inside', $label_location = 'inside', $count_location = 'inside', $mode = 'mixed', $layout = 'default', $scheme = 'default', $class = null, $url = null, $attrs = '' ) {

	// Check accounts list.
	if ( empty( $accounts ) ) {
		return false;
	}

	// Post ID.
	$post_id = powerkit_share_buttons_get_current_post_id( $url );

	// Title location.
	if ( ! in_array( $title_location, array( 'inside', 'outside' ), true ) ) {
		$title_location = 'inside';
	}

	// Label location.
	if ( ! in_array( $label_location, array( 'inside', 'outside' ), true ) ) {
		$label_location = 'inside';
	}

	// Count location.
	if ( ! in_array( $count_location, array( 'inside', 'outside' ), true ) ) {
		$count_location = 'inside';
	}

	// Wrap Classes.
	$classes = array( 'pk-share-buttons-wrap' );

	if ( $layout ) {
		$classes[] = 'pk-share-buttons-layout-' . $layout;
	}

	if ( $scheme ) {
		$classes[] = 'pk-share-buttons-scheme-' . $scheme;
	}

	if ( $counts ) {
		$classes[] = 'pk-share-buttons-has-counts';
	}

	if ( $total ) {
		$classes[] = 'pk-share-buttons-has-total-counts';
	}

	if ( $class ) {
		$classes[] = $class;
	}

	$mode = apply_filters( 'powerkit_share_buttons_mode', $mode, $post_id );

	switch ( $mode ) {
		case 'none':
			$classes[] = 'pk-share-buttons-mode-none';
			break;

		case 'php':
			$classes[] = 'pk-share-buttons-mode-php';
			break;

		case 'cached':
			$classes[] = 'pk-share-buttons-mode-cached';
			break;

		case 'rest':
			$classes[] = 'pk-share-buttons-mode-rest';

			// Smart Load restapi scripts.
			add_action( 'wp_footer', 'powerkit_share_buttons_rest_api_scripts', 99 );
			break;
		case 'mixed':
			$classes[] = 'pk-share-buttons-mode-php';
			$classes[] = 'pk-share-buttons-mode-rest';

			// Smart Load restapi scripts.
			add_action( 'wp_footer', 'powerkit_share_buttons_rest_api_scripts', 99 );
			break;
	}

	// Icon prefix.
	$powerkit_share_buttons_icon_prefix = apply_filters( 'powerkit_share_buttons_icon_prefix', 'pk-icon' );
	?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-share-url="<?php echo esc_url( powerkit_share_buttons_get_url( $post_id, $url ) ); ?>" <?php call_user_func( 'printf', '%s', $attrs ); ?>>

			<?php
			if ( $total ) {
				if ( 'cached' === $mode || 'rest' === $mode ) {
					$total_count = powerkit_share_buttons_get_total_count( $accounts, $post_id, $url, true );
				} else {
					$total_count = powerkit_share_buttons_get_total_count( $accounts, $post_id, $url );
				}

				// Total Class.
				$total_class = 'pk-share-buttons-total';

				// If counter exists.
				if ( intval( $total_count ) > 0 ) {
					$total_count = powerkit_share_buttons_count_format( $total_count );
				} else {
					$total_class = 'pk-share-buttons-total pk-share-buttons-total-no-count';
				}

				// Total Title.
				$total_title = apply_filters( 'powerkit_share_buttons_total_title', esc_html__( 'Total', 'powerkit' ), $class, $total_count );

				// Total Label.
				$total_label = apply_filters( 'powerkit_share_buttons_total_label', esc_html__( 'Shares', 'powerkit' ), $class, $total_count );

				// Total Output.
				?>
				<div class="<?php echo esc_attr( $total_class ); ?>">
					<?php
					$total_output = apply_filters( 'powerkit_share_buttons_total_output', null, $class, $total_count );
					if ( $total_output ) {
						echo wp_kses( $total_output, 'post' );
					} else {
						?>
							<div class="pk-share-buttons-title pk-font-primary"><?php echo wp_kses( $total_title, 'post' ); ?></div>
							<div class="pk-share-buttons-count pk-font-heading"><?php echo esc_html( $total_count ); ?></div>
							<div class="pk-share-buttons-label pk-font-secondary"><?php echo wp_kses( $total_label, 'post' ); ?></div>
						<?php
					}
					?>
				</div>
				<?php
			}
			?>

			<div class="pk-share-buttons-items">

				<?php
				foreach ( $accounts as $account ) {

					// Get Account Data.
					$process_url = powerkit_share_buttons_process_url( $account, $post_id, $url );

					$account_data = powerkit_share_buttons_get_accounts( $account, $post_id, $process_url );

					// Break if account is unregistered.
					if ( ! $account_data ) {
						continue;
					}

					// Title.
					$title = $account_data['name'];

					// Share URL.
					$share_url = $account_data['share_url'];

					// Label.
					$label = get_option( "powerkit_share_buttons_label_{$account}", $account_data['label'] );

					// Item Classes.
					$item_classes = array( 'pk-share-buttons-item', 'pk-share-buttons-' . $account );

					// Check support accounts for extra locations.
					if ( false !== strpos( $class, 'highlight-text' ) || false !== strpos( $class, 'blockquote' ) ) {
						$list = powerkit_share_buttons_support_text_accounts();

						if ( ! in_array( $account, $list, true ) ) {
							continue;
						}

						if ( isset( $account_data['share_text_url'] ) ) {
							$share_url = $account_data['share_text_url'];
						}
					}

					// Get Account Count.
					if ( 'cached' === $mode || 'rest' === $mode ) {
						$account_count = $counts ? powerkit_share_buttons_get_cached_count( $account, $post_id, $url ) : false;

						if ( ! $account_count && isset( $account_data['count_walker'] ) ) {
							$account_count = 0;
						}
					} else {
						$account_count = $counts ? powerkit_share_buttons_get_count( $account, $post_id, $url ) : false;
					}

					$item_classes[] = $account_count ? 'pk-share-buttons-item-count' : 'pk-share-buttons-no-count';
					?>
						<div class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>" data-id="<?php echo esc_attr( $account ); ?>">

							<a href="<?php echo esc_url( $share_url, null, '' ); ?>" class="pk-share-buttons-link" target="_blank">

								<?php if ( $icons ) { ?>
									<i class="pk-share-buttons-icon <?php echo sprintf( '%2$s %2$s-%1$s', esc_attr( $account ), esc_attr( $powerkit_share_buttons_icon_prefix ) ); ?>"></i>
								<?php } ?>

								<?php if ( 'inside' === $title_location && $titles ) { ?>
									<span class="pk-share-buttons-title pk-font-primary"><?php echo esc_html( $title ); ?></span>
								<?php } ?>

								<?php if ( 'inside' === $label_location && $labels ) { ?>
									<span class="pk-share-buttons-label pk-font-primary"><?php echo esc_html( $label ); ?></span>
								<?php } ?>

								<?php if ( 'inside' === $count_location && $counts ) { ?>
									<span class="pk-share-buttons-count pk-font-secondary"><?php echo esc_html( powerkit_share_buttons_count_format( $account_count ) ); ?></span>
								<?php } ?>
							</a>

							<?php if ( 'outside' === $title_location && $titles ) { ?>
								<span class="pk-share-buttons-title pk-font-primary"><?php echo esc_html( $title ); ?></span>
							<?php } ?>

							<?php if ( 'outside' === $label_location && $labels ) { ?>
								<span class="pk-share-buttons-label pk-font-primary"><?php echo esc_html( $label ); ?></span>
							<?php } ?>

							<?php if ( 'outside' === $count_location && $counts ) { ?>
								<span class="pk-share-buttons-count pk-font-secondary"><?php echo esc_html( powerkit_share_buttons_count_format( $account_count ) ); ?></span>
							<?php } ?>
						</div>
					<?php
				}
				?>
			</div>
		</div>
	<?php
}

/**
 * Return accounts that have support share text
 */
function powerkit_share_buttons_support_text_accounts() {
	$list = array( 'facebook', 'twitter', 'pinterest', 'mail' );

	return $list;
}

/**
 * Return format color layouts
 *
 * @param array  $location Params of location.
 * @param string $name     Name of location.
 */
function powerkit_share_buttons_format_layouts( $location, $name ) {
	$layouts = apply_filters( 'powerkit_share_buttons_color_layouts', array(), $name );

	// Default layouts.
	if ( isset( $location['fields']['layouts'] ) ) {
		$default_layouts = $location['fields']['layouts'];
		$layouts_build   = array();
		if ( is_array( $default_layouts ) && $default_layouts && $layouts ) {
			foreach ( $default_layouts as $layout_key ) {
				if ( array_key_exists( $layout_key, $layouts ) ) {
					$layouts_build[ $layout_key ] = $layouts[ $layout_key ];
				}
			}
		}
		$layouts = $layouts_build;
	}

	return $layouts;
}

/**
 * Return color layout
 *
 * @param string $location_name Name of location.
 */
function powerkit_share_buttons_handler_layout( $location_name ) {
	$locations = apply_filters( 'powerkit_share_buttons_locations', array() );

	foreach ( $locations as $item ) {
		if ( $location_name === $item['location'] ) {
			$layout = get_option( "powerkit_share_buttons_{$location_name}_layout" );

			$format_layouts = powerkit_share_buttons_format_layouts( $item, $location_name );

			if ( $format_layouts ) {
				if ( $layout && array_key_exists( $layout, $format_layouts ) ) {
					return $layout;
				} else {
					return key( $format_layouts );
				}
			}
		}
	}

	return 'default';
}


/**
 * Return format color schemes
 *
 * @param array $location Params of location.
 */
function powerkit_share_buttons_format_color_schemes( $location ) {
	$schemes = apply_filters( 'powerkit_share_buttons_color_schemes', array() );

	// Default schemes.
	if ( isset( $location['fields']['schemes'] ) ) {
		$default_schemes = $location['fields']['schemes'];

		$schemes_build = array();

		if ( is_array( $default_schemes ) && $default_schemes && $schemes ) {
			foreach ( $default_schemes as $scheme_key ) {
				if ( array_key_exists( $scheme_key, $schemes ) ) {
					$schemes_build[ $scheme_key ] = $schemes[ $scheme_key ];
				}
			}
		}
		$schemes = $schemes_build;
	}

	return $schemes;
}

/**
 * Return color scheme
 *
 * @param string $location_name Name of location.
 */
function powerkit_share_buttons_handler_color_scheme( $location_name ) {
	$locations = apply_filters( 'powerkit_share_buttons_locations', array() );

	foreach ( $locations as $item ) {
		if ( $location_name === $item['location'] ) {
			$scheme = get_option( "powerkit_share_buttons_{$location_name}_scheme" );

			$format_schemes = powerkit_share_buttons_format_color_schemes( $item );

			if ( $format_schemes ) {
				if ( $scheme && array_key_exists( $scheme, $format_schemes ) ) {
					return $scheme;
				} else {
					return key( $format_schemes );
				}
			}
		}
	}

	return 'default';
}

/**
 * Display Shares in Locations
 *
 * @since 1.0.0
 * @param string $location  Shares location.
 * @param string $url       Custom URL.
 */
function powerkit_share_buttons_location( $location = 'after-content', $url = null ) {

	// Display share.
	$display_share = get_option( "powerkit_share_buttons_{$location}_display" );

	if ( $display_share ) {

		$accounts = array();

		$accounts_order = get_option( "powerkit_share_buttons_{$location}_order_multiple_list", array() );
		$accounts_check = get_option( "powerkit_share_buttons_{$location}_multiple_list", array() );

		// Sort.
		if ( $accounts_order && $accounts_check ) {
			$accounts_order = array_flip( $accounts_order );

			foreach ( $accounts_order as $key => $val ) {
				if ( in_array( $key, $accounts_check, true ) ) {
					$accounts[] = $key;
				}
			}
		}

		$title_location = get_option( "powerkit_share_buttons_{$location}_title_location" );
		$label_location = get_option( "powerkit_share_buttons_{$location}_label_location" );
		$count_location = get_option( "powerkit_share_buttons_{$location}_count_location" );

		$labels = get_option( "powerkit_share_buttons_{$location}_display_labels" );
		$total  = get_option( "powerkit_share_buttons_{$location}_display_total_share_count" );
		$counts = get_option( "powerkit_share_buttons_{$location}_display_count" );
		$scheme = get_option( "powerkit_share_buttons_{$location}_scheme" );
		$class  = null;

		// Layouts.
		$layout = powerkit_share_buttons_handler_layout( $location );

		// Scheme.
		$scheme = powerkit_share_buttons_handler_color_scheme( $location );

		// Add location to the wrapper class.
		$class = trim( 'pk-share-buttons-' . $location . ' ' . $class );

		// Before | After Content.
		$locations = apply_filters( 'powerkit_share_buttons_locations', array() );
		$mode      = 'smart';
		$before    = '';
		$after     = '';
		$attrs     = '';

		foreach ( $locations as $location_data ) {
			if ( $location_data['location'] === $location ) {
				$before = isset( $location_data['before'] ) ? $location_data['before'] : $before;
				$after  = isset( $location_data['after'] ) ? $location_data['after'] : $after;
				$mode   = isset( $location_data['mode'] ) ? $location_data['mode'] : $mode;
				$icons  = isset( $location_data['meta']['icons'] ) ? $location_data['meta']['icons'] : true;
				$titles = isset( $location_data['meta']['titles'] ) ? $location_data['meta']['titles'] : false;
				$attrs  = isset( $location_data['attrs'] ) ? $location_data['attrs'] : $attrs;
			}
		}

		// Before Shares.
		echo wp_kses_post( $before );

		// Get Shares.
		powerkit_share_buttons( $accounts, $total, $icons, $titles, $labels, $counts, $title_location, $label_location, $count_location, $mode, $layout, $scheme, $class, $url, $attrs );

		// After Shares.
		echo wp_kses_post( $after );

	}
}

/**
 * Count format
 *
 * @since 1.0.0
 * @param int $value    Count number.
 */
function powerkit_share_buttons_count_format( $value = 0 ) {
	$result = '';
	$value  = (int) $value;

	$count_format = apply_filters( 'powerkit_share_buttons_count_format', true );

	if ( $count_format ) {
		if ( $value > 999 && $value <= 999999 ) {
			$result = floor( $value / 1000 ) . esc_html( 'K', 'powerkit' );
		} elseif ( $value > 999999 ) {
			$result = floor( $value / 1000000 ) . esc_html( 'M', 'powerkit' );
		} else {
			$result = $value;
		}
	} else {
		$result = $value;
	}

	return $result;
}

/**
 * Add Social Share REST API Scripts
 */
function powerkit_share_buttons_rest_api_scripts() {
	?>
	<script type="text/javascript">
		"use strict";

		(function($) {

			$( window ).on( 'load', function() {

				// Each All Share boxes.
				$( '.pk-share-buttons-mode-rest' ).each( function() {

					var powerkitButtonsIds = [],
						powerkitButtonsBox = $( this );

					// Check Counts.
					if ( ! powerkitButtonsBox.hasClass( 'pk-share-buttons-has-counts' ) && ! powerkitButtonsBox.hasClass( 'pk-share-buttons-has-total-counts' ) ) {
						return;
					}

					powerkitButtonsBox.find( '.pk-share-buttons-item' ).each( function() {
						if ( $( this ).attr( 'data-id' ).length > 0 ) {
							powerkitButtonsIds.push( $( this ).attr( 'data-id' ) );
						}
					});

					// Generate accounts data.
					var powerkitButtonsData = {};

					if( powerkitButtonsIds.length > 0 ) {
						powerkitButtonsData = {
							'ids'     : powerkitButtonsIds.join(),
							'post_id' : powerkitButtonsBox.attr( 'data-post-id' ),
							'url'     : powerkitButtonsBox.attr( 'data-share-url' ),
						};
					}

					// Get results by REST API.
					$.ajax({
						type: 'GET',
						url: '<?php echo esc_url( get_rest_url( null, '/social-share/v1/get-shares' ) ); ?>',
						data: powerkitButtonsData,
						beforeSend: function(){

							// Add Loading Class.
							powerkitButtonsBox.addClass( 'pk-share-buttons-loading' );
						},
						success: function( response ) {

							if ( ! $.isEmptyObject( response ) && ! response.hasOwnProperty( 'code' ) ) {

								// Accounts loop.
								$.each( response, function( index, data ) {

									if ( index !== 'total_count' ) {

										// Find Bsa Item.
										var powerkitButtonsItem = powerkitButtonsBox.find( '.pk-share-buttons-item[data-id="' + index + '"]');

										// Set Count.
										if ( data.hasOwnProperty( 'count' ) && data.count  ) {

											powerkitButtonsItem.removeClass( 'pk-share-buttons-no-count' ).addClass( 'pk-share-buttons-item-count' );
											powerkitButtonsItem.find( '.pk-share-buttons-count' ).html( data.count );

										} else {
											powerkitButtonsItem.addClass( 'pk-share-buttons-no-count' );
										}
									}
								});

								if ( powerkitButtonsBox.hasClass( 'pk-share-buttons-has-total-counts' ) && response.hasOwnProperty( 'total_count' ) ) {
									var powerkitButtonsTotalBox = powerkitButtonsBox.find( '.pk-share-buttons-total' );

									if ( response.total_count ) {
										powerkitButtonsTotalBox.find( '.pk-share-buttons-count' ).html( response.total_count );
										powerkitButtonsTotalBox.show().removeClass( 'pk-share-buttons-total-no-count' );
									}
								}
							}

							// Remove Loading Class.
							powerkitButtonsBox.removeClass( 'pk-share-buttons-loading' );
						},
						error: function() {

							// Remove Loading Class.
							powerkitButtonsBox.removeClass( 'pk-share-buttons-loading' );
						}
					});
				});
			});

		})(jQuery);
	</script>
	<?php
}

/**
 * Social Share APi Response
 *
 * @param array $request REST API Request.
 */
function powerkit_share_buttons_get_accounts_restapi( $request ) {

	// Get Social Accounts.
	$social_accounts = powerkit_share_buttons_get_accounts();
	$social_accounts = array_keys( $social_accounts );

	// Error, when Social Accounts are empty.
	if ( empty( $social_accounts ) ) {
		return rest_ensure_response(
			array(
				'code'    => 'accounts_not_found',
				'message' => esc_html__( 'Social Accounts not found.', 'powerkit' ),
			)
		);
	}

	// Post ID.
	if ( isset( $request['post_id'] ) ) {
		$post_id = (int) $request['post_id'];

		if ( $post_id <= 0 ) {
			$post_id = false;
		}
	} else {
		$post_id = false;
	}

	// URL.
	if ( isset( $request['url'] ) ) {
		$url = $request['url'];
	} else {
		$url = null;
	}

	// Get Counts.
	$share_counts = array();

	if ( isset( $request['ids'] ) ) {
		$ids = explode( ',', $request['ids'] );
		$ids = array_map( 'trim', $ids );
	} else {
		$ids = $social_accounts;
	}

	$total_count = 0;

	foreach ( $ids as $account ) {
		if ( in_array( $account, $social_accounts, true ) ) {

			$account_count = powerkit_share_buttons_get_count( $account, $post_id, $url );
			$class         = $account_count ? 'pk-share-buttons-item-count' : 'pk-share-buttons-no-count';

			$total_count += (int) $account_count;

			$share_counts[ $account ] = array(
				'count' => powerkit_share_buttons_count_format( $account_count ),
				'class' => $class,
			);
		}
	}

	$share_counts['total_count'] = powerkit_share_buttons_count_format( $total_count );

	// Return Succes Result.
	return rest_ensure_response( $share_counts );
}

/**
 * Register Share REST API Route
 */
function powerkit_share_buttons_register_api_routes() {

	register_rest_route(
		'social-share/v1',
		'/get-shares',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'powerkit_share_buttons_get_accounts_restapi',
			'permission_callback' => function() {
				return true;
			},
		)
	);
}
add_action( 'rest_api_init', 'powerkit_share_buttons_register_api_routes' );
