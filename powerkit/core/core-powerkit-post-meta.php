<?php
/**
 * Post Meta Helper Functions
 *
 * These helper functions return post meta.
 *
 * @package    Powerkit
 * @subpackage Core
 * @version    1.0.0
 * @since      1.0.0
 */

if ( ! function_exists( 'powerkit_allowed_post_meta' ) ) {
	/**
	 * Allowed Post Meta
	 *
	 * @param bool  $compact If compact version shall be displayed.
	 * @param array $exclude Exclude list.
	 */
	function powerkit_allowed_post_meta( $compact = false, $exclude = array() ) {
		$allowed = array(
			'category' => esc_html__( 'Category', 'powerkit' ),
			'date'     => esc_html__( 'Date', 'powerkit' ),
			'author'   => esc_html__( 'Author', 'powerkit' ),
			'comments' => esc_html__( 'Comments count', 'powerkit' ),
		);

		if ( powerkit_post_views_enabled() ) {
			$allowed['views'] = esc_html__( 'Views', 'powerkit' );
		}

		if ( powerkit_module_enabled( 'reading_time' ) ) {
			$allowed['reading_time'] = esc_html__( 'Reading time', 'powerkit' );
		}

		$allowed = apply_filters( 'powerkit_allowed_post_meta', $allowed );

		// Computes the difference of arrays.
		$allowed = array_diff_key( $allowed, array_flip( (array) $exclude ) );

		// If compact.
		if ( $compact ) {
			return array_keys( $allowed );
		}

		return $allowed;
	}
}

if ( ! function_exists( 'powerkit_block_post_meta' ) ) {
	/**
	 * Block Post Meta
	 *
	 * A wrapper function that returns all post meta types either
	 * in an ordered list <ul> or as a single element <span>.
	 *
	 * @param array $settings Settings of block.
	 * @param mixed $meta     Contains post meta types.
	 * @param bool  $echo     Echo or return.
	 * @param bool  $compact  If meta compact.
	 */
	function powerkit_block_post_meta( $settings, $meta, $echo = true, $compact = false ) {

		$allowed = array();

		if ( isset( $settings['showMetaCategory'] ) && $settings['showMetaCategory'] ) {
			$allowed[] = 'category';
		}

		if ( isset( $settings['showMetaAuthor'] ) && $settings['showMetaAuthor'] ) {
			$allowed[] = 'author';
		}

		if ( isset( $settings['showMetaDate'] ) && $settings['showMetaDate'] ) {
			$allowed[] = 'date';
		}

		if ( isset( $settings['showMetaComments'] ) && $settings['showMetaComments'] ) {
			$allowed[] = 'comments';
		}

		if ( isset( $settings['showMetaViews'] ) && $settings['showMetaViews'] ) {
			$allowed[] = 'views';
		}

		if ( isset( $settings['showMetaReadingTime'] ) && $settings['showMetaReadingTime'] ) {
			$allowed[] = 'reading_time';
		}

		if ( isset( $settings['showMetaShares'] ) && $settings['showMetaShares'] ) {
			$allowed[] = 'shares';
		}

		if ( isset( $settings['metaCompact'] ) && $settings['metaCompact'] ) {
			$compact = true;
		}

		if ( ! $allowed ) {
			return;
		}

		powerkit_get_post_meta( $meta, $compact, $echo, $allowed );
	}
}

if ( ! function_exists( 'powerkit_get_post_meta' ) ) {
	/**
	 * Post Meta
	 *
	 * A wrapper function that returns all post meta types either
	 * in an ordered list <ul> or as a single element <span>.
	 *
	 * @param mixed $meta     Contains post meta types.
	 * @param bool  $compact  If compact version shall be displayed.
	 * @param bool  $echo     Echo or return.
	 * @param array $allowed  Allowed meta types.
	 */
	function powerkit_get_post_meta( $meta, $compact = false, $echo = true, $allowed = array() ) {

		$func_handler = apply_filters( 'powerkit_get_post_meta_handler', false );

		if ( $func_handler ) {
			return call_user_func( $func_handler, $meta, $compact, $echo, $allowed );
		}

		// Return if no post meta types provided.
		if ( ! $meta ) {
			return;
		}

		// Set default allowed post meta types.
		if ( ! $allowed ) {
			$allowed = powerkit_allowed_post_meta();
		}

		if ( is_array( $meta ) ) {
			// Intersect provided and allowed meta types.
			$meta = array_intersect( $allowed, $meta );
		}

		$output = '';

		if ( $meta && is_array( $meta ) ) {

			$output .= '<ul class="pk-post-meta post-meta">';

			// Add normal meta types to the list.
			foreach ( $meta as $type ) {
				$function = "powerkit_get_meta_$type";

				if ( function_exists( $function ) ) {
					$output .= $function( 'li', $compact );
				}
			}

			$output .= '</ul>';

		} else {
			if ( in_array( $meta, $allowed, true ) ) {
				// Output single meta type.
				$function = "powerkit_get_meta_$meta";

				if ( function_exists( $function ) ) {
					$output .= $function( 'div', $compact );
				}
			}
		}

		// If echo is enabled.
		if ( $echo ) {
			return call_user_func( 'printf', '%s', wp_kses( $output, 'post' ) );
		}

		return $output;
	}
}

if ( ! function_exists( 'powerkit_get_meta_date' ) ) {
	/**
	 * Post Date
	 *
	 * @param string $tag     Element tag, i.e. div or span.
	 * @param bool   $compact If compact version shall be displayed.
	 */
	function powerkit_get_meta_date( $tag = 'span', $compact = false ) {

		$output = '<' . esc_html( $tag ) . ' class="pk-meta-date meta-date">';

		if ( false === $compact ) {
			$time_string = get_the_date();
		} else {
			$time_string = get_the_date( 'd.m.y' );
		}

		$output .= apply_filters( 'powerkit_post_meta_date_output', '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>' );

		$output .= '</' . esc_html( $tag ) . '>';

		return $output;
	}
}

if ( ! function_exists( 'powerkit_get_meta_author' ) ) {
	/**
	 * Post Author
	 *
	 * @param string $tag     Element tag, i.e. div or span.
	 * @param bool   $compact If compact version shall be displayed.
	 */
	function powerkit_get_meta_author( $tag = 'span', $compact = false ) {

		$authors = array( get_the_author_meta( 'ID' ) );

		$output = '<' . esc_html( $tag ) . ' class="pk-meta-author meta-author">';

		$output .= sprintf(
			'<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			/* translators: %s: author name */
			esc_attr( sprintf( __( 'View all posts by %s', 'powerkit' ), get_the_author() ) ),
			// Author.
			get_the_author()
		);

		$output .= '</' . esc_html( $tag ) . '>';

		return $output;
	}
}

if ( ! function_exists( 'powerkit_get_meta_category' ) ) {
	/**
	 * Post Сategory
	 *
	 * @param string $tag     Element tag, i.e. div or span.
	 * @param bool   $compact If compact version shall be displayed.
	 * @param int    $post_id Post ID.
	 */
	function powerkit_get_meta_category( $tag = 'span', $compact = false, $post_id = null ) {

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$output = '<' . esc_html( $tag ) . ' class="pk-meta-category meta-category">';

		$output .= get_the_category_list( '', '', $post_id );

		$output .= '</' . esc_html( $tag ) . '>';

		return $output;
	}
}

if ( ! function_exists( 'powerkit_get_meta_comments' ) ) {
	/**
	 * Post Comments
	 *
	 * @param string $tag     Element tag, i.e. div or span.
	 * @param bool   $compact If compact version shall be displayed.
	 */
	function powerkit_get_meta_comments( $tag = 'span', $compact = false ) {
		$output = '';

		$output .= '<' . esc_html( $tag ) . ' class="pk-meta-comments meta-comments">';

		if ( true === $compact ) {
			$output .= '<i class="pk-icon pk-icon-comment"></i>';
			ob_start();
			comments_popup_link( '0', '1', '%', 'comments-link', '' );
			$output .= ob_get_clean();
		} else {
			ob_start();
			comments_popup_link( esc_html__( 'No comments', 'powerkit' ), esc_html__( 'One comment', 'powerkit' ), '% ' . esc_html__( 'comments', 'powerkit' ), 'comments-link', '' );
			$output .= ob_get_clean();
		}

		$output .= '</' . esc_html( $tag ) . '>';

		return $output;
	}
}

if ( ! function_exists( 'powerkit_get_meta_reading_time' ) ) {
	/**
	 * Post Reading Time
	 *
	 * @param string $tag     Element tag, i.e. div or span.
	 * @param bool   $compact If compact version shall be displayed.
	 */
	function powerkit_get_meta_reading_time( $tag = 'span', $compact = false ) {

		if ( ! powerkit_module_enabled( 'reading_time' ) ) {
			return;
		}

		$reading_time = powerkit_get_post_reading_time();

		$output = '<' . esc_html( $tag ) . ' class="pk-meta-reading-time meta-reading-time">';

		if ( true === $compact ) {
			$output .= '<i class="pk-icon pk-icon-watch"></i>' . intval( $reading_time ) . ' ' . esc_html( 'min', 'powerkit' );
		} else {
			/* translators: %s number of minutes */
			$output .= esc_html( sprintf( _n( '%s minute read', '%s minute read', $reading_time, 'powerkit' ), $reading_time ) );
		}

		$output .= '</' . esc_html( $tag ) . '>';

		return $output;
	}
}

if ( ! function_exists( 'powerkit_get_meta_views' ) ) {
	/**
	 * Post Reading Time
	 *
	 * @param string $tag     Element tag, i.e. div or span.
	 * @param bool   $compact If compact version shall be displayed.
	 */
	function powerkit_get_meta_views( $tag = 'span', $compact = false ) {

		switch ( powerkit_post_views_enabled() ) {
			case 'post_views':
				$views = pvc_get_post_views();
				break;
			case 'pk_post_views':
				$views = powerkit_get_post_views( null, false );
				break;
			default:
				return;
		}

		// Don't display if minimum threshold is not met.
		if ( $views < apply_filters( 'powerkit_post_minimum_views', 1 ) ) {
			return;
		}

		$output = '<' . esc_html( $tag ) . ' class="pk-meta-views meta-views">';

		$views_rounded = powerkit_get_round_number( $views );

		if ( true === $compact ) {
			$output .= '<i class="pk-icon pk-icon-eye"></i>' . esc_html( $views_rounded );
		} else {
			/* translators: %s number of post views */
			$output .= esc_html( sprintf( _n( '%s view', '%s views', $views, 'powerkit' ), $views_rounded ) );
		}

		$output .= '</' . esc_html( $tag ) . '>';

		return $output;
	}
}
