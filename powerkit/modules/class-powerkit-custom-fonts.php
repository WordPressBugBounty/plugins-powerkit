<?php
/**
 * Custom Fonts
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Custom_Fonts extends Powerkit_Module {

		/**
		 * Webfonts method
		 *
		 * @var string $load_method Webfonts method.
		 */
		public $load_method = 'async';

		/**
		 * Font base.
		 *
		 * This is used in case of Elementor's Font param
		 *
		 * @var string
		 */
		private static $font_base = 'pk-custom-fonts';

		/**
		 * Register module
		 */
		public function register() {
			$this->name            = esc_html__( 'Custom Fonts', 'powerkit' );
			$this->desc            = esc_html__( 'Adds the ability to download custom fonts.', 'powerkit' );
			$this->slug            = 'custom_fonts';
			$this->type            = 'default';
			$this->category        = 'basic';
			$this->priority        = 1040;
			$this->public          = true;
			$this->enabled         = false;
			$this->badge           = esc_html__( 'Advanced', 'powerkit' );
			$this->load_extensions = array(
				'fonts',
			);
			$this->links           = array(
				array(
					'name' => esc_html__( 'Go to settings', 'powerkit' ),
					'url'  => powerkit_get_page_url( 'fonts&tab=' . $this->slug, 'themes' ),
				),
				array(
					'name'   => esc_html__( 'View documentation', 'powerkit' ),
					'url'    => powerkit_get_setting( 'documentation' ) . '/content-presentation/custom-fonts/',
					'target' => '_blank',
				),
			);
		}

		/**
		 * Initialize module
		 */
		public function initialize() {
			add_filter( 'powerkit_fonts_list', array( $this, 'custom_fonts' ), 10 );
			add_filter( 'csco_customizer_fonts_choices', array( $this, 'csco_custom_fonts' ), 20 );
			add_filter( 'upload_mimes', array( $this, 'allow_mimes' ) );
			add_filter( 'powerkit_fonts_register_settings', array( $this, 'register_settings' ), 10 );
			add_action( 'customize_controls_print_styles', array( $this, 'frontend_enqueue' ), 100 );
			add_action( 'wp_head', array( $this, 'frontend_enqueue' ), 100 );
			add_action( 'admin_head', array( $this, 'editor_enqueue' ), 100 );
			add_filter( 'init', array( $this, 'set_load_method' ) );
			add_filter( 'elementor/fonts/groups', array( $this, 'elementor_group' ) );
			add_filter( 'elementor/fonts/additional_fonts', array( $this, 'add_elementor_fonts' ) );
		}

		/**
		 * Set Webfonts method
		 */
		public function set_load_method() {
			$this->load_method = apply_filters( 'powerkit_webfonts_load_method', 'async' );
		}

		/**
		 * Add custom fonts
		 *
		 * @since 1.0.0
		 * @param array $fonts List fonts.
		 * @return array
		 */
		public function custom_fonts( $fonts ) {

			if ( is_customize_preview() ) {

				$custom_fonts = get_option( 'powerkit_custom_fonts_list' );

				if ( is_array( $custom_fonts ) && $custom_fonts ) {

					$exclude = array();

					$fonts['families']['custom_fonts'] = array(
						'text'     => esc_html__( 'Custom Fonts', 'powerkit' ),
						'children' => array(),
					);

					foreach ( $custom_fonts as $key => $item ) {
						if ( 'clone' === $key ) {
							continue;
						}
						$id = sanitize_title( $item['name'] );

						if ( $id ) {
							if ( ! in_array( $id, $exclude, true ) ) {
								$fonts['families']['custom_fonts']['children'][] = array(
									'id'   => $id,
									'text' => $item['name'],
								);

								$exclude[] = $id;
							}

							$variant = str_replace( '400', 'regular', $item['weight'] );

							if ( 'italic' === $item['style'] ) {
								$variant .= 'italic';
							}

							if ( isset( $fonts['variants'][ $id ] ) && $fonts['variants'][ $id ] ) {
								if ( ! in_array( $variant, $fonts['variants'][ $id ], true ) ) {
									$fonts['variants'][ $id ][] = $variant;
								}
							} else {
								$fonts['variants'][ $id ][] = $variant;
							}
						}
					}
				}
			}

			return $fonts;
		}

		/**
		 * Add custom fonts to csco theme
		 *
		 * @since 1.0.0
		 * @param array $fonts List fonts.
		 * @return array
		 */
		public function csco_custom_fonts( $fonts ) {

			if ( is_customize_preview() ) {

				$custom_fonts = get_option( 'powerkit_custom_fonts_list' );

				if ( is_array( $custom_fonts ) && $custom_fonts ) {

					$exclude = array();

					$fonts['fonts']['families']['custom_fonts'] = array(
						'text'     => esc_html__( 'Custom Fonts', 'powerkit' ),
						'children' => array(),
					);

					foreach ( $custom_fonts as $key => $item ) {
						if ( 'clone' === $key ) {
							continue;
						}
						$id = sanitize_title( $item['name'] );

						if ( $id ) {
							if ( ! in_array( $id, $exclude, true ) ) {
								$fonts['fonts']['families']['custom_fonts']['children'][] = array(
									'id'   => $id,
									'text' => $item['name'],
								);

								$exclude[] = $id;
							}

							$variant = str_replace( '400', 'regular', $item['weight'] );

							if ( 'italic' === $item['style'] ) {
								$variant .= 'italic';
							}

							if ( isset( $fonts['fonts']['variants'][ $id ] ) && $fonts['fonts']['variants'][ $id ] ) {
								if ( ! in_array( $variant, $fonts['fonts']['variants'][ $id ], true ) ) {
									$fonts['fonts']['variants'][ $id ][] = $variant;
								}
							} else {
								$fonts['fonts']['variants'][ $id ][] = $variant;
							}
						}
					}
				}
			}

			return $fonts;
		}

		/**
		 * Add Custom Font group to elementor font list.
		 *
		 * Group name "Custom" is added as the first element in the array.
		 *
		 * @param  Array $font_groups default font groups in elementor.
		 * @return Array              Modified font groups with newly added font group.
		 */
		public function elementor_group( $font_groups ) {
			$new_group[ self::$font_base ] = esc_html__( 'Custom Fonts', 'powerkit' );

			$font_groups = $new_group + $font_groups;

			return $font_groups;
		}

		/**
		 * Add Custom Fonts to the Elementor Page builder's font param.
		 *
		 * @param Array $fonts Custom Font's array.
		 */
		public function add_elementor_fonts( $fonts ) {

			$fonts_list = get_option( 'powerkit_custom_fonts_list' );

			if ( is_array( $fonts_list ) && $fonts_list ) {
				foreach ( $fonts_list as $item ) {
					$id = sanitize_title( $item['name'] );

					if ( $id ) {
						$fonts[ $id ] = self::$font_base;
					}
				}
			}

			return $fonts;
		}

		/**
		 * Register new mime types and file extensions.
		 *
		 * @since    1.0.0
		 * @param array $mimes Current array of mime types..
		 */
		public function allow_mimes( $mimes ) {
			$mimes['woff']  = 'application/x-font-woff';
			$mimes['woff2'] = 'application/x-font-woff2';

			return $mimes;
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
				'name'     => esc_html__( 'Custom Fonts', 'powerkit' ),
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

			powerkit_uuid_hash();

			$this->save_options_page();

			$powerkit_custom_fonts_id = get_option( 'powerkit_custom_fonts_counter', 1 );

			$params = array(
				'name'       => '',
				'weight'     => '400',
				'style'      => 'normal',
				'file_woff'  => '',
				'file_woff2' => '',
			);

			$action = 'new';

			// Check wpnonce.
			if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'] ) ) { // Input var ok; sanitization ok.
				return;
			}

			// Check custom action.
			if ( isset( $_REQUEST['action'] ) ) { // Input var ok; sanitization ok.
				$action = sanitize_title( $_REQUEST['action'] ); // Input var ok; sanitization ok.

				if ( isset( $_REQUEST['powerkit_custom_fonts_id'] ) && 'edit' === $action ) { // Input var ok.
					$powerkit_custom_fonts_id = sanitize_key( $_REQUEST['powerkit_custom_fonts_id'] ); // Input var ok.
				}
			}

			// Edit font.
			if ( 'edit' === $action ) {
				$font_list = get_option( 'powerkit_custom_fonts_list' );

				if ( isset( $font_list[ $powerkit_custom_fonts_id ] ) ) {
					$params = array_merge( (array) $params, (array) $font_list[ $powerkit_custom_fonts_id ] );
				}
			}

			// Delete font.
			if ( 'delete' === $action ) {
				$this->delete_font();
			}

			// Settings page link.
			$powerkit_custom_fonts_link = powerkit_get_page_url( 'fonts&tab=' . $this->slug, 'themes' );
			?>
			<form method="post" action="<?php echo esc_url( $powerkit_custom_fonts_link ); ?>">
				<div class="col-container">
					<div id="col-left">
						<div class="col-wrap">
							<div class="form-wrap">
								<div class="form-field">
									<h2><?php esc_html_e( 'Instructions', 'powerkit' ); ?></h2>
									<ol>
										<li>
											<?php
												$link_webfont = sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( 'https://www.fontsquirrel.com/tools/webfont-generator' ), esc_html__( 'Font Squirrel', 'powerkit' ) );

												// translators: args - format woff, format woff, format woff2, link webfont-generator.
												echo sprintf( esc_html__( 'Prepare your webfont files in %1$s and %2$s formats. You may convert your %3$s fonts at %4$s.', 'powerkit' ), '<code>woff</code>', '<code>woff2</code>', '<code>ttf</code>', wp_kses( $link_webfont, 'post' ) );
											?>
										</li>
										<li><?php esc_html_e( 'Upload your font files.', 'powerkit' ); ?></li>
										<li><?php esc_html_e( 'Navigate to Appearance &rarr; Customise &rarr; Typography and select your font in typography controls under Custom Fonts.', 'powerkit' ); ?></li>
									</ol>
								</div>

								<?php if ( 'edit' === $action ) : ?>
									<h3>
										<?php esc_html_e( 'Edit Font', 'powerkit' ); ?>

										<small><a href="<?php echo esc_url( $powerkit_custom_fonts_link ); ?>"><?php esc_html_e( ' cancel ', 'powerkit' ); ?></a></small>
									</h3>
								<?php else : ?>
									<h3><?php esc_html_e( 'Add New Font', 'powerkit' ); ?></h3>
								<?php endif; ?>

								<!-- Name -->
								<div class="form-field form-required">
									<label for="powerkit_custom_fonts_name"><?php esc_html_e( 'Name', 'powerkit' ); ?></label>

									<input id="powerkit_custom_fonts_name" name="powerkit_custom_fonts_name" type="text" aria-required="true" value="<?php echo esc_attr( stripslashes( $params['name'] ) ); ?>" />
								</div>

								<!-- Font File [WOFF] -->
								<div class="form-field">
									<label><?php esc_html_e( 'Font .woff', 'powerkit' ); ?></label>

									<div class="upload-font-container" data-type="application/x-font-woff">

										<?php $file_woff = $params['file_woff'] ? basename( get_attached_file( $params['file_woff'] ) ) : null; ?>

										<p><input class="filename" type="text" placeholder="<?php esc_html_e( 'Choose file..', 'powerkit' ); ?>" readonly value="<?php echo esc_html( $file_woff ); ?>"></p>

										<!-- Add & remove file links -->
										<div class="submitbox">
											<a class="upload-font-link button <?php echo esc_attr( $params['file_woff'] ? 'hidden' : '' ); ?>" href="#"><?php esc_html_e( 'Add File', 'powerkit' ); ?></a>
											<a class="delete-font-link submitdelete <?php echo esc_attr( ! $params['file_woff'] ? 'hidden' : '' ); ?>" href="#"><?php esc_html_e( 'Remove', 'powerkit' ); ?></a>
										</div>

										<input class="uploaded-font-id" id="powerkit_custom_fonts_file_woff" name="powerkit_custom_fonts_file_woff" type="hidden" value="<?php echo esc_attr( stripslashes( $params['file_woff'] ) ); ?>" />
									</div>
								</div>

								<!-- Font File [WOFF2] -->
								<div class="form-field">
									<label><?php esc_html_e( 'Font .woff2', 'powerkit' ); ?></label>

									<div class="upload-font-container" data-type="application/x-font-woff2">

										<?php $file_woff2 = $params['file_woff2'] ? basename( get_attached_file( $params['file_woff2'] ) ) : null; ?>

										<p><input class="filename" type="text" placeholder="<?php esc_html_e( 'Choose file..', 'powerkit' ); ?>" readonly value="<?php echo esc_html( $file_woff2 ); ?>"></p>

										<!-- Add & remove file links -->
										<div class="submitbox">
											<a class="upload-font-link button <?php echo esc_attr( $params['file_woff2'] ? 'hidden' : '' ); ?>" href="#"><?php esc_html_e( 'Add File', 'powerkit' ); ?></a>
											<a class="delete-font-link submitdelete <?php echo esc_attr( ! $params['file_woff2'] ? 'hidden' : '' ); ?>" href="#"><?php esc_html_e( 'Remove', 'powerkit' ); ?></a>
										</div>

										<input class="uploaded-font-id" id="powerkit_custom_fonts_file_woff2" name="powerkit_custom_fonts_file_woff2" type="hidden" value="<?php echo esc_attr( stripslashes( $params['file_woff2'] ) ); ?>" />
									</div>
								</div>

								<!-- Font Weight -->
								<div class="form-field">
									<label for="powerkit_custom_fonts_weight"><?php esc_html_e( 'Weight', 'powerkit' ); ?></label>

									<select class="regular-text" id="powerkit_custom_fonts_weight" name="powerkit_custom_fonts_weight">
										<option value="100" <?php selected( '100', $params['weight'] ); ?>><?php esc_html_e( '100', 'powerkit' ); ?></option>
										<option value="200" <?php selected( '200', $params['weight'] ); ?>><?php esc_html_e( '200', 'powerkit' ); ?></option>
										<option value="300" <?php selected( '300', $params['weight'] ); ?>><?php esc_html_e( '300', 'powerkit' ); ?></option>
										<option value="400" <?php selected( '400', $params['weight'] ); ?>><?php esc_html_e( '400 (regular)', 'powerkit' ); ?></option>
										<option value="500" <?php selected( '500', $params['weight'] ); ?>><?php esc_html_e( '500', 'powerkit' ); ?></option>
										<option value="600" <?php selected( '600', $params['weight'] ); ?>><?php esc_html_e( '600', 'powerkit' ); ?></option>
										<option value="700" <?php selected( '700', $params['weight'] ); ?>><?php esc_html_e( '700', 'powerkit' ); ?></option>
										<option value="800" <?php selected( '800', $params['weight'] ); ?>><?php esc_html_e( '800', 'powerkit' ); ?></option>
										<option value="900" <?php selected( '900', $params['weight'] ); ?>><?php esc_html_e( '900', 'powerkit' ); ?></option>
									</select>
								</div>

								<!-- Font Style -->
								<div class="form-field">
									<label for="powerkit_custom_fonts_style"><?php esc_html_e( 'Style', 'powerkit' ); ?></label>

									<select class="regular-text" id="powerkit_custom_fonts_style" name="powerkit_custom_fonts_style">
										<option value="normal" <?php selected( 'normal', $params['style'] ); ?>><?php esc_html_e( 'normal', 'powerkit' ); ?></option>
										<option value="italic" <?php selected( 'italic', $params['style'] ); ?>><?php esc_html_e( 'italic', 'powerkit' ); ?></option>
									</select>
								</div>

								<input name="powerkit_custom_fonts_id" type="hidden" value="<?php echo esc_html( $powerkit_custom_fonts_id ); ?>" />

								<?php wp_nonce_field(); ?>

								<?php if ( 'edit' === $action ) : ?>
									<p class="submit"><input class="button button-primary" name="edit_settings" type="submit" value="<?php esc_html_e( 'Save Change', 'powerkit' ); ?>" /></p>
								<?php else : ?>
									<p class="submit"><input class="button button-primary" name="save_settings" type="submit" value="<?php esc_html_e( 'Add Font', 'powerkit' ); ?>" /></p>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div id="col-right">
						<div class="col-wrap wrap">
							<table class="wrap wp-list-table widefat fixed striped">
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
									$families = get_option( 'powerkit_custom_fonts_list', array() );

									// Sort families.
									if ( is_array( $families ) && $families ) {
										$families_keys = array_keys( $families );

										foreach ( $families as $key => $row ) {
											$families_value[ $key ] = $row['name'];
											$families_name[ $key ]  = $row['weight'];
											$families_order[ $key ] = $row['style'];
										}
										array_multisort( $families_value, SORT_DESC, $families_order, SORT_DESC, $families_name, SORT_ASC, $families, $families_keys );

										$families = array_combine( $families_keys, $families );
									}

									// Loop families.
									if ( $families ) {
										foreach ( $families as $key => $family ) {

											$edit_link = add_query_arg(
												array(
													'_wpnonce' => wp_create_nonce(),
													'powerkit_custom_fonts_id' => $key,
													'action'   => 'edit',
												), $powerkit_custom_fonts_link
											);

											$delete_link = add_query_arg(
												array(
													'_wpnonce' => wp_create_nonce(),
													'powerkit_custom_fonts_id' => $key,
													'action'   => 'delete',
												), $powerkit_custom_fonts_link
											);
											?>
											<tr>
												<td scope="col" class="manage-column">
													<a class="row-title" href="<?php echo esc_url( $edit_link ); ?>"><?php echo esc_html( $family['name'] ); ?></a>

													<div class="row-actions">
														<span class="edit">
															<a href="<?php echo esc_url( $edit_link ); ?>" role="button"><?php esc_html_e( 'Edit', 'powerkit' ); ?></a> |
														</span>
														<span class="delete">
															<a href="<?php echo esc_url( $delete_link ); ?>" class="powerkit-fonts-delete" role="button"><?php esc_html_e( 'Delete', 'powerkit' ); ?></a> |
														</span>
														<span class="view">
															<a href="#" class="powerkit-fonts-view-code" role="button"><?php esc_html_e( 'View Code', 'powerkit' ); ?></a>
														</span>
													</div>
												</td>
												<td scope="col" class="manage-column"><?php echo esc_html( $family['slug'] ); ?></td>
												<td scope="col" class="manage-column"><?php echo esc_html( $family['weight'] ); ?></td>
												<td scope="col" class="manage-column"><?php echo esc_html( $family['style'] ); ?></td>
											</tr>
											<tr class="template-code hidden">
												<td scope="col" class="manage-column" colspan="4">
												<?php
												// Example code.
												$code = sprintf(
													'h1 {
														font-family: "%s";
														font-weight: %s;
														font-style: %s;
													}',
													$family['slug'],
													$family['weight'],
													$family['style']
												);

												$code = str_replace( "\t\t", '', $code );
												?>
													<div id="template" class="template-box hidden">
														<textarea readonly="readonly"><?php echo wp_kses( $code, 'post' ); ?></textarea>
													</div>
												</td>
											</tr>
											<tr class="hidden"></tr>
											<?php
										}
									} else {
										?>
											<tr>
												<td scope="col" colspan="4"><?php esc_html_e( 'No fonts found. Add new fonts with the form to the left.', 'powerkit' ); ?></td>
											</tr>
										<?php
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</form>
			<?php
		}

		/**
		 * Delete font
		 *
		 * @since 1.0.0
		 */
		protected function delete_font() {
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'] ) ) { // Input var ok; sanitization ok.
				return;
			}

			if ( ! isset( $_GET['powerkit_custom_fonts_id'] ) ) { // Input var ok.
				return;
			}

			// ID Font.
			$id = sanitize_key( $_GET['powerkit_custom_fonts_id'] ); // Input var ok.

			// Custom List.
			$font_list = (array) get_option( 'powerkit_custom_fonts_list', array() );

			// If exist font.
			if ( isset( $font_list[ $id ] ) ) {

				// Attachments delete.
				if ( isset( $font_list[ $id ]['file_woff'] ) ) {
					wp_delete_attachment( $font_list[ $id ]['file_woff'], true );
				}
				if ( isset( $font_list[ $id ]['file_woff2'] ) ) {
					wp_delete_attachment( $font_list[ $id ]['file_woff2'], true );
				}

				// Delete font.
				unset( $font_list[ $id ] );

				// Save list.
				update_option( 'powerkit_custom_fonts_list', $font_list );

				// Output message.
				printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Font successfully deleted.', 'powerkit' ) );
			}
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

			if ( ! isset( $_POST['powerkit_custom_fonts_id'] ) ) { // Input var ok.
				return;
			}

			// Update id counter.
			if ( isset( $_POST['save_settings'] ) ) { // Input var ok.
				update_option( 'powerkit_custom_fonts_counter', intval( get_option( 'powerkit_custom_fonts_counter', 1 ) ) + 1 );
			}

			// ID Font.
			$id = sanitize_key( $_POST['powerkit_custom_fonts_id'] ); // Input var ok.

			// Custom List.
			$font_list = (array) get_option( 'powerkit_custom_fonts_list', array() );

			// Reset current settings.
			$font_list[ $id ] = array();

			// Set new settings.
			if ( isset( $_POST['powerkit_custom_fonts_name'] ) ) { // Input var ok.
				$font_list[ $id ]['slug'] = sanitize_title( $_POST['powerkit_custom_fonts_name'] ); // Input var ok; sanitization ok.
			}
			if ( isset( $_POST['powerkit_custom_fonts_name'] ) ) { // Input var ok.
				$font_list[ $id ]['name'] = sanitize_text_field( $_POST['powerkit_custom_fonts_name'] ); // Input var ok; sanitization ok.
			}
			if ( isset( $_POST['powerkit_custom_fonts_file_woff'] ) ) { // Input var ok.
				$font_list[ $id ]['file_woff'] = sanitize_text_field( $_POST['powerkit_custom_fonts_file_woff'] ); // Input var ok; sanitization ok.
			}
			if ( isset( $_POST['powerkit_custom_fonts_file_woff2'] ) ) { // Input var ok.
				$font_list[ $id ]['file_woff2'] = sanitize_text_field( $_POST['powerkit_custom_fonts_file_woff2'] ); // Input var ok; sanitization ok.
			}
			if ( isset( $_POST['powerkit_custom_fonts_weight'] ) ) { // Input var ok.
				$font_list[ $id ]['weight'] = sanitize_text_field( $_POST['powerkit_custom_fonts_weight'] ); // Input var ok; sanitization ok.
			}
			if ( isset( $_POST['powerkit_custom_fonts_style'] ) ) { // Input var ok.
				$font_list[ $id ]['style'] = sanitize_text_field( $_POST['powerkit_custom_fonts_style'] ); // Input var ok; sanitization ok.
			}

			// Save list.
			update_option( 'powerkit_custom_fonts_list', $font_list );

			// Output message.
			printf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Settings saved.', 'powerkit' ) );
		}

		/**
		 * Register fonts in the editor.
		 */
		public function editor_enqueue() {
			global $pagenow;

			if ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) {
				$this->frontend_enqueue();
			}
		}

		/**
		 * Frontend enqueue
		 *
		 * @since 1.0.0
		 */
		public function frontend_enqueue() {

			$custom_fonts = get_option( 'powerkit_custom_fonts_list' );

			if ( is_array( $custom_fonts ) && $custom_fonts ) {

				$exclude = array();

				$font_face = null;

				foreach ( $custom_fonts as $key => $item ) {

					$id = sanitize_title( $item['name'] );

					if ( $id ) {
						$src   = array();
						$files = array();

						$files['woff']  = wp_get_attachment_url( $item['file_woff'] );
						$files['woff2'] = wp_get_attachment_url( $item['file_woff2'] );

						foreach ( $files as $key_file => $file_url ) {
							if ( $file_url ) {
								$src[] = sprintf( 'url("%1$s") format("%2$s")', $file_url, $key_file );
							}
						}

						if ( $src ) {
							$src_line = implode( ',', $src );

							$display = 'async' === $this->load_method ? 'swap' : 'auto';

							$font_face .= sprintf( '@font-face { font-family: "%1$s"; src: %2$s; font-display: %3$s; font-weight: %4$s; font-style: %5$s;}', $id, $src_line, $display, $item['weight'], $item['style'] );
						}
					}
				}

				if ( $font_face ) {
					?>
						<style><?php print( $font_face ); // XSS. ?></style>
					<?php
				}
			}
		}
	}

	new Powerkit_Custom_Fonts();
}
