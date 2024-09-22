<?php
/**
 * Post Views Query
 *
 * @package    Powerkit
 * @subpackage Modules/Query
 */

class Powerkit_Post_Views_Query {

	/**
	 * Construct.
	 */
	public function __construct() {
		add_action( 'pre_get_posts', array( $this, 'extend_pre_query' ), 1 );
		add_filter( 'posts_join', array( $this, 'posts_join' ), 10, 2 );
		add_filter( 'posts_groupby', array( $this, 'posts_groupby' ), 10, 2 );
		add_filter( 'posts_orderby', array( $this, 'posts_orderby' ), 10, 2 );
	}

	/**
	 * Extend query with post_views orderby parameter.
	 *
	 * @param object $query The query.
	 */
	public function extend_pre_query( $query ) {
		if ( isset( $query->query_vars['orderby'] ) && 'pk_post_views' === $query->query_vars['orderby'] ) {
			$query->pk_post_views = true;
		}
	}

	/**
	 * Modify the db query to use post_views parameter.
	 *
	 * @param string $join  The join.
	 * @param object $query The query.
	 */
	public function posts_join( $join, $query ) {

		if ( isset( $query->pk_post_views ) && $query->pk_post_views ) {
			global $wpdb;

			$join .= ' LEFT JOIN ' . $wpdb->prefix . 'pk_post_views ON ' . $wpdb->prefix . 'pk_post_views.id = ' . $wpdb->prefix . 'posts.ID';
		}

		return $join;
	}

	/**
	 * Group posts using the post ID.
	 *
	 * @param string $groupby The groupby.
	 * @param object $query   The query.
	 */
	public function posts_groupby( $groupby, $query ) {

		if ( isset( $query->pk_post_views ) && $query->pk_post_views ) {
			global $wpdb;

			$groupby = trim( $groupby );

			if ( false === strpos( $groupby, $wpdb->prefix . 'posts.ID' ) ) {
				$groupby = ( '' !== $groupby ? $groupby . ', ' : '' ) . $wpdb->prefix . 'posts.ID';
			}
		}

		return $groupby;
	}

	/**
	 * Order posts by post views.
	 *
	 * @param string $orderby The orderby.
	 * @param object $query   The query.
	 */
	public function posts_orderby( $orderby, $query ) {
		if ( isset( $query->pk_post_views ) && $query->pk_post_views ) {
			global $wpdb;

			$order = $query->get( 'order' );

			$orderby = $wpdb->prefix . 'pk_post_views.count ' . $order . ', ' . $wpdb->prefix . 'posts.ID ' . $order;
		}

		return $orderby;
	}
}


new Powerkit_Post_Views_Query();
