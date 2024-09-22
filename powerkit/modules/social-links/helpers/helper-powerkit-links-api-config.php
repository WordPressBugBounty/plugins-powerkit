<?php
/**
 * Social API Config
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Social API Config Class
 */
class Powerkit_Links_Api_Config {

	/**
	 * Cache Timeout
	 *
	 * @var string $cache_timeout  Cache Timeout.
	 */
	public static $cache_timeout;

	/**
	 * Config
	 *
	 * @var string $config Config List.
	 */
	public static $config = array();

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		self::$cache_timeout = (int) apply_filters( 'powerkit_social_links_cache_timeout', 60 );

		self::$config['dribbble_user']        = get_option( 'powerkit_social_links_dribbble_user' );
		self::$config['facebook_user']        = get_option( 'powerkit_social_links_facebook_user' );
		self::$config['instagram_user']       = get_option( 'powerkit_social_links_instagram_user' );
		self::$config['youtube_slug']         = get_option( 'powerkit_social_links_youtube_slug' );
		self::$config['youtube_channel_type'] = get_option( 'powerkit_social_links_youtube_channel_type' );
		self::$config['telegram_chat']        = get_option( 'powerkit_social_links_telegram_chat' );
		self::$config['pinterest_user']       = get_option( 'powerkit_social_links_pinterest_user' );
		self::$config['soundcloud_user_id']   = get_option( 'powerkit_social_links_soundcloud_user_id' );
		self::$config['vimeo_user']           = get_option( 'powerkit_social_links_vimeo_user' );
		self::$config['twitter_user']         = get_option( 'powerkit_social_links_twitter_user' );
		self::$config['behance_user']         = get_option( 'powerkit_social_links_behance_user' );
		self::$config['github_user']          = get_option( 'powerkit_social_links_github_user' );
		self::$config['vk_id']                = get_option( 'powerkit_social_links_vk_id' );
		self::$config['vk_type']              = get_option( 'powerkit_social_links_vk_type' );
		self::$config['twitch_user_id']       = get_option( 'powerkit_social_links_twitch_user_id' );
		self::$config['flickr_user_id']       = get_option( 'powerkit_social_links_flickr_user_id' );
		self::$config['snapchat_user']        = get_option( 'powerkit_social_links_snapchat_user' );
		self::$config['medium_user']          = get_option( 'powerkit_social_links_medium_user' );
		self::$config['reddit_user']          = get_option( 'powerkit_social_links_reddit_user' );
		self::$config['reddit_type']          = get_option( 'powerkit_social_links_reddit_type' );
		self::$config['strava_user']          = get_option( 'powerkit_social_links_strava_user' );
		self::$config['ok_slug']              = get_option( 'powerkit_social_links_ok_slug' );
		self::$config['ok_type']              = get_option( 'powerkit_social_links_ok_type' );
		self::$config['linkedin_slug']        = get_option( 'powerkit_social_links_linkedin_slug' );
	}
}
