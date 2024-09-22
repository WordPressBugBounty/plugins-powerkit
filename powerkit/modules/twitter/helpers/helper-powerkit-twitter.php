<?php
/**
 * Helpers Twitter
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Convert links to clickable format
 *
 * @param string $name Specific template.
 * @param array  $twitter Array of twitter.
 * @param array  $params Array of params.
 */
function powerkit_twitter_template_handler( $name, $twitter, $params ) {
	$templates = apply_filters( 'powerkit_twitter_templates', array() );

	$new = isset( $templates['default'] ) ? false : true;

	if ( $new && count( $templates ) > 0 ) {
		$first_item = array_shift( $templates );

		if ( function_exists( $first_item['func'] ) ) {
			call_user_func( $first_item['func'], $twitter, $params );
		} else {
			call_user_func( 'powerkit_twitter_default_template', $twitter, $params );
		}
	} elseif ( isset( $templates[ $name ] ) && function_exists( $templates[ $name ]['func'] ) ) {
		call_user_func( $templates[ $name ]['func'], $twitter, $params );
	} else {
		call_user_func( 'powerkit_twitter_default_template', $twitter, $params );
	}
}

/**
 * Get templates options
 *
 * @return array Items.
 */
function powerkit_twitter_get_templates_options() {
	$options = array();

	$templates = apply_filters( 'powerkit_twitter_templates', array() );

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
 * Convert links to clickable format
 *
 * @param string $links       Text with links.
 * @param bool   $targetblank Open links in a new tab.
 * @return string Text with replaced links.
 */
function powerkit_twitter_convert_links( $links, $targetblank = true ) {

	// The target.
	$target = $targetblank ? ' target="_blank" ' : '';

	// Convert link to url.
	$links = preg_replace( '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[A-Z0-9+&@#\/%=~_|]/i', '<a href="\0" target="_blank">\0</a>', $links );

	// Convert @ to follow.
	$links = preg_replace( '/(@([_a-z0-9\-]+))/i', '<a href="http://twitter.com/$2" title="Follow $2" $target >$1</a>', $links );

	// Convert # to search.
	$links = preg_replace( '/(#([_a-z0-9\-]+))/i', '<a href="https://twitter.com/search?q=$2" title="Search $1" $target >$1</a>', $links );

	// Return links.
	return $links;
}

/**
 * Get recent feed from twitter
 *
 * @param array  $params     Recent options.
 * @param string $cache_name The cache name.
 */
function powerkit_twitter_get_recent( $params, $cache_name = 'powerkit_twitter_data' ) {
	$params = array_merge(
		array(
			'title'    => esc_html__( 'Twitter Feed', 'powerkit' ),
			'number'   => 5,
			'template' => 'default',
			'header'   => true,
			'button'   => true,
		),
		(array) $params
	);

	$twitter_feed_manual = apply_filters( 'powerkit_twitter_feed', array(), array(), $params );

	if ( $twitter_feed_manual ) {

		if ( isset( $twitter_feed_manual['items'] ) && $twitter_feed_manual['items'] ) {
			powerkit_twitter_template_handler( $params['template'], $twitter_feed_manual, $params );
		} else {
			powerkit_alert_warning( sprintf( __( 'The list is empty. To display the feed, add elements on the <a href="%s" target="_blank">settings page</a>.', 'powerkit' ), admin_url( 'options-general.php?page=powerkit_connect&tab=twitter' ) ) );
		}
	} else {
		powerkit_alert_warning( sprintf( __( 'No data found, please fill in the fields on the <a href="%s" target="_blank">settings page</a>.', 'powerkit' ), admin_url( 'options-general.php?page=powerkit_connect&tab=twitter' ) ) );
	}
}

/**
 * Set manual twitter data.
 *
 * @param array $tweets    The tweets.
 * @param array $request The request.
 * @param array $params  The params.
 */
function powerkit_set_manual_twitter_data( $tweets, $request, $params ) {

	$tweets['name']      = 'Unknown';
	$tweets['username']  = 'unknown';
	$tweets['following'] = 0;
	$tweets['followers'] = 0;
	$tweets['avatar_1x'] = '';
	$tweets['avatar_2x'] = '';

	if ( is_array( $tweets ) && get_option( 'powerkit_connect_twitter_username' ) ) {
		$tweets['username'] = get_option( 'powerkit_connect_twitter_username' );
	}

	if ( is_array( $tweets ) && get_option( 'powerkit_connect_twitter_custom_name' ) ) {
		$tweets['name'] = get_option( 'powerkit_connect_twitter_custom_name' );
	}

	if ( is_array( $tweets ) && get_option( 'powerkit_connect_twitter_following' ) ) {
		$tweets['following'] = (int) get_option( 'powerkit_connect_twitter_following' );
	}

	if ( is_array( $tweets ) && get_option( 'powerkit_connect_twitter_custom_followers' ) ) {
		$tweets['followers'] = (int) get_option( 'powerkit_connect_twitter_custom_followers' );
	}

	if ( is_array( $tweets ) && get_option( 'powerkit_connect_twitter_custom_avatar' ) ) {
		$tweets['avatar_1x'] = get_option( 'powerkit_connect_twitter_custom_avatar' );
	}

	if ( is_array( $tweets ) && get_option( 'powerkit_connect_twitter_custom_avatar_2x' ) ) {
		$tweets['avatar_2x'] = get_option( 'powerkit_connect_twitter_custom_avatar_2x' );
	} elseif ( is_array( $tweets ) && get_option( 'powerkit_connect_twitter_custom_avatar' ) ) {
		$tweets['avatar_2x'] = get_option( 'powerkit_connect_twitter_custom_avatar' );
	}

	$manual_tweets = get_option( 'powerkit_connect_twitter_feed' );

	if ( is_array( $manual_tweets ) && ! empty( $manual_tweets ) ) {
		foreach ( $manual_tweets as $key => $element ) {

			$tweets['items'][ $key ]['tweet_id'] = $element['tweet_id'];
			$tweets['items'][ $key ]['text']     = $element['text'];
			$tweets['items'][ $key ]['date']     = $element['date'];
			$tweets['items'][ $key ]['retweets'] = (int) $element['retweets'];
		}
	}

	return $tweets;
}
add_filter( 'powerkit_twitter_feed', 'powerkit_set_manual_twitter_data', 0, 3 );
