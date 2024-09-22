<?php
/**
 * Helpers Social Links
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Author social fields
 */
function powerkit_get_author_fields() {
	$fields = array(
		'facebook'   => esc_html__( 'Facebook Profile URL', 'powerkit' ),
		'twitter'    => esc_html__( 'Twitter Profile URL', 'powerkit' ),
		'instagram'  => esc_html__( 'Instagram Profile URL', 'powerkit' ),
		'pinterest'  => esc_html__( 'Pinterest Profile URL', 'powerkit' ),
		'youtube'    => esc_html__( 'YouTube Profile URL', 'powerkit' ),
		'telegram'   => esc_html__( 'Telegram Profile URL', 'powerkit' ),
		'vimeo'      => esc_html__( 'Vimeo Profile URL', 'powerkit' ),
		'soundcloud' => esc_html__( 'SoundCloud Profile URL', 'powerkit' ),
		'spotify'    => esc_html__( 'Spotify Profile URL', 'powerkit' ),
		'dribbble'   => esc_html__( 'Dribbble Profile URL', 'powerkit' ),
		'behance'    => esc_html__( 'Behance Profile URL', 'powerkit' ),
		'github'     => esc_html__( 'GitHub Profile URL', 'powerkit' ),
		'vk'         => esc_html__( 'VK Profile URL', 'powerkit' ),
		'linkedin'   => esc_html__( 'LinkedIn Profile URL', 'powerkit' ),
		'twitch'     => esc_html__( 'Twitch Profile URL', 'powerkit' ),
		'flickr'     => esc_html__( 'Flickr Profile URL', 'powerkit' ),
		'snapchat'   => esc_html__( 'Snapchat Profile URL', 'powerkit' ),
		'medium'     => esc_html__( 'Medium Profile URL', 'powerkit' ),
		'tumblr'     => esc_html__( 'Tumblr Profile URL', 'powerkit' ),
		'bloglovin'  => esc_html__( 'Bloglovin Profile URL', 'powerkit' ),
		'rss'        => esc_html__( 'RSS Profile URL', 'powerkit' ),
	);

	return apply_filters( 'powerkit_get_author_fields', $fields );
}

/**
 * Author Social Links
 *
 * @param mixed  $author    The author.
 * @param string $class     Class name of the wrapping <div>.
 * @param bool   $title     Title of the link.
 * @param bool   $only_data Output only data.
 */
function powerkit_author_social_links( $author, $class = 'default', $title = false, $only_data = false ) {

	$links = powerkit_get_author_fields();

	$list = array();

	// Website.
	$url = get_the_author_meta( 'url', $author );

	if ( $url ) {
		$list[] = array(
			'label' => esc_html__( 'Website', 'powerkit' ),
			'key'   => 'website',
			'url'   => $url,
		);
	}

	// Loop through the array of social links.
	foreach ( $links as $key => $label ) {

		if ( isset( $author->$key ) ) {
			$author_url = $author->$key;
		} else {
			$author_url = get_the_author_meta( $key, $author );
		}

		if ( empty( $author_url ) ) {
			$author_url = get_post_meta( $author, sprintf( 'cap-%s', $key ), true );
		}

		// Check if slug of a social icon is not empty.
		if ( $author_url ) {
			$list[] = array(
				'url'   => $author_url,
				'key'   => $key,
				'label' => $label,
			);
		}
	}

	if ( $only_data ) {
		return $list;
	}

	// Link Attributes.
	$target = get_option( 'powerkit_social_links_link_target', 'new' ) === 'new' ? '_blank' : '_self';
	$rel    = get_option( 'powerkit_social_links_nofollow' ) ? 'nofollow' : '';

	// Check if the list is not empty.
	if ( $list ) {
		?>
		<div class="pk-author-social-links pk-social-links-wrap pk-social-links-template-<?php echo esc_html( $class ); ?>">
			<div class="pk-social-links-items">
				<?php
				// Loop through the array of social links.
				foreach ( $list as $data ) {
					?>
						<div class="pk-social-links-item pk-social-links-<?php echo esc_html( $data['key'] ); ?>">
							<a href="<?php echo esc_url( $data['url'] ); ?>" class="pk-social-links-link" target="<?php echo esc_attr( $target ); ?>" rel="<?php echo esc_attr( $rel ); ?>">
								<i class="pk-icon pk-icon-<?php echo esc_html( $data['key'] ); ?>"></i>
								<?php if ( $title ) { ?>
									<span class="pk-social-links-title pk-font-primary"><?php echo esc_html( $data['label'] ); ?></span>
								<?php } ?>
							</a>
						</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}
}

/**
 * Get social params with filter
 *
 * @param string $id ID group.
 * @param string $key Filter key.
 * @return mix Specific social params.
 */
function powerkit_social_links_specific_param( $id, $key = false ) {
	$params = apply_filters( 'powerkit_social_links_list', array() );

	foreach ( $params as $param ) {
		if ( $key ) {
			if ( $param['id'] === $id && isset( $param[ $key ] ) ) {
				return $param[ $key ];
			}
		} else {
			if ( $param['id'] === $id ) {
				return $param;
			}
		}
	}
}

/**
 * Get templates options
 *
 * @return array Items.
 */
function powerkit_social_links_get_templates() {
	$options = array();

	$templates = apply_filters( 'powerkit_social_links_templates', array() );

	if ( $templates ) {
		foreach ( $templates as $key => $item ) {
			if ( isset( $item['public'] ) && false === $item['public'] ) {
				continue;
			}
			if ( isset( $item['name'] ) ) {
				$options[ $key ] = $item['name'];
			}
		}
	}

	return $options;
}

/**
 * Get color schemes options
 *
 * @return array Items.
 */
function powerkit_social_links_get_color_schemes() {
	$options = array();

	$color_schemes = apply_filters( 'powerkit_social_links_color_schemes', array() );

	if ( $color_schemes ) {
		foreach ( $color_schemes as $key => $item ) {
			if ( isset( $item['public'] ) && false === $item['public'] ) {
				continue;
			}
			if ( isset( $item['name'] ) ) {
				$options[ $key ] = $item['name'];
			}
		}
	}

	return $options;
}



/**
 * Adapt link with option
 *
 * @param array  $link Url link social.
 * @param string $slug The slug default.
 */
function powerkit_social_links_parse_link( $link, $slug = null ) {

	$parse = array();

	if ( is_array( $link ) ) {
		foreach ( $link as $extra_name => $links ) {
			$link_type = get_option( $extra_name );
			foreach ( $links as $type => $url ) {
				if ( $link_type === $type ) {
					$link = $url;
				}
			}
		}
	}

	if ( ! is_array( $link ) ) {
		preg_match( '/%(powerkit_social_links_.*?)%/', $link, $parse );

		if ( ! $slug ) {
			$slug = get_option( $parse[1] );
		}

		// Return replaced value.
		if ( isset( $parse[0] ) && isset( $parse[1] ) ) {
			return str_replace( $parse[0], $slug, $link );
		}

		return $link;
	}

	// Return empty value.
	return '';
}


/**
 * Template appearance
 *
 * @param array $params Parameters.
 */
function powerkit_social_links_appearance( $params ) {

	$wrap_class = null;

	$params = array_merge(
		array(
			'cache'    => true,
			'maximum'  => -1,
			'template' => 'inline',
			'align'    => 'default',
			'scheme'   => 'light',
			'labels'   => true,
			'titles'   => true,
			'counts'   => true,
			'mode'     => 'mixed',
		),
		(array) $params
	);

	// Socials list.
	$links = array();

	$social_links_order = get_option( 'powerkit_social_links_order_multiple_list', array() );
	$social_links_check = get_option( 'powerkit_social_links_multiple_list', array() );

	// Sort.
	if ( $social_links_order && $social_links_check ) {
		$social_links_order = array_flip( $social_links_order );

		foreach ( $social_links_order as $key => $val ) {
			if ( in_array( $key, $social_links_check, true ) ) {
				$links[] = $key;
			}
		}
	}

	// Maximum.
	if ( isset( $params['maximum'] ) && intval( $params['maximum'] ) > 0 ) {
		$links = array_slice( $links, 0, $params['maximum'], true );
	}

	/*
	 * ---------- Build Template.
	 */

	// Template columns.
	if ( in_array( $params['template'], array( 'col-2', 'col-3', 'col-4', 'col-5', 'col-6' ), true ) ) {
		$wrap_class .= ' pk-social-links-template-columns';
	}

	// Wrap Class.
	$wrap_class .= sprintf( ' pk-social-links-template-%s', $params['template'] );

	// Align Class.
	$wrap_class .= sprintf( ' pk-social-links-align-%s', $params['align'] );

	/*
	 * ---------- Build Scheme.
	 */

	// Light Background.
	if ( 'light-bg' === $params['scheme'] ) {
		$wrap_class .= ' pk-social-links-scheme-light';
	}

	// Light Rounded.
	if ( 'light-rounded' === $params['scheme'] ) {
		$wrap_class .= ' pk-social-links-scheme-bold';
	}

	// Wrap Color scheme.
	$wrap_class .= sprintf( ' pk-social-links-scheme-%s', $params['scheme'] );

	// Wrap Class | Counts, titles, labels.
	$wrap_class .= ( $params['titles'] ) ? ' pk-social-links-titles-enabled' : ' pk-social-links-titles-disabled';
	$wrap_class .= ( $params['counts'] ) ? ' pk-social-links-counts-enabled' : ' pk-social-links-counts-disabled';
	$wrap_class .= ( $params['labels'] ) ? ' pk-social-links-labels-enabled' : ' pk-social-links-labels-disabled';

	if ( $params['counts'] ) {
		switch ( $params['mode'] ) {
			case 'php':
				$wrap_class .= ' pk-social-links-mode-php';
				break;

			case 'rest':
				$wrap_class .= ' pk-social-links-mode-rest';

				// Smart Load restapi scripts.
				add_action( 'wp_footer', 'powerkit_social_links_rest_api_scripts', 99 );
				break;
			case 'mixed':
				$wrap_class .= ' pk-social-links-mode-php';
				$wrap_class .= ' pk-social-links-mode-rest';

				// Smart Load restapi scripts.
				add_action( 'wp_footer', 'powerkit_social_links_rest_api_scripts', 99 );
				break;
		}
	}

	// Link Attributes.
	$target = get_option( 'powerkit_social_links_link_target', 'new' ) === 'new' ? '_blank' : '_self';
	$rel    = get_option( 'powerkit_social_links_nofollow' ) ? 'nofollow noopener' : '';

	// Icon prefix.
	$powerkit_social_links_icon_prefix = apply_filters( 'powerkit_social_links_icon_prefix', 'pk-icon' );
	?>
	<div class="pk-social-links-wrap <?php echo esc_attr( $wrap_class ); ?>">
		<div class="pk-social-links-items">
			<?php
			if ( $links ) {
				foreach ( $links as $item ) {
					$id = is_array( $item ) ? $item['id'] : $item;

					$title = get_option( sprintf( 'powerkit_social_links_title_%s', $id ) );
					$label = get_option( sprintf( 'powerkit_social_links_label_%s', $id ) );

					// Mode.
					$link_mode = powerkit_social_links_specific_param( $id, 'mode' );

					// Link User.
					$link = powerkit_social_links_parse_link( powerkit_social_links_specific_param( $id, 'link' ) );

					// Account Count.
					$result = array();

					if ( $params['counts'] ) {
						if ( 'rest' === $params['mode'] ) {
							$result = powerkit_social_links_get_count( $id, 'forcibly', true ); // Count User.
						} else {
							$result = powerkit_social_links_get_count( $id, $params['cache'], true ); // Count User.
						}
					}

					$class = ( ! isset( $result['count'] ) || ! $result['count'] ) ? ' pk-social-links-no-count' : '';
					?>
					<div class="pk-social-links-item pk-social-links-<?php echo esc_attr( $id ); ?> <?php echo esc_attr( $class ); ?>" data-id="<?php echo esc_attr( $id ); ?>">
						<a href="<?php echo esc_url( $link ); ?>" class="pk-social-links-link" target="<?php echo esc_attr( $target ); ?>" rel="<?php echo esc_attr( $rel ); ?>" aria-label="<?php echo esc_html( $title ); ?>">
							<i class="pk-social-links-icon <?php echo sprintf( '%2$s %2$s-%1$s', esc_attr( $id ), esc_attr( $powerkit_social_links_icon_prefix ) ); ?>"></i>
							<?php if ( $params['titles'] && $title ) { ?>
								<span class="pk-social-links-title pk-font-heading"><?php echo esc_html( $title ); ?></span>
							<?php } ?>

							<?php if ( $params['counts'] ) { ?>
								<span class="pk-social-links-count pk-font-secondary"><?php echo esc_html( isset( $result['count'] ) ? $result['count'] : 0 ); ?></span>
							<?php } ?>

							<?php if ( isset( $result['error'] ) && ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) ) { ?>
								<span class="pk-social-links-count pk-font-secondary pk-social-links-note pk-tippy">
									<span class="pk-social-links-note-icon"></span>

									<?php powerkit_alert_warning( powerkit_social_links_specific_param( $id, 'name' ) . ' : ' . $result['error'] ); ?>
								</span>
							<?php } ?>

							<?php if ( $params['labels'] && $label && ( 'counter' === $link_mode ) ) { ?>
								<span class="pk-social-links-label pk-font-secondary"><?php echo esc_html( $label ); ?></span>
							<?php } ?>
						</a>
					</div>
					<?php
				}
			} else {
				/* translators: Social Settings Link. */
				powerkit_alert_warning( sprintf( __( 'Please select social links in <object><a href="%s" target="_blank">Social Links Settings</a></object>.', 'powerkit' ), esc_url( powerkit_get_page_url( 'social_links' ) ) ) );
			}
			?>
		</div>
	</div>
	<?php
}


/**
 * Function get social links
 *
 * @param bool   $labels   Display labels.
 * @param bool   $titles   Display titles.
 * @param bool   $counts   Display counts.
 * @param string $template Template links.
 * @param string $scheme   Color scheme.
 * @param string $mode     Counter mode.
 * @param mixed  $maximum  Maximum Number of Social Links.
 */
function powerkit_social_links( $labels = true, $titles = true, $counts = true, $template = 'inline', $scheme = 'light', $mode = 'mixed', $maximum = -1 ) {
	$params = array(
		'labels'   => $labels,
		'titles'   => $titles,
		'counts'   => $counts,
		'template' => $template,
		'scheme'   => $scheme,
		'mode'     => $mode,
		'maximum'  => $maximum,
		'cache'    => true,
	);

	powerkit_social_links_appearance( $params );
}


/**
 * Add Social Links REST API Scripts
 */
function powerkit_social_links_rest_api_scripts() {
	?>
	<script type="text/javascript">
		"use strict";

		(function($) {

			$( window ).on( 'load', function() {

				// Get all links.
				var powerkitSLinksIds = [];

				var powerkitSLinksRestBox = $( '.pk-social-links-mode-rest' );

				// Generate links Ids.
				$( powerkitSLinksRestBox ).each( function( index, wrap ) {

					if ( ! $( wrap ).hasClass( 'pk-social-links-counts-disabled' ) ) {

						$( wrap ).find( '.pk-social-links-item' ).each( function() {
							if ( $( this ).attr( 'data-id' ).length > 0 ) {
								powerkitSLinksIds.push( $( this ).attr( 'data-id' ) );
							}
						});
					}
				});

				// Generate links data.
				var powerkitSLinksData = {};

				if( powerkitSLinksIds.length > 0 ) {
					powerkitSLinksData = { 'ids' : powerkitSLinksIds.join() };
				}

				// Check data.
				if ( ! Object.entries( powerkitSLinksData ).length ) {
					return;
				}

				// Get results by REST API.
				$.ajax({
					type: 'GET',
					url: '<?php echo esc_url( get_rest_url( null, '/social-counts/v1/get-counts' ) ); ?>',
					data: powerkitSLinksData,
					beforeSend: function(){

						// Add Loading Class.
						powerkitSLinksRestBox.addClass( 'pk-social-links-loading' );
					},
					success: function( response ) {

						if ( ! $.isEmptyObject( response ) && ! response.hasOwnProperty( 'code' ) ) {

							// SLinks loop.
							$.each( response, function( index, data ) {

								// Find Bsa Item.
								var powerkitSLinksItem = powerkitSLinksRestBox.find( '.pk-social-links-item[data-id="' + index + '"]');

								// Set Class.
								if ( data.hasOwnProperty( 'class' ) ) {
									powerkitSLinksItem.addClass( data.class );
								}

								// Set Count.
								if ( data.hasOwnProperty( 'result' ) && data.result !== null && data.result.hasOwnProperty( 'count' ) ) {

									if ( data.result.count ) {
										// Class Item.
										powerkitSLinksItem.removeClass( 'pk-social-links-no-count' ).addClass( 'pk-social-links-item-count' );

										// Count item.
										powerkitSLinksItem.find( '.pk-social-links-count' ).not( '.pk-tippy' ).html( data.result.count );
									}
								} else {
									powerkitSLinksItem.addClass( 'pk-social-links-no-count' );
								}

							});
						}

						// Remove Loading Class.
						powerkitSLinksRestBox.removeClass( 'pk-social-links-loading' );
					},
					error: function() {

						// Remove Loading Class.
						powerkitSLinksRestBox.removeClass( 'pk-social-links-loading' );
					}
				});
			});

		})(jQuery);
	</script>
	<?php
}


/**
 * This is our callback function that embeds our resource in a WP_REST_Response
 *
 * @param array $request REST API Request.
 */
function powerkit_social_links_restapi( $request ) {

	// Get Social Links.
	$social_social_links = apply_filters( 'powerkit_social_links_list', array() );
	$social_social_links = array_keys( $social_social_links );

	// Error, when Social Links are empty.
	if ( empty( $social_social_links ) ) {
		return rest_ensure_response(
			array(
				'code'    => 'social_links_not_found',
				'message' => esc_html__( 'Social Links not found.', 'powerkit' ),
			)
		);
	}

	// Labels.
	if ( isset( $request['labels'] ) ) {
		$labels = (bool) $request['labels'];
	} else {
		$labels = false;
	}

	// Cache Results.
	if ( isset( $request['cache'] ) ) {
		$cache = (bool) $request['cache'];
	} else {
		$cache = true;
	}

	// Get Counts.
	$link_counts = array();

	if ( isset( $request['ids'] ) ) {
		$ids = explode( ',', $request['ids'] );
		$ids = array_map( 'trim', $ids );
	} else {
		$ids = $social_social_links;
	}

	foreach ( $ids as $link_id ) {
		if ( in_array( $link_id, $social_social_links, true ) ) {

			$result = powerkit_social_links_get_count( $link_id, $cache, true ); // Count User.

			$class  = ( ! isset( $result['count'] ) || ! $result['count'] ) ? ' pk-social-links-no-count' : '';
			$class .= ( isset( $result['error'] ) ) ? ' pk-social-links-error' : '';

			$link_counts[ $link_id ] = array(
				'result' => $result,
				'class'  => $class,
			);
		}
	}

	// Return Succes Result.
	return rest_ensure_response( $link_counts );
}

/**
 * This function is where we register our routes for our example endpoint.
 */
function powerkit_social_links_register_api_routes() {

	register_rest_route(
		'social-counts/v1',
		'/get-counts',
		array(
			// By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
			'methods'             => WP_REST_Server::READABLE,
			// Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
			'callback'            => 'powerkit_social_links_restapi',
			'permission_callback' => function() {
				return true;
			},
		)
	);
}
add_action( 'rest_api_init', 'powerkit_social_links_register_api_routes' );
