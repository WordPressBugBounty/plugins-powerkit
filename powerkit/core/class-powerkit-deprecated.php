<?php
/**
 * Deprecated features and migration functions
 *
 * @package    Powerkit
 * @subpackage Core
 */

/**
 * Migration to 2.0.7
 *
 * @param string $current Current version.
 * @param string $new     New version.
 */
add_action( 'powerkit_plugin_upgrade', function ( $current, $new ) {
	if ( version_compare( $current, '2.0.7', '<' ) ) {
		require_once POWERKIT_PATH . 'modules/post-views/helpers/db-powerkit-post-views.php';

		$post_views_db = new Powerkit_Post_Views_DB();

		$post_views_db->activate_single();
	}
}, 10, 2 );

/**
 * Migration to 2.2.2
 *
 * @param string $current Current version.
 * @param string $new     New version.
 */
add_action( 'powerkit_plugin_upgrade', function ( $current, $new ) {
	if ( version_compare( $current, '2.2.2', '<' ) ) {
		$transients = array(
			'powerkit_connect_instagram_app_access_token',
			'powerkit_connect_instagram_app_type',
			'powerkit_connect_instagram_app_user_id',
			'powerkit_connect_instagram_app_username',
			'powerkit_connect_instagram_app_refresh',
			'powerkit_connect_facebook_app_access_token',
			'powerkit_connect_facebook_app_accounts',
			'powerkit_connect_facebook_app_refresh',
			'powerkit_connect_twitter_app_user_id',
			'powerkit_connect_twitter_app_screen_name',
			'powerkit_connect_twitter_app_oauth_token',
			'powerkit_connect_twitter_app_oauth_token_secret',
		);

		foreach ( $transients as $key => $transient ) {
			if ( get_transient( $transient ) && ! get_option( $transient ) ) {
				update_option( $transient, get_transient( $transient ) );
			}
		}
	}
}, 10, 2 );

/**
 * Migration to 2.3.0
 *
 * @param string $current Current version.
 * @param string $new     New version.
 */
add_action( 'powerkit_plugin_upgrade', function ( $current, $new ) {
	if ( version_compare( $current, '2.3.0', '<' ) ) {
		if ( 'before' === get_option( 'powerkit_toc_display', 'none' ) ) {
			update_option( 'powerkit_toc_enable_automatically', true );
		}
		if ( 'after' === get_option( 'powerkit_toc_display', 'none' ) ) {
			update_option( 'powerkit_toc_enable_automatically', true );
		}
	}
}, 10, 2 );


/**
 * Migration to 2.6.2
 *
 * @param string $current Current version.
 * @param string $new     New version.
 */
add_action( 'powerkit_plugin_upgrade', function ( $current, $new ) {
	if ( version_compare( $current, '2.6.2', '<' ) && get_option( 'powerkit_db_version' ) ) {
		update_option( 'powerkit_enabled_headers_footers', 1 );
	}
}, 10, 2 );
