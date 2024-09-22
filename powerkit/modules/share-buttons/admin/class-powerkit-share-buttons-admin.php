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
class Powerkit_Share_Buttons_Admin extends Powerkit_Module_Admin {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'init', array( $this, 'register_options_default' ) );
		add_action( 'admin_menu', array( $this, 'register_options_page' ) );
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
			'powerkit_share_buttons_transient',
		);

		return $list;
	}

	/**
	 * Register options default
	 *
	 * @since 1.0.0
	 */
	public function register_options_default() {

		// Save options default [locations].
		$locations = apply_filters( 'powerkit_share_buttons_locations', array() );

		if ( $locations ) {
			foreach ( $locations as $key => $item ) {
				$location = $item['location'];

				add_filter(
					"default_option_powerkit_share_buttons_{$location}_display", function ( $default ) use ( $item ) {
						return isset( $item['display'] ) ? $item['display'] : $default;
					}
				);
				add_filter(
					"default_option_powerkit_share_buttons_{$location}_multiple_list", function ( $default ) use ( $item ) {
						return isset( $item['shares'] ) ? (array) $item['shares'] : $default;
					}
				);
				add_filter(
					"default_option_powerkit_share_buttons_{$location}_order_multiple_list", function ( $default ) use ( $item ) {
						return isset( $item['shares'] ) ? (array) $item['shares'] : $default;
					}
				);
				add_filter(
					"default_option_powerkit_share_buttons_{$location}_display_labels", function ( $default ) use ( $item ) {
						return isset( $item['meta']['labels'] ) ? $item['meta']['labels'] : true;
					}
				);
				add_filter(
					"default_option_powerkit_share_buttons_{$location}_display_total_share_count", function ( $default ) use ( $item ) {
						return isset( $item['display_total'] ) ? $item['display_total'] : true;
					}
				);
				add_filter(
					"default_option_powerkit_share_buttons_{$location}_display_count", function ( $default ) use ( $item ) {
						return isset( $item['display_count'] ) ? $item['display_count'] : true;
					}
				);
				add_filter(
					"default_option_powerkit_share_buttons_{$location}_title_location", function ( $default ) use ( $item ) {
						return isset( $item['title_location'] ) ? $item['title_location'] : $default;
					}
				);
				add_filter(
					"default_option_powerkit_share_buttons_{$location}_label_location", function ( $default ) use ( $item ) {
						return isset( $item['label_location'] ) ? $item['label_location'] : $default;
					}
				);
				add_filter(
					"default_option_powerkit_share_buttons_{$location}_count_location", function ( $default ) use ( $item ) {
						return isset( $item['count_location'] ) ? $item['count_location'] : $default;
					}
				);
				add_filter(
					"default_option_powerkit_share_buttons_{$location}_layout", function ( $default ) use ( $item ) {
						return isset( $item['layout'] ) ? $item['layout'] : $default;
					}
				);
				add_filter(
					"default_option_powerkit_share_buttons_{$location}_scheme", function ( $default ) use ( $item ) {
						return isset( $item['scheme'] ) ? $item['scheme'] : $default;
					}
				);
			}
		}
	}

	/**
	 * Register admin page
	 *
	 * @since 1.0.0
	 */
	public function register_options_page() {
		add_options_page( esc_html__( 'Share Buttons', 'powerkit' ), esc_html__( 'Share Buttons', 'powerkit' ), 'manage_options', powerkit_get_page_slug( $this->slug ), array( $this, 'build_options_page' ) );
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

			<div class="wrap pk-share-buttons-wrap">
				<h1><?php esc_html_e( 'Share Buttons', 'powerkit' ); ?></h1>

				<div class="pk-share-buttons-settings">
					<?php $locations = apply_filters( 'powerkit_share_buttons_locations', array() ); ?>

					<?php if ( $locations ) : ?>
						<form class="pk-share-buttons-basic" method="post">
							<div class="pk-share-buttons-tabs">
								<?php
								$locations = array_values( $locations );

								$tab = sanitize_title( isset( $_GET['tab'] ) ? $_GET['tab'] : $locations[0]['location'] ); // Input var ok; sanitization ok.
								?>

								<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
									<?php
									foreach ( $locations as $item ) {
										$class = ( $item['location'] === $tab ) ? 'nav-tab-active' : ''; // Input var ok.

										printf(
											'<a class="nav-tab %4$s" href="%1$s&tab=%2$s">%3$s</a>',
											esc_url( powerkit_get_page_url( $this->slug ) ), esc_attr( $item['location'] ), esc_html( $item['name'] ), esc_attr( $class )
										);
									}
									?>

									<a class="nav-tab nav-tab-advanced <?php echo esc_attr( 'advanced-settings' === $tab ? 'nav-tab-active' : '' ); ?>"
										href="<?php echo esc_url( powerkit_get_page_url( $this->slug ) ); ?>&tab=<?php echo esc_attr( 'advanced-settings' ); ?>">
										<?php esc_html_e( 'Advanced Settings', 'powerkit' ); ?>
									</a>
								</nav>

								<?php
								foreach ( $locations as $item ) {
									if ( $item['location'] === $tab ) { // Input var ok.
										$location = $item['location'];
										?>
									<div id="tab-<?php echo esc_attr( $location ); ?>" class="tab-wrap">
										<table class="form-table">
											<tbody>
												<!-- Display Share Buttons -->
												<tr class="visible">
													<th scope="row"><label for="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_display"><?php esc_html_e( 'Display Share Buttons', 'powerkit' ); ?></label></th>
													<td><input class="powerkit_share_buttons_display" id="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_display" name="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_display" type="checkbox" value="true" <?php checked( (bool) get_option( "powerkit_share_buttons_{$location}_display", false ) ); ?>></td>
												</tr>

												<!-- Share Buttons -->
												<tr>
													<th scope="row"><?php esc_html_e( 'Share Buttons', 'powerkit' ); ?></label></th>
													<td>
														<div class="pk-share-buttons-source">
															<?php
															$accounts = powerkit_share_buttons_get_accounts();

															if ( $accounts ) {
																foreach ( $accounts as $key => $account ) {

																	// Check support accounts for extra locations.
																	if ( 'highlight-text' === $location || 'blockquote' === $location ) {
																		$list = powerkit_share_buttons_support_text_accounts();

																		if ( ! in_array( $key, $list, true ) ) {
																			continue;
																		}
																	}

																	$powerkit_share_buttons_multiple_list = get_option( "powerkit_share_buttons_{$location}_multiple_list", array() );

																	$checked = in_array( $key, $powerkit_share_buttons_multiple_list, true ) ? 'checked' : '';
																	?>
																	<p class="share-item">
																		<label for="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_multiple_list_<?php echo esc_attr( $key ); ?>"><input class="powerkit_share_buttons_multiple_list" data-item="<?php echo esc_attr( $key ); ?>" id="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_multiple_list_<?php echo esc_attr( $key ); ?>" name="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_multiple_list[]" type="checkbox" value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $checked ); ?>> <?php echo esc_attr( $account['name'] ); ?></label>

																		<?php if ( in_array( $key, array( 'whatsapp', 'fb-messenger' ), true ) ) : ?>
																			<code class="description"><?php esc_html_e( '[mobile only]', 'powerkit' ); ?></code>
																		<?php endif; ?>
																	</p>
																	<?php
																}
															}
															?>
														</div>
													</td>
												</tr>

												<!-- Order -->
												<tr>
													<th scope="row"><label for="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_order_multiple_list"><?php esc_html_e( 'Order', 'powerkit' ); ?></label></th>
													<td>
														<ul class="social-sortable">
															<?php
															$accounts_save = get_option( "powerkit_share_buttons_{$location}_order_multiple_list", array() );

															$accounts = powerkit_share_buttons_get_accounts();

															// Sort.
															if ( $accounts_save && $accounts ) {
																$accounts_save = array_flip( $accounts_save );
																$accounts      = array_merge( $accounts_save, $accounts );
															}

															// Output.
															if ( $accounts ) {
																foreach ( $accounts as $key => $account ) {

																	// Check support accounts for extra locations.
																	if ( 'highlight-text' === $location || 'blockquote' === $location ) {
																		$list = powerkit_share_buttons_support_text_accounts();

																		if ( ! in_array( $key, $list, true ) ) {
																			continue;
																		}
																	}
																	?>
																	<li class="ui-state-default <?php echo esc_attr( $key ); ?>">
																		<span class="dashicons dashicons-leftright"></span>
																		<input type="text" name="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_order_multiple_list[]" value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $account['name'] ); ?>
																	</li>
																	<?php
																}
															}
															?>
														</ul>
													</td>
												</tr>

												<!-- Display Labels -->
												<?php if ( isset( $item['meta']['labels'] ) ? $item['meta']['labels'] : true ) : ?>
													<tr>
														<th scope="row"><label for="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_display_labels"><?php esc_html_e( 'Display Labels', 'powerkit' ); ?></label></th>
														<td><input id="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_display_labels" name="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_display_labels" type="checkbox" value="true" <?php checked( (bool) get_option( "powerkit_share_buttons_{$location}_display_labels", true ) ); ?>></td>
													</tr>
												<?php endif; ?>

												<!-- Display Total Share Count -->
												<?php if ( isset( $item['fields']['display_total'] ) && $item['fields']['display_total'] ) : ?>
													<tr>
														<th scope="row"><label for="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_display_total_share_count"><?php esc_html_e( 'Display Total Share Count', 'powerkit' ); ?></label></th>
														<td><input id="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_display_total_share_count" name="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_display_total_share_count" type="checkbox" value="true" <?php checked( (bool) get_option( "powerkit_share_buttons_{$location}_display_total_share_count", false ) ); ?>></td>
													</tr>
												<?php endif; ?>

												<!-- Display Counts -->
												<?php if ( isset( $item['fields']['display_count'] ) && $item['fields']['display_count'] ) : ?>
													<tr>
														<th scope="row"><label for="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_display_count"><?php esc_html_e( 'Display Counts', 'powerkit' ); ?></label></th>
														<td><input id="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_display_count" name="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_display_count" type="checkbox" value="true" <?php checked( (bool) get_option( "powerkit_share_buttons_{$location}_display_count", false ) ); ?>></td>
													</tr>
												<?php endif; ?>

												<!-- Title Location -->
												<?php
												if ( isset( $item['meta']['titles'] ) ? $item['meta']['titles'] : false ) {

													$title_location = isset( $item['fields']['title_locations'] ) ? (array) $item['fields']['title_locations'] : array( 'inside', 'outside' );

													if ( in_array( 'inside', $title_location, true ) && in_array( 'outside', $title_location, true ) ) {
														?>
														<tr>
															<th scope="row"><?php esc_html_e( 'Title Location', 'powerkit' ); ?></th>
															<td>
																<label>
																	<input type="radio" name="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_title_location" id="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_title_location" value="inside" <?php checked( get_option( "powerkit_share_buttons_{$location}_title_location", 'inside' ), 'inside' ); ?>>
																	<?php esc_html_e( 'Inside', 'powerkit' ); ?>&nbsp;
																</label>
																<label>
																	<input type="radio" name="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_title_location" id="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_title_location" value="outside" <?php checked( get_option( "powerkit_share_buttons_{$location}_title_location", 'inside' ), 'outside' ); ?>>
																	<?php esc_html_e( 'Outside', 'powerkit' ); ?>&nbsp;
																</label>
															</td>
														</tr>
														<?php
													}
												}
												?>

												<!-- Label Location -->
												<?php
												if ( isset( $item['meta']['labels'] ) ? $item['meta']['labels'] : true ) {

													$label_location = isset( $item['fields']['label_locations'] ) ? (array) $item['fields']['label_locations'] : array( 'inside', 'outside' );

													if ( in_array( 'inside', $label_location, true ) && in_array( 'outside', $label_location, true ) ) {
														?>
														<tr>
															<th scope="row"><?php esc_html_e( 'Label Location', 'powerkit' ); ?></th>
															<td>
																<label>
																	<input type="radio" name="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_label_location" id="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_label_location" value="inside" <?php checked( get_option( "powerkit_share_buttons_{$location}_label_location", 'inside' ), 'inside' ); ?>>
																	<?php esc_html_e( 'Inside', 'powerkit' ); ?>&nbsp;
																</label>
																<label>
																	<input type="radio" name="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_label_location" id="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_label_location" value="outside" <?php checked( get_option( "powerkit_share_buttons_{$location}_label_location", 'inside' ), 'outside' ); ?>>
																	<?php esc_html_e( 'Outside', 'powerkit' ); ?>&nbsp;
																</label>
															</td>
														</tr>
														<?php
													}
												}
												?>

												<!-- Count Location -->
												<?php
												$count_location = isset( $item['fields']['count_locations'] ) ? (array) $item['fields']['count_locations'] : array( 'inside', 'outside' );

												if ( in_array( 'inside', $count_location, true ) && in_array( 'outside', $count_location, true ) ) {
													?>
													<tr>
														<th scope="row"><?php esc_html_e( 'Count Location', 'powerkit' ); ?></th>
														<td>
															<label>
																<input type="radio" name="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_count_location" id="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_count_location" value="inside" <?php checked( get_option( "powerkit_share_buttons_{$location}_count_location", 'inside' ), 'inside' ); ?>>
																<?php esc_html_e( 'Inside', 'powerkit' ); ?>&nbsp;
															</label>
															<label>
																<input type="radio" name="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_count_location" id="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_count_location" value="outside" <?php checked( get_option( "powerkit_share_buttons_{$location}_count_location", 'inside' ), 'outside' ); ?>>
																<?php esc_html_e( 'Outside', 'powerkit' ); ?>&nbsp;
															</label>
														</td>
													</tr>
												<?php } ?>

												<!-- Layout -->
												<?php
												$layouts = powerkit_share_buttons_format_layouts( $item, $location );

												if ( count( (array) $layouts ) > 1 ) {
													?>
													<tr>
														<th scope="row"><label for="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_layout"><?php esc_html_e( 'Layout', 'powerkit' ); ?></label></th>
														<td>
															<select class="regular-text" name="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_layout" id="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_layout">
																<?php
																if ( $layouts ) {
																	foreach ( $layouts as $key => $i ) {
																		?>
																			<option value="<?php echo esc_attr( $key ); ?>" <?php selected( get_option( "powerkit_share_buttons_{$location}_layout" ), $key ); ?>><?php echo esc_attr( $i['name'] ); ?></option>
																		<?php
																	}
																}
																?>
															</select>
														</td>
													</tr>
												<?php } ?>

												<!-- Color Scheme -->
												<?php
												$schemes = powerkit_share_buttons_format_color_schemes( $item );

												if ( count( (array) $schemes ) > 1 ) {
													?>
													<tr>
														<th scope="row"><label for="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_scheme"><?php esc_html_e( 'Color Scheme', 'powerkit' ); ?></label></th>
														<td>
															<select class="regular-text" name="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_scheme" id="powerkit_share_buttons_<?php echo esc_attr( $location ); ?>_scheme">
																<?php
																if ( $schemes ) {
																	foreach ( $schemes as $key => $i ) {
																		?>
																			<option value="<?php echo esc_attr( $key ); ?>" <?php selected( get_option( "powerkit_share_buttons_{$location}_scheme" ), $key ); ?>><?php echo esc_attr( $i['name'] ); ?></option>
																		<?php
																	}
																}
																?>
															</select>
														</td>
													</tr>
												<?php } ?>
											</tbody>
										</table>

										<input type="hidden" name="<?php echo esc_attr( "powerkit_share_buttons_action_{$location}" ); ?>" value="true">
									</div>
										<?php
									}
								}
								?>

								<?php if ( 'advanced-settings' === $tab ) { ?>
									<div id="tab-advanced-settings" class="tab-wrap">
										<input type="hidden" name="advanced_settings" value="1">
										<!-- Share Labels -->
										<h3><?php esc_html_e( 'Share Labels', 'powerkit' ); ?></h3>
										<table class="form-table">
											<tbody>
												<?php
												$accounts = apply_filters( 'powerkit_share_buttons_accounts', array(), null, null );

												foreach ( $accounts as $key => $account ) {
													?>
													<tr class="visible simple">
														<th scope="row">
															<label for="powerkit_share_buttons_label_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $account['name'] ); ?></label>
														</th>
														<td>
														<input class="regular-text" id="powerkit_share_buttons_label_<?php echo esc_attr( $key ); ?>" name="powerkit_share_buttons_label_<?php echo esc_attr( $key ); ?>" type="text" value="<?php echo esc_attr( get_option( "powerkit_share_buttons_label_{$key}", $account['label'] ) ); ?>">
														</td>
													</tr>
													<?php
												}
												?>
											</tbody>
										</table>

										<hr>

										<!-- Recover social counts -->
										<h3><?php esc_html_e( 'Recover social counts', 'powerkit' ); ?></h3>
										<p class="description"><?php esc_html_e( 'If enabled, the total share count will be displayed for both http and https versions of a page', 'powerkit' ); ?></p>
										<table class="form-table">
											<tbody>
												<tr class="visible">
													<th scope="row">
														<label for="powerkit_share_buttons_recover"><?php esc_html_e( 'Enable recover counts', 'powerkit' ); ?></label>
													</th>
													<td><input class="powerkit_share_buttons_recover" id="powerkit_share_buttons_recover" name="powerkit_share_buttons_recover" type="checkbox" value="true" <?php checked( (bool) get_option( 'powerkit_share_buttons_recover', false ) ); ?>></td>
												</tr>
											</tbody>
										</table>

										<hr>

										<!-- Bitly Link Shortening -->
										<h3><?php esc_html_e( 'Bitly Link Shortening', 'powerkit' ); ?></h3>
										<p class="description"><?php esc_html_e( 'If you like to have all of your links automatically shortened, enter your Access Token from bitly.com', 'powerkit' ); ?></p>

										<div class="form-field">
											<h4><?php esc_html_e( 'Instructions', 'powerkit' ); ?></h4>
											<ol>
												<li><?php esc_html_e( 'Log in to your', 'powerkit' ); ?> <?php echo sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( 'https://bitly.com/' ), esc_html__( 'Bitly account', 'powerkit' ) ); ?></li>
												<li><?php esc_html_e( 'Go to Settings → Advanced Settings → FOR DEVELOPERS OAuth → Generic Access Token', 'powerkit' ); ?></li>
												<li><?php esc_html_e( 'Generate your Token', 'powerkit' ); ?></li>
												<li><?php esc_html_e( 'Copy your "Token"', 'powerkit' ); ?></li>
											</ol>
										</div>
										<table class="form-table">
											<tbody>
												<tr class="visible">
													<th scope="row">
														<label for="powerkit_share_buttons_bitly_api_token"><?php esc_html_e( 'Bitly API Token', 'powerkit' ); ?></label>
													</th>
													<td><input id="powerkit_share_buttons_bitly_api_token" class="regular-text" name="powerkit_share_buttons_bitly_api_token" type="text" value="<?php echo esc_attr( get_option( 'powerkit_share_buttons_bitly_api_token' ) ); ?>"></td>
												</tr>
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
					<?php else : ?>
						<p class="submit">
							<?php esc_html_e( 'No available locations found.', 'powerkit' ); ?>
						</p>
					<?php endif; ?>
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

			// Save social buttons [locations].
			$locations = apply_filters( 'powerkit_share_buttons_locations', array() );

			if ( $locations ) {
				foreach ( $locations as $key => $item ) {
					$location = $item['location'];

					if ( ! isset( $_POST[ "powerkit_share_buttons_action_{$location}" ] ) ) { // Input var ok.
						continue;
					}

					if ( isset( $_POST[ "powerkit_share_buttons_{$location}_display" ] ) ) { // Input var ok.
						update_option( "powerkit_share_buttons_{$location}_display", true );
					} else {
						update_option( "powerkit_share_buttons_{$location}_display", false );
					}

					if ( isset( $_POST[ "powerkit_share_buttons_{$location}_multiple_list" ] ) ) { // Input var ok.
						update_option( "powerkit_share_buttons_{$location}_multiple_list", array_map( 'sanitize_key', (array) $_POST[ "powerkit_share_buttons_{$location}_multiple_list" ] ) ); // Input var ok; sanitization ok.
					} else {
						update_option( "powerkit_share_buttons_{$location}_multiple_list", array() );
					}

					if ( isset( $_POST[ "powerkit_share_buttons_{$location}_order_multiple_list" ] ) ) { // Input var ok.
						update_option( "powerkit_share_buttons_{$location}_order_multiple_list", array_map( 'sanitize_key', (array) $_POST[ "powerkit_share_buttons_{$location}_order_multiple_list" ] ) ); // Input var ok; sanitization ok.
					} else {
						update_option( "powerkit_share_buttons_{$location}_order_multiple_list", array() );
					}

					if ( isset( $_POST[ "powerkit_share_buttons_{$location}_title_location" ] ) ) { // Input var ok.
						update_option( "powerkit_share_buttons_{$location}_title_location", sanitize_text_field( wp_unslash( $_POST[ "powerkit_share_buttons_{$location}_title_location" ] ) ) ); // Input var ok.
					}

					if ( isset( $_POST[ "powerkit_share_buttons_{$location}_label_location" ] ) ) { // Input var ok.
						update_option( "powerkit_share_buttons_{$location}_label_location", sanitize_text_field( wp_unslash( $_POST[ "powerkit_share_buttons_{$location}_label_location" ] ) ) ); // Input var ok.
					}

					if ( isset( $_POST[ "powerkit_share_buttons_{$location}_count_location" ] ) ) { // Input var ok.
						update_option( "powerkit_share_buttons_{$location}_count_location", sanitize_text_field( wp_unslash( $_POST[ "powerkit_share_buttons_{$location}_count_location" ] ) ) ); // Input var ok.
					}

					if ( isset( $_POST[ "powerkit_share_buttons_{$location}_layout" ] ) ) { // Input var ok.
						update_option( "powerkit_share_buttons_{$location}_layout", sanitize_text_field( wp_unslash( $_POST[ "powerkit_share_buttons_{$location}_layout" ] ) ) ); // Input var ok.
					}

					if ( isset( $_POST[ "powerkit_share_buttons_{$location}_scheme" ] ) ) { // Input var ok.
						update_option( "powerkit_share_buttons_{$location}_scheme", sanitize_text_field( wp_unslash( $_POST[ "powerkit_share_buttons_{$location}_scheme" ] ) ) ); // Input var ok.
					}

					if ( isset( $item['meta']['labels'] ) ? $item['meta']['labels'] : true ) {
						if ( isset( $_POST[ "powerkit_share_buttons_{$location}_display_labels" ] ) ) { // Input var ok.
							update_option( "powerkit_share_buttons_{$location}_display_labels", true );
						} else {
							update_option( "powerkit_share_buttons_{$location}_display_labels", false );
						}
					}

					if ( isset( $item['fields']['display_total'] ) && $item['fields']['display_total'] ) {
						if ( isset( $_POST[ "powerkit_share_buttons_{$location}_display_total_share_count" ] ) ) { // Input var ok.
							update_option( "powerkit_share_buttons_{$location}_display_total_share_count", true );
						} else {
							update_option( "powerkit_share_buttons_{$location}_display_total_share_count", false );
						}
					}

					if ( isset( $item['fields']['display_count'] ) && $item['fields']['display_count'] ) {
						if ( isset( $_POST[ "powerkit_share_buttons_{$location}_display_count" ] ) ) { // Input var ok.
							update_option( "powerkit_share_buttons_{$location}_display_count", true );
						} else {
							update_option( "powerkit_share_buttons_{$location}_display_count", false );
						}
					}
				}
			}

			// Advanced Settings.
			if ( isset( $_POST['advanced_settings'] ) ) { // Input var ok.
				$accounts = apply_filters( 'powerkit_share_buttons_accounts', array(), null, null );
				foreach ( $accounts as $key => $account ) {
					if ( isset( $_POST[ "powerkit_share_buttons_label_{$key}" ] ) ) { // Input var ok.
						update_option( "powerkit_share_buttons_label_{$key}", sanitize_text_field( wp_unslash( $_POST[ "powerkit_share_buttons_label_{$key}" ] ) ) ); // Input var ok.
					}
				}

				if ( isset( $_POST['powerkit_share_buttons_recover'] ) ) { // Input var ok.
					update_option( 'powerkit_share_buttons_recover', true );
				} else {
					update_option( 'powerkit_share_buttons_recover', false );
				}
				if ( isset( $_POST['powerkit_share_buttons_bitly_api_token'] ) ) { // Input var ok.
					update_option( 'powerkit_share_buttons_bitly_api_token', sanitize_text_field( wp_unslash( $_POST['powerkit_share_buttons_bitly_api_token'] ) ) ); // Input var ok.
				}
			}

			// Reset cache.
			Powerkit_Connect::reset_cache(
				array(
					'powerkit_share_buttons_count',
					'powerkit_share_buttons_transient',
				)
			);

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
			wp_enqueue_style( 'powerkit-share-buttons', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/admin-powerkit-share-buttons.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

			// Scripts.
			wp_enqueue_script( 'powerkit-share-buttons', plugin_dir_url( __FILE__ ) . 'js/admin-powerkit-share-buttons.js', array( 'jquery' ), powerkit_get_setting( 'version' ), false );
		}
	}
}
