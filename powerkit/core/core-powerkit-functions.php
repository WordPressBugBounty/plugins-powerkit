<?php
/**
 * The basic functions for the plugin and modules
 *
 * @package    Powerkit
 * @subpackage Core
 * @version    1.0.0
 * @since      1.0.0
 */

/**
 * This function include files to the plugin.
 *
 * @param  string $dir Directory where you need to search for files.
 */
function powerkit_load_files( $dir ) {

	$path = POWERKIT_PATH . $dir;

	// Loop through files.
	foreach ( glob( $path . '/*' ) as $file ) {
		$basename = basename( $file );

		if ( is_file( $file ) ) {

			require_once wp_normalize_path( $file );

		} elseif ( file_exists( sprintf( '%1$s/%2$s/%2$s.php', $path, $basename ) ) ) {

			require_once wp_normalize_path( sprintf( '%1$s/%2$s/%2$s.php', $path, $basename ) );

		} elseif ( file_exists( sprintf( '%1$s/%2$s/class-powerkit-%2$s.php', $path, $basename ) ) ) {

			require_once wp_normalize_path( sprintf( '%1$s/%2$s/class-powerkit-%2$s.php', $path, $basename ) );
		}
	}
}

/**
 * This function checks if the module is enabled
 *
 * @param array $info The module info.
 */
function powerkit_register_module_info( $info ) {

	$modules = (array) powerkit_get_data( 'modules' );

	// Slug of module.
	$slug = $info['slug'];

	// Check exists slug.
	if ( $slug ) {

		// Enabled load extensions.
		$module_enabled = get_option( 'powerkit_enabled_' . $slug, $info['enabled'] );

		if ( $module_enabled && 'default' === $info['type'] && $info['load_extensions'] ) {
			foreach ( $info['load_extensions'] as $extension ) {
				$modules[ $extension ]['enabled'] = true;
			}
		}

		// Add new info.
		if ( isset( $modules[ $slug ] ) ) {
			$modules[ $slug ] = array_merge( (array) $info, (array) $modules[ $slug ] );
		} else {
			$modules[ $slug ] = (array) $info;
		}

		// Update info.
		powerkit_set_data( 'modules', $modules );
	}
}

/**
 * This function return modules.
 */
function powerkit_get_modules() {
	$modules = powerkit_get_data( 'modules' );

	// Sort modules.
	if ( is_array( $modules ) && $modules ) {
		$modules_keys = array_keys( $modules );

		foreach ( $modules as $key => $row ) {
			$modules_priority[ $key ] = $row['priority'];
			$modules_name[ $key ]     = $row['name'];
		}
		array_multisort( $modules_priority, SORT_ASC, $modules_name, SORT_ASC, $modules, $modules_keys );

		$modules = array_combine( $modules_keys, $modules );
	}

	return $modules;
}

/**
 * This function return meta info of module
 *
 * @param string $slug  The slug.
 * @param string $field The field.
 */
function powerkit_get_module_meta( $slug, $field = false ) {
	$modules = (array) powerkit_get_data( 'modules' );

	if ( $field ) {
		if ( isset( $modules[ $slug ][ $field ] ) ) {
			return $modules[ $slug ][ $field ];
		}
	}
	if ( isset( $modules[ $slug ] ) ) {
		return $modules[ $slug ];
	}
}

/**
 * This function return value from сonnect module.
 *
 * @param string $name The field name of сonnect.
 */
function powerkit_connect( $name ) {

	$default = array(
		'instagram_app_url'              => 'https://api.codesupply.co/instagram-connect.php',
		'instagram_app_id'               => 'MjU5MzMzMzEwNDI0NzQxOA==',
		'instagram_app_fb_client_id'     => 'MzA1NDQ5ODgzNjk4NjQ5',
		'instagram_app_fb_url'           => 'https://api.codesupply.co/facebook-connect.php?business=true',
		'instagram_app_type'             => '',
		'instagram_app_access_token'     => '',
		'instagram_app_user_id'          => '',
		'instagram_app_username'         => '',
		'facebook_app_id'                => 'MzA1NDQ5ODgzNjk4NjQ5',
		'facebook_app_url'               => 'https://api.codesupply.co/facebook-connect.php',
		'facebook_app_access_token'      => '',
		'facebook_app_accounts'          => '',
		'facebook_share_access_token'    => 'MTc5NjYwNzk4Nzc0Mjk2JTdDZmV3YV9LRU9TMHBlbHNwUE9md19qcWxqcVRr',
		'twitter_app_url'                => 'https://api.codesupply.co/twitter-connect.php',
		'twitter_app_consumer_key'       => '',
		'twitter_app_consumer_secret'    => '',
		'twitter_app_oauth_token'        => '',
		'twitter_app_oauth_token_secret' => '',
		'twitter_app_user_id'            => '',
		'twitter_app_screen_name'        => '',
		'youtube_key'                    => 'QUl6YVN5QUxlUkNTMkVoWThnY0xndlRiTlU3a3g4cXdsVDNLdU9N',
		'telegram_token'                 => 'NTM1NTAwMjM4OkFBR3dUVDBOMDhoeHFPamxHYVhDVDFGa01mb2c2blRnQ2ZR',
		'soundcloud_client_id'           => 'OTcyMjBmYjM0YWQwMzRiNWQ0YjU5Yjk2N2ZkMTcxN2U=',
		'dribbble_token'                 => '',
		'vimeo_token'                    => 'ODhiMDU4NjA4YWViMmU2MjdiYjc4MmY2MzNkNjVjNjQ=',
		'behance_client_id'              => 'R0QxcmhQcUpvaWdaN0xqcFFEVEltMkZjOGdPemkxajQ=',
		'twitch_client_id'               => 'dmk0MDZ5OWhhNDV5MmRzcmtzcDZvMTd1bWt5NTR3',
		'vk_token'                       => 'ZWRkNjQ1ZGU3ZDQ1OTQwZjllMTMyYTMyNmIxM2MxNWJjNWYxMWNhMzRkY2MzYTc1MGE2MmQxOTI4YjY5MjExZThmNTU0Nzc5ZWU3OTNmMTk2YTJiNw==',
	);

	$name = str_replace( 'powerkit_connect_', '', $name );

	// Set value by slug.
	$value = get_option( 'powerkit_connect_' . $name );

	// Set default value.
	if ( ! $value && key_exists( $name, $default ) ) {

		if ( base64_encode( base64_decode( $default[ $name ], true ) ) === $default[ $name ] ) {
			$value = base64_decode( $default[ $name ] );
		} else {
			$value = $default[ $name ];
		}
	}

	return $value;
}

/**
 * This function checks if the module is enabled
 *
 * @param string $slug The module slug.
 */
function powerkit_module_enabled( $slug ) {
	$module = powerkit_get_module_meta( $slug );

	// Default status.
	$status = $module['enabled'];

	// Check database.
	if ( 'default' === $module['type'] ) {
		$enabled = get_option( 'powerkit_enabled_' . $slug, $module['enabled'] );

		$status = '0' === $enabled ? false : $enabled;
	}

	return apply_filters( 'powerkit_module_enabled', $status, $slug );
}

/**
 * This function return unique slug name to refer to this menu by.
 *
 * @param string $slug The module slug.
 */
function powerkit_get_page_slug( $slug ) {
	return sprintf( 'powerkit_%s', $slug );
}

/**
 * This function return admin page url.
 *
 * @param string $slug The module slug.
 * @param string $type The type page.
 */
function powerkit_get_page_url( $slug, $type = 'general' ) {
	switch ( $type ) {
		case 'general':
			return admin_url( sprintf( 'options-general.php?page=%s', powerkit_get_page_slug( $slug ) ) );
		case 'writing':
			return admin_url( sprintf( 'options-writing.php?page=%s', powerkit_get_page_slug( $slug ) ) );
		case 'reading':
			return admin_url( sprintf( 'options-reading.php?page=%s', powerkit_get_page_slug( $slug ) ) );
		case 'discussion':
			return admin_url( sprintf( 'options-reading.php?page=%s', powerkit_get_page_slug( $slug ) ) );
		case 'media':
			return admin_url( sprintf( 'options-media.php?page=%s', powerkit_get_page_slug( $slug ) ) );
		case 'permalink':
			return admin_url( sprintf( 'options-permalink.php?page=%s', powerkit_get_page_slug( $slug ) ) );
		case 'themes':
			return admin_url( sprintf( 'themes.php?page=%s', powerkit_get_page_slug( $slug ) ) );
		case 'admin':
			return admin_url( sprintf( 'admin.php?page=%s', powerkit_get_page_slug( $slug ) ) );
		default:
			return admin_url( sprintf( '%s?page=%s', $type, powerkit_get_page_slug( $slug ) ) );
	}
}
