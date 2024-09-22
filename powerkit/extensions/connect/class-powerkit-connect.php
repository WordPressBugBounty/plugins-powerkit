<?php
/**
 * Connect
 *
 * @package    Powerkit
 * @subpackage Extensions
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Connect extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = 'connect';
			$this->slug     = 'connect';
			$this->type     = 'extension';
			$this->category = 'basic';
			$this->public   = false;
			$this->enabled  = true;
		}

		/**
		 * Initialize module
		 */
		public function initialize() {
			add_action( 'admin_menu', array( $this, 'register_options_page' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_ajax_powerkit_reset_cache', array( $this, 'ajax_reset_cache' ) );
			add_action( 'wp_ajax_nopriv_powerkit_reset_cache', array( $this, 'ajax_reset_cache' ) );
		}

		/**
		 * Register admin page
		 *
		 * @since 1.0.0
		 */
		public function register_options_page() {
			add_options_page( esc_html__( 'Connect', 'powerkit' ), esc_html__( 'Connect', 'powerkit' ), 'manage_options', powerkit_get_page_slug( $this->slug ), array( $this, 'build_options_page' ) );
		}

		/**
		 * Build admin page
		 *
		 * @since 1.0.0
		 */
		public function build_options_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient rights to view this page.', 'powerkit' ) );
			}
			$this->logout_account();
			$this->save_options_page();
			?>

				<div class="wrap">
					<h1><?php esc_html_e( 'Connect', 'powerkit' ); ?></h1>

					<div class="settings">
						<?php $connect_list = apply_filters( 'powerkit_register_connect_list', array() ); ?>

						<?php if ( $connect_list ) : ?>
							<div class="tabs">
								<?php
								$connect_list = array_values( $connect_list );

								$tab = sanitize_title( isset( $_GET['tab'] ) ? $_GET['tab'] : $connect_list[0]['id'] ); // Input var ok; sanitization ok.
								?>

								<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
									<?php
									foreach ( $connect_list as $item ) {
										$class = ( $item['id'] === $tab ) ? 'nav-tab-active' : ''; // Input var ok.
										printf(
											'<a class="nav-tab %4$s" href="%1$s&tab=%2$s">%3$s</a>',
											esc_url( powerkit_get_page_url( $this->slug ) ),
											esc_attr( $item['id'] ),
											esc_html( $item['name'] ),
											esc_attr( $class )
										);
									}
									?>
								</nav>

								<?php
								foreach ( $connect_list as $item ) {
									// Instagram tab.
									if ( 'instagram' === $item['id'] && $item['id'] === $tab ) { // Input var ok.
										?>
											<div id="tab-<?php echo esc_attr( $item['id'] ); ?>" class="tab-wrap">
												<?php $this->instagram_custom_tab( $tab ); ?>
											</div>
										<?php
										// Facebook tab.
									} elseif ( 'facebook' === $item['id'] && $item['id'] === $tab ) { // Input var ok.
										?>
											<div id="tab-<?php echo esc_attr( $item['id'] ); ?>" class="tab-wrap">
												<?php $this->facebook_custom_tab( $tab ); ?>
											</div>
										<?php
										// Twitter tab.
									} elseif ( 'twitter' === $item['id'] && $item['id'] === $tab ) { // Input var ok.
										?>
											<div id="tab-<?php echo esc_attr( $item['id'] ); ?>" class="tab-wrap">
												<?php $this->twitter_custom_tab( $tab ); ?>
											</div>
										<?php
									} elseif ( $item['id'] === $tab ) {
										?>
										<form class="basic" method="post">
											<div id="tab-<?php echo esc_attr( $item['id'] ); ?>" class="tab-wrap">
												<!-- Render Fields -->
												<?php
												if ( isset( $item['fields'] ) && $item['fields'] ) {
													?>
													<table class="form-table">
														<tbody>
															<?php
															foreach ( $item['fields'] as $field ) {
																?>
																	<tr>
																		<th scope="row"><label class="title" for="<?php echo esc_attr( $field['key'] ); ?>"><?php echo esc_html( $field['caption'] ); ?></label></th>
																		<td>
																			<input class="regular-text" id="<?php echo esc_attr( $field['key'] ); ?>" name="<?php echo esc_attr( $field['key'] ); ?>" type="text" value="<?php echo esc_attr( powerkit_connect( $field['key'] ) ); ?>" />

																			<?php if ( isset( $field['instruction'] ) ) { ?>
																				<p><?php echo wp_kses_post( $field['instruction'] ); ?></p>
																			<?php } ?>
																		</td>
																	</tr>
																<?php
															}
															?>
														</tbody>
													</table>
													<?php
												}
												?>
											</div>

											<?php wp_nonce_field(); ?>

											<p class="submit">
												<input class="button button-primary" name="save_settings" type="submit" value="<?php esc_html_e( 'Save changes', 'powerkit' ); ?>" />
											</p>
										</form>
										<?php
									}
								}
								?>
							</div>
						<?php else : ?>
							<p class="submit">
								<?php esc_html_e( 'The list of social network settings is empty!!!', 'powerkit' ); ?>
							</p>
						<?php endif; ?>
					</div>
				</div>

				<style>
				.tab-badge-success {
					display: inline-block;
					border: 1px solid #46b450;
					padding: 0.5rem 0.75rem;
					color: #32373c;
					font-weight: 600;
					border-radius: 5px;
					margin-top: 1rem;
				}
				</style>
			<?php
		}

		/**
		 * Instagram custom tab
		 *
		 * @param string $tab The name of tab.
		 */
		public function instagram_custom_tab( $tab ) {
			$this->msg_disabled_api();

			require_once plugin_dir_path( __FILE__ ) . 'tab-instagram.php';
		}

		/**
		 * Facebook custom tab
		 *
		 * @param string $tab The name of tab.
		 */
		public function facebook_custom_tab( $tab ) {
			$this->msg_disabled_api();

			require_once plugin_dir_path( __FILE__ ) . 'tab-facebook.php';
		}

		/**
		 * Twitter custom tab
		 *
		 * @param string $tab The name of tab.
		 */
		public function twitter_custom_tab( $tab ) {
			$this->msg_disabled_api();

			require_once plugin_dir_path( __FILE__ ) . 'tab-twitter.php';
		}

		/**
		 * Output message for disabled api
		 */
		public function msg_disabled_api() {
			?>
			<br>
			<div class="notice notice-error inline">
				<p>
					<?php
					/* translators: %s: link connect */
					echo sprintf( 'Automatic sync has been disabled on July 16, 2023. Please <a target="_blank" href="%s">read more</a> for details.', 'https://codesupply.co/social-integrations/' );
					?>
				</p>
			</div>
			<?php
		}

		/**
		 * Settings save
		 */
		public function save_options_page() {
			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'] ) ) { // Input var ok; sanitization ok.
				return;
			}

			if ( isset( $_POST['save_settings'] ) ) { // Input var ok.
				// Save social params.
				$connect_list = apply_filters( 'powerkit_register_connect_list', array() );
				foreach ( $connect_list as $item ) {
					$id = $item['id'];
					if ( isset( $item['fields'] ) && $item['fields'] ) {
						foreach ( $item['fields'] as $field ) {
							if ( isset( $_POST[ $field['key'] ] ) ) { // Input var ok.
								update_option( $field['key'], sanitize_text_field( wp_unslash( $_POST[ $field['key'] ] ) ) ); // Input var ok.
							}
						}
					}
				}

				self::reset_cache();

				printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'The settings are saved.', 'powerkit' ) );
			}

			if ( isset( $_POST['save_instagram_settings'] ) ) { // Input var ok.
				if ( isset( $_POST['powerkit_connect_instagram_username'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_instagram_username', sanitize_text_field( wp_unslash( $_POST['powerkit_connect_instagram_username'] ) ) ); // Input var ok.
				}
				if ( isset( $_POST['powerkit_connect_instagram_custom_name'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_instagram_custom_name', sanitize_text_field( wp_unslash( $_POST['powerkit_connect_instagram_custom_name'] ) ) ); // Input var ok.
				}
				if ( isset( $_POST['powerkit_connect_instagram_following'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_instagram_following', sanitize_text_field( wp_unslash( $_POST['powerkit_connect_instagram_following'] ) ) ); // Input var ok.
				}
				if ( isset( $_POST['powerkit_connect_instagram_custom_followers'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_instagram_custom_followers', sanitize_text_field( wp_unslash( $_POST['powerkit_connect_instagram_custom_followers'] ) ) ); // Input var ok.
				}
				if ( isset( $_POST['powerkit_connect_instagram_custom_avatar'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_instagram_custom_avatar', sanitize_text_field( wp_unslash( $_POST['powerkit_connect_instagram_custom_avatar'] ) ) ); // Input var ok.
				}
				if ( isset( $_POST['powerkit_connect_instagram_custom_avatar_2x'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_instagram_custom_avatar_2x', sanitize_text_field( wp_unslash( $_POST['powerkit_connect_instagram_custom_avatar_2x'] ) ) ); // Input var ok.
				}
				if ( isset( $_POST['powerkit_connect_instagram_feed'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_instagram_feed', $_POST['powerkit_connect_instagram_feed'] ); // Input var ok.
				} else {
					delete_option( 'powerkit_connect_instagram_feed' );
				}

				printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'The settings are saved.', 'powerkit' ) );
			}

			if ( isset( $_POST['save_facebook_settings'] ) ) { // Input var ok.
				if ( isset( $_POST['powerkit_connect_facebook_app_id'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_facebook_app_id', sanitize_text_field( wp_unslash( $_POST['powerkit_connect_facebook_app_id'] ) ) ); // Input var ok.
				}

				printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'The settings are saved.', 'powerkit' ) );
			}

			if ( isset( $_POST['save_twitter_settings'] ) ) { // Input var ok.
				if ( isset( $_POST['powerkit_connect_twitter_username'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_twitter_username', sanitize_text_field( wp_unslash( $_POST['powerkit_connect_twitter_username'] ) ) ); // Input var ok.
				}
				if ( isset( $_POST['powerkit_connect_twitter_custom_name'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_twitter_custom_name', sanitize_text_field( wp_unslash( $_POST['powerkit_connect_twitter_custom_name'] ) ) ); // Input var ok.
				}
				if ( isset( $_POST['powerkit_connect_twitter_following'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_twitter_following', sanitize_text_field( wp_unslash( $_POST['powerkit_connect_twitter_following'] ) ) ); // Input var ok.
				}
				if ( isset( $_POST['powerkit_connect_twitter_custom_followers'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_twitter_custom_followers', sanitize_text_field( wp_unslash( $_POST['powerkit_connect_twitter_custom_followers'] ) ) ); // Input var ok.
				}
				if ( isset( $_POST['powerkit_connect_twitter_custom_avatar'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_twitter_custom_avatar', sanitize_text_field( wp_unslash( $_POST['powerkit_connect_twitter_custom_avatar'] ) ) ); // Input var ok.
				}
				if ( isset( $_POST['powerkit_connect_twitter_custom_avatar_2x'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_twitter_custom_avatar_2x', sanitize_text_field( wp_unslash( $_POST['powerkit_connect_twitter_custom_avatar_2x'] ) ) ); // Input var ok.
				}
				if ( isset( $_POST['powerkit_connect_twitter_feed'] ) ) { // Input var ok.
					update_option( 'powerkit_connect_twitter_feed', $_POST['powerkit_connect_twitter_feed'] ); // Input var ok.
				} else {
					delete_option( 'powerkit_connect_twitter_feed' );
				}

				printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'The settings are saved.', 'powerkit' ) );
			}
		}

		/**
		 * Logout account
		 */
		public function logout_account() {
			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'] ) ) { // Input var ok; sanitization ok.
				return;
			}

			if ( isset( $_POST['logout_account'] ) ) { // Input var ok.

				// Logout Instagram.
				if ( isset( $_POST['logout_account_type'] ) && 'instagram' === $_POST['logout_account_type'] ) {
					delete_option( 'powerkit_connect_instagram_app_type' );
					delete_option( 'powerkit_connect_instagram_app_access_token' );
					delete_option( 'powerkit_connect_instagram_app_user_id' );
					delete_option( 'powerkit_connect_instagram_app_username' );
					delete_option( 'powerkit_connect_instagram_app_refresh_time' );
				}

				// Logout Facebook.
				if ( isset( $_POST['logout_account_type'] ) && 'facebook' === $_POST['logout_account_type'] ) {
					delete_option( 'powerkit_connect_facebook_app_access_token' );
					delete_option( 'powerkit_connect_facebook_app_accounts' );
				}

				// Logout Facebook.
				if ( isset( $_POST['logout_account_type'] ) && 'twitter' === $_POST['logout_account_type'] ) {
					delete_option( 'powerkit_connect_twitter_app_consumer_key' );
					delete_option( 'powerkit_connect_twitter_app_consumer_secret' );
					delete_option( 'powerkit_connect_twitter_app_user_id' );
					delete_option( 'powerkit_connect_twitter_app_screen_name' );
					delete_option( 'powerkit_connect_twitter_app_oauth_token' );
					delete_option( 'powerkit_connect_twitter_app_oauth_token_secret' );
				}

				if ( isset( $_POST['logout_account_type'] ) ) {
					$this->location_reset_cache( $_POST['logout_account_type'] );
				}

				printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Account disabled successfully.', 'powerkit' ) );
			}
		}

		/**
		 * Tab reset cache
		 *
		 * @param string $slug Slug of tab.
		 */
		public function location_reset_cache( $slug ) {

			// Base.
			$list = array(
				'powerkit_social_follow',
				'powerkit_social_links_counter',
			);

			// Instagram.
			if ( 'instagram' === $slug ) {
				$list = array_merge(
					$list,
					array(
						'powerkit_instagram_data',
						'powerkit_instagram_recent',
					)
				);

			}

			// Twitter.
			if ( 'twitter' === $slug ) {
				$list = array_merge(
					$list,
					array(
						'powerkit_twitter_data',
						'powerkit_twitter_block_cache',
						'powerkit_twitter_shortcode_cache',
						'powerkit_twitter_widget_cache',
					)
				);
			}

			self::reset_cache( $list );
		}

		/**
		 * Ajax Reset cache
		 */
		public function ajax_reset_cache() {
			powerkit_uuid_hash();

			// Check wpnonce.
			if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'] ) ) { // Input var ok; sanitization ok.
				return;
			}

			$list = apply_filters( 'powerkit_ajax_reset_cache', array() );

			if ( ! isset( $_REQUEST['page'] ) ) { // Input var ok.
				return false;
			}

			$page = sanitize_key( $_REQUEST['page'] ); // Input var ok; sanitization ok.

			if ( ! isset( $list[ $page ] ) ) {
				return false;
			}

			self::reset_cache( $list[ $page ] );

			die();
		}

		/**
		 * Reset cache
		 *
		 * @param array $list Reset list.
		 */
		public static function reset_cache( $list = false ) {
			if ( is_array( $list ) ) {

				$list = $list;

			} elseif ( is_string( $list ) && $list ) {

				$list = explode( ' ', $list );

			} else {
				$list = apply_filters( 'powerkit_reset_cache', array() );

				$puck = array();

				foreach ( $list as $item ) {
					if ( is_array( $item ) ) {
						$puck = array_merge( $puck, $item );
					} elseif ( is_string( $item ) ) {
						$puck = array_merge( $puck, explode( ' ', $item ) );
					}
				}

				$list = $puck;
			}

			if ( $list ) {
				global $wpdb;

				foreach ( $list as $option_name ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name LIKE '%%%s%%'", $option_name ) ); // db call ok; no-cache ok.
					$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '%%%s%%'", $option_name ) ); // db call ok; no-cache ok.
				}

				printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Cache purged.', 'powerkit' ) );
			}
		}

		/**
		 * Register the stylesheets and JavaScript for the admin area.
		 *
		 * @param string $page Current page.
		 */
		public function enqueue_scripts( $page ) {
			if ( is_customize_preview() || 'toplevel_page_powerkit_manager' === $page || 'settings_page_powerkit_connect' === $page ) {

				wp_enqueue_script( 'jquery-ui-sortable' );

				wp_enqueue_style( 'admin-powerkit-connect', plugin_dir_url( __FILE__ ) . 'css/admin-powerkit-connect.css', array(), powerkit_get_setting( 'version' ), false );
				wp_enqueue_script( 'admin-powerkit-connect', plugin_dir_url( __FILE__ ) . 'js/admin-powerkit-connect.js', array( 'jquery' ), powerkit_get_setting( 'version' ), false );

				wp_localize_script(
					'admin-powerkit-connect',
					'pk_connect',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
					)
				);
			}
		}
	}

	new Powerkit_Connect();
}
