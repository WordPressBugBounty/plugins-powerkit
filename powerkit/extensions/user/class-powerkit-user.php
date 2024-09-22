<?php
/**
 * Gallery
 *
 * @package    Powerkit
 * @subpackage Extensions
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_User extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = 'user';
			$this->slug     = 'user';
			$this->type     = 'extension';
			$this->category = 'basic';
			$this->public   = false;
			$this->enabled  = true;
		}

		/**
		 * Initialize module
		 */
		public function initialize() {
			// Helpers Functions for the module.
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-user.php';

			// Filters.
			add_filter( 'init', array( $this, 'powerkit_guest_support_meta' ) );
			add_filter( 'pre_get_avatar', array( $this, 'powerkit_guest_support_avatar' ), 10, 3 );
			add_filter( 'author_link', array( $this, 'powerkit_guest_support_link' ), 9999, 3 );
		}

		/**
		 * Add support meta info for guest authors.
		 */
		public function powerkit_guest_support_meta() {
			$fields = array(
				'user_url',
				'url',
				'display_name',
				'first_name',
				'last_name',
				'user_email',
				'website',
				'description',
			);

			foreach ( $fields as $field ) {
				/**
				 * Filters the value of the requested user metadata.
				 *
				 * @param string   $value    The value of the metadata.
				 * @param int      $user_id  The user ID for the value.
				 * @param int|bool $original The original user ID, as passed to the function.
				 */
				add_filter( 'get_the_author_' . $field, function( $value, $user_id, $original = null ) use ( $field ) {
					if ( powerkit_is_guest( $user_id ) && empty( $value ) ) {
						$guest_value = powerkit_get_guest_meta( $field, $user_id );
						if ( $guest_value ) {
							$value = $guest_value;
						}
					}
					return $value;
				}, 10, 3 );
			}
		}

		/**
		 * Add support avatar for guest authors.
		 *
		 * @param string $link            The URL to the author's page.
		 * @param int    $author_id       The author's id.
		 * @param string $author_nicename The author's nice name.
		 */
		public function powerkit_guest_support_link( $link, $author_id, $author_nicename ) {
			global $wp_rewrite;

			if ( powerkit_is_guest( $author_id ) ) {
				$guest = powerkit_get_guest_meta( 'user_login', $author_id );

				if ( $guest ) {
					$link = $wp_rewrite->get_author_permastruct();

					$link = str_replace( '%author%', $guest, $link );

					$link = home_url( user_trailingslashit( $link ) );
				}
			}
			return $link;
		}

		/**
		 * Add support avatar for guest authors.
		 *
		 * @param string $avatar      HTML for the user's avatar. Default null.
		 * @param mixed  $id_or_email The Gravatar to retrieve.
		 * @param array  $args        Arguments passed to get_avatar_url(), after processing.
		 */
		public function powerkit_guest_support_avatar( $avatar, $id_or_email, $args ) {
			if ( powerkit_is_guest( $id_or_email ) && empty( $avatar ) ) {
				$guest_avatar = coauthors_get_avatar( (object) array(
					'ID'   => $id_or_email,
					'type' => 'guest-author',
				), $args['size'] );

				if ( $guest_avatar ) {
					$avatar = $guest_avatar;
				}
			}
			return $avatar;
		}
	}

	new Powerkit_User();
}
