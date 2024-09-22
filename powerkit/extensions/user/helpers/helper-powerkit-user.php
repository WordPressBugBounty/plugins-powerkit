<?php
/**
 * Helpers to empower authors and users
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Check enabled CoAuthors.
 */
function powerkit_coauthors_enabled() {
	return class_exists( 'CoAuthors_Plus' );
}

/**
 * Retrieve list of guests.
 */
function powerkit_get_guests() {

	$list = wp_cache_get( 'powerkit_get_guests' );

	if ( ! $list ) {

		$list = array();

		$query = new WP_Query();

		$guests = $query->query( array(
			'post_type'      => 'guest-author',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		) );

		foreach ( $guests as $guest ) {
			array_push( $list, $guest );
		}

		wp_cache_set( 'powerkit_get_guests', $list, 'powerkit', 1 );
	}

	return $list;
}

/**
 * Determines whether a user is a guest.
 *
 * @param int $id The id of guest.
 */
function powerkit_is_guest( $id ) {
	if ( ! powerkit_coauthors_enabled() ) {
		return false;
	}

	if ( ! is_numeric( $id ) ) {
		return false;
	}

	$guests = powerkit_get_guests();

	foreach ( $guests as $guest ) {
		if ( (int) $id === (int) $guest->ID ) {
			return true;
		}
	}
}

/**
 * Retrieve guest info by a given field.
 *
 * @param string $field The field of guest.
 * @param int    $id    The id of guest.
 */
function powerkit_get_guest_meta( $field, $id ) {
	$field = str_replace( array( 'user_url', 'url' ), 'website', $field );

	return get_post_meta( $id, 'cap-' . $field, true );
}

/**
 * Retrieve list of users and guests.
 */
function powerkit_get_users() {

	$users = wp_cache_get( 'powerkit_get_users' );

	if ( ! $users ) {
		$args = array(
			'orderby'    => 'display_name',
			'capability' => array( 'edit_posts' ),
		);

		// Capability queries were only introduced in WP 5.9.
		if ( version_compare( $GLOBALS['wp_version'], '5.9', '<' ) ) {
			$args['who'] = 'authors';
			unset( $args['capability'] );
		}

		$users = get_users( apply_filters( 'powerkit_get_users_args', $args ) );

		if ( powerkit_coauthors_enabled() ) {

			$guests = powerkit_get_guests();

			foreach ( $guests as $guest ) {
				$guest->first_name = powerkit_get_guest_meta( 'first_name', $guest->ID );
				$guest->last_name  = powerkit_get_guest_meta( 'last_name', $guest->ID );
				$guest->user_email = powerkit_get_guest_meta( 'user_email', $guest->ID );
				$guest->website    = powerkit_get_guest_meta( 'website', $guest->ID );

				$user = (object) array(
					'ID'                  => $guest->ID,
					'display_name'        => $guest->post_title,
					'user_registered'     => $guest->post_date,
					'user_login'          => $guest->first_name,
					'user_nicename'       => $guest->last_name,
					'user_email'          => $guest->user_email,
					'user_url'            => $guest->website,
					'user_pass'           => '',
					'user_activation_key' => '',
					'user_status'         => 0,
				);

				array_push( $users, $user );
			}
		}

		wp_cache_set( 'powerkit_get_users', $users, 'powerkit', 1 );
	}

	usort( $users, function( $a, $b ) {
		if ( function_exists( 'mb_strtolower' ) ) {
			return strcmp( mb_strtolower( $a->display_name ), mb_strtolower( $b->display_name ) );
		} else {
			return strcmp( strtolower( $a->display_name ), strtolower( $b->display_name ) );
		}
	} );

	return $users;
}

/**
 * Check the user's belonging to the post.
 *
 * @param int $author_id The ID of author.
 * @param int $post_id   The ID of post.
 */
function powerkit_check_post_author( $author_id, $post_id ) {

	if ( (string) get_post_field( 'post_author', $post_id ) === (string) $author_id ) {
		return true;
	}

	if ( powerkit_coauthors_enabled() ) {
		$coauthors = (array) get_coauthors();

		foreach ( $coauthors as $coauthor ) {
			if ( isset( $coauthor->ID ) && (string) $coauthor->ID === (string) $author_id ) {
				return true;
			}
		}
	}
}
