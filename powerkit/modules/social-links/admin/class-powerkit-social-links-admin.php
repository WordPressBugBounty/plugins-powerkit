<?php
/**
 * The admin-specific functionality of the module.
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Modules/Admin
 */

/**
 * The admin-specific functionality of the module.
 */
class Powerkit_Social_Links_Admin extends Powerkit_Module_Admin {
	/**
	 * Initialize
	 */
	public function initialize() {
		add_filter( 'powerkit_reset_cache', array( $this, 'register_reset_cache' ) );
		add_filter( 'powerkit_ajax_reset_cache', array( $this, 'register_reset_cache' ) );
		add_filter( 'user_contactmethods', array( $this, 'contactmethods' ), 1000, 1 );
		add_filter( 'coauthors_guest_author_fields', array( $this, 'guest_author_fields' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'register_options_page' ) );
	}

	/**
	 * Register Reset Cache
	 *
	 * @param array $list Change list reset cache.
	 */
	public function register_reset_cache( $list ) {
		$slug = powerkit_get_page_slug( $this->slug );

		$list[ $slug ] = 'powerkit_social_links_counter';

		return $list;
	}

	/**
	 * Adds custom user contact methods
	 *
	 * @param array $user_contacts array of all user contacts.
	 */
	public function contactmethods( $user_contacts ) {
		return array_merge( $user_contacts, powerkit_get_author_fields() );
	}

	/**
	 * Social Links fields in Guest Author profiles
	 *
	 * @param array $fields Fields to be returned.
	 * @param array $groups Field groups.
	 */
	public function guest_author_fields( $fields, $groups ) {
		if ( in_array( 'all', $groups, true ) || in_array( 'contact-info', $groups, true ) ) {
			foreach ( powerkit_get_author_fields() as $slug => $name ) {
				$fields[] = array(
					'key'   => $slug,
					'label' => $name,
					'group' => 'contact-info',
				);
			}
		}
		return $fields;
	}

	/**
	 * Register admin page
	 *
	 * @since 1.0.0
	 */
	public function register_options_page() {
		add_options_page( esc_html__( 'Social Links', 'powerkit' ), esc_html__( 'Social Links', 'powerkit' ), 'manage_options', powerkit_get_page_slug( $this->slug ), array( $this, 'build_options_page' ) );
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

		$this->save_options_page();
		?>
			<div class="wrap pk-social-links-wrap">
				<h1><?php esc_html_e( 'Social Links', 'powerkit' ); ?></h1>

				<div class="pk-social-links-settings">
					<?php $social_social_links = apply_filters( 'powerkit_social_links_list', array() ); ?>
					<form class="pk-social-links-basic" method="post">
						<div class="pk-social-links-tabs">
							<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
								<a class="nav-tab nav-tab-active general" href="#tab-general"><?php esc_html_e( 'General', 'powerkit' ); ?></a>

								<?php foreach ( $social_social_links as $item ) { ?>
									<a class="nav-tab <?php echo esc_attr( $item['id'] ); ?>" href="#tab-<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a>
								<?php } ?>
							</nav>

							<div id="tab-general" class="tab-wrap tab-active">
								<h2><?php esc_html_e( 'General Settings', 'powerkit' ); ?></h2>
								<table class="form-table">
									<tbody>
										<!-- Social Links -->
										<tr>
											<th scope="row"><?php esc_html_e( 'Social Links', 'powerkit' ); ?></label></th>
											<td>
												<div class="pk-social-links-source">
													<?php
													$links = $social_social_links;

													if ( $links ) {
														foreach ( $links as $key => $link ) {
															$powerkit_social_links_multiple_list = get_option( 'powerkit_social_links_multiple_list', array() );

															$checked = in_array( $key, $powerkit_social_links_multiple_list, true ) ? 'checked' : '';
															?>
															<p class="social-item"><label for="powerkit_social_links_multiple_list_<?php echo esc_attr( $key ); ?>"><input class="powerkit_social_links_multiple_list" data-item="<?php echo esc_attr( $key ); ?>" id="powerkit_social_links_multiple_list_<?php echo esc_attr( $key ); ?>" name="powerkit_social_links_multiple_list[]" type="checkbox" value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $checked ); ?>> <?php echo esc_attr( $link['name'] ); ?></label></p>
															<?php
														}
													}
													?>
												</div>
											</td>
										</tr>
										<!-- Order -->
										<tr>
											<th scope="row"><label for="powerkit_social_links_order_multiple_list"><?php esc_html_e( 'Order', 'powerkit' ); ?></label></th>
											<td>
												<ul class="social-sortable">
													<?php
													$social_links_save = get_option( 'powerkit_social_links_order_multiple_list', array() );

													$links = $social_social_links;

													// Sort.
													if ( $social_links_save && $links ) {
														$social_links_save = array_flip( $social_links_save );
														$links             = array_merge( $social_links_save, $links );
													}

													// Output.
													if ( $links ) {
														foreach ( $links as $key => $link ) {
															?>
															<li class="ui-state-default <?php echo esc_attr( $key ); ?>">
																<span class="dashicons dashicons-leftright"></span>
																<input type="text" name="powerkit_social_links_order_multiple_list[]" value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $link['name'] ); ?>
															</li>
															<?php
														}
													}
													?>
												</ul>
											</td>
										</tr>
										<!-- Link Target -->
										<tr>
											<th scope="row"><?php esc_html_e( 'Link Target', 'powerkit' ); ?></th>
											<td>
												<p><label><input class="tog" id="powerkit_social_links_link_target_same" name="powerkit_social_links_link_target" type="radio" value="same" <?php checked( get_option( 'powerkit_social_links_link_target', 'new' ), 'same' ); ?>> <?php esc_html_e( 'Open in same window', 'powerkit' ); ?></label></p>
												<p><label><input class="tog" id="powerkit_social_links_link_target_new" name="powerkit_social_links_link_target" type="radio" value="new" <?php checked( get_option( 'powerkit_social_links_link_target', 'new' ), 'new' ); ?>> <?php esc_html_e( 'Open in new window/tab', 'powerkit' ); ?></label></p>
											</td>
										</tr>
										<p></p>
										<!-- Apply "nofollow" attribute -->
										<tr>
											<th scope="row"><label class="title" for="powerkit_social_links_nofollow"><?php esc_html_e( 'Apply "nofollow" attribute', 'powerkit' ); ?></label></th>
											<td><input id="powerkit_social_links_nofollow" name="powerkit_social_links_nofollow" type="checkbox" value="true" <?php checked( (bool) get_option( 'powerkit_social_links_nofollow', true ) ); ?>></td>
										</tr>
									</tbody>
								</table>
							</div>

							<?php foreach ( $social_social_links as $item ) { ?>
								<div id="tab-<?php echo esc_attr( $item['id'] ); ?>" class="tab-wrap">
									<h2><?php echo esc_attr( powerkit_social_links_specific_param( $item['id'], 'name' ) ); ?> <?php esc_html_e( 'Settings', 'powerkit' ); ?></h2>

									<table class="form-table">
										<tbody>
											<!-- Title -->
											<tr>
												<th scope="row"><label for="powerkit_social_links_title_<?php echo esc_attr( $item['id'] ); ?>"><?php esc_html_e( 'Title', 'powerkit' ); ?></label></th>
												<td><input class="regular-text" id="powerkit_social_links_title_<?php echo esc_attr( $item['id'] ); ?>" name="powerkit_social_links_title_<?php echo esc_attr( $item['id'] ); ?>" type="text" value="<?php echo esc_attr( get_option( 'powerkit_social_links_title_' . $item['id'], powerkit_social_links_specific_param( $item['id'], 'name' ) ) ); ?>" /></td>
											</tr>
											<!-- Label -->
											<?php if ( 'counter' === powerkit_social_links_specific_param( $item['id'], 'mode' ) ) { ?>
												<tr>
													<th scope="row"><label for="powerkit_social_links_label_<?php echo esc_attr( $item['id'] ); ?>"><?php esc_html_e( 'Label', 'powerkit' ); ?></label></th>
													<td><input class="regular-text" id="powerkit_social_links_label_<?php echo esc_attr( $item['id'] ); ?>" name="powerkit_social_links_label_<?php echo esc_attr( $item['id'] ); ?>" type="text" value="<?php echo esc_attr( get_option( 'powerkit_social_links_label_' . $item['id'], powerkit_social_links_specific_param( $item['id'], 'label' ) ) ); ?>" /></td>
												</tr>
											<?php } ?>
											<!-- Render Fields -->
											<?php
											if ( isset( $item['fields'] ) && $item['fields'] ) {
												foreach ( $item['fields'] as $field_key => $field_caption ) {
													if ( is_array( $field_caption ) ) {
														?>
														<tr>
															<th scope="row"><label class="title" for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses( $field_caption['title'], 'post' ); ?></label></th>
															<td>
																<select class="regular-text" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>">
																	<?php foreach ( $field_caption['options'] as $key => $val ) { ?>
																		<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, get_option( $field_key ) ); ?>><?php echo esc_attr( $val ); ?></option>
																	<?php } ?>
																</select>
															</td>
														</tr>
														<?php
													} else {
														?>
														<tr>
															<th scope="row"><label class="title" for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses( $field_caption, 'post' ); ?></label></th>
															<td><input class="regular-text" id="<?php echo esc_attr( $field_key ); ?>" name="<?php echo esc_attr( $field_key ); ?>" type="text" value="<?php echo esc_attr( get_option( $field_key ) ); ?>" /></td>
														</tr>
														<?php
													}
												}
											}
											?>
										</tbody>
									</table>
								</div>
							<?php } ?>
						</div>

						<?php wp_nonce_field(); ?>

						<p class="submit">
							<input class="button button-primary" name="save_settings" type="submit" value="<?php esc_html_e( 'Save changes', 'powerkit' ); ?>" />
						</p>
					</form>
				</div>
			</div>
		<?php
	}

	/**
	 * Settings save
	 *
	 * @since 1.0.0
	 */
	protected function save_options_page() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'] ) ) { // Input var ok; sanitization ok.
			return;
		}

		if ( isset( $_POST['save_settings'] ) ) { // Input var ok.

			if ( isset( $_POST['powerkit_social_links_multiple_list'] ) ) { // Input var ok.
				update_option( 'powerkit_social_links_multiple_list', array_map( 'sanitize_key', (array) $_POST['powerkit_social_links_multiple_list'] ) ); // Input var ok; sanitization ok.
			} else {
				update_option( 'powerkit_social_links_multiple_list', array() );
			}
			if ( isset( $_POST['powerkit_social_links_order_multiple_list'] ) ) { // Input var ok.
				update_option( 'powerkit_social_links_order_multiple_list', array_map( 'sanitize_key', (array) $_POST['powerkit_social_links_order_multiple_list'] ) ); // Input var ok; sanitization ok.
			} else {
				update_option( 'powerkit_social_links_order_multiple_list', array() );
			}
			if ( isset( $_POST['powerkit_social_links_link_target'] ) ) { // Input var ok.
				update_option( 'powerkit_social_links_link_target', sanitize_text_field( wp_unslash( $_POST['powerkit_social_links_link_target'] ) ) ); // Input var ok.
			}
			if ( isset( $_POST['powerkit_social_links_nofollow'] ) ) { // Input var ok.
				update_option( 'powerkit_social_links_nofollow', true );
			} else {
				update_option( 'powerkit_social_links_nofollow', false );
			}

			// Save social params.
			$social_social_links = apply_filters( 'powerkit_social_links_list', array() );

			foreach ( $social_social_links as $item ) {
				$id = $item['id'];

				if ( isset( $_POST[ "powerkit_social_links_title_{$id}" ] ) ) { // Input var ok.
					update_option( "powerkit_social_links_title_{$id}", sanitize_text_field( wp_unslash( $_POST[ "powerkit_social_links_title_{$id}" ] ) ) ); // Input var ok.
				}

				if ( isset( $_POST[ "powerkit_social_links_label_{$id}" ] ) ) { // Input var ok.

					if ( 'counter' === powerkit_social_links_specific_param( $id, 'mode' ) ) {
						update_option( "powerkit_social_links_label_{$id}", sanitize_text_field( wp_unslash( $_POST[ "powerkit_social_links_label_{$id}" ] ) ) ); // Input var ok.
					}
				}

				if ( isset( $item['fields'] ) && $item['fields'] ) {
					foreach ( $item['fields'] as $field_key => $field_caption ) {
						if ( isset( $_POST[ $field_key ] ) ) { // Input var ok.
							update_option( $field_key, sanitize_text_field( wp_unslash( $_POST[ $field_key ] ) ) ); // Input var ok.
						}
					}
				}
			}

			// Reset cache.
			Powerkit_Connect::reset_cache( 'powerkit_social_links_counter' );

			printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Settings saved.', 'powerkit' ) );
		}
	}

	/**
	 * Register the stylesheets and JavaScript for the admin area.
	 *
	 * @param string $page Current page.
	 */
	public function admin_enqueue_scripts( $page ) {
		if ( 'settings_page_' . powerkit_get_page_slug( $this->slug ) === $page ) {

			wp_enqueue_script( 'jquery-ui-sortable' );

			// Styles.
			wp_enqueue_style( 'powerkit-social-links', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/admin-powerkit-social-links.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

			// Scripts.
			wp_enqueue_script( 'powerkit-social-links', plugin_dir_url( __FILE__ ) . 'js/admin-powerkit-social-links.js', array( 'jquery' ), powerkit_get_setting( 'version' ), false );
		}
	}
}
