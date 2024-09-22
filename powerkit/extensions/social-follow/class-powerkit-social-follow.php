<?php
/**
 * Social Follow
 *
 * @package    Powerkit
 * @subpackage Extensions
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Social_Follow extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = 'Social Follow';
			$this->slug     = 'social_follow';
			$this->type     = 'extension';
			$this->category = 'basic';
			$this->public   = false;
			$this->enabled  = true;
		}

		/**
		 * Transient Prefix
		 *
		 * @since 1.0.0
		 * @var   string $cache_timeout Cache Timeout (minutes).
		 */
		private $trans_prefix = 'powerkit_social_follow_';

		/**
		 * Initialize module
		 */
		public function initialize() {
			// Reset cache.
			add_filter( 'powerkit_reset_cache', array( $this, 'register_reset_cache' ) );
			add_filter( 'powerkit_ajax_reset_cache', array( $this, 'register_reset_cache' ) );
		}

		/**
		 * Register Reset Cache
		 *
		 * @since    1.0.0
		 * @param    array $list Change list reset cache.
		 * @access   private
		 */
		public function register_reset_cache( $list ) {
			$slug = powerkit_get_page_slug( $this->slug );

			$list[ $slug ] = array(
				'powerkit_social_follow',
			);

			return $list;
		}

		/**
		 * Return social follow
		 *
		 * @param string $name     Name of social network.
		 * @param string $username Username of social network.
		 * @param string $type     Type of social network.
		 */
		public function get_data( $name, $username, $type = null ) {
			$social_function = sprintf( 'get_%s', $name );

			if ( ! $username ) {
				return;
			}

			if ( method_exists( $this, $social_function ) ) {
				return $this->$social_function( $username, $type );
			}
		}

		/**
		 * Get youtube data.
		 *
		 * @param string $username Username of social network.
		 * @param string $type     Type of social network.
		 */
		public function get_youtube( $username, $type = null ) {

			$result = array();

			// Get data for social network.
			$counter = new Powerkit_Links_Social_Counter();

			$counter->trans_prefix = $this->trans_prefix;

			$counter->config['youtube_slug']         = $username;
			$counter->config['youtube_channel_type'] = $type;

			$data = $counter->get_count( 'youtube', true );

			// Followers.

			// Manual Count Override.
			$count_override = get_option( 'powerkit_social_links_youtube_override' );

			if ( $count_override ) {
				$result['followers'] = $count_override;
			} elseif ( isset( $data['count'] ) && ! isset( $data['error'] ) ) {
				$result['followers'] = $data['count'];
			} elseif ( isset( $data['error'] ) ) {
				powerkit_alert_warning( $data['error'] );
			}

			// Avatar.
			if ( isset( $data['data'] ) && is_object( $data['data'] ) && isset( $data['data']->items[0]->snippet->thumbnails->default->url ) ) {
				$result['avatar_1x'] = $data['data']->items[0]->snippet->thumbnails->default->url;
			}
			if ( isset( $data['data'] ) && is_object( $data['data'] ) && isset( $data['data']->items[0]->snippet->thumbnails->medium->url ) ) {
				$result['avatar_2x'] = $data['data']->items[0]->snippet->thumbnails->medium->url;
			}

			// Username.
			if ( isset( $data['data'] ) && is_object( $data['data'] ) && isset( $data['data']->items[0]->snippet->title ) ) {
				$result['text'] = $data['data']->items[0]->snippet->title;
			}

			// Link.
			if ( 'channel' === $type ) {
				$result['link'] = sprintf( 'https://www.youtube.com/channel/%s/', $username );
			} else {
				$result['link'] = sprintf( 'https://www.youtube.com/user/%s/', $username );
			}

			return $result;
		}

		/**
		 * Get facebook data.
		 *
		 * @param string $username Username of social network.
		 * @param string $type     Type of social network.
		 */
		public function get_facebook( $username, $type = null ) {

			$result = array();

			// Get data for social network.
			$counter = new Powerkit_Links_Social_Counter();

			$counter->trans_prefix = $this->trans_prefix;

			$counter->config['facebook_user'] = $username;

			$data = $counter->get_count( 'facebook', true );

			// Followers.

			// Manual Count Override.
			$count_override = get_option( 'powerkit_social_links_facebook_override' );

			if ( $count_override ) {
				$result['followers'] = $count_override;
			} elseif ( isset( $data['count'] ) && ! isset( $data['error'] ) ) {
				$result['followers'] = $data['count'];
			} elseif ( isset( $data['error'] ) ) {
				powerkit_alert_warning( $data['error'] );
			}

			// Link.
			$result['link'] = sprintf( 'https://facebook.com/%s/', $username );

			// Avatar.
			$result['avatar_1x'] = "https://graph.facebook.com/{$username}/picture?width=80&height=80";
			$result['avatar_2x'] = "https://graph.facebook.com/{$username}/picture?width=160&height=160";

			// Username.
			$result['text'] = $username;

			return $result;
		}

		/**
		 * Get instagram data.
		 *
		 * @param string $username Username of social network.
		 * @param string $type     Type of social network.
		 */
		public function get_instagram( $username, $type = null ) {

			$result = array();
			// Get data for social network.
			$counter = new Powerkit_Links_Social_Counter();

			$counter->trans_prefix = $this->trans_prefix . md5( ( $username ) . powerkit_connect( 'instagram_app_access_token' ) );

			$counter->config['instagram_user'] = $username;

			$data = $counter->get_count( 'instagram', true );

			// Followers.

			// Manual Count Override.
			$count_override = get_option( 'powerkit_social_links_instagram_override' );

			if ( $count_override ) {
				$result['followers'] = $count_override;
			} elseif ( isset( $data['count'] ) && ! isset( $data['error'] ) ) {
				$result['followers'] = $data['count'];
			} elseif ( isset( $data['error'] ) ) {
				powerkit_alert_warning( $data['error'] );
			}

			/* Manual Override */

			if ( get_option( 'powerkit_connect_instagram_custom_followers' ) ) {
				$result['followers'] = (int) get_option( 'powerkit_connect_instagram_custom_followers' );
			}

			if ( get_option( 'powerkit_connect_instagram_custom_avatar' ) ) {
				$result['avatar_1x'] = get_option( 'powerkit_connect_instagram_custom_avatar' );
			}

			if ( get_option( 'powerkit_connect_instagram_custom_avatar_2x' ) ) {
				$result['avatar_2x'] = get_option( 'powerkit_connect_instagram_custom_avatar_2x' );
			} elseif ( get_option( 'powerkit_connect_instagram_custom_avatar' ) ) {
				$result['avatar_2x'] = get_option( 'powerkit_connect_instagram_custom_avatar' );
			}

			// Link.
			$result['link'] = sprintf( 'https://www.instagram.com/%s/', $username );

			// Username.
			$result['text'] = $username;

			return $result;
		}
	}

	/**
	 * Get social follow
	 *
	 * @param string $name     Name of social network.
	 * @param string $username Username of social network.
	 * @param string $type     Type of social network.
	 */
	function powerkit_get_social_follow( $name, $username, $type = null ) {
		$object = new Powerkit_Social_Follow();

		return $object->get_data( $name, $username, $type );
	}

	new Powerkit_Social_Follow();
}
