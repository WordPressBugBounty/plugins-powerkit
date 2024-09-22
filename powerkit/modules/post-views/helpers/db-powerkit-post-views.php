<?php
/**
 * Post Views DB
 *
 * @package    Powerkit
 * @subpackage Modules/DB
 */

class Powerkit_Post_Views_DB {

	/**
	 * Construct.
	 */
	public function __construct() {
		add_action( 'powerkit_post_views_save_options', array( $this, 'multisite_activation' ) );
		add_action( 'powerkit_plugin_activation', array( $this, 'multisite_activation' ) );
		add_action( 'powerkit_plugin_deactivation', array( $this, 'multisite_deactivation' ) );
	}

	/**
	 * Multisite activation.
	 *
	 * @global object $wpdb
	 * @param bool $networkwide The networkwide.
	 */
	public function multisite_activation( $networkwide ) {
		if ( is_multisite() && $networkwide ) {
			global $wpdb;

			$activated_blogs = array();
			$current_blog_id = $wpdb->blogid;
			$blogs_ids       = $wpdb->get_col( $wpdb->prepare( 'SELECT blog_id FROM ' . $wpdb->blogs, '' ) );

			foreach ( $blogs_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				$this->activate_single();
				$activated_blogs[] = (int) $blog_id;
			}

			switch_to_blog( $current_blog_id );

			update_site_option( 'pk_post_views_activated_blogs', $activated_blogs, array() );
		} else {
			$this->activate_single();
		}
	}

	/**
	 * Single site activation.
	 *
	 * @global array $wp_roles
	 */
	public function activate_single() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'pk_post_views';

		$charset_collate = $wpdb->get_charset_collate();

		// Required for dbdelta.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Create post views table.
		maybe_create_table( $table_name, 'CREATE TABLE ' . $table_name . ' ( id bigint unsigned NOT NULL, type tinyint(1) unsigned NOT NULL, period varchar(20) NOT NULL, count bigint unsigned NOT NULL, PRIMARY KEY (id)) ' . $charset_collate . ';' );
	}

	/**
	 * Multisite deactivation.
	 *
	 * @global array $wpdb
	 * @param bool $networkwide The networkwide.
	 */
	public function multisite_deactivation( $networkwide ) {
		if ( is_multisite() && $networkwide ) {
			global $wpdb;

			$current_blog_id = $wpdb->blogid;
			$blogs_ids       = $wpdb->get_col( $wpdb->prepare( 'SELECT blog_id FROM ' . $wpdb->blogs, '' ) );

			if ( ! ( $activated_blogs = get_site_option( 'pk_post_views_activated_blogs', false, false ) ) ) {
				$activated_blogs = array();
			}

			foreach ( $blogs_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				$this->deactivate_single( true );

				if ( in_array( (int) $blog_id, $activated_blogs, true ) ) {
					unset( $activated_blogs[ array_search( $blog_id, $activated_blogs ) ] );
				}
			}

			switch_to_blog( $current_blog_id );
			update_site_option( 'post_views_counter_activated_blogs', $activated_blogs );
		} else {
			$this->deactivate_single();
		}
	}

	/**
	 * Single site deactivation.
	 *
	 * @param bool $multi The multi.
	 */
	public function deactivate_single( $multi = false ) {
		global $wpdb;

		// Delete table from database.
		$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'pk_post_views' );
	}
}


new Powerkit_Post_Views_DB();
