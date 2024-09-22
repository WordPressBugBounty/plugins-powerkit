<?php
/**
 * The basic helpers functions
 *
 * @package    Powerkit
 * @subpackage Core
 * @version    1.0.0
 * @since      1.0.0
 */

/**
 * Output error message.
 *
 * @param string $message The error message.
 */
function powerkit_alert_warning( $message ) {
	if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
		?>
		<p class="pk-alert pk-alert-warning" role="alert">
			<object>
				<?php call_user_func( 'printf', '%s', $message ); ?>
			</object>

			<?php esc_html_e( ' Don’t worry, this error is visible to site admins only, and your site visitors won’t see it.', 'powerkit' ); ?>
		</p>
		<?php
	}
}

/**
 * Processing path of style.
 *
 * @param string $path URL to the stylesheet.
 */
function powerkit_style( $path ) {
	// Check RTL.
	if ( is_rtl() ) {
		return $path;
	}

	// Check Dev.
	$dev = POWERKIT_PATH . 'assets/css/powerkit-dev.css';

	if ( file_exists( $dev ) ) {
		return str_replace( '.css', '-dev.css', $path );
	}

	return $path;
}

/**
 * Generate AMP style.
 *
 * @param string $path Path to the stylesheet.
 */
function powerkit_amp_style( $path ) {
	if ( file_exists( $path ) ) {
		$output = call_user_func( 'file_get_contents', $path );

		call_user_func( 'printf', '%s', $output );
	}
}

/**
 * Check post views module.
 *
 * @return string Type.
 */
function powerkit_post_views_enabled() {

	// Post Views Counter.
	if ( class_exists( 'Post_Views_Counter' ) ) {
		return 'post_views';
	}

	// Powerkit Post Views.
	if ( powerkit_module_enabled( 'post_views' ) ) {
		return 'pk_post_views';
	}
}

/**
 * Process shortcode atts.
 *
 * @param array $atts Attributes in shortcode tag.
 */
function powerkit_shortcode_atts( $atts ) {
	if ( is_array( $atts ) ) {
		foreach ( $atts as $name => $val ) {
			if ( is_string( $val ) && 'true' === $val ) {
				$atts[ $name ] = true;
			}
			if ( is_string( $val ) && 'false' === $val ) {
				$atts[ $name ] = false;
			}
		}
	}
	return $atts;
}

/**
 * Retrieves a post meta field for the given post ID.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Optional. The meta key to retrieve. By default, returns
 *                        data for all keys. Default empty.
 * @param bool   $single  Optional. If true, returns only the first value for the specified meta key.
 *                        This parameter has no effect if $key is not specified. Default false.
 * @param mixed  $default Default value.
 * @return mixed Will be an array if $single is false. Will be value of the meta
 *               field if $single is true.
 */
function powerkit_get_post_metadata( $post_id, $key = '', $single = false, $default = null ) {

	if ( ! metadata_exists( 'post', $post_id, $key ) && $default ) {
		return $default;
	}

	return get_metadata( 'post', $post_id, $key, $single );
}

/**
 * Get locale in uniform format.
 */
function powerkit_get_locale() {

	$locale = get_locale();

	if ( preg_match( '#^[a-z]{2}\-[A-Z]{2}$#', $locale ) ) {
		$locale = str_replace( '-', '_', $locale );
	} elseif ( preg_match( '#^[a-z]{2}$#', $locale ) ) {
		if ( function_exists( 'mb_strtoupper' ) ) {
			$locale .= '_' . mb_strtoupper( $locale, 'UTF-8' );
		} else {
			$locale .= '_' . mb_strtoupper( $locale );
		}
	}

	if ( empty( $locale ) ) {
		$locale = 'en_US';
	}

	return apply_filters( 'powerkit_locale', $locale );
}

/**
 * Get rounded number.
 *
 * @param int $number    Input number.
 * @param int $min_value Minimum value to round number.
 * @param int $decimal   How may decimals shall be in the rounded number.
 */
function powerkit_get_round_number( $number, $min_value = 1000, $decimal = 1 ) {
	if ( $number < $min_value ) {
		return number_format_i18n( $number );
	}
	$alphabets = array(
		1000000000 => esc_html__( 'B', 'powerkit' ),
		1000000    => esc_html__( 'M', 'powerkit' ),
		1000       => esc_html__( 'K', 'powerkit' ),
	);
	foreach ( $alphabets as $key => $value ) {
		if ( $number >= $key ) {
			return number_format_i18n( round( $number / $key, $decimal ), $decimal ) . $value;
		}
	}
}

/**
 * Convert dates to readable format
 *
 * @param string $a Time string (timeformat).
 * @return string Formatted time.
 */
function powerkit_relative_time( $a ) {

	// Get current timestampt.
	$b = strtotime( 'now' );

	// Get timestamp when tweet created.
	$c = strtotime( $a );

	// Get difference.
	$d = $b - $c;

	// Calculate different time values.
	$minute = 60;
	$hour   = $minute * 60;
	$day    = $hour * 24;
	$week   = $day * 7;

	if ( is_numeric( $d ) && $d > 0 ) :

		// If less then 3 seconds.
		if ( $d < 3 ) {
			return esc_html__( 'right now', 'powerkit' );
		}

		// If less then minute.
		if ( $d < $minute ) {
			return floor( $d ) . esc_html__( ' seconds ago', 'powerkit' );
		}

		// If less then 2 minutes.
		if ( $d < $minute * 2 ) {
			return esc_html__( 'about 1 minute ago', 'powerkit' );
		}

		// If less then hour.
		if ( $d < $hour ) {
			return floor( $d / $minute ) . esc_html__( ' minutes ago', 'powerkit' );
		}

		// If less then 2 hours.
		if ( $d < $hour * 2 ) {
			return esc_html__( 'about 1 hour ago', 'powerkit' );
		}

		// If less then day.
		if ( $d < $day ) {
			return floor( $d / $hour ) . esc_html__( ' hours ago', 'powerkit' );
		}

		// If more then day, but less then 2 days.
		if ( $d > $day && $d < $day * 2 ) {
			return esc_html__( 'yesterday', 'powerkit' );
		}

		// If less then year.
		if ( $d < $day * 365 ) {
			return floor( $d / $day ) . esc_html__( ' days ago', 'powerkit' );
		}

		// else return more than a year.
		return esc_html__( 'over a year ago', 'powerkit' );
	endif;
}

/**
 * Truncates string with specified length
 *
 * @param  string $string      Text string.
 * @param  int    $length      Letters length.
 * @param  string $etc         End truncate.
 * @param  bool   $break_words Break words or not.
 * @return string
 */
function powerkit_str_truncate( $string, $length = 80, $etc = '&hellip;', $break_words = false ) {
	if ( 0 === $length ) {
		return '';
	}

	if ( function_exists( 'mb_strlen' ) ) {

		// MultiBite string functions.
		if ( mb_strlen( $string ) > $length ) {
			$length -= min( $length, mb_strlen( $etc ) );
			if ( ! $break_words ) {
				$string = preg_replace( '/\s+?(\S+)?$/', '', mb_substr( $string, 0, $length + 1 ) );
			}

			return mb_substr( $string, 0, $length ) . $etc;
		}
	} else {

		// Default string functions.
		if ( strlen( $string ) > $length ) {
			$length -= min( $length, strlen( $etc ) );
			if ( ! $break_words ) {
				$string = preg_replace( '/\s+?(\S+)?$/', '', substr( $string, 0, $length + 1 ) );
			}

			return substr( $string, 0, $length ) . $etc;
		}
	}

	return $string;
}

/**
 * Set number to Short Form
 *
 * @param int $n       The number.
 * @param int $decimal The decimal.
 */
function powerkit_abridged_number( $n, $decimal = 1 ) {

	// First strip any formatting.
	$n = (float) str_replace( ',', '', $n );

	// Is this a number?
	if ( ! is_numeric( $n ) ) {
		return false;
	}

	// Return current count.
	if ( $n < 1000 ) {
		return number_format_i18n( $n );
	}

	// Add suffix.
	$suffix = array(
		1000000000 => esc_html__( 'B', 'powerkit' ), // Billion.
		1000000    => esc_html__( 'M', 'powerkit' ), // Million.
		1000       => esc_html__( 'K', 'powerkit' ), // Thousand.
	);
	foreach ( $suffix as $k => $v ) {
		if ( $n >= $k ) {
			return number_format_i18n( $n / $k, $decimal ) . $v;
		}
	}
}

/**
 * Time ago
 *
 * @param  string $time The time.
 * @return string
 */
function powerkit_timing_ago( $time ) {
	$periods = array( esc_html__( 'second', 'powerkit' ), esc_html__( 'minute', 'powerkit' ), esc_html__( 'hour', 'powerkit' ), esc_html__( 'day', 'powerkit' ), esc_html__( 'week', 'powerkit' ), esc_html__( 'month', 'powerkit' ), esc_html__( 'year', 'powerkit' ), esc_html__( 'decade', 'powerkit' ) );
	$lengths = array( '60', '60', '24', '7', '4.35', '12', '10' );

	$now = time();

	$difference = $now - $time;
	$tense      = esc_html__( 'ago', 'powerkit' );

	$lengths_count = count( $lengths );

	for ( $j = 0; $difference >= $lengths[ $j ] && $j < $lengths_count - 1; $j++ ) {
		$difference /= $lengths[ $j ];
	}

	$difference = round( $difference );

	if ( 1 !== $difference ) {
		$periods[ $j ] .= 's';
	}

	return "$difference $periods[$j] {$tense} ";
}

/**
 * Encode data
 *
 * @param  mixed  $content    The content.
 * @param  string $secret_key The key.
 * @return string
 */
function powerkit_encode_data( $content, $secret_key = 'powerkit' ) {

	$content = wp_json_encode( $content );

	return base64_encode( $content );
}

/**
 * Decode data
 *
 * @param  string $content    The content.
 * @param  string $secret_key The key.
 * @return string
 */
function powerkit_decode_data( $content, $secret_key = 'powerkit' ) {

	$content = base64_decode( $content );

	return json_decode( $content );
}

/**
 * Encrypt data
 *
 * @param  mixed  $content    The content.
 * @param  string $secret_key The key.
 * @return string
 */
function powerkit_encrypt_data( $content, $secret_key = 'powerkit' ) {

	$content = maybe_serialize( $content );

	if ( function_exists( 'openssl_encrypt' ) && function_exists( 'hash' ) ) {
		$encrypt_method = 'AES-256-CBC';

		$key = hash( 'sha256', $secret_key );
		$iv  = substr( hash( 'sha256', 'secret key' ), 0, 16 );

		return base64_encode( openssl_encrypt( $content, $encrypt_method, $key, 0, $iv ) );
	} else {
		return base64_encode( $content );
	}
}

/**
 * Decrypt data
 *
 * @param  string $content    The content.
 * @param  string $secret_key The key.
 * @return string
 */
function powerkit_decrypt_data( $content, $secret_key = 'powerkit' ) {

	if ( function_exists( 'openssl_encrypt' ) && function_exists( 'hash' ) ) {
		$encrypt_method = 'AES-256-CBC';

		$key = hash( 'sha256', $secret_key );
		$iv  = substr( hash( 'sha256', 'secret key' ), 0, 16 );

		$content = openssl_decrypt( base64_decode( $content ), $encrypt_method, $key, 0, $iv );
	} else {
		$content = base64_decode( $content );
	}

	$content = maybe_unserialize( $content );

	return $content;
}

/**
 * Generate uuid hash
 *
 * @param string $name   The name.
 * @param string $action The action.
 */
function powerkit_uuid_hash( $name = '_wpnonce', $action = -1 ) {
	$user = wp_get_current_user();
	$uid  = (int) $user->ID;

	if ( ! $uid ) {
		$uid = apply_filters( 'nonce_user_logged_out', $uid, $action );
	}

	$token = wp_get_session_token();
	$i     = wp_nonce_tick();

	$hash = substr( wp_hash( $i . '|' . $action . '|' . $uid . '|' . $token, 'nonce' ), -12, 10 );

	if ( ! isset( ${'_REQUEST'}[ $name ] ) ) {
		${'_REQUEST'}[ $name ] = $hash;
	}
}

/**
 * Get the user uuid
 *
 * @return string
 */
function powerkit_get_user_uuid() {
	if ( getenv( 'HTTP_CLIENT_IP' ) ) {
		return getenv( 'HTTP_CLIENT_IP' );
	} elseif ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
		return getenv( 'HTTP_X_FORWARDED_FOR' );
	} elseif ( getenv( 'HTTP_X_FORWARDED' ) ) {
		return getenv( 'HTTP_X_FORWARDED' );
	} elseif ( getenv( 'HTTP_FORWARDED_FOR' ) ) {
		return getenv( 'HTTP_FORWARDED_FOR' );
	} elseif ( getenv( 'HTTP_FORWARDED' ) ) {
		return getenv( 'HTTP_FORWARDED' );
	} elseif ( getenv( 'REMOTE_ADDR' ) ) {
		return getenv( 'REMOTE_ADDR' );
	}

	return uniqid( 'x', true );
}

/**
 * Convert by title Cyrillic characters to Latin characters
 *
 * @param string $text The text.
 */
function powerkit_text_with_translit( $text ) {

	$gost = array(
		'А' => 'A',
		'Б' => 'B',
		'В' => 'V',
		'Г' => 'G',
		'Ѓ' => 'G`',
		'Ґ' => 'G`',
		'Д' => 'D',
		'Е' => 'E',
		'Ё' => 'YO',
		'Є' => 'YE',
		'Ж' => 'ZH',
		'З' => 'Z',
		'Ѕ' => 'Z',
		'И' => 'I',
		'Й' => 'Y',
		'Ј' => 'J',
		'І' => 'I',
		'Ї' => 'YI',
		'К' => 'K',
		'Ќ' => 'K',
		'Л' => 'L',
		'Љ' => 'L',
		'М' => 'M',
		'Н' => 'N',
		'Њ' => 'N',
		'О' => 'O',
		'П' => 'P',
		'Р' => 'R',
		'С' => 'S',
		'Т' => 'T',
		'У' => 'U',
		'Ў' => 'U',
		'Ф' => 'F',
		'Х' => 'H',
		'Ц' => 'TS',
		'Ч' => 'CH',
		'Џ' => 'DH',
		'Ш' => 'SH',
		'Щ' => 'SHH',
		'Ъ' => '``',
		'Ы' => 'YI',
		'Ь' => '`',
		'Э' => 'E`',
		'Ю' => 'YU',
		'Я' => 'YA',
		'а' => 'a',
		'б' => 'b',
		'в' => 'v',
		'г' => 'g',
		'ѓ' => 'g',
		'ґ' => 'g',
		'д' => 'd',
		'е' => 'e',
		'ё' => 'yo',
		'є' => 'ye',
		'ж' => 'zh',
		'з' => 'z',
		'ѕ' => 'z',
		'и' => 'i',
		'й' => 'y',
		'ј' => 'j',
		'і' => 'i',
		'ї' => 'yi',
		'к' => 'k',
		'ќ' => 'k',
		'л' => 'l',
		'љ' => 'l',
		'м' => 'm',
		'н' => 'n',
		'њ' => 'n',
		'о' => 'o',
		'п' => 'p',
		'р' => 'r',
		'с' => 's',
		'т' => 't',
		'у' => 'u',
		'ў' => 'u',
		'ф' => 'f',
		'х' => 'h',
		'ц' => 'ts',
		'ч' => 'ch',
		'џ' => 'dh',
		'ш' => 'sh',
		'щ' => 'shh',
		'ь' => '',
		'ы' => 'yi',
		'ъ' => "'",
		'э' => 'e`',
		'ю' => 'yu',
		'я' => 'ya',
	);

	return strtr( $text, $gost );
}

/**
 * Check social links exists.
 */
function powerkit_social_links_exists() {
	if ( ! powerkit_module_enabled( 'social_links' ) ) {
		return;
	}

	if ( get_option( 'powerkit_social_links_multiple_list' ) ) {
		return true;
	}
}

/**
 * Check mailchimp form exists.
 *
 * @param string $id The list ID.
 */
function powerkit_mailchimp_form_exists( $id = 'default' ) {
	if ( ! powerkit_module_enabled( 'opt_in_forms' ) ) {
		return;
	}

	$token = get_option( 'powerkit_mailchimp_token' );

	if ( $token ) {

		if ( ! $id || 'default' === $id ) {
			$id = get_option( 'powerkit_mailchimp_list' );
		}

		if ( $id ) {
			return true;
		}
	}
}

/**
 * Check share buttons exists.
 *
 * @param string $location The location.
 */
function powerkit_share_buttons_exists( $location ) {
	if ( ! powerkit_module_enabled( 'share_buttons' ) ) {
		return;
	}

	if ( ! get_option( "powerkit_share_buttons_{$location}_display" ) ) {
		return;
	}

	$accounts = get_option( "powerkit_share_buttons_{$location}_multiple_list", array( 'facebook', 'twitter', 'pinterest' ) );

	if ( $accounts ) {
		return true;
	}
}

/**
 * Get the available image sizes
 */
function powerkit_get_available_image_sizes() {
	$wais = & $GLOBALS['_wp_additional_image_sizes'];

	$sizes       = array();
	$image_sizes = get_intermediate_image_sizes();

	if ( is_array( $image_sizes ) && $image_sizes ) {
		foreach ( $image_sizes as $size ) {
			if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ), true ) ) {
				$sizes[ $size ] = array(
					'width'  => get_option( "{$size}_size_w" ),
					'height' => get_option( "{$size}_size_h" ),
					'crop'   => (bool) get_option( "{$size}_crop" ),
				);
			} elseif ( isset( $wais[ $size ] ) ) {
				$sizes[ $size ] = array(
					'width'  => $wais[ $size ]['width'],
					'height' => $wais[ $size ]['height'],
					'crop'   => $wais[ $size ]['crop'],
				);
			}

			// Size registered, but has 0 width and height.
			if ( 0 === (int) $sizes[ $size ]['width'] && 0 === (int) $sizes[ $size ]['height'] ) {
				unset( $sizes[ $size ] );
			}
		}
	}

	return $sizes;
}

/**
 * Gets the data of a specific image size.
 *
 * @param string $size Name of the size.
 */
function powerkit_get_image_size( $size ) {
	if ( ! is_string( $size ) ) {
		return;
	}

	$sizes = powerkit_get_available_image_sizes();

	return isset( $sizes[ $size ] ) ? $sizes[ $size ] : false;
}

/**
 * Get the list available image sizes
 */
function powerkit_get_list_available_image_sizes() {
	$intermediate_image_sizes = get_intermediate_image_sizes();

	$image_sizes = array();

	foreach ( $intermediate_image_sizes as $size ) {
		$image_sizes[ $size ] = $size;

		$data = powerkit_get_image_size( $size );

		if ( isset( $data['width'] ) || isset( $data['height'] ) ) {

			$width  = '~';
			$height = '~';

			if ( isset( $data['width'] ) && $data['width'] ) {
				$width = $data['width'] . 'px';
			}
			if ( isset( $data['height'] ) && $data['height'] ) {
				$height = $data['height'] . 'px';
			}

			$image_sizes[ $size ] .= sprintf( ' [%s, %s]', $width, $height );
		}
	}

	$image_sizes = apply_filters( 'powerkit_list_available_image_sizes', $image_sizes );

	return $image_sizes;
}

/**
 * Get fields array for Button in some PK blocks
 *
 * @param string $field_prefix    Field prefix.
 * @param string $section_name    Section name.
 * @param array  $active_callback Active callback.
 */
function powerkit_get_gutenberg_button_fields( $field_prefix = 'button', $section_name = '', $active_callback = array() ) {

	$fields = array(
		array(
			'key'             => $field_prefix . 'Style',
			'label'           => esc_html__( 'Style', 'powerkit' ),
			'section'         => $section_name,
			'type'            => 'select',
			'default'         => '',
			'choices'         => array(
				''        => esc_html__( 'Default', 'powerkit' ),
				'outline' => esc_html__( 'Outline', 'powerkit' ),
				'squared' => esc_html__( 'Squared', 'powerkit' ),
			),
			'active_callback' => $active_callback,
		),
		array(
			'key'             => $field_prefix . 'Size',
			'label'           => esc_html__( 'Size', 'powerkit' ),
			'section'         => $section_name,
			'type'            => 'select',
			'default'         => '',
			'choices'         => array(
				''   => esc_html__( 'Default', 'powerkit' ),
				'sm' => esc_html__( 'Small', 'powerkit' ),
				'lg' => esc_html__( 'Large', 'powerkit' ),
			),
			'active_callback' => $active_callback,
		),
		array(
			'key'             => $field_prefix . 'FullWidth',
			'label'           => esc_html__( 'Full Width', 'powerkit' ),
			'section'         => $section_name,
			'type'            => 'toggle',
			'default'         => false,
			'active_callback' => $active_callback,
		),
		array(
			'key'             => $field_prefix . 'ColorBg',
			'label'           => esc_html__( 'Background Color', 'powerkit' ),
			'section'         => $section_name,
			'type'            => 'color',
			'default'         => '',
			'output'          => array(
				array(
					'element'  => '$ .wp-block-button a.wp-block-button__link',
					'property' => 'background-color',
					'suffix'   => '!important',
				),
			),
			'active_callback' => $active_callback,
		),
		array(
			'key'             => $field_prefix . 'ColorBgHover',
			'label'           => esc_html__( 'Background Color Hover', 'powerkit' ),
			'section'         => $section_name,
			'type'            => 'color',
			'default'         => '',
			'output'          => array(
				array(
					'element'  => '$ .wp-block-button a.wp-block-button__link:hover, $ .wp-block-button a.wp-block-button__link:focus',
					'property' => 'background-color',
					'suffix'   => '!important',
				),
			),
			'active_callback' => $active_callback,
		),
		array(
			'key'             => $field_prefix . 'ColorText',
			'label'           => esc_html__( 'Text Color', 'powerkit' ),
			'section'         => $section_name,
			'type'            => 'color',
			'default'         => '',
			'output'          => array(
				array(
					'element'  => '$ .wp-block-button__link',
					'property' => 'color',
					'suffix'   => '!important',
				),
			),
			'active_callback' => $active_callback,
		),
		array(
			'key'             => $field_prefix . 'ColorTextHover',
			'label'           => esc_html__( 'Text Color Hover', 'powerkit' ),
			'section'         => $section_name,
			'type'            => 'color',
			'default'         => '',
			'output'          => array(
				array(
					'element'  => '$ .wp-block-button a.wp-block-button__link:hover, $ .wp-block-button a.wp-block-button__link:focus',
					'property' => 'color',
					'suffix'   => '!important',
				),
			),
			'active_callback' => $active_callback,
		),
	);

	return $fields;
}

/**
 * Print core/button in some PK blocks
 *
 * @param string $text         Text of button.
 * @param string $url          Url of button.
 * @param string $target       Target.
 * @param string $field_prefix Field prefix.
 * @param array  $attributes   Attributes.
 */
function powerkit_print_gutenberg_blocks_button( $text, $url, $target = '', $field_prefix = 'button', $attributes = array() ) {
	$class_name      = 'wp-block-button';
	$link_class_name = 'wp-block-button__link';

	// Style.
	if ( isset( $attributes[ $field_prefix . 'Style' ] ) && $attributes[ $field_prefix . 'Style' ] ) {
		$class_name .= ' is-style-' . $attributes[ $field_prefix . 'Style' ];
	}

	// Size.
	if ( isset( $attributes[ $field_prefix . 'Size' ] ) && $attributes[ $field_prefix . 'Size' ] ) {
		$class_name .= ' is-pk-button-size-' . $attributes[ $field_prefix . 'Size' ];
	}

	// FullWidth.
	if ( isset( $attributes[ $field_prefix . 'FullWidth' ] ) && $attributes[ $field_prefix . 'FullWidth' ] ) {
		$class_name .= ' is-pk-button-full-width';
	}

	// Color.
	if ( isset( $attributes[ $field_prefix . 'ColorText' ] ) && $attributes[ $field_prefix . 'ColorText' ] ) {
		$link_class_name .= ' has-text-color';
	}

	// Background.
	if ( isset( $attributes[ $field_prefix . 'ColorBg' ] ) && $attributes[ $field_prefix . 'ColorBg' ] ) {
		$link_class_name .= ' has-background';
	}
	?>
	<div class="<?php echo esc_attr( $class_name ); ?>">
		<a class="<?php echo esc_attr( $link_class_name ); ?>" href="<?php echo esc_url( $url ); ?>" target="<?php echo esc_attr( $target ); ?>">
			<?php echo wp_kses_post( $text ); ?>
		</a>
	</div>
	<?php
}
