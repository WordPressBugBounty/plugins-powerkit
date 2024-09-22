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
class Powerkit_Typekit_Fonts_Admin extends Powerkit_Module_Admin {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_filter( 'powerkit_fonts_register_settings', array( $this, 'register_settings' ), 20 );
	}

	/**
	 * Register settings
	 *
	 * @since 1.0.0
	 * @param array $settings List settings.
	 * @return array
	 */
	public function register_settings( $settings ) {

		$settings[] = array(
			'id'       => $this->slug,
			'name'     => esc_html__( 'Adobe Fonts (formerly Typekit)', 'powerkit' ),
			'function' => array( $this, 'build_setting_page' ),
		);

		return $settings;
	}

	/**
	 * Build admin page
	 *
	 * @since 1.0.0
	 */
	public function build_setting_page() {

		$this->save_options_page();
		$this->synchronization();
		?>
			<div class="wrap">
				<div class="col-container">
					<div id="col-left">
						<div class="col-wrap">
							<div class="form-wrap">
								<form class="wrap" method="post">
									<!-- Instructions -->
									<div class="form-field">
										<h2><?php esc_html_e( 'Instructions', 'powerkit' ); ?></h2>
										<ol>
											<li><?php esc_html_e( 'Log in to your', 'powerkit' ); ?> <?php echo sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( 'https://fonts.adobe.com/' ), esc_html__( 'Adobe Fonts account', 'powerkit' ) ); ?></li>
											<li><?php esc_html_e( 'To get your API key, go to link', 'powerkit' ); ?> <?php echo sprintf( '<a href="%1$s" target="_blank">%1$s</a>', esc_url( 'https://fonts.adobe.com/account/tokens' ) ); ?></li>
											<li><?php esc_html_e( 'Copy your "Token" under the "Your API tokens" label.', 'powerkit' ); ?></li>
										</ol>
									</div>
									<!-- Typekit Token -->
									<div class="form-field">
										<label for="powerkit_typekit_fonts_token"><?php esc_html_e( 'Token', 'powerkit' ); ?></label>
										<input name="powerkit_typekit_fonts_token" id="powerkit_typekit_fonts_token" type="text" value="<?php echo esc_attr( get_option( 'powerkit_typekit_fonts_token' ) ); ?>">
									</div>
									<!-- Kits -->
									<?php
									$token = get_option( 'powerkit_typekit_fonts_token' );

									if ( $token ) {

										$typekit = new Powerkit_Typekit_Api();

										$typekit_data = $typekit->get( null, $token );

										if ( $typekit_data && isset( $typekit_data['kits'] ) && $typekit_data['kits'] ) {
										?>
											<div class="form-field">
												<label for="powerkit_typekit_fonts_kit"><?php esc_html_e( 'Kits', 'powerkit' ); ?></label>
												<select name="powerkit_typekit_fonts_kit" id="powerkit_typekit_fonts_kit">
													<option value=""><?php esc_html_e( '- not selected -', 'powerkit' ); ?></option>
													<?php
													foreach ( $typekit_data['kits'] as $item ) :

														$data_kit = $typekit->get( $item['id'], $token );
													?>
														<option <?php selected( $item['id'], get_option( 'powerkit_typekit_fonts_kit' ) ); ?> value="<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $data_kit['kit']['name'] ); ?></option>
													<?php endforeach; ?>
												</select>
											<?php } else { ?>
												<code><?php esc_html_e( 'Invalid token or no font kits created.', 'powerkit' ); ?></code>
											<?php } ?>
										</div>
									<?php } ?>

									<?php wp_nonce_field(); ?>

									<p class="submit"><input class="button button-primary" name="save_settings" type="submit" value="<?php esc_html_e( 'Save Changes', 'powerkit' ); ?>" /></p>
								</form>

								<?php if ( isset( $typekit_data['kits'] ) && $typekit_data['kits'] ) { ?>
									<form method="post" class="form-reset">
										<?php wp_nonce_field(); ?>

										<p class="submit"><input class="button" name="powerkit_typekit_reset_cache" type="submit" value="<?php esc_html_e( 'Synchronize', 'powerkit' ); ?>" /></p>
									</form>
								<?php } ?>
							</div>
						</div>
					</div>
					<div id="col-right">
						<div class="col-wrap">
							<?php
							$typekit_kit = get_option( 'powerkit_typekit_fonts_kit' );

							if ( $token && $typekit_kit ) {
								$typekit = new Powerkit_Typekit_Api();

								$typekit_data = $typekit->get( $typekit_kit, $token );

								// If exist families.
								if ( isset( $typekit_data['kit']['families'] ) && $typekit_data['kit']['families'] ) {

									// First stack for example.
									if ( isset( $typekit_data['kit']['families']['0']['css_stack'] ) ) {
										$first_stack = $typekit_data['kit']['families']['0']['css_stack'];
									}
									// First family variant for example.
									if ( isset( $typekit_data['kit']['families']['0']['variations']['0'] ) ) {
										$first_variant = $typekit_data['kit']['families']['0']['variations']['0'];

										$first_variant = powerkit_typekit_font_convert_format( $first_variant );
									}

									// Example code.
									if ( isset( $first_stack ) && isset( $first_variant ) ) {

										$first_weight = isset( $first_variant['weight'] ) ? $first_variant['weight'] : 'normal';
										$first_style  = isset( $first_variant['style'] ) ? $first_variant['style'] : 'normal';

											$code = sprintf(
												'h1 {
													font-family: %s;
													font-weight: %s;
													font-style: %s;
												}',
												$first_stack,
												$first_weight,
												$first_style
											);

										$code = str_replace( "\t\t", '', $code );
										?>
										<div id="template">
											<h3><?php esc_html_e( 'Example of using a Typekit font', 'powerkit' ); ?></h3>

											<textarea readonly="readonly" style="height: 120px; min-height: 120px;"><?php echo wp_kses( $code, 'post' ); ?></textarea>
										</div>
									<?php } ?>

									<h3><?php esc_html_e( 'Available Fonts', 'powerkit' ); ?></h3>

									<table class="wp-list-table widefat fixed striped">
										<thead>
											<tr>
												<td scope="col" class="manage-column"><?php esc_html_e( 'Name', 'powerkit' ); ?></td>
												<td scope="col" class="manage-column"><?php esc_html_e( 'Font Family', 'powerkit' ); ?></td>
												<td scope="col" class="manage-column"><?php esc_html_e( 'Font Weight', 'powerkit' ); ?></td>
												<td scope="col" class="manage-column"><?php esc_html_e( 'Font Style', 'powerkit' ); ?></td>
											</tr>
										</thead>
										<tbody id="the-list">
											<?php
											// Loop families.
											foreach ( $typekit_data['kit']['families'] as $family ) {

												// Loop variations.
												if ( $family['variations'] ) {
													foreach ( $family['variations'] as $variant ) {
														$var_format = powerkit_typekit_font_convert_format( $variant );

														$family_weight = isset( $var_format['weight'] ) ? $var_format['weight'] : 'normal';
														$family_style  = isset( $var_format['style'] ) ? $var_format['style'] : 'normal';
														$family_slug   = isset( $family['css_names'][0] ) ? $family['css_names'][0] : $family['slug'];
														?>
														<tr>
															<td scope="col" class="manage-column"><?php echo esc_html( $family['name'] ); ?></td>
															<td scope="col" class="manage-column"><?php echo esc_html( $family_slug ); ?></td>
															<td scope="col" class="manage-column"><?php echo esc_html( $family_weight ); ?></td>
															<td scope="col" class="manage-column"><?php echo esc_html( $family_style ); ?></td>
														</tr>
														<?php
													}
												}
											}
											?>
										</tbody>
									</table>
									<?php
								} elseif ( isset( $typekit_data['kit'] ) ) {
									?>
										<code><?php esc_html_e( 'No fonts found. Please add fonts to your Typekit font kit.', 'powerkit' ); ?></code>
									<?php
								}
							}
							?>
						</div>
					</div>
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

			$this->synchronization( true );

			if ( isset( $_POST['powerkit_typekit_fonts_token'] ) ) { // Input var ok.
				update_option( 'powerkit_typekit_fonts_token', sanitize_text_field( wp_unslash( $_POST['powerkit_typekit_fonts_token'] ) ) ); // Input var ok.
			}
			if ( isset( $_POST['powerkit_typekit_fonts_kit'] ) ) { // Input var ok.
				update_option( 'powerkit_typekit_fonts_kit', sanitize_text_field( wp_unslash( $_POST['powerkit_typekit_fonts_kit'] ) ) ); // Input var ok.
			}
			printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Settings saved.', 'powerkit' ) );
		}
	}

	/**
	 * Synchronization TypeKit
	 *
	 * @param boool $forcibly Forcibly Synchronization.
	 */
	protected function synchronization( $forcibly = false ) {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'] ) ) { // Input var ok; sanitization ok.
			return;
		}

		if ( isset( $_POST['powerkit_typekit_reset_cache'] ) || $forcibly ) { // Input var ok.
			global $wpdb;

			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'pk_typekit_%%'" ); // db call ok; no-cache ok.

			printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Font kits synchronized.', 'powerkit' ) );
		}
	}
}
