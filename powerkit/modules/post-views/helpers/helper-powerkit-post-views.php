<?php
/**
 * Helpers Post Views
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Google Analytics API Call
 *
 * @param string $url    The url.
 * @param array  $params The params.
 * @param bool   $type   The type.
 */
function powerkit_post_views_api_call( $url, $params = array(), $type = 'GET' ) {

	$options = powerkit_post_views_options();

	if ( time() >= $options['expires'] ) {
		$options = powerkit_post_views_refresh_token();
	}

	if ( 'POST' === $type ) {
		$request_args = array(
			'body'    => json_encode( $params ),
			'headers' => array(
				'Authorization' => 'Bearer ' . $options['token'],
				'Content-Type'  => 'application/json',
			),
		);

		$result = wp_remote_post( $url, $request_args );
	} else {

		$qs = '?access_token=' . rawurlencode( $options['token'] );

		foreach ( $params as $k => $v ) {

			$qs .= '&' . $k . '=' . rawurlencode( $v );

		}

		$request = new WP_Http();

		$result = $request->request( $url . $qs );
	}

	$json = new stdClass();

	$options['error'] = null;

	$json = json_decode( wp_remote_retrieve_body( $result ), true );

	if ( is_array( $result ) && isset( $result['response']['code'] ) && 200 === $result['response']['code'] ) {

		$json = json_decode( wp_remote_retrieve_body( $result ) );

		update_option( 'powerkit_post_views_options', $options );

		return $json;

	} else {

		if ( isset( $json['error']['errors'][0]['message'] ) ) {

			$options['error'] = $json['error']['errors'][0]['message'];

			update_option( 'powerkit_post_views_options', $options );

		} elseif ( isset( $json['error']['message'] ) ) {

			$options['error'] = $json['error']['message'];

			update_option( 'powerkit_post_views_options', $options );

		} else {
			$options['error'] = esc_html__( 'Failed to get data of Google Analytics.', 'powerkit' );

			update_option( 'powerkit_post_views_options', $options );
		}

		return new stdClass();
	}
}

/**
 * Google Analytics refresh token.
 */
function powerkit_post_views_refresh_token() {

	$options = powerkit_post_views_options();

	/* If the token has expired, we create it again */
	if ( ! empty( $options['token_refresh'] ) ) {

		$request = new WP_Http();

		$result = $request->request(
			'https://accounts.google.com/o/oauth2/token',
			array(
				'method' => 'POST',
				'body'   => array(
					'client_id'     => $options['clientid'],
					'client_secret' => $options['psecret'],
					'refresh_token' => $options['token_refresh'],
					'grant_type'    => 'refresh_token',
				),
			)
		);

		$options['error'] = null;

		if ( is_array( $result ) && isset( $result['response']['code'] ) && 200 === $result['response']['code'] ) {

			$tjson = json_decode( $result['body'] );

			$request = new WP_Http();

			$result = $request->request( 'https://www.googleapis.com/oauth2/v3/userinfo?access_token=' . rawurlencode( $tjson->access_token ) );

			if ( is_array( $result ) && isset( $result['response']['code'] ) && 200 === $result['response']['code'] ) {

				$ijson = json_decode( $result['body'] );

				$options['token'] = $tjson->access_token;

				if ( isset( $tjson->refresh_token ) && ! empty( $tjson->refresh_token ) ) {
					$options['token_refresh'] = $tjson->refresh_token;
				}

				$options['expires'] = time() + $tjson->expires_in;
				$options['gid']     = $ijson->sub;

			} elseif ( is_array( $result ) && isset( $result['response']['code'] ) && 403 === $result['response']['code'] ) {

				$json = json_decode( $result['body'], true );

				$options['error'] = $json['error']['errors'][0]['message'];
			} else {
				$options['error'] = esc_html__( 'Failed to update token of Google Analytics.', 'powerkit' );
			}
		} else {
			$options['error'] = esc_html__( 'Failed to refresh token of Google Analytics.', 'powerkit' );
		}

		update_option( 'powerkit_post_views_options', $options );
	}

	return $options;
}

/**
 * Get post views settings.
 */
function powerkit_post_views_options() {

	$options = get_option( 'powerkit_post_views_options', array() );

	$options['clientid']      = isset( $options['clientid'] ) ? $options['clientid'] : null;
	$options['psecret']       = isset( $options['psecret'] ) ? $options['psecret'] : null;
	$options['gid']           = isset( $options['gid'] ) ? $options['gid'] : null;
	$options['gmail']         = isset( $options['gmail'] ) ? $options['gmail'] : null;
	$options['token']         = isset( $options['token'] ) ? $options['token'] : null;
	$options['defaultval']    = isset( $options['defaultval'] ) ? $options['defaultval'] : 0;
	$options['token_refresh'] = isset( $options['token_refresh'] ) ? $options['token_refresh'] : null;
	$options['expires']       = isset( $options['expires'] ) ? $options['expires'] : null;
	$options['property_id']   = isset( $options['property_id'] ) ? $options['property_id'] : null;
	$options['column']        = isset( $options['column'] ) ? $options['column'] : false;
	$options['trailing']      = isset( $options['trailing'] ) ? $options['trailing'] : false;

	if ( ! isset( $options['startdate'] ) ) {
		$options['startdate'] = date( 'Y-m-d', strtotime( '-1 year' ) );
	}

	return $options;
}

/**
 * Get cache time
 *
 * @since 1.0.0
 * @param string|int $post_id   Post ID.
 */
function powerkit_post_views_get_cache_time( $post_id = false ) {

	// Post Id.
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	// Post age in seconds.
	$post_age = floor( intval( date( 'U' ) ) - intval( get_post_time( 'U', true, $post_id ) ) );

	$two_months_period  = apply_filters( 'powerkit_post_views_two_months', 5184000 );
	$three_weeks_period = apply_filters( 'powerkit_post_views_three_weeks', 1814400 );

	if ( isset( $post_age ) && $post_age > $two_months_period ) {

		// Post older than 60 days - expire cache after 12 hours.
		$seconds = apply_filters( 'powerkit_post_views_refresh_60_days', 43200 );

	} elseif ( isset( $post_age ) && $post_age > $three_weeks_period ) {

		// Post older than 21 days - expire cache after 4 hours.
		$seconds = apply_filters( 'powerkit_post_views_refresh_21_days', 14400 );

	} else {

		// Expire cache after one hour.
		$seconds = apply_filters( 'powerkit_post_views_refresh_1_hour', 3600 );
	}

	return $seconds;
}

/**
 * Get no-cached post views
 *
 * @param int $post_id The post id.
 */
function powerkit_get_nocached_post_views( $post_id ) {
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	global $wpdb;

	$query = 'SELECT * FROM ' . $wpdb->prefix . 'pk_post_views WHERE id = ' . $post_id . ' AND type = 1';

	// Get cached data.
	$post_views = wp_cache_get( md5( $query ), 'pk-get-no-post-views' );

	// Cached data not found?
	if ( false === $post_views ) {
		$post_row = $wpdb->get_row( $query );

		if ( $post_row ) {
			$post_views = (float) $post_row->count;

			// Set the cache expiration, 5 minutes by default.
			$expire = absint( apply_filters( 'pk_object_cache_expire', 5 * 60, 'post-views' ) );

			wp_cache_add( md5( $query ), $post_views, 'pk-get-no-post-views', $expire );
		}
	}

	return $post_views;
}

/**
 * Get cached post views
 *
 * @param int $post_id The post id.
 */
function powerkit_get_cached_post_views( $post_id ) {
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	global $wpdb;

	$query = 'SELECT * FROM ' . $wpdb->prefix . 'pk_post_views WHERE id = ' . $post_id . ' AND type = 1';

	// Get cached data.
	$post_views = wp_cache_get( md5( $query ), 'pk-get-post-views' );

	// Cached data not found?
	if ( false === $post_views ) {
		$post_row = $wpdb->get_row( $query );

		if ( $post_row && ( intval( date( 'U' ) ) < intval( $post_row->period ) ) ) {
			$post_views = (float) $post_row->count;

			// Set the cache expiration, 5 minutes by default.
			$expire = absint( apply_filters( 'pk_object_cache_expire', 5 * 60, 'post-views' ) );

			wp_cache_add( md5( $query ), $post_views, 'pk-get-post-views', $expire );
		}
	}

	return $post_views;
}

/**
 * Set cached post views
 *
 * @param int $post_id The post id.
 * @param int $count   The count.
 */
function powerkit_set_cached_post_views( $post_id, $count ) {
	global $wpdb;

	$period = powerkit_post_views_get_cache_time( $post_id ) + intval( date( 'U' ) );

	$count = (float) $count;

	return $wpdb->query(
		$wpdb->prepare(
			'INSERT INTO ' . $wpdb->prefix . 'pk_post_views (id, type, period, count)
			VALUES (%1$d, %2$d, %3$s, %4$d)
			ON DUPLICATE KEY UPDATE period = %3$s, count = %4$d',
			$post_id,
			1,
			$period,
			$count
		)
	);
}

/**
 * Get post views
 *
 * @param int   $post_id The post id.
 * @param array $format  The format.
 * @param bool  $cached  The cached.
 */
function powerkit_get_post_views( $post_id = null, $format = true, $cached = true ) {

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( ! $post_id ) {
		return apply_filters( 'powerkit_get_post_views', $options['defaultval'], $post_id, $format );
	}

	$options = powerkit_post_views_options();

	if ( empty( $options['token'] ) ) {
		return apply_filters( 'powerkit_get_post_views', $options['defaultval'], $post_id, $format );
	}

	// Set start date.
	$start_date = $options['startdate'];

	// Get permalink.
	$basename = basename( get_permalink( $post_id ) );

	if ( $options['trailing'] ) {
		$basename .= '/';
	}

	$permalink = '/' . $basename;

	// Get post date.
	$post_date = get_the_date( 'Y-m-d', $post_id );

	// Check if the published date is earlier than default start date.
	if ( strtotime( $post_date ) > strtotime( $options['startdate'] ) ) {
		$start_date = $post_date;
	}

	// Get cached post views.
	$result = $cached ? powerkit_get_cached_post_views( $post_id ) : false;

	if ( false === $result || '' === $result ) {

		$json = powerkit_post_views_api_call(
			'https://analyticsdata.googleapis.com/v1beta/properties/' . $options['property_id'] . ':runReport?alt=json',
			array(
				'dimensions'      => array(
					array(
						'name' => 'pagePath',
					),
				),
				'metrics'         => array(
					array(
						'name' => 'screenPageViews',
					),
				),
				'dateRanges'      => array(
					array(
						'startDate' => $start_date,
						'endDate'   => 'today',
					),
				),
				'dimensionFilter' => array(
					'filter' => array(
						'fieldName'    => 'pagePath',
						'stringFilter' => array(
							'matchType' => 'PARTIAL_REGEXP',
							'value'     => $permalink,
						),
					),
				),
				'limit'           => '1000',
			),
			'POST'
		);

		if ( isset( $json->rows[0]->metricValues[0]->value ) ) {

			$total_result = $json->rows[0]->metricValues[0]->value;

			// Set cached views.
			powerkit_set_cached_post_views( $post_id, $total_result );

			$result = $total_result;
		} else {

			$value = $options['defaultval'];

			// If we have an old value let's put that instead of the default one in case of an error.
			$db_value = powerkit_get_nocached_post_views( $post_id );

			if ( false !== $db_value && '' !== $db_value ) {
				$value = $db_value;
			}

			// Set cached views.
			powerkit_set_cached_post_views( $post_id, $value );

			$result = $value;
		}
	}

	$result = apply_filters( 'powerkit_get_post_views', $result, $post_id, $format );

	return ( $format ) ? number_format_i18n( (float) $result ) : $result;
}
